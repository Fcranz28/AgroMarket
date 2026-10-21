<?php

namespace Database\Factories;

use App\Models\Category;
use App\Modules\Identity\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'category_id' => Category::factory(),
            'user_id' => User::factory()->farmer()->verified(),
            'name' => ucfirst($name),
            'slug' => Str::slug($name).'-'.Str::random(6),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 2, 50),
            'unit' => 'kg',
            'stock' => fake()->numberBetween(10, 100),
            'image_path' => null,
        ];
    }

    /**
     * Indicate that the product belongs to the given farmer.
     */
    public function forFarmer(User $farmer): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $farmer->id,
        ]);
    }

    /**
     * Indicate that the product has no stock left.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
        ]);
    }
}
