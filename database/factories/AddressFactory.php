<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Street;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $street = Street::factory()->create();
        $user = User::factory()->create();

        return [
            'applicant_name' => fake()->name(),
            'applicant_phone' => fake()->phoneNumber(),
            'house_number' => fake()->bothify('??##?'),
            'street_id' => $street->id,
            'town' => fake()->city(),
            'owner_name' => fake()->name(),
            'owner_phone' => fake()->phoneNumber(),
            'payment_method' => 'online',
            'reference_code' => fake()->unique()->uuid(),
            'status' => 'approved',
            'reviewed_at' => now(),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'qr_code' => 'NJK-QR-' . fake()->unique()->numberBetween(1000, 9999),
            'code' => 'NJK-ADDR-' . fake()->unique()->numberBetween(1000, 9999),
            'user_id' => $user->id,
        ];
    }

    /**
     * Mark the address as pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'reviewed_at' => null,
        ]);
    }

    /**
     * Mark the address as rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'reviewed_at' => now(),
        ]);
    }

    /**
     * Set a specific QR code.
     */
    public function withQrCode(string $code): static
    {
        return $this->state(fn (array $attributes) => [
            'qr_code' => $code,
        ]);
    }
}
