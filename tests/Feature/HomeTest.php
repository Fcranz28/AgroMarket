<?php

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
    $this->get(route('home'))->assertSuccessful()->assertDontSee('Productos destacados');
});
