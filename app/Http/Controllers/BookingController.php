<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function create()
    {
        $services = Service::all();
        return view('booking_form', compact('services'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'service_type' => 'required|string',
            'booking_date' => 'required|date',
            'license_plate' => 'required|string|max:20',
        ]);

        $queueNumber = $this->generateQueueNumber();

        $order = Order::create([
            'user_id' => null,
            'service_type' => $validatedData['service_type'],
            'booking_date' => $validatedData['booking_date'],
            'license_plate' => $validatedData['license_plate'],
            'queue_number' => $queueNumber,
            'status' => 'Menunggu',
        ]);

        return redirect('/status-pesanan')->with('success', 'Pesanan berhasil dibuat! Nomor antrean Anda: ' . $queueNumber);
    }

    private function generateQueueNumber()
    {
        $lastOrder = Order::orderBy('queue_number', 'desc')->first();
        
        if ($lastOrder) {
            return $lastOrder->queue_number + 1;
        }
        
        return 1;
    }

    public function status()
    {
        $activeOrders = Order::whereIn('status', ['Menunggu', 'Proses'])
            ->orderBy('queue_number', 'asc')
            ->get();
        
        return view('status', compact('activeOrders'));
    }

    public function history()
    {
        $completedOrders = Order::where('status', 'Selesai')
            ->orderBy('updated_at', 'desc')
            ->get();
        
        return view('history', compact('completedOrders'));
    }
}
