<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingSlot;
use App\Models\OperationalDay;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SlotController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get all slots for a given month
     */
    public function index(Request $request)
    {
        // If the request is an AJAX request or includes a 'month' parameter,
        // return JSON data for the calendar.
        if ($request->ajax() || $request->has('month')) {
            $month = $request->input('month', date('Y-m'));
            
            try {
                $date = Carbon::createFromFormat('Y-m', $month);
                $slots = BookingSlot::whereBetween('tanggal', [
                    $date->startOfMonth()->format('Y-m-d'),
                    $date->endOfMonth()->format('Y-m-d')
                ])->orderBy('tanggal')->orderBy('jam_mulai')->get();

                return response()->json([
                    'status' => 'success',
                    'data' => $slots
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid month format'
                ], 400);
            }
        }

        // Non-AJAX: render the admin page
        return view('admin.jadwal-slot-simple');
    }

    /**
     * Create a new booking slot
     */
    public function store(Request $request)
    {
        \Log::debug('Received slot creation request:', $request->all());
        
        $validated = $request->validate([
            'tanggal' => 'required|date|after_or_equal:today',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'kapasitas' => 'required|integer|min:1'
        ]);

        // Check if the day is operational (optional check)
        $englishDay = Carbon::parse($validated['tanggal'])->format('l');
        $mapEnToId = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu',
        ];

        $hariKey = $mapEnToId[$englishDay] ?? $englishDay;
        $operationalDay = OperationalDay::where('hari', $hariKey)->first();

        // Jika belum ada hari operasional, buat default
        if (!$operationalDay) {
            $operationalDay = OperationalDay::create([
                'hari' => $hariKey,
                'status_operasional' => true,
                'jam_buka' => '08:00',
                'jam_tutup' => '18:00',
                'created_by' => auth()->id()
            ]);
        }
        
        if (!$operationalDay->status_operasional) {
            return response()->json([
                'status' => 'error',
                'message' => 'Hari tersebut tidak operasional. Aktifkan dulu di pengaturan hari operasional.'
            ], 422);
        }

        // Input times should be in H:i format from the form's time inputs
        $jamMulai = $validated['jam_mulai'] . ':00';
        $jamSelesai = $validated['jam_selesai'] . ':00';
        
        // Get operational hours in local time
        $jamBuka = Carbon::parse($operationalDay->jam_buka)
            ->setTimezone(config('app.timezone'))
            ->format('H:i:s');
        $jamTutup = Carbon::parse($operationalDay->jam_tutup)
            ->setTimezone(config('app.timezone'))
            ->format('H:i:s');
            
        \Log::debug('Time comparison after timezone adjustment:', [
            'input_utc_mulai' => $validated['jam_mulai'],
            'input_utc_selesai' => $validated['jam_selesai'],
            'local_mulai' => $jamMulai,
            'local_selesai' => $jamSelesai,
            'operational_buka' => $jamBuka,
            'operational_tutup' => $jamTutup,
            'timezone' => config('app.timezone')
        ]);

        \Log::debug('Comparing times:', [
            'input_mulai' => $validated['jam_mulai'],
            'input_selesai' => $validated['jam_selesai'],
            'normalized_mulai' => $jamMulai,
            'normalized_selesai' => $jamSelesai,
            'jam_buka' => $jamBuka,
            'jam_tutup' => $jamTutup
        ]);

        // If operational hours are set, ensure the slot times fall within them
        if (($jamBuka && $jamMulai < $jamBuka) || ($jamTutup && $jamSelesai > $jamTutup)) {
            \Log::debug('Slot time validation:', [
                'jam_mulai' => $jamMulai,
                'jam_selesai' => $jamSelesai,
                'operational_jam_buka' => $operationalDay->jam_buka,
                'operational_jam_tutup' => $operationalDay->jam_tutup
            ]);
            $errorDetail = '';
            if ($jamBuka && $jamMulai < $jamBuka) {
                $errorDetail = 'Jam mulai terlalu pagi';
            } elseif ($jamTutup && $jamSelesai > $jamTutup) {
                $errorDetail = 'Jam selesai terlalu malam';
            }
            
            return response()->json([
                'status' => 'error',
                'message' => sprintf(
                    'Jam tidak sesuai dengan jam operasional - %s (Operasional: %s - %s, Slot: %s - %s)',
                    $errorDetail,
                    $jamBuka ?? 'tidak diatur',
                    $jamTutup ?? 'tidak diatur',
                    $jamMulai,
                    $jamSelesai
                ),
                'debug' => [
                    'input_jam_mulai' => $validated['jam_mulai'],
                    'input_jam_selesai' => $validated['jam_selesai'],
                    'normalized_mulai' => $jamMulai,
                    'normalized_selesai' => $jamSelesai,
                    'hari' => $operationalDay->hari,
                    'jam_buka' => $jamBuka,
                    'jam_tutup' => $jamTutup,
                    'comparison_mulai' => $jamMulai < $jamBuka ? 'terlalu_pagi' : 'ok',
                    'comparison_selesai' => $jamSelesai > $jamTutup ? 'terlalu_malam' : 'ok'
                ]
            ], 422);
        }

        // Create slot using normalized times (store as H:i:s)
        $slotData = $validated;
        $slotData['jam_mulai'] = $jamMulai;
        $slotData['jam_selesai'] = $jamSelesai;

        $slot = BookingSlot::create(array_merge($slotData, [
            'created_by' => auth()->id(),
            'status' => 'tersedia'
        ]));

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Slot berhasil dibuat',
                'data' => $slot
            ]);
        }
        
        return redirect()->route('admin.slots.index')->with('success', 'Slot berhasil dibuat');
    }

    /**
     * Update an existing booking slot
     */
    public function update(Request $request, BookingSlot $slot)
    {
        $validated = $request->validate([
            'jam_mulai' => 'sometimes|required|date_format:H:i',
            'jam_selesai' => 'sometimes|required|date_format:H:i|after:jam_mulai',
            'kapasitas' => 'sometimes|required|integer|min:' . ($slot->terisi ?: 1),
            'status' => 'sometimes|required|in:tersedia,nonaktif'
        ]);

        // If updating time, normalize the format
        if (isset($validated['jam_mulai'])) {
            $validated['jam_mulai'] = $validated['jam_mulai'] . ':00';
        }
        if (isset($validated['jam_selesai'])) {
            $validated['jam_selesai'] = $validated['jam_selesai'] . ':00';
        }

        $slot->update($validated);

        if ($slot->wasChanged('kapasitas') && $slot->kapasitas <= $slot->terisi) {
            $slot->update(['status' => 'penuh']);
            
            // Notify admin about slot being full
            $this->notificationService->createSystemNotification(
                null,
                'Slot Booking Penuh',
                "Slot pada tanggal {$slot->tanggal} jam {$slot->jam_mulai} - {$slot->jam_selesai} telah penuh",
                ['slot_id' => $slot->id]
            );
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Slot berhasil diupdate',
            'data' => $slot
        ]);
    }

    /**
     * Update operational days settings
     */
    public function updateOperationalDays(Request $request)
    {
        // Get form data
        $all = $request->all();
        $days = [];
        
        // Parse form data dengan format days[Hari][field]
        foreach ($all as $key => $value) {
            if (preg_match('/^days\[(.+?)\]\[(.+?)\]$/', $key, $matches)) {
                $dayName = $matches[1];
                $field = $matches[2];
                $days[$dayName][$field] = $value;
            }
        }
        
        \Log::info('Parsed operational days data:', $days);

        DB::transaction(function () use ($days) {
            foreach ($days as $dayName => $settings) {
                $status = isset($settings['status_operasional']) ? true : false;
                $jam_buka = $settings['jam_buka'] ?? '08:00';
                $jam_tutup = $settings['jam_tutup'] ?? '18:00';

                OperationalDay::updateOrCreate(
                    ['hari' => $dayName],
                    [
                        'status_operasional' => $status,
                        'jam_buka' => $jam_buka,
                        'jam_tutup' => $jam_tutup,
                        'created_by' => auth()->id(),
                    ]
                );
                
                \Log::info('Saved operational day:', [
                    'hari' => $dayName,
                    'status' => $status,
                    'jam_buka' => $jam_buka,
                    'jam_tutup' => $jam_tutup
                ]);
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Jadwal operasional berhasil diupdate'
        ]);
    }

    /**
     * Delete a booking slot
     */
    public function destroy(BookingSlot $slot)
    {
        // Check if slot has bookings
        if ($slot->terisi > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak dapat menghapus slot yang sudah memiliki booking'
            ], 422);
        }

        $slot->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Slot berhasil dihapus'
        ]);
    }

    /**
     * Disable a specific date (e.g., for holidays)
     */
    public function disableDate(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date|after_or_equal:today'
        ]);

        DB::transaction(function () use ($validated) {
            BookingSlot::where('tanggal', $validated['tanggal'])
                      ->where('status', '!=', 'penuh')
                      ->update(['status' => 'nonaktif']);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Tanggal berhasil dinonaktifkan'
        ]);
    }
}