<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalOrders = Order::count();
        $waitingOrders = Order::where('status', 'Menunggu')->count();
        $processingOrders = Order::where('status', 'Proses')->count();
        $completedOrders = Order::where('status', 'Selesai')->count();

        return view('admin.dashboard', compact(
            'totalOrders',
            'waitingOrders',
            'processingOrders',
            'completedOrders'
        ));
    }

    public function orders()
    {
        $orders = Order::orderBy('queue_number', 'asc')->get();
        
        return view('admin.orders', compact('orders'));
    }

    public function updateOrder(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $validatedData = $request->validate([
            'status' => 'required|in:Menunggu,Proses,Selesai',
        ]);

        $order->status = $validatedData['status'];
        $order->save();

        return redirect('/admin/orders')->with('success', 'Status pesanan berhasil diperbarui!');
    }
}
