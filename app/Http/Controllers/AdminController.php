<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Service;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalOrders = Order::count();
        $waitingOrders = Order::where('status', 'Menunggu')->count();
        $processingOrders = Order::where('status', 'Proses')->count();
        $completedOrders = Order::where('status', 'Selesai')->count();
        $pendingPayments = Order::where('payment_status', 'pending')->count();
        $verifiedPayments = Order::where('payment_status', 'verified')->count();
        $refundRequests = Order::whereNotNull('refund_requested_at')->whereNull('refund_processed_at')->count();

        return view('admin.dashboard', compact(
            'totalOrders',
            'waitingOrders',
            'processingOrders',
            'completedOrders',
            'pendingPayments',
            'verifiedPayments',
            'refundRequests'
        ));
    }

    public function orders()
    {
        // Hanya tampil order yang sudah terverifikasi pembayarannya untuk kelola proses
        $orders = Order::where('payment_status', 'verified')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Pastikan semua order yang sudah verified tapi masih Terkonfirmasi diubah ke Menunggu
        Order::where('payment_status', 'verified')
            ->where('status', 'Terkonfirmasi')
            ->update(['status' => 'Menunggu']);
        
        return view('admin.orders', compact('orders'));
    }
    
    public function verifications()
    {
        // Order yang perlu verifikasi pembayaran
        $pendingOrders = Order::where('payment_status', 'pending')
            ->whereNotNull('payment_proof')
            ->orderBy('created_at', 'asc')
            ->get();
        
        // Order yang mengajukan refund
        $refundRequests = Order::whereNotNull('refund_requested_at')
            ->whereNull('refund_processed_at')
            ->orderBy('refund_requested_at', 'asc')
            ->get();
        
        return view('admin.verifications', compact('pendingOrders', 'refundRequests'));
    }

    public function pendingPayments()
    {
        $pendingOrders = Order::where('payment_status', 'pending')
            ->whereNotNull('payment_proof')
            ->orderBy('created_at', 'asc')
            ->get();
        
        $refundRequests = Order::whereNotNull('refund_requested_at')
            ->whereNull('refund_processed_at')
            ->orderBy('refund_requested_at', 'asc')
            ->get();
        
        return view('admin.pending-payments', compact('pendingOrders', 'refundRequests'));
    }

    public function updateOrder(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $validatedData = $request->validate([
            'status' => 'required|in:Pending Pembayaran,Terkonfirmasi,Menunggu,Proses,Selesai,Refund,Batal',
        ]);

        $oldStatus = $order->status;
        $order->status = $validatedData['status'];
        $order->save();
        
        // Send notification for status change
        if ($oldStatus !== $validatedData['status']) {
            \Log::info('Order details:', ['order_id' => $order->id, 'user_id' => $order->user_id, 'old_status' => $oldStatus, 'new_status' => $validatedData['status']]);
            
            // Create notification for any user (use order ID as user_id for testing)
            $statusMessages = [
                'Menunggu' => 'Pesanan Anda sedang dalam antrian.',
                'Proses' => 'Pesanan Anda sedang diproses. Mobil sedang dicuci.',
                'Selesai' => 'Pesanan Anda telah selesai. Terima kasih!'
            ];
            
            if (isset($statusMessages[$validatedData['status']])) {
                \App\Models\Notification::create([
                    'type' => 'status_update',
                    'user_id' => $order->user_id ?: 5, // Use order user_id or customer ID 5 for old orders
                    'title' => 'Status Pesanan Diperbarui',
                    'message' => "Pesanan #{$order->id}: {$statusMessages[$validatedData['status']]}",
                    'data' => json_encode(['order_id' => $order->id, 'type' => 'status_update', 'status' => $validatedData['status']]),
                    'is_read' => false
                ]);
                \Log::info('Notification created for status change');
            }
        }

        return redirect()->route('admin.orders')->with('success', 'Status pesanan berhasil diperbarui!');
    }

    public function deleteOrder($id)
    {
        $order = Order::findOrFail($id);
        
        // Only allow deletion of completed orders or refunded orders
        if (in_array($order->status, ['Terkonfirmasi', 'Selesai', 'Refund', 'Batal'])) {
            $order->delete();
            return redirect()->route('admin.orders')->with('success', 'Pesanan berhasil dihapus!');
        }
        
        return redirect()->route('admin.orders')->with('error', 'Pesanan yang belum selesai atau sedang diproses tidak dapat dihapus!');
    }

    public function processRefund(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        if (!$order->hasRefundRequest() || $order->isRefundProcessed()) {
            return redirect()->route('admin.pending-payments')->with('error', 'Pengajuan refund tidak valid!');
        }
        
        $validated = $request->validate([
            'action' => 'required|in:approve,reject'
        ]);
        
        $refundNotes = $request->input('refund_notes', null);
        
        $status = $validated['action'] === 'approve' ? 'Refund' : 'Batal';
        
        $order->update([
            'status' => $status,
            'refund_processed_at' => now(),
            'refund_processed_by' => auth()->id(),
            'refund_notes' => $refundNotes
        ]);
        
        // Send notification
        if ($order->user_id) {
            if ($validated['action'] === 'approve') {
                \App\Models\Notification::create([
                    'type' => 'refund_approved',
                    'user_id' => $order->user_id,
                    'title' => 'Refund Disetujui',
                    'message' => "Pengajuan refund untuk pesanan #{$order->id} telah disetujui. Dana akan dikembalikan dalam 3-5 hari kerja.",
                    'data' => json_encode(['order_id' => $order->id, 'type' => 'refund_approved']),
                    'is_read' => false
                ]);
            } else {
                \App\Models\Notification::create([
                    'type' => 'refund_rejected',
                    'user_id' => $order->user_id,
                    'title' => 'Refund Ditolak',
                    'message' => "Pengajuan refund untuk pesanan #{$order->id} ditolak. {$refundNotes}",
                    'data' => json_encode(['order_id' => $order->id, 'type' => 'refund_rejected']),
                    'is_read' => false
                ]);
            }
        }
        
        $message = $validated['action'] === 'approve' ? 'Refund berhasil disetujui!' : 'Refund berhasil ditolak!';
        return redirect()->route('admin.verifications')->with('success', $message);
    }

    public function services()
    {
        $services = Service::orderBy('created_at', 'desc')->get();
        return view('admin.services.index', compact('services'));
    }

    public function createService()
    {
        return view('admin.services.create');
    }

    public function storeService(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'price' => 'required|integer|min:0',
            'duration' => 'nullable|integer|min:0',
        ]);

        Service::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
        ]);

        return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil ditambahkan');
    }

    public function editService(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    public function updateService(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'price' => 'required|integer|min:0',
            'duration' => 'nullable|integer|min:0',
        ]);

        $original = $service->getOriginal();

        $service->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'duration' => $validated['duration'] ?? $service->duration,
        ]);

        // Log history
        DB::table('service_histories')->insert([
            'service_id' => $service->id,
            'changed_by' => auth()->id(),
            'old_values' => json_encode($original),
            'new_values' => json_encode($service->getAttributes()),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil diperbarui');
    }

    public function destroyService(Service $service)
    {
        // Log delete
        DB::table('service_histories')->insert([
            'service_id' => $service->id,
            'changed_by' => auth()->id(),
            'old_values' => json_encode($service->getAttributes()),
            'new_values' => json_encode(null),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $service->delete();
        return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil dihapus');
    }

    public function discounts()
    {
        $discounts = Discount::orderBy('expires_at', 'asc')->get();
        return view('admin.discounts.index', compact('discounts'));
    }

    public function createDiscount()
    {
        $services = Service::orderBy('name')->get();
        return view('admin.discounts.create', compact('services'));
    }

    public function storeDiscount(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'service_id' => 'required|integer|exists:services,id',
            'code' => 'required|string|max:50|unique:discounts,code',
            'percent' => 'required|integer|min:1|max:100',
            'description' => 'nullable|string',
            'expires_at' => 'required|date',
        ]);

        Discount::create([
            'name' => $validated['name'],
            'service_id' => $validated['service_id'],
            'code' => $validated['code'],
            'percent' => $validated['percent'],
            'description' => $validated['description'] ?? null,
            'expires_at' => $validated['expires_at'],
            'active' => true,
        ]);

        return redirect()->route('admin.discounts.index')->with('success', 'Diskon berhasil ditambahkan');
    }

    public function editDiscount(Discount $discount)
    {
        $services = Service::orderBy('name')->get();
        return view('admin.discounts.edit', compact('discount', 'services'));
    }

    public function updateDiscount(Request $request, Discount $discount)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'service_id' => 'required|integer|exists:services,id',
            'code' => 'required|string|max:50|unique:discounts,code,' . $discount->id,
            'percent' => 'required|integer|min:1|max:100',
            'description' => 'nullable|string',
            'expires_at' => 'required|date',
            'active' => 'required|boolean',
        ]);

        $original = $discount->getOriginal();

        $discount->update($validated);

        DB::table('discount_histories')->insert([
            'discount_id' => $discount->id,
            'changed_by' => auth()->id(),
            'old_values' => json_encode($original),
            'new_values' => json_encode($discount->getAttributes()),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.discounts.index')->with('success', 'Diskon berhasil diperbarui');
    }

    public function destroyDiscount(Discount $discount)
    {
        DB::table('discount_histories')->insert([
            'discount_id' => $discount->id,
            'changed_by' => auth()->id(),
            'old_values' => json_encode($discount->getAttributes()),
            'new_values' => json_encode(null),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $discount->delete();
        return redirect()->route('admin.discounts.index')->with('success', 'Diskon berhasil dihapus');
    }
    public function showPaymentProof($filename)
    {
        // Try both possible paths
        $paths = [
            storage_path('app/public/payment-proofs/' . $filename),
            storage_path('app/public/payment_proofs/' . $filename)
        ];
        
        $path = null;
        foreach ($paths as $p) {
            if (File::exists($p)) {
                $path = $p;
                break;
            }
        }
        
        if (!$path) {
            abort(404);
        }

        $file = File::get($path);
        $type = File::mimeType($path);

        return Response::make($file, 200)->header('Content-Type', $type);
    }
    
    public function profile()
    {
        return view('admin.profile');
    }
    
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id()
        ]);
        
        auth()->user()->update($validated);
        
        return redirect()->route('admin.profile')->with('success', 'Profil berhasil diperbarui!');
    }
}

