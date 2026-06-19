<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingSlot;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SimpleScheduleController extends Controller
{
    public function index()
    {
        return view('admin.jadwal-slot-simple');
    }

    public function store(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');
        $waktuSlots = $request->input('waktu_slot', []);
        $kapasitas = $request->input('kapasitas', 4);
        
        $startDate = Carbon::parse($tanggalMulai);
        $endDate = Carbon::parse($tanggalSelesai);
        
        $slotsCreated = 0;
        
        // Loop untuk setiap tanggal dalam range
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $tanggal = $date->format('Y-m-d');
            
            // Hapus slot lama untuk tanggal ini
            \DB::table('booking_slots')->where('tanggal', $tanggal)->delete();
            
            // Buat slot baru untuk tanggal ini
            foreach ($waktuSlots as $waktu) {
                if (empty(trim($waktu))) continue;
                
                $times = explode('-', trim($waktu));
                if (count($times) === 2) {
                    \DB::table('booking_slots')->insert([
                        'tanggal' => $tanggal,
                        'jam_mulai' => trim($times[0]) . ':00',
                        'jam_selesai' => trim($times[1]) . ':00',
                        'kapasitas' => $kapasitas,
                        'terisi' => 0,
                        'status' => 'tersedia',
                        'created_by' => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    $slotsCreated++;
                }
            }
        }

        return redirect('/admin/jadwal-slot')
            ->with('success', "Berhasil membuat {$slotsCreated} slot untuk {$startDate->format('d/m/Y')} - {$endDate->format('d/m/Y')}");
    }

    public function list()
    {
        try {
            $schedules = DB::table('booking_slots')
                ->select('tanggal', DB::raw('SUM(kapasitas) as slot_count'), DB::raw('COUNT(CASE WHEN status = "tersedia" THEN 1 END) > 0 as active'))
                ->groupBy('tanggal')
                ->orderBy('tanggal')
                ->get()
                ->map(function ($item) {
                    $date = Carbon::parse($item->tanggal);
                    return [
                        'tanggal' => $date->format('Y-m-d'),
                        'hari' => $date->locale('id')->isoFormat('dddd'),
                        'slot_count' => $item->slot_count,
                        'active' => (bool) $item->active
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $schedules
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function toggle(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'active' => 'required|boolean'
        ]);

        $status = $validated['active'] ? 'tersedia' : 'nonaktif';
        
        BookingSlot::where('tanggal', $validated['tanggal'])
            ->update(['status' => $status]);

        return response()->json([
            'status' => 'success',
            'message' => 'Status jadwal berhasil diubah'
        ]);
    }

    public function delete(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date'
        ]);

        // Check if any slots have bookings
        $hasBookings = BookingSlot::where('tanggal', $validated['tanggal'])
            ->where('terisi', '>', 0)
            ->exists();

        if ($hasBookings) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak dapat menghapus jadwal yang sudah memiliki booking'
            ], 422);
        }

        BookingSlot::where('tanggal', $validated['tanggal'])->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Jadwal berhasil dihapus'
        ]);
    }
}