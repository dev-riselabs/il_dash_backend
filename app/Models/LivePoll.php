<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LivePoll extends Model
{
    use HasFactory;

    protected $fillable = ['event_session_id', 'prompt', 'options', 'status', 'opened_at', 'closed_at'];

    protected $casts = [
        'options' => 'array',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(EventSession::class, 'event_session_id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(LivePollResponse::class);
    }
}
