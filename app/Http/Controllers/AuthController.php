<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidCredentials;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Register a new user
     */
    public function register(RegisterRequest $request)
    {
        try {
            $validated = $request->validated();
            $data = $this->authService->register($validated);

            return ApiResponse::response($data, 'User registered successfully', 201, true);

        } catch (Exception $e) {
            Log::error('User registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);
            return ApiResponse::response(null, 'Internal server error', 500, false);
        }
    }

    /**
     * Login user
     */
    public function login(LoginRequest $request)
    {
        try {
            $validated = $request->validated();
            $data = $this->authService->login($validated);

            return ApiResponse::response($data, 'Login successful', 200, true);

        } catch (InvalidCredentials $e) {
            return ApiResponse::response(null, $e->getMessage(), $e->getCode(), false);
        }
        catch (Exception $e) {
            Log::error('User login failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);
            return ApiResponse::response(null, 'Internal server error', 500, false);
        }
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return ApiResponse::response(null, 'User not authenticated', 401, false);
            }

            $this->authService->logout($user);
            return ApiResponse::response(null, 'Logout successful', 200, true);

        } catch (Exception $e) {
            Log::error('User logout failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $request->user()?->id,
            ]);
            return ApiResponse::response(null, 'Internal server error', 500, false);
        }
    }

    /**
     * Get authenticated user
     */
    public function me(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return ApiResponse::response(null, 'User not authenticated', 401, false);
            }

            $data = $this->authService->getUserProfile($user);
            return ApiResponse::response($data, 'User retrieved successfully', 200, true);

        } catch (Exception $e) {
            Log::error('Get user failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $request->user()?->id,
            ]);
            return ApiResponse::response(null, 'Internal server error', 500, false);
        }
    }
}
