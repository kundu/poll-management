<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * User types
     */
    const USER_TYPE_USER = 1;
    const USER_TYPE_ADMIN = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'user_type' => 'integer',
        ];
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->user_type === self::USER_TYPE_ADMIN;
    }

    /**
     * Check if user is regular user
     */
    public function isUser(): bool
    {
        return $this->user_type === self::USER_TYPE_USER;
    }

    /**
     * Get polls created by this user (admin only)
     */
    public function polls(): HasMany
    {
        return $this->hasMany(Poll::class, 'created_by');
    }

    /**
     * Get votes cast by this user
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }
}
