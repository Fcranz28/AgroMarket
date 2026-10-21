<?php

use App\Modules\Catalog\Models\Category;
use App\Modules\Catalog\Models\Product;

beforeEach(function () {
    $this->withoutVite();
});

it('features the latest products in stock', function () {
    Product::factory()->create(['name' => 'Mango Kent', 'stock' => 10]);
    Product::factory()->create(['name' => 'Papa agotada', 'stock' => 0]);

    $this->get(route('home'))
        ->assertSuccessful()
        ->assertViewHas('featuredProducts', fn ($products) => $products->pluck('name')->all() === ['Mango Kent'])
        ->assertSee('Mango Kent');
});

it('suggests other products without repeating the featured ones', function () {
    Product::factory()->count(10)->create();

    $response = $this->get(route('home'));
    $featuredIds = $response->viewData('featuredProducts')->modelKeys();
    $suggestedIds = $response->viewData('suggestedProducts')->modelKeys();

    expect($suggestedIds)->toHaveCount(2)
        ->and(array_intersect($featuredIds, $suggestedIds))->toBeEmpty();
});

it('shows the home page without products', function () {
    $this->get(route('home'))
        ->assertSuccessful()
        ->assertDontSee('Recién cosechado')
        ->assertDontSee('Compra por categoría')
        ->assertSee('Preguntas frecuentes');
});

it('lists only the categories that have products, linked to the filtered catalog', function () {
    $fruits = Category::factory()->create(['name' => 'Frutas', 'slug' => 'frutas']);
    Category::factory()->create(['name' => 'Semillas', 'slug' => 'semillas']);
    Product::factory()->for($fruits)->create();

    $this->get(route('home'))
        ->assertSee('Compra por categoría')
        ->assertSee(route('products.index', ['category' => 'frutas']), false)
        ->assertDontSee(route('products.index', ['category' => 'semillas']), false);
});

it('shows the sale units and a stock warning on the product cards', function () {
    $product = Product::factory()->create(['name' => 'Quinua blanca']);
    $product->syncUnits([
        ['unit' => 'kg', 'price' => '12.00', 'stock' => 4],
        ['unit' => 'arroba', 'price' => '140.00', 'stock' => 2],
    ]);

    $this->get(route('home'))
        ->assertSee('Recién cosechado')
        ->assertSeeInOrder(['Quinua blanca', 'kg', 'arroba'])
        ->assertSee('Pocas unidades')
        ->assertSee('data-test="add-to-cart-'.$product->id.'"', false);
});

it('draws an illustration that matches products without a photo', function () {
    Product::factory()->create(['name' => 'Palta Hass', 'image_path' => null]);

    $this->get(route('home'))->assertSee('M100 22c30 0 44 34 52 66', false);
});
