<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventSession extends Model
{
    use HasFactory;

    protected $table = 'event_sessions';

    protected $fillable = [
        'event_id', 'event_day_id', 'track_id', 'venue_id', 'sector_id',
        'title', 'description', 'type', 'status', 'starts_at', 'ends_at',
        'ai_summary', 'transcript_url', 'attendance_in_person',
        'attendance_virtual', 'average_rating_x10',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function day(): BelongsTo
    {
        return $this->belongsTo(EventDay::class, 'event_day_id');
    }

    public function track(): BelongsTo
    {
        return $this->belongsTo(Track::class);
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class);
    }

    public function speakers(): BelongsToMany
    {
        return $this->belongsToMany(Speaker::class, 'session_speakers')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(Attendee::class, 'session_attendance')
            ->withPivot('mode', 'joined_at', 'left_at', 'duration_seconds')
            ->withTimestamps();
    }

    public function resources(): HasMany
    {
        return $this->hasMany(SessionResource::class);
    }

    public function timelineEvents(): HasMany
    {
        return $this->hasMany(SessionTimelineEvent::class);
    }

    public function insights(): HasMany
    {
        return $this->hasMany(SessionInsight::class);
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(SessionQuote::class);
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(FeedbackSubmission::class);
    }

    public function resolutions(): HasMany
    {
        return $this->hasMany(Resolution::class);
    }

    public function getAverageRatingAttribute(): ?float
    {
        return $this->average_rating_x10 === null ? null : $this->average_rating_x10 / 10;
    }
}
