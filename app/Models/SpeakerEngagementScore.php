<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpeakerEngagementScore extends Model
{
    use HasFactory;

    protected $fillable = ['speaker_id', 'event_day_id', 'sessions_count', 'score'];

    public function speaker(): BelongsTo
    {
        return $this->belongsTo(Speaker::class);
    }

    public function day(): BelongsTo
    {
        return $this->belongsTo(EventDay::class, 'event_day_id');
    }
}
