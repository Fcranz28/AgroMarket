<?php

namespace Database\Factories;

use App\Models\User;
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
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => User::ROLE_USER,
            'onboarding_completed' => true,
        ];
    }

    /**
     * Indicate that the user still has to complete the onboarding.
     */
    public function pendingOnboarding(): static
    {
        return $this->state(fn (array $attributes) => [
            'onboarding_completed' => false,
        ]);
    }

    /**
     * Indicate that the user is an administrator.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_ADMIN,
        ]);
    }

    /**
     * Indicate that the user is a farmer awaiting verification.
     */
    public function farmer(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_FARMER,
            'verification_status' => User::STATUS_PENDING,
        ]);
    }

    /**
     * Indicate that the user's farmer verification was approved.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'verification_status' => User::STATUS_APPROVED,
        ]);
    }

    /**
     * Indicate that the user is banned.
     */
    public function banned(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_banned' => true,
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
