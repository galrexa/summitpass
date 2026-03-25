<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mountain extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'height_mdpl',
        'difficulty',
        'min_level',
        'base_price',
        'max_days',
        'description',
        'image_url',
    ];

    /**
     * Relationships
     */

    public function basecamps()
    {
        return $this->hasMany(Basecamp::class);
    }

    public function checkpoints()
    {
        return $this->hasMany(Checkpoint::class);
    }

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    /**
     * Scopes
     */

    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    public function scopeByMinLevel($query, $level)
    {
        return $query->where('min_level', '<=', $level);
    }

    public function scopeByPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('base_price', [$minPrice, $maxPrice]);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                     ->orWhere('location', 'like', "%{$search}%");
    }

    /**
     * Accessors
     */

    public function getFormattedPriceAttribute()
    {
        return 'Rp' . number_format($this->base_price, 0, ',', '.');
    }

    public function getFormattedHeightAttribute()
    {
        return $this->height_mdpl . ' MDPL';
    }
}