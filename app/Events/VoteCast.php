<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Poll;
use App\Models\PollOption;

class VoteCast implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $poll;
    public $pollOption;
    public $voteCount;
    public $totalVotes;
    public $percentage;

    /**
     * Create a new event instance.
     */
    public function __construct(Poll $poll, PollOption $pollOption, int $voteCount, int $totalVotes, float $percentage)
    {
        $this->poll = $poll;
        $this->pollOption = $pollOption;
        $this->voteCount = $voteCount;
        $this->totalVotes = $totalVotes;
        $this->percentage = $percentage;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('admin-votes'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'poll_id' => $this->poll->id,
            'poll_title' => $this->poll->title,
            'option_id' => $this->pollOption->id,
            'option_text' => $this->pollOption->option_text,
            'vote_count' => $this->voteCount,
            'total_votes' => $this->totalVotes,
            'percentage' => $this->percentage,
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'vote.cast';
    }
}
