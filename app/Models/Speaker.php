<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Speaker extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'organization',
        'job_title', 'bio', 'photo_url', 'country',
    ];

    public function sessions(): BelongsToMany
    {
        return $this->belongsToMany(EventSession::class, 'session_speakers')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(SessionQuote::class);
    }

    public function engagementScores(): HasMany
    {
        return $this->hasMany(SpeakerEngagementScore::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
