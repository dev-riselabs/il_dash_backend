<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedbackSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendee_id', 'event_session_id', 'channel', 'star_rating',
        'review_text', 'key_takeaway', 'sentiment_label', 'sentiment_score', 'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'sentiment_score' => 'float',
    ];

    public function attendee(): BelongsTo
    {
        return $this->belongsTo(Attendee::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(EventSession::class, 'event_session_id');
    }
}
