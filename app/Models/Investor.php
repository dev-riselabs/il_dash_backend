<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Investor extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'country', 'region', 'logo_url', 'sectors_of_interest'];

    protected $casts = [
        'sectors_of_interest' => 'array',
    ];

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    public function signals(): HasMany
    {
        return $this->hasMany(InvestmentSignal::class);
    }
}
