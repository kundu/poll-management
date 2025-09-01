<?php

namespace App\Services;

use App\Exceptions\InvalidCredentials;
use App\Repositories\UserRepository;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Register a new user.
     *
     * @param array $userData The user registration data (expects 'name', 'email', 'password').
     * @return array{
     *     user: array{
     *         id: int,
     *         name: string,
     *         email: string,
     *         user_type: int
     *     },
     *     token: string
     * }
     */
    public function register(array $userData): array
    {
        $user = $this->userRepository->create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => Hash::make($userData['password']),
            'user_type' => User::USER_TYPE_USER,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user_type' => $user->user_type,
            ],
            'token' => $token,
        ];
    }

    /**
     * Login user with credentials.
     *
     * @param array $credentials The login credentials (expects 'email' and 'password').
     * @return array{
     *     user: array{
     *         id: int,
     *         name: string,
     *         email: string,
     *         user_type: int
     *     },
     *     token: string
     * }
     * @throws InvalidCredentials If authentication fails.
     */
    public function login(array $credentials): array
    {
        if (!Auth::attempt($credentials)) {
            throw new InvalidCredentials();
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user_type' => $user->user_type,
            ],
            'token' => $token,
        ];
    }

    /**
     * Logout the given user (delete all tokens).
     *
     * @param User $user The user to logout.
     * @return bool True on success.
     */
    public function logout(User $user): bool
    {
        $user->tokens()->delete();
        return true;
    }

    /**
     * Get user profile data.
     *
     * @param User $user The user whose profile to retrieve.
     * @return array{
     *     id: int,
     *     name: string,
     *     email: string,
     *     user_type: int,
     *     created_at: \Illuminate\Support\Carbon|string|null
     * }
     */
    public function getUserProfile(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'user_type' => $user->user_type,
            'created_at' => $user->created_at,
        ];
    }
}
