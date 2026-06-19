<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_type',
        'booking_date',
        'license_plate',
        'queue_number',
        'status',
        'original_price',
        'discount_percent',
        'final_price',
        'discount_name',
        'payment_status',
        'payment_method',
        'payment_reference',
        'payment_proof',
        'payment_verified_at',
        'verified_by',
        'payment_notes',
        'transaction_id',
        'gateway_response',
        'refund_reason',
        'refund_requested_at',
        'refund_processed_at',
        'refund_processed_by',
        'refund_notes',
        'hidden_from_user',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'queue_number' => 'integer',
        'booking_date' => 'datetime',
        'original_price' => 'integer',
        'discount_percent' => 'integer',
        'final_price' => 'integer',
        'payment_verified_at' => 'datetime',
        'verified_by' => 'integer',
        'gateway_response' => 'array',
        'refund_requested_at' => 'datetime',
        'refund_processed_at' => 'datetime',
        'refund_processed_by' => 'integer',
        'hidden_from_user' => 'boolean',
    ];

    protected $attributes = [
        'status' => 'Menunggu',
        'payment_status' => 'pending',
    ];

    // Relationships
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function refundProcessor()
    {
        return $this->belongsTo(User::class, 'refund_processed_by');
    }

    // Helper methods
    public function isPaymentPending()
    {
        return $this->payment_status === 'pending';
    }

    public function isPaymentVerified()
    {
        return $this->payment_status === 'verified';
    }

    public function isPaymentFailed()
    {
        return $this->payment_status === 'failed';
    }

    public function hasPaymentProof()
    {
        return !empty($this->payment_proof);
    }

    public function getPaymentStatusLabel()
    {
        switch ($this->payment_status) {
            case 'pending':
                return 'Menunggu Pembayaran';
            case 'verified':
                return 'Lunas';
            case 'failed':
                return 'Gagal';
            default:
                return 'Tidak Diketahui';
        }
    }

    public function getPaymentStatusClass()
    {
        switch ($this->payment_status) {
            case 'pending':
                return 'warning';
            case 'verified':
                return 'success';
            case 'failed':
                return 'danger';
            default:
                return 'secondary';
        }
    }

    public function canRequestRefund()
    {
        return in_array($this->payment_status, ['pending']) && 
               in_array($this->status, ['Pending Pembayaran', 'Menunggu Pembayaran']) &&
               empty($this->refund_requested_at);
    }

    public function canDeleteFromHistory()
    {
        return in_array($this->status, ['Terkonfirmasi', 'Selesai', 'Refund', 'Batal']) ||
               $this->payment_status === 'verified';
    }

    public function hasRefundRequest()
    {
        return !empty($this->refund_requested_at);
    }

    public function isRefundProcessed()
    {
        return !empty($this->refund_processed_at);
    }
}
