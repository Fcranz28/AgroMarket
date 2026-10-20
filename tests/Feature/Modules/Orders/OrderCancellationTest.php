<?php

use App\Modules\Catalog\Models\Product;
use App\Modules\Identity\Models\User;
use App\Modules\Orders\Events\OrderCancelled;
use App\Modules\Orders\Models\Order;
use App\Modules\Orders\Models\OrderItem;
use App\Modules\Payments\Services\PaymentGateway;
use Illuminate\Support\Facades\Event;

use function Pest\Laravel\mock;

beforeEach(function () {
    $this->farmer = User::factory()->farmer()->verified()->create();
    $this->order = Order::factory()->create();
    OrderItem::factory()->for($this->order)->for(Product::factory()->forFarmer($this->farmer))->create();
});

it('announces the cancellation with an event', function () {
    Event::fake([OrderCancelled::class]);

    $this->actingAs($this->farmer)
        ->patchJson(route('farmer.orders.status', $this->order), ['status' => 'cancelled'])
        ->assertSuccessful();

    Event::assertDispatched(OrderCancelled::class, fn (OrderCancelled $event) => $event->order->is($this->order));
});

it('keeps the order cancelled when the refund fails', function () {
    mock(PaymentGateway::class)->shouldReceive('refund')->andThrow(new RuntimeException('Stripe caído'));

    $this->actingAs($this->farmer)
        ->patchJson(route('farmer.orders.status', $this->order), ['status' => 'cancelled'])
        ->assertSuccessful()
        ->assertJsonFragment(['message' => 'Pedido cancelado. El reembolso no se pudo procesar automáticamente; el equipo lo revisará.']);

    expect($this->order->fresh())
        ->status->toBe('cancelled')
        ->payment_status->toBe('paid');
});
