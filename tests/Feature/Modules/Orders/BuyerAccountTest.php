<?php

use App\Modules\Identity\Livewire\AddressBook;
use App\Modules\Identity\Models\User;
use App\Modules\Orders\Enums\OrderStatus;
use App\Modules\Orders\Enums\PaymentStatus;
use App\Modules\Orders\Models\Order;
use App\Modules\Orders\Models\OrderItem;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

beforeEach(function () {
    $this->withoutVite();
    $this->buyer = User::factory()->create(['name' => 'Lucía']);
});

it('lists the paid orders with their status and the account sections', function () {
    $order = Order::factory()->for($this->buyer)->create(['status' => OrderStatus::Shipped, 'payment_status' => PaymentStatus::Paid]);
    OrderItem::factory()->for($order)->create();

    $this->actingAs($this->buyer)
        ->get(route('orders.index'))
        ->assertSuccessful()
        ->assertSee("Pedido #{$order->id}")
        ->assertSee('Enviado')
        ->assertSee(['Mis pedidos', 'Mi perfil', 'Direcciones', 'Seguridad'])
        ->assertSeeLivewire(AddressBook::class)
        ->assertSee("tab: 'orders'", false);
});

it('only offers to report products of delivered orders', function (OrderStatus $status, bool $canReport) {
    $order = Order::factory()->for($this->buyer)->create(['status' => $status, 'payment_status' => PaymentStatus::Paid]);
    OrderItem::factory()->for($order)->create();

    $response = $this->actingAs($this->buyer)->get(route('orders.index'));

    $canReport ? $response->assertSee('Reportar un problema') : $response->assertDontSee('Reportar un problema');
})->with([
    'delivered' => [OrderStatus::Delivered, true],
    'shipped' => [OrderStatus::Shipped, false],
]);

it('opens the tab of the form that failed', function (string $bag, string $tab) {
    $errors = (new ViewErrorBag)->put($bag, new MessageBag(['current_password' => ['Incorrecta.']]));

    $this->actingAs($this->buyer)
        ->withSession(['errors' => $errors])
        ->get(route('orders.index'))
        ->assertSee("tab: '{$tab}'", false)
        ->assertSee('Incorrecta.');
})->with([
    'profile' => ['updateProfile', 'profile'],
    'password' => ['updatePassword', 'security'],
]);

it('shows the delivery timeline and the totals of an order', function () {
    $order = Order::factory()->for($this->buyer)->create(['status' => OrderStatus::Processing, 'payment_status' => PaymentStatus::Paid, 'total' => '23.60']);
    OrderItem::factory()->for($order)->create(['price' => '11.80', 'quantity' => 2]);

    $this->actingAs($this->buyer)
        ->get(route('orders.show', $order))
        ->assertSuccessful()
        ->assertSeeInOrder(['Pendiente', 'En proceso', 'Enviado', 'Entregado'])
        ->assertSee('S/. 20.00')
        ->assertSee('S/. 3.60')
        ->assertSee('S/. 23.60');
});

it('hides the timeline of cancelled orders', function () {
    $order = Order::factory()->for($this->buyer)->create(['status' => OrderStatus::Cancelled, 'payment_status' => PaymentStatus::Paid]);

    $this->actingAs($this->buyer)
        ->get(route('orders.show', $order))
        ->assertSee('Cancelado')
        ->assertDontSee('aria-label="Seguimiento"', false);
});
