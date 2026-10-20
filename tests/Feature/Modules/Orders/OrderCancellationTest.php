<?php

use App\Modules\Catalog\Models\Product;
use App\Modules\Identity\Models\User;
use App\Modules\Orders\Events\OrderCancelled;
use App\Modules\Orders\Models\Order;
use App\Modules\Orders\Models\OrderItem;
use App\Modules\Payments\Jobs\RefundPayment;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;

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

it('queues the refund on the payments queue', function () {
    Queue::fake();

    $this->actingAs($this->farmer)
        ->patchJson(route('farmer.orders.status', $this->order), ['status' => 'cancelled'])
        ->assertSuccessful()
        ->assertJsonFragment(['message' => 'Pedido cancelado. El reembolso se está procesando.']);

    Queue::assertPushedOn('payments', RefundPayment::class, fn (RefundPayment $job) => $job->orderId === $this->order->id);
    expect($this->order->fresh()->status)->toBe('cancelled');
});

it('marks the order as refund_failed when every retry fails', function () {
    $job = new RefundPayment($this->order->stripe_payment_intent_id, $this->order->id);

    $job->failed(new RuntimeException('Stripe caído'));

    expect($this->order->fresh()->payment_status)->toBe('refund_failed')
        ->and($job->tries)->toBe(5)
        ->and($job->uniqueId())->toBe($this->order->stripe_payment_intent_id);
});
