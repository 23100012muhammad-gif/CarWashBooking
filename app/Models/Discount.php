<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'service_id',
        'code',
        'percent',
        'description',
        'expires_at',
        'active',
    ];

    protected $casts = [
        'service_id' => 'integer',
        'percent' => 'integer',
        'active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the service that owns the discount.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}


