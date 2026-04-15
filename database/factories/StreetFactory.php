<?php

namespace Database\Factories;

use App\Models\Street;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Street>
 */
class StreetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->streetName(),
            'code' => 'NJK-STR-' . fake()->unique()->numberBetween(1000, 9999),
            'town' => fake()->city(),
            'type' => fake()->randomElement(['primary', 'secondary', 'tertiary']),
            'description' => fake()->sentence(),
            'status' => 'approved',
            'start_latitude' => fake()->latitude(),
            'start_longitude' => fake()->longitude(),
            'end_latitude' => fake()->latitude(),
            'end_longitude' => fake()->longitude(),
            'distance' => fake()->randomFloat(2, 0.1, 10),
        ];
    }

    /**
     * Mark the street as pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Create a primary street.
     */
    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'primary',
        ]);
    }
}
