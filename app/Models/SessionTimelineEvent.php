<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessionTimelineEvent extends Model
{
    use HasFactory;

    protected $fillable = ['event_session_id', 'occurred_at', 'kind', 'actor_name', 'note'];

    protected $casts = [
        'occurred_at' => 'datetime',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(EventSession::class, 'event_session_id');
    }
}
