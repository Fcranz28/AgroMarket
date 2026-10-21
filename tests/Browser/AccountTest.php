<?php

use App\Modules\Catalog\Models\Product;
use App\Modules\Identity\Models\User;
use App\Modules\Orders\Enums\OrderStatus;
use App\Modules\Orders\Enums\PaymentStatus;
use App\Modules\Orders\Models\Order;
use App\Modules\Orders\Models\OrderItem;

it('signs in and shows the user menu', function () {
    $user = User::factory()->create(['name' => 'Lucía Quispe', 'email' => 'lucia@example.com']);

    visit('/login')
        ->fill('email', $user->email)
        ->fill('password', 'password')
        ->click('@login-submit')
        ->assertPathIs('/')
        ->assertSee('Hola, Lucía Quispe')
        ->assertNoJavaScriptErrors();
});

it('rejects wrong credentials', function () {
    $user = User::factory()->create();

    visit('/login')
        ->fill('email', $user->email)
        ->fill('password', 'otra-clave')
        ->click('@login-submit')
        ->assertPathIs('/login')
        ->assertSee('El email o la contraseña son incorrectos.');
});

it('remembers the dark theme across pages', function () {
    visit('/')
        ->click('@theme-toggle')
        ->assertScript('document.documentElement.dataset.theme', 'dark')
        ->navigate('/nosotros')
        ->assertScript('document.documentElement.dataset.theme', 'dark')
        ->assertScript("localStorage.getItem('theme')", 'dark');
});

it('lets buyers manage their addresses and follow an order', function () {
    $buyer = User::factory()->create();
    $order = Order::factory()->for($buyer)->create(['status' => OrderStatus::Shipped, 'payment_status' => PaymentStatus::Paid]);
    OrderItem::factory()->for($order)->for(Product::factory())->create();

    $this->actingAs($buyer);

    visit('/pedidos')
        ->assertSee("Pedido #{$order->id}")
        ->click('Rastrear')
        ->waitForText('Seguimiento del pedido')
        ->assertSee('Enviado')
        ->click('@modal-close-tracking')
        ->click('Direcciones')
        ->fill('#newAddress', 'Av. El Sol 345, Cusco')
        ->press('Guardar dirección')
        ->waitForText('Principal')
        ->assertSee('Av. El Sol 345, Cusco')
        ->assertNoJavaScriptErrors();

    expect($buyer->addresses()->pluck('address')->all())->toBe(['Av. El Sol 345, Cusco']);
});

it('opens the user menu and the cart from the mobile bar', function () {
    Product::factory()->create(['name' => 'Mango Kent', 'stock' => 5]);

    visit('/')->on()->mobile()
        ->click('@mobile-user-menu')
        ->assertSee('Iniciar Sesión')
        ->assertSee('Registrarse')
        ->click('@open-cart-mobile')
        ->waitForText('Tu carrito está vacío.')
        ->assertNoJavaScriptErrors();
});
