<?php

namespace App\Services;

use App\Repositories\PollRepository;
use App\Repositories\PollOptionRepository;
use App\Repositories\VoteRepository;
use App\Exceptions\PollNotFoundException;
use App\Exceptions\PollOptionNotFoundException;
use App\Exceptions\AlreadyVotedException;
use App\Exceptions\GuestVotingNotAllowedException;
use App\Jobs\BroadcastVoteEvent;
use App\Models\Poll;
use App\Models\Vote;

class VoteService
{
    protected PollRepository $pollRepository;
    protected PollOptionRepository $pollOptionRepository;
    protected VoteRepository $voteRepository;

    public function __construct(
        PollRepository $pollRepository,
        PollOptionRepository $pollOptionRepository,
        VoteRepository $voteRepository
    ) {
        $this->pollRepository = $pollRepository;
        $this->pollOptionRepository = $pollOptionRepository;
        $this->voteRepository = $voteRepository;
    }

    /**
     * Submit a vote for a poll option.
     *
     * @param Poll $poll The poll object to vote on.
     * @param int $pollOptionId The ID of the poll option being voted for.
     * @param int|null $userId The ID of the user voting (null for guest).
     * @param string|null $voterIp The voter's IP address (for guests).
     * @return Vote The created Vote model.
     *
     * @throws PollNotFoundException If the poll is inactive.
     * @throws PollOptionNotFoundException If the poll option does not exist or does not belong to the poll.
     * @throws AlreadyVotedException If the user or IP has already voted.
     * @throws GuestVotingNotAllowedException If guest voting is not allowed and no user ID is provided.
     */
    public function submitVote($poll, int $pollOptionId, ?int $userId = null, ?string $voterIp = null): Vote
    {
        // Check if poll is active
        if ($poll->status != 1) {
            throw new PollNotFoundException('Poll is inactive');
        }

        // Check if poll option exists and belongs to the poll
        $option = $this->pollOptionRepository->find($pollOptionId);
        if (!$option || $option->poll_id != $poll->id) {
            throw new PollOptionNotFoundException('Poll option not found or does not belong to this poll');
        }

        // Check if user/IP has already voted
        if ($this->hasAlreadyVoted($poll->id, $userId, $voterIp)) {
            throw new AlreadyVotedException();
        }

        // Check if guest voting is allowed
        if (!$userId && !$poll->allow_guest_voting) {
            throw new GuestVotingNotAllowedException();
        }

        // Create the vote
        $voteData = [
            'poll_id' => $poll->id,
            'poll_option_id' => $pollOptionId,
            'user_id' => $userId,
            'voter_ip' => $voterIp,
        ];

        $vote = $this->voteRepository->create($voteData);

        // Dispatch job to broadcast vote event
        BroadcastVoteEvent::dispatch($poll->id, $pollOptionId);

        return $vote;
    }

    /**
     * Check if user or IP has already voted on this poll.
     *
     * @param int $pollId The poll ID.
     * @param int|null $userId The user ID (if authenticated).
     * @param string|null $voterIp The voter's IP address (for guests).
     * @return bool True if already voted, false otherwise.
     */
    private function hasAlreadyVoted(int $pollId, ?int $userId, ?string $voterIp): bool
    {
        if ($userId) {
            return $this->voteRepository->hasUserVoted($pollId, $userId);
        }

        if ($voterIp) {
            return $this->voteRepository->hasIpVoted($pollId, $voterIp);
        }

        return false;
    }

    /**
     * Get poll results with vote counts for each option.
     *
     * @param int $pollId The poll ID.
     * @return Poll The poll model with options and vote counts loaded.
     *
     * @throws PollNotFoundException If the poll is not found or not active.
     */
    public function getPollResults(int $pollId): Poll
    {
        $poll = $this->pollRepository->getPollWithDetails($pollId);

        if (!$poll) {
            throw new PollNotFoundException('Poll not found');
        }

        // Check if poll is active
        if (!$poll->isActive()) {
            throw new PollNotFoundException('Poll results are not available');
        }

        // Load options with vote counts
        $poll->load(['options' => function ($query) {
            $query->withCount('votes')->orderBy('order_index');
        }]);

        return $poll;
    }
}
