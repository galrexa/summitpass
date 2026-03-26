<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $fillable = ['name', 'display_name'];

    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }

    public static function byName(string $name): self
    {
        return static::where('name', $name)->firstOrFail();
    }
}
