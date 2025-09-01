<?php

namespace App\Http\Controllers;

use App\Services\PollService;
use App\Services\VoteService;
use App\Http\Requests\VoteRequest;
use App\Helpers\ApiResponse;
use App\Exceptions\PollNotFoundException;
use App\Exceptions\PollOptionNotFoundException;
use App\Exceptions\AlreadyVotedException;
use App\Exceptions\GuestVotingNotAllowedException;
use App\Models\Poll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class PublicPollController extends Controller
{
    protected PollService $pollService;
    protected VoteService $voteService;

    public function __construct(PollService $pollService, VoteService $voteService)
    {
        $this->pollService = $pollService;
        $this->voteService = $voteService;
    }

    /**
     * Get list of active polls
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $polls = $this->pollService->getActivePolls($perPage);

            return ApiResponse::response($polls, 'Polls retrieved successfully', 200, true);

        } catch (Exception $e) {
            Log::error('Error retrieving active polls', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);
            return ApiResponse::response(null, 'Internal server error', 500, false);
        }
    }

    /**
     * Get poll details
     */
    public function show(int $id)
    {
        try {
            $poll = $this->pollService->getPublicPollDetails($id, auth('sanctum')->id());
            $data = ['poll' => $poll];

            return ApiResponse::response($data, 'Poll details retrieved successfully', 200, true);

        } catch (PollNotFoundException $e) {
            return ApiResponse::response(null, $e->getMessage(), $e->getCode(), false);
        } catch (Exception $e) {
            Log::error('Error retrieving poll details', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'poll_id' => $id,
                'user_id' => auth('sanctum')->id(),
            ]);
            return ApiResponse::response(null, 'Internal server error', 500, false);
        }
    }

    /**
     * Submit a vote
     */
    public function vote(VoteRequest $request, Poll $poll)
    {
        try {
            $validated = $request->validated();
            $userId = auth('sanctum')->id();

            // Only set voter IP for guest users (when userId is null)
            $voterIp = $userId ? null : $request->ip();

            $vote = $this->voteService->submitVote(
                $poll,
                $validated['poll_option_id'],
                $userId,
                $voterIp
            );

            return ApiResponse::response($vote, 'Vote submitted successfully', 201, true);

        } catch (AlreadyVotedException $e) {
            return ApiResponse::response(null, $e->getMessage(), $e->getCode(), false);
        } catch (GuestVotingNotAllowedException $e) {
            return ApiResponse::response(null, $e->getMessage(), $e->getCode(), false);
        } catch (PollNotFoundException $e) {
            return ApiResponse::response(null, $e->getMessage(), $e->getCode(), false);
        } catch (PollOptionNotFoundException $e) {
            return ApiResponse::response(null, $e->getMessage(), $e->getCode(), false);
        } catch (Exception $e) {
            Log::error('Error submitting vote', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'poll_id' => $poll->id,
                'user_id' => auth('sanctum')->id(),
            ]);
            return ApiResponse::response(null, 'Internal server error', 500, false);
        }
    }

    /**
     * Get poll results
     */
    public function results(int $id)
    {
        try {
            $poll = $this->voteService->getPollResults($id);

            // Calculate total votes and percentages
            $totalVotes = $poll->options->sum('votes_count');

            $results = $poll->options->map(function ($option) use ($totalVotes) {
                $percentage = $totalVotes > 0 ? round(($option->votes_count / $totalVotes) * 100, 2) : 0;

                return [
                    'id' => $option->id,
                    'option_text' => $option->option_text,
                    'order_index' => $option->order_index,
                    'votes_count' => $option->votes_count,
                    'percentage' => $percentage,
                ];
            });

            $data = [
                'poll' => [
                    'id' => $poll->id,
                    'title' => $poll->title,
                    'description' => $poll->description,
                    'total_votes' => $totalVotes,
                ],
                'results' => $results,
            ];

            return ApiResponse::response($data, 'Poll results retrieved successfully', 200, true);

        } catch (PollNotFoundException $e) {
            return ApiResponse::response(null, $e->getMessage(), $e->getCode(), false);
        } catch (Exception $e) {
            Log::error('Error retrieving poll results', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'poll_id' => $id,
            ]);
            return ApiResponse::response(null, 'Internal server error', 500, false);
        }
    }
}
