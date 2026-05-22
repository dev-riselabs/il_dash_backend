<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessionQuote extends Model
{
    use HasFactory;

    protected $fillable = ['event_session_id', 'speaker_id', 'quote_text', 'said_at'];

    protected $casts = [
        'said_at' => 'datetime',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(EventSession::class, 'event_session_id');
    }

    public function speaker(): BelongsTo
    {
        return $this->belongsTo(Speaker::class);
    }
}
