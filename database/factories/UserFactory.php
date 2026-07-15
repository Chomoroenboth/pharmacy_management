<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'phone_number' => fake()->unique()->phoneNumber(),
            'date_of_birth' => fake()->date(),
            'address' => fake()->address(),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * Note: You can keep or delete this method. Since your migration
     * does not have an 'email_verified_at' column, you can delete this
     * method if you don't use email verification.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            // Left blank or removed because email_verified_at is not in your migration
        ]);
    }
}
