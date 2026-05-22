<?php

namespace App\Events;

use App\Models\Deal;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DealStageChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Deal $deal)
    {
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('deals')];
    }

    public function broadcastAs(): string
    {
        return 'deal.stage_changed';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->deal->id,
            'title' => $this->deal->title,
            'stage' => $this->deal->stage,
            'value_naira' => $this->deal->value_naira,
            'investor_id' => $this->deal->investor_id,
            'sector_id' => $this->deal->sector_id,
        ];
    }
}
