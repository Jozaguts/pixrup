<?php

namespace Database\Factories;

use App\Models\GlowupJob;
use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GlowupJob>
 */
class GlowupJobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $roomTypes = ['living_room', 'kitchen', 'bathroom', 'bedroom', 'facade'];
        $styles = ['modern', 'minimalist', 'luxury', 'rustic', 'outdoor'];

        return [
            'property_id' => Property::factory(),
            'user_id' => User::factory(),
            'room_type' => fake()->randomElement($roomTypes),
            'style' => fake()->randomElement($styles),
            'before_url' => fake()->imageUrl(),
            'after_url' => null,
            'status' => GlowupJob::STATUS_PENDING,
            'meta' => [
                'prompt' => fake()->sentence(6),
            ],
        ];
    }

    public function done(): self
    {
        return $this->state(fn (): array => [
            'status' => GlowupJob::STATUS_DONE,
            'after_url' => fake()->imageUrl(),
            'usage_recorded_at' => now(),
        ]);
    }
}
