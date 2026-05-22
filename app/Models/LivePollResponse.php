<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LivePollResponse extends Model
{
    use HasFactory;

    protected $table = 'live_poll_responses';

    protected $fillable = ['live_poll_id', 'attendee_id', 'option', 'submitted_at'];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function poll(): BelongsTo
    {
        return $this->belongsTo(LivePoll::class, 'live_poll_id');
    }

    public function attendee(): BelongsTo
    {
        return $this->belongsTo(Attendee::class);
    }
}
