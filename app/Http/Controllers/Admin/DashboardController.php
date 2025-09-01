<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Poll;
use App\Models\Vote;
use App\Helpers\ApiResponse;
use Exception;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function showLogin()
    {
        return view('admin.login');
    }

    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function polls()
    {
        return view('admin.polls');
    }

    public function settings()
    {
        return view('admin.settings');
    }

    public function liveVotes()
    {
        return view('admin.live-votes');
    }

    public function getDashboardData()
    {
        try {
            // Get statistics
            $stats = [
                'total_polls' => Poll::count(),
                'active_polls' => Poll::where('status', 1)->count(),
                'inactive_polls' => Poll::where('status', 0)->count(),
                'total_votes' => Vote::count(),
            ];

            // Get recent polls
            $recentPolls = Poll::with('creator')
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($poll) {
                    return [
                        'id' => $poll->id,
                        'title' => $poll->title,
                        'description' => $poll->description,
                        'status' => $poll->status,
                        'created_at' => $poll->created_at,
                        'creator' => $poll->creator ? [
                            'id' => $poll->creator->id,
                            'name' => $poll->creator->name,
                            'email' => $poll->creator->email,
                        ] : null,
                    ];
                });

            $data = [
                'stats' => $stats,
                'recent_polls' => $recentPolls,
            ];

            return ApiResponse::response($data, 'Dashboard data retrieved successfully', 200, true);

        } catch (Exception $e) {
            Log::error('Failed to retrieve dashboard data: ' , [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return ApiResponse::response(null, 'Failed to retrieve dashboard data: ' . $e->getMessage(), 500, false);
        }
    }
}
