<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poll extends Model
{
    use HasFactory;

    /**
     * Poll statuses
     */
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'allow_guest_voting',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'integer',
        'allow_guest_voting' => 'boolean',
    ];

    /**
     * Validation rules
     */
    public static function rules(): array
    {
        return [
            'title' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'status' => 'required|integer|in:0,1',
            'allow_guest_voting' => 'required|boolean',
            'options' => 'required|array|min:2|max:10',
            'options.*.option_text' => 'required|string|max:200',
            'options.*.order_index' => 'required|integer|min:0',
        ];
    }

    /**
     * Check if poll is active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if poll is inactive
     */
    public function isInactive(): bool
    {
        return $this->status === self::STATUS_INACTIVE;
    }

    /**
     * Get the user who created this poll
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the options for this poll
     */
    public function options(): HasMany
    {
        return $this->hasMany(PollOption::class)->orderBy('order_index');
    }

    /**
     * Get the votes for this poll
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    /**
     * Get total votes count
     */
    public function getTotalVotesAttribute(): int
    {
        return $this->votes()->count();
    }

    /**
     * Get authenticated votes count
     */
    public function getAuthenticatedVotesAttribute(): int
    {
        return $this->votes()->whereNotNull('user_id')->count();
    }

    /**
     * Get guest votes count
     */
    public function getGuestVotesAttribute(): int
    {
        return $this->votes()->whereNotNull('voter_ip')->count();
    }

    /**
     * Check if a user has voted on this poll
     */
    public function hasUserVoted(?int $userId): bool
    {
        if (!$userId) {
            return false;
        }
        return $this->votes()->where('user_id', $userId)->exists();
    }

    /**
     * Check if an IP has voted on this poll
     */
    public function hasIpVoted(?string $ip): bool
    {
        if (!$ip) {
            return false;
        }
        return $this->votes()->where('voter_ip', $ip)->exists();
    }
}
