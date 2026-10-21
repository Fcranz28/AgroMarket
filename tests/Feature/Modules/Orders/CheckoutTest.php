<?php

use App\Modules\Catalog\Models\Product;
use App\Modules\Orders\Services\SessionCart;

beforeEach(function () {
    $this->withoutVite();
});

it('sends shoppers with an empty cart back to the catalog', function () {
    $this->get(route('checkout.index'))
        ->assertRedirect(route('products.index'))
        ->assertSessionHas('status', 'Tu carrito está vacío.');
});

it('summarises the session cart and passes only ids and quantities to the payment script', function () {
    $product = Product::factory()->create(['name' => 'Quinua blanca', 'price' => '11.80', 'stock' => 10]);
    app(SessionCart::class)->add($product->id, quantity: 2);

    $this->get(route('checkout.index'))
        ->assertSuccessful()
        ->assertSee('Quinua blanca')
        ->assertSee('S/. 23.60')
        ->assertSee('S/. 20.00')
        ->assertSee('S/. 3.60')
        ->assertSee('window.checkoutCart = [{"id":'.$product->id.',"unit_id":null,"cantidad":2}]', false);
});
