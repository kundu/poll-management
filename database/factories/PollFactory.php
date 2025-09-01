<?php

namespace Database\Factories;

use App\Models\Poll;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Poll>
 */
class PollFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Poll::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement([Poll::STATUS_INACTIVE, Poll::STATUS_ACTIVE]),
            'allow_guest_voting' => fake()->boolean(),
            'created_by' => User::factory(),
        ];
    }

    /**
     * Indicate that the poll is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Poll::STATUS_ACTIVE,
        ]);
    }

    /**
     * Indicate that the poll is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Poll::STATUS_INACTIVE,
        ]);
    }

    /**
     * Indicate that the poll allows guest voting.
     */
    public function allowsGuestVoting(): static
    {
        return $this->state(fn (array $attributes) => [
            'allow_guest_voting' => true,
        ]);
    }

    /**
     * Indicate that the poll does not allow guest voting.
     */
    public function noGuestVoting(): static
    {
        return $this->state(fn (array $attributes) => [
            'allow_guest_voting' => false,
        ]);
    }
}
