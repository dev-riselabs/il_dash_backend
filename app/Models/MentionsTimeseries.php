<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MentionsTimeseries extends Model
{
    use HasFactory;

    protected $table = 'mentions_timeseries';

    protected $fillable = ['captured_at', 'platform', 'mentions_count'];

    protected $casts = [
        'captured_at' => 'datetime',
    ];
}
