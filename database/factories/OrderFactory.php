<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'total' => fake()->randomFloat(2, 10, 500),
            'status' => 'pending',
            'shipping_address' => fake()->address(),
            'phone' => fake()->numerify('9########'),
            'stripe_payment_intent_id' => 'pi_'.Str::random(24),
            'payment_status' => 'paid',
            'guest_name' => fake()->firstName(),
            'guest_lastname' => fake()->lastName(),
            'guest_email' => fake()->safeEmail(),
            'document_type' => 'DNI',
            'document_number' => fake()->numerify('########'),
        ];
    }

    /**
     * Indicate that the order was placed by a guest.
     */
    public function guest(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
        ]);
    }

    /**
     * Indicate that the order has not been paid.
     */
    public function unpaid(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'pending',
        ]);
    }
}
