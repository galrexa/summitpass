<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'gateway',
        'transaction_id',
        'status',
        'amount',
        'paid_at',
        'gateway_response',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'gateway_response' => 'array',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp' . number_format($this->amount, 0, ',', '.');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
