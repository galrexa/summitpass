<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mountain_id',
        'basecamp_id',
        'operator_id',
        'start_date',
        'end_date',
        'status',
        'total_price',
        'booking_reference',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Relationships
     */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mountain()
    {
        return $this->belongsTo(Mountain::class);
    }

    public function basecamp()
    {
        return $this->belongsTo(Basecamp::class);
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function checkpointLogs()
    {
        return $this->hasMany(CheckpointLog::class);
    }

    /**
     * Scopes
     */

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now())
                     ->where('status', 'pending');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Methods
     */

    public function generateBookingReference()
    {
        return 'SP-' . strtoupper(uniqid()) . '-' . $this->id;
    }

    public function isActive()
    {
        return $this->status === 'active' && 
               $this->start_date->lte(now()) && 
               $this->end_date->gte(now());
    }

    public function canCheckIn()
    {
        return $this->status === 'pending' && 
               $this->start_date->isToday();
    }

    /**
     * Attributes
     */

    protected function formattedTotalPrice(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => 'Rp' . number_format($this->total_price, 0, ',', '.'),
        );
    }
}