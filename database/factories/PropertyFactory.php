<?php

/**
 * Description: File registering the PropertyFactory for creating property fixtures.
 * Parameters: None.
 * Returns: Void.
 * Expected Result: Enables convenient property model generation for tests.
 */

namespace Database\Factories;

use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Description: Factory providing seeded data for Property model instances.
 * Parameters: None.
 * Returns: Not applicable.
 * Expected Result: Supplies realistic property records for tests and seeders.
 *
 * @extends Factory<\App\Models\Property>
 */
class PropertyFactory extends Factory
{
    protected $model = Property::class;

    /**
     * Description: Define the model's default state for generated properties.
     * Parameters: None.
     * Returns: array<string, mixed>
     * Expected Result: Produces attribute array suitable for property creation.
     */
    public function definition(): array
    {
        return [
            'title' => fake()->streetName(),
            'status' => 'in-progress',
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->stateAbbr(),
            'postal_code' => fake()->postcode(),
            'country' => 'US',
            'lat' => fake()->latitude(33, 37),
            'lng' => fake()->longitude(-122, -118),
        ];
    }
}
