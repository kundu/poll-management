<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreatePollRequest;
use App\Services\PollService;
use App\Helpers\ApiResponse;
use App\Exceptions\PollNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class PollController extends Controller
{
    protected PollService $pollService;

    public function __construct(PollService $pollService)
    {
        $this->pollService = $pollService;
    }

    /**
     * Create a new poll
     */
    public function store(CreatePollRequest $request)
    {
        try {
            $validated = $request->validated();

            $pollData = [
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'allow_guest_voting' => $validated['allow_guest_voting'],
            ];

            $options = $validated['options'];
            $adminId = auth()->id();

            $poll = $this->pollService->createPoll($pollData, $options, $adminId);

            return ApiResponse::response($poll, 'Poll created successfully', 201, true);

        } catch (Exception $e) {
            Log::error('Unexpected error in poll creation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'user_id' => auth()->user()?->id
            ]);
            return ApiResponse::response(null, 'Internal server error', 500, false);
        }
    }

    /**
     * Get polls with filters
     */
    public function index(Request $request)
    {
        try {
            $filters = $request->only(['status', 'search', 'created_by', 'date_from', 'date_to', 'sort', 'order']);
            $perPage = $request->get('per_page', 10);

            $polls = $this->pollService->getPolls($filters, $perPage);

            $data = [
                'polls' => $polls->items(),
                'pagination' => [
                    'current_page' => $polls->currentPage(),
                    'per_page' => $polls->perPage(),
                    'total' => $polls->total(),
                    'last_page' => $polls->lastPage()
                ]
            ];

            return ApiResponse::response($data, 'Polls retrieved successfully', 200, true);

        } catch (PollNotFoundException $e) {
            return ApiResponse::response(null, $e->getMessage(), 404, false);
        } catch (Exception $e) {
            Log::error('Unexpected error in getting polls', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'user_id' => auth()->user()?->id
            ]);
            return ApiResponse::response(null, 'Internal server error', 500, false);
        }
    }

    /**
     * Get polls with vote summary data (for live votes panel)
     */
    public function getPollsWithVoteSummary(Request $request)
    {
        try {
            $filters = $request->only(['status', 'search', 'created_by', 'date_from', 'date_to', 'sort', 'order']);
            $perPage = $request->get('per_page', 10);

            $polls = $this->pollService->getPollsWithVoteSummary($filters, $perPage);

            // Transform the data to include calculated percentages
            $pollsData = $polls->items();
            foreach ($pollsData as $poll) {
                $totalVotes = $poll->total_votes ?? 0;
                foreach ($poll->options as $option) {
                    $voteCount = $option->votes_count ?? 0;
                    $option->percentage = $totalVotes > 0 ? round(($voteCount / $totalVotes) * 100, 2) : 0;
                }
            }

            $data = [
                'polls' => $pollsData,
                'pagination' => [
                    'current_page' => $polls->currentPage(),
                    'per_page' => $polls->perPage(),
                    'total' => $polls->total(),
                    'last_page' => $polls->lastPage()
                ]
            ];

            return ApiResponse::response($data, 'Polls with vote summary retrieved successfully', 200, true);

        } catch (PollNotFoundException $e) {
            return ApiResponse::response(null, $e->getMessage(), 404, false);
        } catch (Exception $e) {
            Log::error('Unexpected error in getting polls with vote summary', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'user_id' => auth()->user()?->id
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
            $poll = $this->pollService->getPollDetails($id);
            $data = ['poll' => $poll];
            return ApiResponse::response($data, 'Poll details retrieved successfully', 200, true);

        } catch (PollNotFoundException $e) {
            return ApiResponse::response(null, $e->getMessage(), 404, false);
        } catch (Exception $e) {
            Log::error('Unexpected error in getting poll details', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'poll_id' => $id,
                'user_id' => auth()->user()?->id
            ]);
            return ApiResponse::response(null, 'Internal server error', 500, false);
        }
    }

    /**
     * Update poll status
     */
    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|integer|in:0,1'
        ]);

        try {
            $status = $request->input('status');
            $poll = $this->pollService->updatePollStatus($id, $status);

            return ApiResponse::response($poll, 'Poll status updated successfully', 200, true);

        } catch (PollNotFoundException $e) {
            return ApiResponse::response(null, $e->getMessage(), 404, false);
        } catch (Exception $e) {
            Log::error('Unexpected error in updating poll status', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'poll_id' => $id,
                'user_id' => auth()->user()?->id
            ]);
            return ApiResponse::response(null, 'Internal server error', 500, false);
        }
    }
}
