<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'phone' => fake()->unique()->numerify('7########'),
            'password_hash' => static::$password ??= Hash::make('password'),
            'vehicle_type' => fake()->randomElement(['car', 'truck', 'motorcycle']),
            'engine_number' => strtoupper(fake()->bothify('ENG###??##')),
            'user_role' => 'customer',
            'qr_code' => strtoupper(fake()->unique()->bothify('QR-####-????')),
            'is_active' => true,
            'phone_verified' => true,
            'two_factor_enabled' => false,
            'account_locked' => false,
            'last_location_lat' => fake()->latitude(),
            'last_location_lon' => fake()->longitude(),
            'last_location_update' => now(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function role(string $role): static
    {
        return $this->state(fn (array $attributes) => [
            'user_role' => $role,
        ]);
    }
}
