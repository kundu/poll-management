<?php

namespace App\Repositories;

use App\Models\Vote;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class VoteRepository extends BaseRepository
{
    public function __construct(Vote $model)
    {
        parent::__construct($model);
    }

    /**
     * Check if user has voted on poll
     */
    public function hasUserVoted(int $pollId, int $userId): bool
    {
        return $this->model->where('poll_id', $pollId)
            ->where('user_id', $userId)
            ->exists();
    }

    /**
     * Check if IP has voted on poll
     */
    public function hasIpVoted(int $pollId, string $ip): bool
    {
        return $this->model->where('poll_id', $pollId)
            ->where('voter_ip', $ip)
            ->exists();
    }

    /**
     * Create authenticated vote
     */
    public function createAuthenticatedVote(int $pollId, int $pollOptionId, int $userId): Vote
    {
        return $this->create([
            'poll_id' => $pollId,
            'poll_option_id' => $pollOptionId,
            'user_id' => $userId,
            'voter_ip' => null,
        ]);
    }

    /**
     * Create guest vote
     */
    public function createGuestVote(int $pollId, int $pollOptionId, string $ip): Vote
    {
        return $this->create([
            'poll_id' => $pollId,
            'poll_option_id' => $pollOptionId,
            'user_id' => null,
            'voter_ip' => $ip,
        ]);
    }

    /**
     * Get votes for a poll
     */
    public function getVotesByPoll(int $pollId): Collection
    {
        return $this->model->where('poll_id', $pollId)
            ->with(['user', 'option'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get authenticated votes for a poll
     */
    public function getAuthenticatedVotesByPoll(int $pollId): Collection
    {
        return $this->model->where('poll_id', $pollId)
            ->whereNotNull('user_id')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get guest votes for a poll
     */
    public function getGuestVotesByPoll(int $pollId): Collection
    {
        return $this->model->where('poll_id', $pollId)
            ->whereNotNull('voter_ip')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get votes by user
     */
    public function getVotesByUser(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->where('user_id', $userId)
            ->with(['poll', 'option'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get vote statistics for a poll
     */
    public function getVoteStatistics(int $pollId): array
    {
        $totalVotes = $this->model->where('poll_id', $pollId)->count();
        $authenticatedVotes = $this->model->where('poll_id', $pollId)->whereNotNull('user_id')->count();
        $guestVotes = $this->model->where('poll_id', $pollId)->whereNotNull('voter_ip')->count();

        return [
            'total_votes' => $totalVotes,
            'authenticated_votes' => $authenticatedVotes,
            'guest_votes' => $guestVotes,
        ];
    }

    /**
     * Get votes by option
     */
    public function getVotesByOption(int $pollOptionId): Collection
    {
        return $this->model->where('poll_option_id', $pollOptionId)
            ->with(['user', 'poll'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get vote count by option
     */
    public function getVoteCountByOption(int $pollOptionId): int
    {
        return $this->model->where('poll_option_id', $pollOptionId)->count();
    }

    /**
     * Delete votes by poll
     */
    public function deleteVotesByPoll(int $pollId): bool
    {
        return $this->model->where('poll_id', $pollId)->delete();
    }

    /**
     * Delete votes by user
     */
    public function deleteVotesByUser(int $userId): bool
    {
        return $this->model->where('user_id', $userId)->delete();
    }

    /**
     * Get recent votes
     */
    public function getRecentVotes(int $limit = 10): Collection
    {
        return $this->model->with(['poll', 'option', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
