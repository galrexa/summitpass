<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkpoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'mountain_id',
        'name',
        'description',
        'order_seq',
        'type',
        'latitude',
        'longitude',
        'altitude',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'altitude' => 'integer',
    ];

    /**
     * Relationships
     */

    public function mountain()
    {
        return $this->belongsTo(Mountain::class);
    }

    public function logs()
    {
        return $this->hasMany(CheckpointLog::class);
    }

    /**
     * Scopes
     */

    public function scopeByMountain($query, $mountainId)
    {
        return $query->where('mountain_id', $mountainId)
                     ->orderBy('order_seq');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Methods
     */

    public function getCoordinates()
    {
        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }

    /**
     * Calculate distance using Haversine formula
     * Distance in kilometers
     */
    public function distanceFromPoint($latitude, $longitude)
    {
        $lat1 = deg2rad($this->latitude);
        $lon1 = deg2rad($this->longitude);
        $lat2 = deg2rad($latitude);
        $lon2 = deg2rad($longitude);

        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;

        $a = sin($dlat / 2) * sin($dlat / 2) +
             cos($lat1) * cos($lat2) * sin($dlon / 2) * sin($dlon / 2);
        $c = 2 * asin(sqrt($a));
        $r = 6371; // Earth's radius in kilometers

        return $c * $r;
    }
}