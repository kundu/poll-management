<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vote extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'poll_id',
        'poll_option_id',
        'user_id',
        'voter_ip',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'poll_id' => 'integer',
        'poll_option_id' => 'integer',
        'user_id' => 'integer',
    ];

    /**
     * Validation rules
     */
    public static function rules(): array
    {
        return [
            'poll_id' => 'required|exists:polls,id',
            'poll_option_id' => 'required|exists:poll_options,id',
            'user_id' => 'nullable|exists:users,id',
            'voter_ip' => 'nullable|ip',
        ];
    }

    /**
     * Get the poll this vote belongs to
     */
    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }

    /**
     * Get the option this vote is for
     */
    public function option(): BelongsTo
    {
        return $this->belongsTo(PollOption::class, 'poll_option_id');
    }

    /**
     * Get the user who cast this vote (if authenticated)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if this is an authenticated vote
     */
    public function isAuthenticatedVote(): bool
    {
        return !is_null($this->user_id);
    }

    /**
     * Check if this is a guest vote
     */
    public function isGuestVote(): bool
    {
        return is_null($this->user_id) && !is_null($this->voter_ip);
    }

    /**
     * Get voter identifier (user email or IP)
     */
    public function getVoterIdentifierAttribute(): string
    {
        if ($this->isAuthenticatedVote()) {
            return $this->user->email ?? 'Unknown User';
        }
        return $this->voter_ip ?? 'Unknown IP';
    }
}
