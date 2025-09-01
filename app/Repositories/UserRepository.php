<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Get all admin users
     */
    public function getAdmins(): Collection
    {
        return $this->model->where('user_type', User::USER_TYPE_ADMIN)->get();
    }

    /**
     * Get all regular users
     */
    public function getRegularUsers(): Collection
    {
        return $this->model->where('user_type', User::USER_TYPE_USER)->get();
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(int $userId): bool
    {
        $user = $this->find($userId);
        return $user ? $user->isAdmin() : false;
    }

    /**
     * Create admin user
     */
    public function createAdmin(array $data): User
    {
        $data['user_type'] = User::USER_TYPE_ADMIN;
        return $this->create($data);
    }

    /**
     * Create regular user
     */
    public function createUser(array $data): User
    {
        $data['user_type'] = User::USER_TYPE_USER;
        return $this->create($data);
    }

    /**
     * Get users with their polls (for admins)
     */
    public function getUsersWithPolls(int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->with('polls')->paginate($perPage);
    }

    /**
     * Get user with voting history
     */
    public function getUserWithVotes(int $userId): ?User
    {
        return $this->model->with(['votes.poll', 'votes.option'])->find($userId);
    }

    /**
     * Get users by type with pagination
     */
    public function getUsersByType(int $userType, int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->where('user_type', $userType)->paginate($perPage);
    }

    /**
     * Search users by email
     */
    public function searchByEmail(string $email, int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->where('email', 'like', "%{$email}%")->paginate($perPage);
    }
}
