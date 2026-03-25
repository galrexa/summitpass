<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trail extends Model
{
    use HasFactory;

    protected $fillable = [
        'mountain_id',
        'name',
        'description',
        'route_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function mountain()
    {
        return $this->belongsTo(Mountain::class);
    }

    public function checkpoints()
    {
        return $this->hasMany(TrailCheckpoint::class)->orderBy('order_seq');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByMountain($query, $mountainId)
    {
        return $query->where('mountain_id', $mountainId);
    }
}
