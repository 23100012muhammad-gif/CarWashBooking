<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Order;

class NotificationService
{
    public static function paymentUploaded(Order $order)
    {
        // Notify admins
        Notification::create([
            'type' => 'payment_uploaded',
            'title' => 'Bukti Pembayaran Baru',
            'message' => "Pesanan #{$order->id} telah mengupload bukti pembayaran sebesar Rp " . number_format($order->final_price, 0, ',', '.'),
            'data' => [
                'order_id' => $order->id,
                'amount' => $order->final_price,
                'payment_method' => $order->payment_method,
            ],
            'order_id' => $order->id,
            'user_id' => null, // Admin notification
        ]);

        // Notify user (if they have an account)
        if ($order->user_id) {
            Notification::create([
                'type' => 'payment_uploaded',
                'title' => 'Bukti Pembayaran Terkirim',
                'message' => "Bukti pembayaran untuk pesanan #{$order->id} telah dikirim. Tim kami akan memverifikasi dalam 1x24 jam.",
                'data' => [
                    'order_id' => $order->id,
                    'amount' => $order->final_price,
                ],
                'order_id' => $order->id,
                'user_id' => $order->user_id,
            ]);
        }
    }

    public static function paymentVerified(Order $order)
    {
        // Notify user (if they have an account)
        if ($order->user_id) {
            Notification::create([
                'type' => 'payment_verified',
                'title' => 'Pembayaran Diverifikasi',
                'message' => "Pembayaran untuk pesanan #{$order->id} telah diverifikasi. Pesanan Anda telah dikonfirmasi.",
                'data' => [
                    'order_id' => $order->id,
                    'amount' => $order->final_price,
                    'verified_by' => $order->verified_by,
                ],
                'order_id' => $order->id,
                'user_id' => $order->user_id,
            ]);
        }
    }

    public static function paymentFailed(Order $order)
    {
        // Notify user (if they have an account)
        if ($order->user_id) {
            Notification::create([
                'type' => 'payment_failed',
                'title' => 'Pembayaran Ditolak',
                'message' => "Pembayaran untuk pesanan #{$order->id} ditolak. Silakan hubungi customer service untuk informasi lebih lanjut.",
                'data' => [
                    'order_id' => $order->id,
                    'amount' => $order->final_price,
                    'reason' => $order->payment_notes,
                ],
                'order_id' => $order->id,
                'user_id' => $order->user_id,
            ]);
        }
    }

    public static function getUnreadCountForUser($userId = null)
    {
        if ($userId) {
            return Notification::forUser($userId)->unread()->count();
        }
        
        return Notification::forAdmins()->unread()->count();
    }

    public static function getNotificationsForUser($userId = null, $limit = 10)
    {
        if ($userId) {
            return Notification::forUser($userId)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        }
        
        return Notification::forAdmins()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
    
    public function createNotification($userId, $title, $message, $data = [])
    {
        return Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'is_read' => false
        ]);
    }
}

