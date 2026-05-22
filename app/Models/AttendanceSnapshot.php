<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceSnapshot extends Model
{
    use HasFactory;

    protected $fillable = ['event_day_id', 'captured_at', 'total', 'in_person', 'virtual'];

    protected $casts = [
        'captured_at' => 'datetime',
    ];

    public function day(): BelongsTo
    {
        return $this->belongsTo(EventDay::class, 'event_day_id');
    }
}
