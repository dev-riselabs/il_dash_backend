<?php

namespace App\Events;

use App\Models\Incident;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class IncidentReported implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Incident $incident)
    {
    }

    /**
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('incidents'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'incident.reported';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->incident->id,
            'type' => $this->incident->type,
            'title' => $this->incident->title,
            'severity' => $this->incident->severity,
            'status' => $this->incident->status,
            'venue_id' => $this->incident->venue_id,
            'occurred_at' => $this->incident->occurred_at?->toIso8601String(),
        ];
    }
}
