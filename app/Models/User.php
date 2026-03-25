<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSoftware\JwtGuard\Traits\JwtSubject;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, JwtSubject;

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
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJwtIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJwtCustomClaims()
    {
        return [
            'email' => $this->email,
            'name' => $this->name,
            'role' => $this->role,
            'level' => $this->level,
        ];
    }

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