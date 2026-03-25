<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Basecamp extends Model
{
    use HasFactory;

    protected $fillable = [
        'mountain_id',
        'name',
        'description',
        'route_order',
    ];

    /**
     * Relationships
     */

    public function mountain()
    {
        return $this->belongsTo(Mountain::class);
    }

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    /**
     * Scopes
     */

    public function scopeByMountain($query, $mountainId)
    {
        return $query->where('mountain_id', $mountainId)
                     ->orderBy('route_order');
    }
}