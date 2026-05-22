<?php

namespace App\Events;

use App\Models\EventSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SessionStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public EventSession $session)
    {
    }

    public function broadcastOn(): array
    {
        return [new Channel('programme')];
    }

    public function broadcastAs(): string
    {
        return 'session.status_changed';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->session->id,
            'title' => $this->session->title,
            'status' => $this->session->status,
            'starts_at' => $this->session->starts_at?->toIso8601String(),
        ];
    }
}
