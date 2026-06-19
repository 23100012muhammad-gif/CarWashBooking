<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BookingSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'kapasitas',
        'terisi',
        'status',
        'created_by'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_mulai' => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
        'kapasitas' => 'integer',
        'terisi' => 'integer'
    ];

    /**
     * Get the admin that created this slot.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all orders for this slot.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Scope a query to only include available slots.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'tersedia')
                    ->where('tanggal', '>=', Carbon::today());
    }

    /**
     * Scope a query to only include slots within date range.
     */
    public function scopeWithinDays($query, $days = 7)
    {
        $endDate = Carbon::today()->addDays($days);
        return $query->whereBetween('tanggal', [Carbon::today(), $endDate]);
    }

    /**
     * Check if slot is available
     */
    public function isAvailable(): bool
    {
        return $this->status === 'tersedia' && $this->terisi < $this->kapasitas;
    }

    /**
     * Increment terisi count safely with check
     */
    public function incrementTerisi(): bool
    {
        if (!$this->isAvailable()) {
            return false;
        }

        $this->increment('terisi');
        
        if ($this->terisi >= $this->kapasitas) {
            $this->update(['status' => 'penuh']);
        }

        return true;
    }
}