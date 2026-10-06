<?php

namespace Database\Factories;

use App\Models\Tour;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tour>
 */
class TourFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'slug' => Str::slug($title),
            'title' => Str::title($title),
            'category' => 'walks',
            'summary' => fake()->sentence(),
            'body' => fake()->paragraph(),
            'duration' => 'Half day',
            'pickup' => '9:00am',
            'dropoff' => '2:00pm',
            'price_group' => 80,
            'price_private' => 110,
            'includes' => ['Hotel pickup at 9am', 'Drop-off at 2pm'],
            'bring' => ['Water bottle'],
            'notes' => [],
            'cover' => 'images/photos/img_4841.webp',
            'gallery' => [],
            'is_available' => true,
            'is_featured' => false,
            'is_published' => true,
            'sort_order' => 0,
        ];
    }

    public function unpublished(): static
    {
        return $this->state(fn () => ['is_published' => false]);
    }

    /** A tour that is shown on the site but cannot be booked at the moment. */
    public function unavailable(): static
    {
        return $this->state(fn () => ['is_available' => false]);
    }

    /** A tour sold at one standard rate, with no private option. */
    public function standardRateOnly(): static
    {
        return $this->state(fn () => ['price_group' => 60, 'price_private' => null]);
    }
}
