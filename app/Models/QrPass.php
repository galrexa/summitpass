<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class QrPass extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_participant_id',
        'qr_token',
        'valid_from',
        'valid_until',
        'status',
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
    ];

    public function participant()
    {
        return $this->belongsTo(BookingParticipant::class, 'booking_participant_id');
    }

    public function trekkingLogs()
    {
        return $this->hasMany(TrekkingLog::class);
    }

    public function isValid(): bool
    {
        return $this->status === 'active'
            && now()->between($this->valid_from, $this->valid_until);
    }

    public static function generateToken(): string
    {
        do {
            $token = Str::uuid()->toString();
        } while (static::where('qr_token', $token)->exists());

        return $token;
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
