<?php

use App\Modules\Catalog\Models\Product;
use App\Modules\Catalog\Models\Review;
use App\Modules\Identity\Models\User;

beforeEach(function () {
    $this->withoutVite();

    $this->product = Product::factory()->create(['name' => 'Papa amarilla']);
    $this->product->syncUnits([
        ['unit' => 'saco (50 kg)', 'price' => '190.00', 'stock' => 8],
        ['unit' => 'kg', 'price' => '4.50', 'stock' => 120],
    ]);
});

it('offers every unit with the cheapest one selected first', function () {
    $this->get(route('products.show', $this->product->slug))
        ->assertSuccessful()
        ->assertSeeInOrder(['S/. 4.50', 'kg'])
        ->assertSeeInOrder(['>kg</button>', '>saco (50 kg)</button>'], false)
        ->assertSee("productId: {$this->product->id}", false);
});

it('does not expose the product model as JSON', function () {
    $this->get(route('products.show', $this->product->slug))
        ->assertDontSee('data-product=', false)
        ->assertDontSee('"user_id"', false);
});

it('asks guests to sign in before reviewing', function () {
    $this->get(route('products.show', $this->product->slug))
        ->assertSee('para dejar un comentario')
        ->assertDontSee(route('reviews.store', $this->product->id));
});

it('shows the review form and the reviews with their rating', function () {
    $reviewer = User::factory()->create(['name' => 'Rosa']);
    Review::create(['product_id' => $this->product->id, 'user_id' => $reviewer->id, 'rating' => 4, 'comment' => 'Muy buena papa']);

    $this->actingAs(User::factory()->create())
        ->get(route('products.show', $this->product->slug))
        ->assertSee(route('reviews.store', $this->product->id))
        ->assertSee('Muy buena papa')
        ->assertSee('4 de 5 estrellas')
        ->assertSee('4.0 · 1 reseña');
});

it('suggests products of the same category', function () {
    $related = Product::factory()->for($this->product->category)->create(['name' => 'Camote morado']);
    Product::factory()->create(['name' => 'Miel de abeja']);

    $this->get(route('products.show', $this->product->slug))
        ->assertSee($related->name)
        ->assertDontSee('Miel de abeja');
});
