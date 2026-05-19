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
        'grade',
        'description',
        'ecosystem_type',
        'image_url',
        'is_active',
        'trail_status',
        'pengelola_id',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude'  => 'float',
        'longitude' => 'float',
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

    public function scopeByGrade($query, $grade)
    {
        return $query->where('grade', $grade);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                     ->orWhere('location', 'like', "%{$search}%");
    }

    public function scopeWithQuotaSummary($query)
    {
        return $query->withCount([
            'bookings as booked_this_week' => fn($q) => $q
                ->whereBetween('start_date', [now()->startOfWeek(), now()->endOfWeek()])
                ->whereIn('status', ['paid', 'active']),
        ]);
    }

    public function getFormattedHeightAttribute()
    {
        return $this->height_mdpl . ' MDPL';
    }
}
