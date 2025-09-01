<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Events\VoteCast;
use App\Models\Poll;
use App\Models\PollOption;

class BroadcastVoteEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $pollId;
    protected $pollOptionId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $pollId, int $pollOptionId)
    {
        $this->pollId = $pollId;
        $this->pollOptionId = $pollOptionId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $poll = Poll::with('options.votes')->find($this->pollId);
            $pollOption = PollOption::with('votes')->find($this->pollOptionId);

            if (!$poll || !$pollOption) {
                return;
            }

            // Calculate current vote counts and percentages
            $totalVotes = $poll->options->sum('votes_count');
            $optionVotes = $pollOption->votes_count;
            $percentage = $totalVotes > 0 ? round(($optionVotes / $totalVotes) * 100, 2) : 0;

            // Broadcast the event
            broadcast(new VoteCast($poll, $pollOption, $optionVotes, $totalVotes, $percentage));

        } catch (\Exception $e) {
            // Log error but don't fail the job
            Log::error('Failed to broadcast vote event', [
                'poll_id' => $this->pollId,
                'option_id' => $this->pollOptionId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
