<?php

namespace Database\Seeders;

use App\Models\Action;
use App\Models\Alert;
use App\Models\Camera;
use App\Models\EventDay;
use App\Models\Incident;
use App\Models\Report;
use App\Models\SecurityStatsSnapshot;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Seeder;

class OperationsSeeder extends Seeder
{
    public function run(): void
    {
        $venues = Venue::all();
        $admin = User::first();

        $incidentTypes = ['Crowd Congestion', 'Access Control', 'Medical', 'Fire', 'Other'];
        $severities = ['low', 'medium', 'high', 'critical'];
        $statuses = ['open', 'responding', 'resolved'];
        for ($i = 0; $i < 18; $i++) {
            $status = $statuses[array_rand($statuses)];
            Incident::create([
                'type' => $incidentTypes[array_rand($incidentTypes)],
                'title' => fake()->randomElement([
                    'Queue buildup at main entrance', 'Unverified pass attempt',
                    'Attendee fainted near Hall A', 'Smoke detected in kitchen',
                    'Lost lanyard reported', 'Parking overflow detected',
                ]),
                'description' => fake()->sentence(10),
                'venue_id' => $venues->random()->id,
                'occurred_at' => now()->subMinutes(random_int(5, 1200)),
                'severity' => $severities[array_rand($severities)],
                'status' => $status,
                'reported_by' => $admin?->id,
                'resolved_at' => $status === 'resolved' ? now()->subMinutes(random_int(1, 60)) : null,
            ]);
        }

        $alertDefs = [
            ['Session running late', 'Programme Tracker', 'medium'],
            ['Negative sentiment spike detected', 'Sentiment', 'high'],
            ['New deal commitment recorded', 'Deal Room', 'info'],
            ['Critical incident at Hall B', 'Security', 'critical'],
            ['Reverb websocket reconnected', 'System', 'low'],
            ['Speaker no-show notification', 'Programme Tracker', 'warning'],
            ['Capacity at 90% in Main Auditorium', 'Security', 'high'],
        ];
        foreach (range(1, 25) as $i) {
            [$title, $source, $sev] = $alertDefs[$i % count($alertDefs)];
            $status = fake()->randomElement(['unread', 'read', 'resolved']);
            Alert::create([
                'title' => $title, 'body' => fake()->sentence(12),
                'severity' => $sev, 'source' => $source,
                'source_ref_id' => null, 'status' => $status,
                'resolved_at' => $status === 'resolved' ? now()->subMinutes(random_int(1, 200)) : null,
                'created_at' => now()->subMinutes(random_int(1, 1800)),
                'updated_at' => now(),
            ]);
        }

        SecurityStatsSnapshot::create([
            'captured_at' => now(),
            'personnel_on_duty' => 142,
            'incidents_today' => Incident::whereDate('occurred_at', today())->count(),
            'incidents_resolved' => Incident::where('status', 'resolved')->whereDate('resolved_at', today())->count(),
            'avg_response_seconds' => 240,
            'safety_level' => 'high',
            'venue_status' => $venues->map(fn ($v) => ['venue_id' => $v->id, 'status' => $v->status])->all(),
        ]);

        foreach ($venues as $v) {
            Camera::create([
                'name' => $v->name.' - Entrance',
                'venue_id' => $v->id,
                'stream_url' => 'https://stream.example/'.$v->slug,
                'thumbnail_url' => 'https://cdn.example/thumb/'.$v->slug.'.jpg',
                'status' => fake()->randomElement(['live', 'live', 'live', 'delayed', 'offline']),
            ]);
        }

        $actionStatuses = ['pending', 'in_progress', 'done', 'blocked'];
        $actionTitles = [
            'Follow up with AfDB on agritech MoU', 'Confirm keynote slides for Day 2',
            'Resolve power dip in Hall A', 'Publish executive summary',
            'Coordinate with security on VIP route', 'Send signed deal to legal',
            'Sync social team on hashtag amplification', 'Verify investor list for Day 3',
        ];
        foreach ($actionTitles as $i => $title) {
            Action::create([
                'title' => $title, 'description' => fake()->sentence(8),
                'related_to' => fake()->randomElement(['session', 'incident', 'deal', 'resolution']),
                'related_id' => random_int(1, 8),
                'sector_id' => null, 'owner_id' => $admin?->id,
                'status' => $actionStatuses[$i % count($actionStatuses)],
                'due_at' => now()->addHours(random_int(1, 72)),
            ]);
        }

        $kinds = ['executive_summary', 'attendance', 'investment', 'sentiment'];
        $firstDay = EventDay::first();
        foreach ($kinds as $kind) {
            Report::create([
                'name' => ucwords(str_replace('_', ' ', $kind)).' '.now()->format('Y-m-d'),
                'kind' => $kind, 'event_day_id' => $firstDay?->id,
                'range_start' => now()->subDay(), 'range_end' => now(),
                'generated_by' => $admin?->id, 'generated_at' => now()->subMinutes(random_int(5, 720)),
                'file_url' => null,
            ]);
        }
    }
}
