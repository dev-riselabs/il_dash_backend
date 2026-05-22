<?php

namespace App\Events;

use App\Models\Resolution;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ResolutionAdded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Resolution $resolution)
    {
    }

    public function broadcastOn(): array
    {
        return [new Channel('resolutions')];
    }

    public function broadcastAs(): string
    {
        return 'resolution.added';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->resolution->id,
            'title' => $this->resolution->title,
            'category' => $this->resolution->category,
            'stage' => $this->resolution->stage,
            'committed_by' => $this->resolution->committed_by,
            'recorded_at' => $this->resolution->recorded_at?->toIso8601String(),
        ];
    }
}
