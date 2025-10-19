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
    ];

    protected $casts = [
        'user_id' => 'integer',
        'queue_number' => 'integer',
        'booking_date' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'Menunggu',
    ];
}
