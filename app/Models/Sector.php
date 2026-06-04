<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Sector extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'color'];

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

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    public function signals(): HasMany
    {
        return $this->hasMany(InvestmentSignal::class);
    }
}
