<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SectorInvestmentSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'sector_id', 'signals_count', 'estimated_value_naira',
        'trend_percent', 'captured_at',
    ];

    protected $casts = [
        'captured_at' => 'datetime',
        'trend_percent' => 'float',
    ];

    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class);
    }
}
