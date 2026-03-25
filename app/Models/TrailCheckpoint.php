<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrailCheckpoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'trail_id',
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

    public function trail()
    {
        return $this->belongsTo(Trail::class);
    }

    public function mountain()
    {
        return $this->belongsTo(Mountain::class);
    }

    public function trekkingLogs()
    {
        return $this->hasMany(TrekkingLog::class);
    }

    public function scopeByTrail($query, $trailId)
    {
        return $query->where('trail_id', $trailId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function getCoordinates(): array
    {
        return ['latitude' => $this->latitude, 'longitude' => $this->longitude];
    }

    public function distanceFromPoint(float $lat, float $lng): float
    {
        $earthRadius = 6371000; // meter
        $dLat = deg2rad($this->latitude - $lat);
        $dLng = deg2rad($this->longitude - $lng);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat)) * cos(deg2rad($this->latitude)) * sin($dLng / 2) ** 2;
        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
