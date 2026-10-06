<?php

namespace Database\Factories;

use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'origin' => fake()->country(),
            'tour_title' => '3-Hour Rainforest Walk',
            'rating' => 5,
            'body' => fake()->paragraph(),
            'source' => 'website',
            'is_published' => true,
        ];
    }

    /** A review that is still waiting for approval in the admin. */
    public function pending(): static
    {
        return $this->state(fn () => ['is_published' => false]);
    }
}
