<?php

namespace App\Repositories;

use App\Models\PollOption;
use Illuminate\Database\Eloquent\Collection;

class PollOptionRepository extends BaseRepository
{
    public function __construct(PollOption $model)
    {
        parent::__construct($model);
    }

    /**
     * Get options for a specific poll
     */
    public function getOptionsByPoll(int $pollId): Collection
    {
        return $this->model->where('poll_id', $pollId)
            ->orderBy('order_index')
            ->get();
    }

    /**
     * Get options with vote counts for a poll
     */
    public function getOptionsWithVoteCounts(int $pollId): Collection
    {
        return $this->model->where('poll_id', $pollId)
            ->withCount('votes')
            ->orderBy('order_index')
            ->get();
    }

    /**
     * Check if option belongs to poll
     */
    public function optionBelongsToPoll(int $optionId, int $pollId): bool
    {
        return $this->model->where('id', $optionId)
            ->where('poll_id', $pollId)
            ->exists();
    }

    /**
     * Get option with poll details
     */
    public function getOptionWithPoll(int $optionId): ?PollOption
    {
        return $this->model->with('poll')->find($optionId);
    }

    /**
     * Update option order
     */
    public function updateOptionOrder(int $optionId, int $orderIndex): bool
    {
        return $this->update($optionId, ['order_index' => $orderIndex]);
    }

    /**
     * Get options with percentage calculations
     */
    public function getOptionsWithPercentages(int $pollId): Collection
    {
        $options = $this->getOptionsWithVoteCounts($pollId);
        $totalVotes = $options->sum('votes_count');

        return $options->map(function ($option) use ($totalVotes) {
            $option->percentage = $totalVotes > 0 ? round(($option->votes_count / $totalVotes) * 100, 2) : 0;
            return $option;
        });
    }

    /**
     * Create multiple options for a poll
     */
    public function createMultipleOptions(int $pollId, array $options): Collection
    {
        $createdOptions = collect();

        foreach ($options as $option) {
            $option['poll_id'] = $pollId;
            $createdOptions->push($this->create($option));
        }

        return $createdOptions;
    }

    /**
     * Delete all options for a poll
     */
    public function deleteOptionsByPoll(int $pollId): bool
    {
        return $this->model->where('poll_id', $pollId)->delete();
    }
}
