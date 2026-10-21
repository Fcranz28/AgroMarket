<?php

use App\Modules\Catalog\Models\Category;
use App\Modules\Catalog\Models\Product;

beforeEach(function () {
    $this->fruits = Category::factory()->create(['name' => 'Frutas', 'slug' => 'frutas']);
    $this->grains = Category::factory()->create(['name' => 'Granos', 'slug' => 'granos']);

    $this->mango = Product::factory()->for($this->fruits)->create(['name' => 'Mango Kent', 'price' => '6.50', 'stock' => 20]);
    $this->quinoa = Product::factory()->for($this->grains)->create(['name' => 'Quinua blanca', 'price' => '12.00', 'stock' => 20]);
    $this->quinoa->syncUnits([
        ['unit' => 'kg', 'price' => '12.00', 'stock' => 20],
        ['unit' => 'arroba', 'price' => '140.00', 'stock' => 5],
    ]);
});

it('filters the catalog by category and keeps the filter in the URL', function () {
    visit('/productos')
        ->assertSee('Mango Kent')
        ->assertSee('Quinua blanca')
        ->click('@category-granos')
        ->waitForText('1 producto')
        ->assertDontSee('Mango Kent')
        ->assertSee('Quinua blanca')
        ->assertQueryStringHas('category', 'granos')
        ->assertNoJavaScriptErrors();
});

it('suggests products while typing in the header search', function () {
    visit('/contacto')
        ->type('#searchInput', 'quin')
        ->waitForText('Ver todos los resultados')
        ->assertSeeIn('@search-suggestions', 'Quinua blanca')
        ->click('Quinua blanca')
        ->assertPathBeginsWith('/producto/');
});

it('adds products to the cart, changes quantities and reaches the checkout', function () {
    visit('/')
        ->click('@add-to-cart-'.$this->mango->id)
        ->waitForText('se agregó al carrito')
        ->assertSeeIn('@cart-count-desktop', '1')
        ->click('@open-cart')
        ->waitForText('Tu carrito')
        ->assertSeeIn('@cart-panel', 'Mango Kent')
        ->click('@cart-increment')
        ->waitForText('S/. 13.00')
        ->assertSeeIn('@cart-count-desktop', '2')
        ->click('Proceder al pago')
        ->assertPathIs('/checkout')
        ->assertSee('Finalizar compra')
        ->assertSee('2 × S/. 6.50')
        ->assertNoJavaScriptErrors();
});

it('adds the chosen unit and quantity from the product page', function () {
    visit('/producto/'.$this->quinoa->slug)
        ->assertSee('S/. 12.00')
        ->click('arroba')
        ->assertSee('S/. 140.00')
        ->click('@quantity-increase')
        ->click('@product-add-to-cart')
        ->waitForText('se agregó al carrito')
        ->assertSeeIn('@cart-count-desktop', '2')
        ->click('@open-cart')
        ->waitForText('Tu carrito')
        ->assertSeeIn('@cart-panel', 'S/. 140.00 / arroba');
});

it('sends shoppers with an empty cart from the checkout to the catalog', function () {
    visit('/checkout')
        ->assertPathIs('/productos')
        ->assertSee('Todos los productos');
});

it('validates the buyer data on the checkout', function () {
    visit('/')
        ->click('@add-to-cart-'.$this->mango->id)
        ->waitForText('se agregó al carrito');

    // Sin claves de Stripe el formulario de tarjeta no carga: se valida llamando a prepare()
    $page = visit('/checkout')->assertSee('Datos del comprador');
    $page->script("Livewire.all().find((component) => component.name === 'payments.checkout').\$wire.prepare()");

    $page->waitForText('Ingresa tus nombres.')
        ->assertSee('Ingresa una dirección de envío.');
});
