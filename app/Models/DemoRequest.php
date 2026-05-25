<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;

class DemoRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        // Section A: Basic Details
        'full_name',
        'email',
        'organization',
        'job_title',
        'phone_number',
        'country',

        // Section B: Event Details
        'event_type',
        'event_name',
        'event_date',
        'event_location',
        'estimated_attendees',

        // Section C: Needs & Intent
        'primary_objectives',
        'deployment_timeline',

        // Section D: Qualifier
        'budget_range',

        // Section E: Final Input
        'additional_notes',

        // Metadata
        'submitted_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'primary_objectives' => 'array',
        'deployment_timeline' => 'array',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'primary_objectives' => 'json',
            'deployment_timeline' => 'json',
        ];
    }
}
