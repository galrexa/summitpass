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
     * Cek apakah user eligible untuk mendaki gunung tertentu
     * berdasarkan pengalaman ketinggian minimum yang dipersyaratkan.
     *
     * @param  Mountain  $mountain
     * @return bool
     */
    public function isEligibleForMountain(Mountain $mountain): bool
    {
        $regulation = $mountain->regulation;
        
        // Jika tidak ada regulasi atau tidak ada syarat pengalaman, allow
        if (!$regulation || !$regulation->min_elevation_experience) {
            return true;
        }
        
        // Cek apakah pengalaman user memenuhi syarat
        return $this->highestSummitMdpl() >= $regulation->min_elevation_experience;
    }

    /**
     * Dapatkan pengalaman minimum yang dibutuhkan untuk gunung tertentu.
     *
     * @param  Mountain  $mountain
     * @return int|null  Ketinggian minimum dalam MDPL, atau null jika tidak ada syarat
     */
    public function getRequiredExperienceFor(Mountain $mountain): ?int
    {
        return $mountain->regulation?->min_elevation_experience;
    }

    /**
     * Dapatkan rekomendasi gunung berdasarkan pengalaman user.
     * Prioritas: gunung yang eligible dan mendorong progres bertahap.
     *
     * @param  int  $limit  Jumlah rekomendasi yang diinginkan
     * @return \Illuminate\Support\Collection<Mountain>
     */
    public function getRecommendedMountains(int $limit = 5): \Illuminate\Support\Collection
    {
        $userExperience = $this->highestSummitMdpl();
        
        return Mountain::active()
            ->with('regulation')
            ->get()
            ->filter(function ($mountain) {
                // Hanya gunung yang eligible untuk user
                return $this->isEligibleForMountain($mountain);
            })
            ->sortBy(function ($mountain) use ($userExperience) {
                $minExp = $mountain->regulation?->min_elevation_experience ?? 0;
                
                // Prioritas:
                // 1. Gunung tanpa syarat (untuk pemula) - prioritas tertinggi
                if ($minExp === 0) {
                    return 0;
                }
                
                // 2. Gunung yang sedikit lebih tinggi dari pengalaman user
                //    untuk mendorong progres bertahap
                $diff = $mountain->height_mdpl - $userExperience;
                
                // Gunung yang lebih tinggi dari pengalaman user diprioritaskan
                // Gunung yang sudah lebih rendah diberi nilai tinggi (prioritas rendah)
                return $diff > 0 ? $diff : 999999;
            })
            ->take($limit);
    }

    /**
     * Dapatkan gunung "next challenge" - sedikit lebih sulit dari pengalaman.
     * Cocok untuk mendorong user naik level secara bertahap.
     *
     * @return Mountain|null
     */
    public function getNextChallengeMountain(): ?Mountain
    {
        $userExperience = $this->highestSummitMdpl();
        
        return Mountain::active()
            ->with('regulation')
            ->get()
            ->filter(function ($mountain) use ($userExperience) {
                // Cari gunung yang 200-500 MDPL lebih tinggi dari pengalaman
                $diff = $mountain->height_mdpl - $userExperience;
                return $diff >= 200 && $diff <= 500 && $this->isEligibleForMountain($mountain);
            })
            ->sortBy('height_mdpl')
            ->first();
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
