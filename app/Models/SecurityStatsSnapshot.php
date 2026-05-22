<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecurityStatsSnapshot extends Model
{
    use HasFactory;

    protected $table = 'security_stats_snapshots';

    protected $fillable = [
        'captured_at', 'personnel_on_duty', 'incidents_today',
        'incidents_resolved', 'avg_response_seconds', 'safety_level', 'venue_status',
    ];

    protected $casts = [
        'captured_at' => 'datetime',
        'venue_status' => 'array',
    ];
}
