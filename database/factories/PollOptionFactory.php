<?php

namespace Database\Factories;

use App\Models\Poll;
use App\Models\PollOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PollOption>
 */
class PollOptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PollOption::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'poll_id' => Poll::factory(),
            'option_text' => fake()->sentence(3),
            'order_index' => fake()->numberBetween(1, 10),
        ];
    }

    /**
     * Set a specific order index.
     */
    public function orderIndex(int $index): static
    {
        return $this->state(fn (array $attributes) => [
            'order_index' => $index,
        ]);
    }
}
