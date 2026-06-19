<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'duration',
    ];

    protected $casts = [
        'price' => 'integer',
        'duration' => 'integer',
    ];

    /**
     * Get the discounts for the service.
     */
    public function discounts()
    {
        return $this->hasMany(Discount::class);
    }
}
