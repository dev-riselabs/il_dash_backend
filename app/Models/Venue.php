<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Venue extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'capacity', 'status'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug) && $model->name) {
                $model->slug = Str::slug($model->name);
            }
        });

        static::updating(function ($model) {
            if (empty($model->slug) && $model->name) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(EventSession::class);
    }

    public function cameras(): HasMany
    {
        return $this->hasMany(Camera::class);
    }
}
