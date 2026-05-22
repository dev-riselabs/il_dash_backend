<?php

namespace App\Events;

use App\Models\FeedbackSubmission;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FeedbackSubmitted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public FeedbackSubmission $feedback)
    {
    }

    public function broadcastOn(): array
    {
        $channels = [new Channel('sentiment')];
        if ($this->feedback->event_session_id) {
            $channels[] = new Channel('sessions.'.$this->feedback->event_session_id);
        }
        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'feedback.submitted';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->feedback->id,
            'event_session_id' => $this->feedback->event_session_id,
            'star_rating' => $this->feedback->star_rating,
            'sentiment_label' => $this->feedback->sentiment_label,
            'submitted_at' => $this->feedback->submitted_at?->toIso8601String(),
        ];
    }
}
