<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrekkingLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'qr_pass_id',
        'trail_checkpoint_id',
        'direction',
        'scanned_at',
        'scanned_by_user_id',
        'latitude',
        'longitude',
        'device_info',
        'anomaly_flag',
        'anomaly_reason',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
        'created_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
        'device_info' => 'array',
        'anomaly_flag' => 'boolean',
    ];

    public function qrPass()
    {
        return $this->belongsTo(QrPass::class);
    }

    public function checkpoint()
    {
        return $this->belongsTo(TrailCheckpoint::class, 'trail_checkpoint_id');
    }

    public function scannedBy()
    {
        return $this->belongsTo(User::class, 'scanned_by_user_id');
    }

    public function scopeByQrPass($query, $qrPassId)
    {
        return $query->where('qr_pass_id', $qrPassId);
    }

    public function scopeWithAnomalies($query)
    {
        return $query->where('anomaly_flag', true);
    }

    public function scopeDirection($query, string $direction)
    {
        return $query->where('direction', $direction);
    }

    public function flagAnomaly(string $reason): void
    {
        $this->update(['anomaly_flag' => true, 'anomaly_reason' => $reason]);
    }
}
