<?php

namespace Database\Factories;

use App\Models\Vote;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vote>
 */
class VoteFactory extends Factory
{
    protected $model = Vote::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'poll_id' => Poll::factory(),
            'poll_option_id' => PollOption::factory(),
            'user_id' => User::factory(),
            'voter_ip' => null,
        ];
    }

    /**
     * Vote by guest with IP
     */
    public function guest(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
            'voter_ip' => fake()->ipv4(),
        ]);
    }

    /**
     * Vote by authenticated user
     */
    public function authenticated(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => User::factory(),
            'voter_ip' => null,
        ]);
    }
}
