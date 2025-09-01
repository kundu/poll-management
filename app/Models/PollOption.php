<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PollOption extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'poll_id',
        'option_text',
        'order_index',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'order_index' => 'integer',
    ];

    /**
     * Validation rules
     */
    public static function rules(): array
    {
        return [
            'poll_id' => 'required|exists:polls,id',
            'option_text' => 'required|string|max:200',
            'order_index' => 'required|integer|min:0',
        ];
    }

    /**
     * Get the poll this option belongs to
     */
    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }

    /**
     * Get the votes for this option
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    /**
     * Get votes count for this option
     */
    public function getVotesCountAttribute(): int
    {
        return $this->votes()->count();
    }

    /**
     * Get percentage of votes for this option
     */
    public function getVotesPercentageAttribute(): float
    {
        $totalVotes = $this->poll->total_votes;
        if ($totalVotes === 0) {
            return 0.0;
        }
        return round(($this->votes_count / $totalVotes) * 100, 2);
    }
}
