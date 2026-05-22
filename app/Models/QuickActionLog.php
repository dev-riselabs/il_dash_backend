<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuickActionLog extends Model
{
    use HasFactory;

    protected $table = 'quick_actions_log';

    protected $fillable = ['action_type', 'payload', 'triggered_by', 'triggered_at'];

    protected $casts = [
        'payload' => 'array',
        'triggered_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triggered_by');
    }
}
