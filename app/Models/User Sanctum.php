<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'level',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Relationships
     */

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    public function operatedTrips()
    {
        return $this->hasMany(Trip::class, 'operator_id');
    }

    /**
     * Scopes
     */

    public function scopeHikers($query)
    {
        return $query->where('role', 'hiker');
    }

    public function scopeOperators($query)
    {
        return $query->where('role', 'operator');
    }

    public function scopeSARTeam($query)
    {
        return $query->where('role', 'sar');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }
}