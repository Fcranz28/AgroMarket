<?php

use App\Modules\Catalog\Models\Product;
use App\Modules\Orders\Livewire\CartCount;
use App\Modules\Orders\Livewire\CartSidebar;
use App\Modules\Orders\Services\SessionCart;
use Livewire\Livewire;

beforeEach(function () {
    $this->withoutVite();
});

it('adds products from the add-to-cart event', function () {
    $product = Product::factory()->create(['name' => 'Palta Hass', 'price' => '9.90', 'stock' => 10]);

    Livewire::test(CartSidebar::class)
        ->dispatch('add-to-cart', productId: $product->id, quantity: 2)
        ->assertDispatched('cart-updated')
        ->assertDispatched('notify', type: 'success', message: 'Palta Hass se agregó al carrito.')
        ->assertSee('Palta Hass')
        ->assertSee('S/. 19.80');

    expect(app(SessionCart::class)->count())->toBe(2);
});

it('warns when a product cannot be added', function () {
    $product = Product::factory()->create(['stock' => 0]);

    Livewire::test(CartSidebar::class)
        ->dispatch('add-to-cart', productId: $product->id)
        ->assertNotDispatched('cart-updated')
        ->assertDispatched('notify', type: 'warning');
});

it('changes quantities and removes lines', function () {
    $product = Product::factory()->create(['stock' => 10]);
    app(SessionCart::class)->add($product->id);
    $key = app(SessionCart::class)->summary()['lines']->first()['key'];

    $component = Livewire::test(CartSidebar::class)
        ->call('increment', $key, 1)
        ->assertDispatched('cart-updated');
    expect(app(SessionCart::class)->count())->toBe(2);

    $component->call('decrement', $key, 2);
    expect(app(SessionCart::class)->count())->toBe(1);

    $component->call('remove', $key)->assertSee('Tu carrito está vacío.');
    expect(app(SessionCart::class)->isEmpty())->toBeTrue();
});

it('only links to checkout when the cart has products', function () {
    Livewire::test(CartSidebar::class)->assertDontSee(route('checkout.index'));

    app(SessionCart::class)->add(Product::factory()->create()->id);

    Livewire::test(CartSidebar::class)->assertSee(route('checkout.index'));
});

it('refreshes the badge when the cart changes', function () {
    $component = Livewire::test(CartCount::class)->assertSeeHtml('hidden');

    app(SessionCart::class)->add(Product::factory()->create(['stock' => 10])->id, quantity: 3);

    $component->dispatch('cart-updated')->assertSee('3')->assertDontSeeHtml('hidden');
});
