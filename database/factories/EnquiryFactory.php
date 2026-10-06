<?php

namespace Database\Factories;

use App\Models\Enquiry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Enquiry>
 */
class EnquiryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => 'contact',
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'summary' => 'General enquiry',
            'data' => ['topic' => 'General enquiry', 'message' => fake()->sentence()],
            'status' => 'new',
        ];
    }

    /** A booking request for a specific tour date. */
    public function booking(?string $tourDate = null): static
    {
        return $this->state(fn () => [
            'type' => 'booking',
            'summary' => '3-Hour Rainforest Walk',
            'data' => ['tour' => '3-Hour Rainforest Walk', 'tour_date' => $tourDate ?? now()->addMonth()->toDateString(), 'adults' => 2],
        ]);
    }
}
