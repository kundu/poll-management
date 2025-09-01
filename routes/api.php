<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PollController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicPollController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');

// Public poll routes
Route::prefix('polls')->group(function () {
    Route::get('/', [PublicPollController::class, 'index']);
    Route::get('/{id}', [PublicPollController::class, 'show']);
    Route::post('/{poll}/vote', [PublicPollController::class, 'vote']);
    Route::get('/{id}/results', [PublicPollController::class, 'results']);
});

// Admin routes
Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'getDashboardData']);
    Route::get('/polls/vote-summary', [PollController::class, 'getPollsWithVoteSummary']);
    Route::apiResource('polls', PollController::class);
    Route::patch('polls/{id}/status', [PollController::class, 'updateStatus']);
});
