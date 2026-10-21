<?php

namespace App\Modules\Orders\Database\Factories;

use App\Modules\Identity\Models\User;
use App\Modules\Orders\Enums\OrderStatus;
use App\Modules\Orders\Enums\PaymentStatus;
use App\Modules\Orders\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Orders\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Order>
     */
    protected $model = Order::class;

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
            'status' => OrderStatus::Pending,
            'shipping_address' => fake()->address(),
            'phone' => fake()->numerify('9########'),
            'stripe_payment_intent_id' => 'pi_'.Str::random(24),
            'payment_status' => PaymentStatus::Paid,
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
            'payment_status' => PaymentStatus::Pending,
        ]);
    }
}
