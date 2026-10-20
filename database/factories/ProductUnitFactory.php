<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductUnit>
 */
class ProductUnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'unit' => fake()->randomElement(['kg', 'arroba', 'saco']),
            'price' => fake()->randomFloat(2, 2, 200),
            'stock' => fake()->numberBetween(5, 50),
        ];
    }
}
