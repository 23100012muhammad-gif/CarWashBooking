<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingSlot;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingSlotController extends Controller
{
    public function index()
    {
        $startDate = request('start_date') ? Carbon::parse(request('start_date')) : Carbon::today();
        $endDate = request('end_date') ? Carbon::parse(request('end_date')) : $startDate->copy()->addDays(7);

        $slots = BookingSlot::whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy('tanggal');

        return view('admin.booking-slots.index', compact('slots', 'startDate', 'endDate'));
    }

    public function store(Request $request)
    {
        // Cek apakah ini request dari sistem baru atau lama
        if ($request->has('jumlah_slot')) {
            // Sistem baru - redirect ke SimpleScheduleController
            $controller = new \App\Http\Controllers\Admin\SimpleScheduleController();
            return $controller->store($request);
        }
        
        // Sistem lama
        $validatedData = $request->validate([
            'tanggal' => 'required|date|after_or_equal:today',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'kapasitas' => 'required|integer|min:1|max:10'
        ]);

        BookingSlot::create([
            'tanggal' => $validatedData['tanggal'],
            'jam_mulai' => $validatedData['jam_mulai'] . ':00',
            'jam_selesai' => $validatedData['jam_selesai'] . ':00',
            'kapasitas' => $validatedData['kapasitas'],
            'terisi' => 0,
            'status' => 'tersedia',
            'created_by' => Auth::id()
        ]);

        return redirect()->back()->with('success', 'Slot booking berhasil ditambahkan');
    }

    public function update(BookingSlot $slot, Request $request)
    {
        $validatedData = $request->validate([
            'kapasitas' => 'sometimes|required|integer|min:1|max:10',
            'status' => 'sometimes|required|in:tersedia,nonaktif'
        ]);

        $slot->update($validatedData);

        return redirect()->back()->with('success', 'Slot booking berhasil diperbarui');
    }

    public function bulkCreate(Request $request)
    {
        $validatedData = $request->validate([
            'tanggal' => 'required|date|after_or_equal:today',
            'jam_mulai' => 'required|array',
            'jam_mulai.*' => 'required|date_format:H:i',
            'jam_selesai' => 'required|array',
            'jam_selesai.*' => 'required|date_format:H:i',
            'kapasitas' => 'required|integer|min:1|max:10'
        ]);

        $created = 0;
        foreach ($validatedData['jam_mulai'] as $index => $startTime) {
            if (isset($validatedData['jam_selesai'][$index])) {
                BookingSlot::firstOrCreate(
                    [
                        'tanggal' => $validatedData['tanggal'],
                        'jam_mulai' => $startTime,
                        'jam_selesai' => $validatedData['jam_selesai'][$index],
                    ],
                    [
                        'kapasitas' => $validatedData['kapasitas'],
                        'terisi' => 0,
                        'status' => 'tersedia',
                        'created_by' => Auth::id()
                    ]
                );
                $created++;
            }
        }

        return redirect()->back()->with('success', "Berhasil membuat $created slot booking");
    }
}