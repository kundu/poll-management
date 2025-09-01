<?php

namespace App\Services;

use App\Repositories\PollRepository;
use App\Repositories\PollOptionRepository;
use App\Repositories\VoteRepository;
use App\Exceptions\PollNotFoundException;
use App\Models\Poll;
use Illuminate\Pagination\LengthAwarePaginator;

class PollService
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
     * Create a new poll with options.
     *
     * @param array $pollData The data for the poll (title, description, etc).
     * @param array $options The options for the poll.
     * @param int $adminId The ID of the admin creating the poll.
     * @return Poll The created Poll model.
     */
    public function createPoll(array $pollData, array $options, int $adminId): Poll
    {
        // Add creator ID to poll data
        $pollData['created_by'] = $adminId;
        $pollData['status'] = Poll::STATUS_INACTIVE; // Default to inactive

        // Create poll with options
        $poll = $this->pollRepository->createPollWithOptions($pollData, $options);

        return $poll;
    }

    /**
     * Get polls with filters.
     *
     * @param array $filters Filters to apply (status, created_by, etc).
     * @param int $perPage Number of polls per page.
     * @return LengthAwarePaginator Paginated list of polls.
     */
    public function getPolls(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return $this->pollRepository->getPollsWithFilters($filters, $perPage);
    }

    /**
     * Get polls with vote summary data (optimized for live votes panel).
     *
     * @param array $filters Filters to apply (status, created_by, etc).
     * @param int $perPage Number of polls per page.
     * @return LengthAwarePaginator Paginated list of polls with vote counts.
     */
    public function getPollsWithVoteSummary(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return $this->pollRepository->getPollsWithVoteSummary($filters, $perPage);
    }

    /**
     * Get active polls for public viewing.
     *
     * @param int $perPage Number of polls per page.
     * @return array{
     *     polls: array,
     *     pagination: array{
     *         current_page: int,
     *         per_page: int,
     *         total: int,
     *         last_page: int
     *     }
     * }
     */
    public function getActivePolls(int $perPage = 10): array
    {
        $filters = ['status' => Poll::STATUS_ACTIVE]; // Only active polls
        $polls = $this->pollRepository->getPollsWithFilters($filters, $perPage);

        return [
            'polls' => $polls->items(),
            'pagination' => [
                'current_page' => $polls->currentPage(),
                'per_page' => $polls->perPage(),
                'total' => $polls->total(),
                'last_page' => $polls->lastPage()
            ]
        ];
    }

    /**
     * Get poll details for public viewing.
     *
     * @param int $pollId The ID of the poll.
     * @param int|null $userId The ID of the user (optional, for checking if voted).
     * @return array The poll data with 'has_voted' key.
     *
     * @throws PollNotFoundException If the poll is not found or not active.
     */
    public function getPublicPollDetails(int $pollId, ?int $userId = null): array
    {
        $poll = $this->pollRepository->getPollWithDetails($pollId);

        if (!$poll) {
            throw new PollNotFoundException('Poll not found');
        }

        // Check if poll is active
        if (!$poll->isActive()) {
            throw new PollNotFoundException('Poll is not available');
        }

        // Check if user has already voted
        $hasVoted = false;
        if ($userId) {
            $hasVoted = $this->voteRepository->hasUserVoted($poll->id, $userId);
        }

        $pollData = $poll->toArray();
        $pollData['has_voted'] = $hasVoted;

        return $pollData;
    }

    /**
     * Get poll details (admin or internal use).
     *
     * @param int $pollId The ID of the poll.
     * @return Poll The poll model with details.
     *
     * @throws PollNotFoundException If the poll is not found.
     */
    public function getPollDetails(int $pollId): Poll
    {
        $poll = $this->pollRepository->getPollWithDetails($pollId);

        if (!$poll) {
            throw new PollNotFoundException('Poll not found with ID: ' . $pollId);
        }

        return $poll;
    }

    /**
     * Update poll status.
     *
     * @param int $pollId The ID of the poll.
     * @param int $status The new status for the poll.
     * @return Poll The updated poll model.
     *
     * @throws PollNotFoundException If the poll is not found.
     */
    public function updatePollStatus(int $pollId, int $status): Poll
    {
        $poll = $this->pollRepository->find($pollId);

        if (!$poll) {
            throw new PollNotFoundException('Poll not found with ID: ' . $pollId);
        }

        $this->pollRepository->update($pollId, ['status' => $status]);

        return $this->pollRepository->find($pollId);
    }
}
