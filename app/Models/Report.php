<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'kind', 'event_day_id', 'range_start', 'range_end',
        'generated_by', 'generated_at', 'file_url',
    ];

    protected $casts = [
        'range_start' => 'datetime',
        'range_end' => 'datetime',
        'generated_at' => 'datetime',
    ];

    public function day(): BelongsTo
    {
        return $this->belongsTo(EventDay::class, 'event_day_id');
    }

    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
