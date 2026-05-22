<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venue extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'capacity', 'status'];

    public function sessions(): HasMany
    {
        return $this->hasMany(EventSession::class);
    }

    public function cameras(): HasMany
    {
        return $this->hasMany(Camera::class);
    }
}
