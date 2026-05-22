<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SentimentScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'scope', 'scope_id', 'captured_at',
        'positive_pct', 'neutral_pct', 'negative_pct', 'score_0_100',
    ];

    protected $casts = [
        'captured_at' => 'datetime',
        'positive_pct' => 'float',
        'neutral_pct' => 'float',
        'negative_pct' => 'float',
    ];
}
