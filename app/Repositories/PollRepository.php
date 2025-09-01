<?php

namespace App\Repositories;

use App\Models\Poll;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PollRepository extends BaseRepository
{
    public function __construct(Poll $model)
    {
        parent::__construct($model);
    }

    /**
     * Get active polls
     */
    public function getActivePolls(): Collection
    {
        return $this->model->where('status', Poll::STATUS_ACTIVE)->with('options')->get();
    }

    /**
     * Get active polls with pagination
     */
    public function getActivePollsPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->where('status', Poll::STATUS_ACTIVE)
            ->with('options')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get polls by creator (admin)
     */
    public function getPollsByCreator(int $creatorId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->where('created_by', $creatorId)
            ->with('options')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get polls with filters
     */
    public function getPollsWithFilters(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->model->with('options');

        // Status filter
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Search filter
        if (isset($filters['search'])) {
            $query->where('title', 'like', "%{$filters['search']}%");
        }

        // Creator filter
        if (isset($filters['created_by'])) {
            $query->where('created_by', $filters['created_by']);
        }

        // Date range filter
        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        // Sorting
        $sortField = $filters['sort'] ?? 'created_at';
        $sortOrder = $filters['order'] ?? 'desc';
        $query->orderBy($sortField, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Get polls with vote summary data (optimized to avoid N+1 queries)
     */
    public function getPollsWithVoteSummary(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->model
            ->with([
                'options' => function ($query) {
                    $query->withCount('votes');
                }
            ])
            ->withCount('votes as total_votes');

        // Status filter
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Search filter
        if (isset($filters['search'])) {
            $query->where('title', 'like', "%{$filters['search']}%");
        }

        // Creator filter
        if (isset($filters['created_by'])) {
            $query->where('created_by', $filters['created_by']);
        }

        // Date range filter
        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        // Sorting
        $sortField = $filters['sort'] ?? 'created_at';
        $sortOrder = $filters['order'] ?? 'desc';
        $query->orderBy($sortField, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Get poll with full details (for admin)
     */
    public function getPollWithDetails(int $pollId): ?Poll
    {
        return $this->model->with([
            'options.votes',
            'votes.user',
            'votes.option',
            'creator'
        ])->find($pollId);
    }

    /**
     * Get poll with options (for public)
     */
    public function getPollWithOptions(int $pollId): ?Poll
    {
        return $this->model->with('options')->find($pollId);
    }

    /**
     * Create poll with options
     */
    public function createPollWithOptions(array $pollData, array $options): Poll
    {
        $poll = $this->create($pollData);

        foreach ($options as $option) {
            $poll->options()->create($option);
        }

        return $poll->load('options');
    }

    /**
     * Update poll with options
     */
    public function updatePollWithOptions(int $pollId, array $pollData, array $options): bool
    {
        $poll = $this->find($pollId);
        if (!$poll) {
            return false;
        }

        // Update poll data
        $poll->update($pollData);

        // Delete existing options
        $poll->options()->delete();

        // Create new options
        foreach ($options as $option) {
            $poll->options()->create($option);
        }

        return true;
    }

    /**
     * Get polls that allow guest voting
     */
    public function getGuestVotingPolls(): Collection
    {
        return $this->model->where('status', Poll::STATUS_ACTIVE)
            ->where('allow_guest_voting', true)
            ->with('options')
            ->get();
    }

    /**
     * Check if poll allows guest voting
     */
    public function allowsGuestVoting(int $pollId): bool
    {
        $poll = $this->find($pollId);
        return $poll ? $poll->allow_guest_voting && $poll->isActive() : false;
    }

    /**
     * Get polls with vote counts
     */
    public function getPollsWithVoteCounts(int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->withCount('votes')
            ->with('options')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Search polls by title
     */
    public function searchPolls(string $search, int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->where('title', 'like', "%{$search}%")
            ->with('options')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
