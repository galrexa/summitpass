<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'nik',
        'passport_number',
        'password',
        'role',
        'google_id',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'nik',
        'passport_number',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Relationships
     */

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'leader_user_id');
    }

    public function bookingParticipations()
    {
        return $this->hasMany(BookingParticipant::class);
    }

    /**
     * Scopes
     */

    public function scopePendaki($query)
    {
        return $query->where('role', 'pendaki');
    }

    public function scopePengelolaTn($query)
    {
        return $query->where('role', 'pengelola_tn');
    }

    public function scopeOfficers($query)
    {
        return $query->where('role', 'officer');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }
}
