<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MountainRegulation extends Model
{
    use HasFactory;

    protected $fillable = [
        'mountain_id',
        'base_price',
        'quota_per_trail_per_day',
        'max_hiking_days',
        'max_participants_per_account',
        'guide_required',
        'checkout_deadline_hour',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'guide_required' => 'boolean',
        'checkout_deadline_hour' => 'integer',
    ];

    public function mountain()
    {
        return $this->belongsTo(Mountain::class);
    }

    public function getFormattedBasePriceAttribute()
    {
        return 'Rp' . number_format($this->base_price, 0, ',', '.');
    }
}
