<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'user_id',
        'nik',
        'name',
        'role',
    ];

    protected $hidden = [
        'nik',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function qrPass()
    {
        return $this->hasOne(QrPass::class);
    }

    public function scopeByBooking($query, $bookingId)
    {
        return $query->where('booking_id', $bookingId);
    }

    public function getMaskedNikAttribute(): string
    {
        return substr($this->nik, 0, 4) . '**********' . substr($this->nik, -2);
    }
}
