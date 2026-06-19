<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentMethod;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function showConfirmation($orderId)
    {
        $order = Order::findOrFail($orderId);
        $paymentMethods = PaymentMethod::active()->ordered()->get();
        
        return view('payment.confirmation', compact('order', 'paymentMethods'));
    }

    public function processPayment(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|string',
            'payment_reference' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Update order with payment method
        $order->update([
            'payment_method' => $request->payment_method,
            'payment_reference' => $request->payment_reference,
            'status' => 'Pending Pembayaran',
        ]);

        // Redirect based on payment method
        if ($request->payment_method === 'bank_transfer') {
            return redirect()->route('payment.bank-transfer', $order->id);
        } elseif (in_array($request->payment_method, ['gopay', 'ovo', 'dana'])) {
            return redirect()->route('payment.ewallet', $order->id);
        }

        return back()->with('error', 'Metode pembayaran tidak valid.');
    }

    public function showBankTransfer($orderId)
    {
        $order = Order::findOrFail($orderId);
        
        // Bank account information (you can move this to config or database)
        $bankAccounts = [
            [
                'bank' => 'BCA',
                'account_number' => '1234567890',
                'account_name' => 'CarWash Connect',
                'branch' => 'Cabang Utama'
            ],
            [
                'bank' => 'Mandiri',
                'account_number' => '0987654321',
                'account_name' => 'CarWash Connect',
                'branch' => 'Cabang Utama'
            ],
            [
                'bank' => 'BNI',
                'account_number' => '1122334455',
                'account_name' => 'CarWash Connect',
                'branch' => 'Cabang Utama'
            ]
        ];

        return view('payment.bank-transfer', compact('order', 'bankAccounts'));
    }

    public function uploadPaymentProof(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        
        $validator = Validator::make($request->all(), [
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'payment_notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Store payment proof
        $file = $request->file('payment_proof');
        $filename = 'payment_proof_' . $orderId . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('payment-proofs', $filename, 'public');

        $order->update([
            'payment_proof' => $path,
            'payment_notes' => $request->payment_notes,
        ]);

        // Send notification
        NotificationService::paymentUploaded($order);

        return redirect()->route('payment.status', $order->id)
            ->with('success', 'Bukti pembayaran berhasil diupload. Tim kami akan memverifikasi dalam 1x24 jam.');
    }

    public function showEwallet($orderId)
    {
        $order = Order::findOrFail($orderId);
        
        // Generate mock payment data (in real implementation, this would come from payment gateway)
        $paymentData = [
            'qr_code' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==',
            'payment_url' => 'https://payment-gateway.com/pay/' . $orderId,
            'expires_at' => now()->addMinutes(15)->format('Y-m-d H:i:s'),
        ];

        return view('payment.ewallet', compact('order', 'paymentData'));
    }

    public function checkPaymentStatus($orderId)
    {
        $order = Order::findOrFail($orderId);
        
        // In real implementation, this would check with payment gateway
        // For now, we'll simulate the check
        if ($order->payment_method === 'bank_transfer') {
            // Bank transfer requires manual verification
            return view('payment.status', compact('order'));
        } else {
            // E-wallet payment - simulate automatic verification
            if (now()->diffInMinutes($order->created_at) > 5) {
                $order->update([
                    'payment_status' => 'verified',
                    'status' => 'Terkonfirmasi',
                    'payment_verified_at' => now(),
                ]);
            }
            
            return view('payment.status', compact('order'));
        }
    }

    public function verifyPayment(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        if ($request->action === 'approve') {
            $order->update([
                'payment_status' => 'verified',
                'status' => 'Menunggu',
                'payment_verified_at' => now(),
                'verified_by' => auth()->id(),
                'payment_notes' => $request->notes,
            ]);
            
            // Send notification
            NotificationService::paymentVerified($order);
            
            $message = 'Pembayaran berhasil diverifikasi.';
        } else {
            $order->update([
                'payment_status' => 'failed',
                'payment_notes' => $request->notes,
            ]);
            
            // Send notification
            NotificationService::paymentFailed($order);
            
            $message = 'Pembayaran ditolak.';
        }

        return back()->with('success', $message);
    }
}
