<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'leader_user_id',
        'mountain_id',
        'trail_id',
        'start_date',
        'end_date',
        'booking_code',
        'guide_requested',
        'tos_accepted_at',
        'status',
        'total_price',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'tos_accepted_at' => 'datetime',
        'guide_requested' => 'boolean',
        'total_price' => 'decimal:2',
    ];

    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_user_id');
    }

    public function mountain()
    {
        return $this->belongsTo(Mountain::class);
    }

    public function trail()
    {
        return $this->belongsTo(Trail::class);
    }

    public function participants()
    {
        return $this->hasMany(BookingParticipant::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function scopeByLeader($query, $userId)
    {
        return $query->where('leader_user_id', $userId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public static function generateBookingCode(): string
    {
        do {
            $code = 'SP-' . strtoupper(Str::random(6));
        } while (static::where('booking_code', $code)->exists());

        return $code;
    }

    public function isActive(): bool
    {
        return $this->status === 'active'
            && now()->between($this->start_date, $this->end_date);
    }
}
