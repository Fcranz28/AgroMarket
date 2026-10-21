<?php

use App\Modules\Catalog\Models\Product;

beforeEach(function () {
    $this->withoutVite();
});

it('renders the public pages', function (string $uri) {
    $this->get($uri)->assertSuccessful();
})->with([
    'home' => '/',
    'about' => '/nosotros',
    'contact' => '/contacto',
    'products' => '/productos',
    'search' => '/buscar',
    'login' => '/login',
    'register' => '/register',
]);

it('renders a product detail page', function () {
    $product = Product::factory()->create();

    $this->get(route('products.show', $product->slug))
        ->assertSuccessful()
        ->assertSee($product->name);
});

it('returns 404 for an unknown product slug', function () {
    $this->get('/producto/no-existe')->assertNotFound();
});

it('redirects guests away from authenticated pages', function (string $uri) {
    $this->get($uri)->assertRedirect(route('login'));
})->with([
    'orders' => '/pedidos',
    'farmer dashboard' => '/agricultor/dashboard',
    'admin dashboard' => '/admin/dashboard',
]);
