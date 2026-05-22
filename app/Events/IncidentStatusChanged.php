<?php

namespace App\Events;

use App\Models\Incident;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class IncidentStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Incident $incident)
    {
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('incidents')];
    }

    public function broadcastAs(): string
    {
        return 'incident.status_changed';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->incident->id,
            'status' => $this->incident->status,
            'resolved_at' => $this->incident->resolved_at?->toIso8601String(),
        ];
    }
}
