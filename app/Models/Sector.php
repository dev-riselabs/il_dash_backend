<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sector extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'color'];

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    public function signals(): HasMany
    {
        return $this->hasMany(InvestmentSignal::class);
    }
}
