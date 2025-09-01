<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;

Route::get('/', function () {
    return view('user.home');
});

Route::get('/polls/{id}', function ($id) {
    return view('user.poll-details', ['pollId' => $id]);
})->name('user.poll.details');

// Admin Dashboard Routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [DashboardController::class, 'showLogin'])->name('admin.login');
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/polls', [DashboardController::class, 'polls'])->name('admin.polls');
    Route::get('/live-votes', [DashboardController::class, 'liveVotes'])->name('admin.live-votes');
    Route::get('/settings', [DashboardController::class, 'settings'])->name('admin.settings');
});
