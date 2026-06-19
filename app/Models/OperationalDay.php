<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationalDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'hari',
        'status_operasional',
        'jam_buka',
        'jam_tutup',
        'created_by'
    ];

    protected $casts = [
        'status_operasional' => 'boolean',
        'jam_buka' => 'datetime:H:i',
        'jam_tutup' => 'datetime:H:i',
    ];

    /**
     * Get the admin that created this operational day.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope a query to only include active operational days.
     */
    public function scopeActive($query)
    {
        return $query->where('status_operasional', true);
    }
}