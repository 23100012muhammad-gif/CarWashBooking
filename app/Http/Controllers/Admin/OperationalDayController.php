<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OperationalDay;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperationalDayController extends Controller
{
    public function index()
    {
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $operationalDays = OperationalDay::all()->keyBy('hari');
        
        return view('admin.operational-days.index', compact('days', 'operationalDays'));
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'days' => 'required|array',
            'days.*' => 'required|array',
            'days.*.status' => 'required|boolean',
            'days.*.jam_buka' => 'required|date_format:H:i',
            'days.*.jam_tutup' => 'required|date_format:H:i|after:days.*.jam_buka'
        ]);

        foreach ($validatedData['days'] as $hari => $data) {
            OperationalDay::updateOrCreate(
                ['hari' => $hari],
                [
                    'status_operasional' => $data['status'],
                    'jam_buka' => $data['jam_buka'],
                    'jam_tutup' => $data['jam_tutup'],
                    'created_by' => Auth::id()
                ]
            );
        }

        return redirect()->back()->with('success', 'Jadwal operasional berhasil diperbarui');
    }

    public function generateSlots(Request $request)
    {
        $validatedData = $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'slot_duration' => 'required|integer|min:30|max:120'
        ]);

        $startDate = Carbon::parse($validatedData['start_date']);
        $endDate = Carbon::parse($validatedData['end_date']);
        $slotDuration = $validatedData['slot_duration'];

        $operationalDays = OperationalDay::where('status_operasional', true)->get()->keyBy('hari');
        $slotsGenerated = 0;
        
        \Log::info('Generate slots request:', [
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'slot_duration' => $slotDuration,
            'operational_days_count' => $operationalDays->count()
        ]);

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $englishDay = $date->format('l');
            $mapEnToId = [
                'Monday' => 'Senin',
                'Tuesday' => 'Selasa', 
                'Wednesday' => 'Rabu',
                'Thursday' => 'Kamis',
                'Friday' => 'Jumat',
                'Saturday' => 'Sabtu',
                'Sunday' => 'Minggu',
            ];
            $dayName = $mapEnToId[$englishDay] ?? $englishDay;
            
            \Log::info('Processing date:', [
                'date' => $date->toDateString(),
                'english_day' => $englishDay,
                'indonesian_day' => $dayName,
                'is_operational' => isset($operationalDays[$dayName])
            ]);
            
            if (!isset($operationalDays[$dayName])) {
                \Log::info('Skipping day (not operational):', ['date' => $date->toDateString(), 'day' => $dayName]);
                continue;
            }

            $opDay = $operationalDays[$dayName];
            $jamBuka = Carbon::parse($opDay->jam_buka);
            $jamTutup = Carbon::parse($opDay->jam_tutup);
            
            // Hapus slot existing untuk tanggal ini dulu
            \App\Models\BookingSlot::where('tanggal', $date->toDateString())->delete();
            
            $currentHour = (int) $jamBuka->format('H');
            $currentMinute = (int) $jamBuka->format('i');
            $endHour = (int) $jamTutup->format('H');
            $endMinute = (int) $jamTutup->format('i');
            
            $currentTime = Carbon::createFromTime($currentHour, $currentMinute, 0);
            $endTime = Carbon::createFromTime($endHour, $endMinute, 0);
            
            \Log::info('Generating slots for date:', [
                'date' => $date->toDateString(),
                'start_time' => $currentTime->format('H:i'),
                'end_time' => $endTime->format('H:i'),
                'duration' => $slotDuration
            ]);
            
            while ($currentTime->copy()->addMinutes($slotDuration)->lte($endTime)) {
                $slotEnd = $currentTime->copy()->addMinutes($slotDuration);
                
                \App\Models\BookingSlot::create([
                    'tanggal' => $date->toDateString(),
                    'jam_mulai' => $currentTime->format('H:i:s'),
                    'jam_selesai' => $slotEnd->format('H:i:s'),
                    'kapasitas' => 4,
                    'terisi' => 0,
                    'status' => 'tersedia',
                    'created_by' => Auth::id()
                ]);

                \Log::info('Created slot:', [
                    'date' => $date->toDateString(),
                    'start' => $currentTime->format('H:i'),
                    'end' => $slotEnd->format('H:i')
                ]);

                $slotsGenerated++;
                $currentTime->addMinutes($slotDuration);
            }
        }

        return redirect()->back()->with('success', "Berhasil membuat $slotsGenerated slot booking");
    }
}