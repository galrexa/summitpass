<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckpointLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'checkpoint_id',
        'logged_at',
        'latitude',
        'longitude',
        'device_info',
        'verified',
        'anomaly_flag',
        'anomaly_reason',
    ];

    protected $casts = [
        'logged_at' => 'datetime',
        'device_info' => 'array',
        'verified' => 'boolean',
        'anomaly_flag' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    /**
     * Relationships
     */

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function checkpoint()
    {
        return $this->belongsTo(Checkpoint::class);
    }

    /**
     * Scopes
     */

    public function scopeByTrip($query, $tripId)
    {
        return $query->where('trip_id', $tripId)
                     ->orderBy('logged_at');
    }

    public function scopeWithAnomalies($query)
    {
        return $query->where('anomaly_flag', true);
    }

    public function scopeUnverified($query)
    {
        return $query->where('verified', false);
    }

    /**
     * Methods
     */

    public function getGPSCoordinates()
    {
        if ($this->latitude && $this->longitude) {
            return [
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
            ];
        }
        return null;
    }

    public function markAsVerified()
    {
        $this->update(['verified' => true]);
    }

    public function flagAnomaly($reason)
    {
        $this->update([
            'anomaly_flag' => true,
            'anomaly_reason' => $reason,
        ]);
    }

    /**
     * Accessors
     */

    public function getFormattedTimeAttribute()
    {
        return $this->logged_at->format('H:i');
    }
}