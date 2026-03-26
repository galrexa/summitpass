<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mountain extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'province',
        'height_mdpl',
        'difficulty',
        'description',
        'image_url',
        'is_active',
        'pengelola_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function regulation()
    {
        return $this->hasOne(MountainRegulation::class);
    }

    public function trails()
    {
        return $this->hasMany(Trail::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function pengelola()
    {
        return $this->belongsTo(User::class, 'pengelola_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                     ->orWhere('location', 'like', "%{$search}%");
    }

    public function getFormattedHeightAttribute()
    {
        return $this->height_mdpl . ' MDPL';
    }
}
