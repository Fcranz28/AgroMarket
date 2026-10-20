<?php

namespace App\Modules\Catalog\Database\Factories;

use App\Modules\Catalog\Models\ProductUnit;
use App\Modules\Catalog\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Catalog\Models\ProductUnit>
 */
class ProductUnitFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<ProductUnit>
     */
    protected $model = ProductUnit::class;

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
