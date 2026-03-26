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
        'role_id',
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

    public function userRole()
    {
        return $this->belongsTo(UserRole::class, 'role_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'leader_user_id');
    }

    public function bookingParticipations()
    {
        return $this->hasMany(BookingParticipant::class);
    }

    public function completedSummits()
    {
        return $this->hasMany(Booking::class, 'leader_user_id')
            ->where('status', 'completed')
            ->with('mountain');
    }

    public function managedMountain()
    {
        return $this->hasOne(Mountain::class, 'pengelola_id');
    }

    /**
     * Accessor: $user->role returns the role name slug (e.g. 'admin', 'pendaki')
     * Maintains backward compatibility with all existing code.
     */
    public function getRoleAttribute(): string
    {
        return $this->userRole?->name ?? '';
    }

    /**
     * Mutator: $user->role = 'pendaki' sets role_id automatically.
     * Maintains backward compatibility with all existing User::create(['role' => ...]) calls.
     */
    public function setRoleAttribute(string $value): void
    {
        $roleId = UserRole::where('name', $value)->value('id');
        if ($roleId) {
            $this->attributes['role_id'] = $roleId;
        }
    }

    public function summitPoints(): int
    {
        return (int) $this->hasMany(Booking::class, 'leader_user_id')
            ->where('status', 'completed')
            ->join('mountains', 'mountains.id', '=', 'bookings.mountain_id')
            ->sum('mountains.height_mdpl');
    }

    public function highestSummitMdpl(): int
    {
        return (int) $this->hasMany(Booking::class, 'leader_user_id')
            ->where('status', 'completed')
            ->join('mountains', 'mountains.id', '=', 'bookings.mountain_id')
            ->max('mountains.height_mdpl');
    }

    /**
     * Scopes
     */

    public function scopePendaki($query)
    {
        return $query->whereHas('userRole', fn($q) => $q->where('name', 'pendaki'));
    }

    public function scopePengelolaTn($query)
    {
        return $query->whereHas('userRole', fn($q) => $q->where('name', 'pengelola_tn'));
    }

    public function scopeOfficers($query)
    {
        return $query->whereHas('userRole', fn($q) => $q->where('name', 'officer'));
    }

    public function scopeAdmins($query)
    {
        return $query->whereHas('userRole', fn($q) => $q->where('name', 'admin'));
    }
}
