<?php

use App\Modules\Catalog\Models\Product;
use App\Modules\Identity\Enums\VerificationStatus;
use App\Modules\Identity\Models\User;
use App\Modules\Identity\Notifications\FarmerVerificationReviewedNotification;
use App\Modules\Orders\Enums\OrderStatus;
use App\Modules\Orders\Events\OrderPlaced;
use App\Modules\Orders\Models\Order;
use App\Modules\Orders\Models\OrderItem;
use App\Modules\Orders\Notifications\NewOrderForFarmerNotification;
use App\Modules\Orders\Notifications\OrderPlacedNotification;
use App\Modules\Orders\Notifications\OrderStatusChangedNotification;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Notification::fake();
    Storage::fake('local');
});

it('confirms the purchase to a registered buyer and tells each farmer what they sold', function () {
    $rosa = User::factory()->farmer()->verified()->create();
    $luis = User::factory()->farmer()->verified()->create();
    $order = Order::factory()->create();
    OrderItem::factory()->count(2)->for($order)->for(Product::factory()->forFarmer($rosa))->create();
    OrderItem::factory()->for($order)->for(Product::factory()->forFarmer($luis))->create();

    OrderPlaced::dispatch($order);

    Notification::assertSentTo($order->user, OrderPlacedNotification::class);
    Notification::assertSentTo($rosa, NewOrderForFarmerNotification::class, fn ($notification) => $notification->items->count() === 2);
    Notification::assertSentTo($luis, NewOrderForFarmerNotification::class, fn ($notification) => $notification->items->count() === 1);
});

it('confirms the purchase to the e-mail of a guest buyer', function () {
    $order = Order::factory()->guest()->create(['guest_email' => 'ana@example.com']);
    OrderItem::factory()->for($order)->create();

    OrderPlaced::dispatch($order);

    Notification::assertSentOnDemand(OrderPlacedNotification::class, fn ($notification, $channels, AnonymousNotifiable $notifiable) => $notifiable->routes['mail'] === 'ana@example.com');
});

it('renders the purchase confirmation mail in spanish', function () {
    $order = Order::factory()->create(['total' => 25]);
    OrderItem::factory()->for($order)->create(['quantity' => 2, 'price' => 12.5, 'unit' => 'kg']);

    $mail = (new OrderPlacedNotification($order))->toMail($order->user)->render();

    expect((string) $mail)->toContain('Gracias por tu compra')->toContain('S/. 25.00');
});

it('tells the buyer when the farmer changes the order status', function () {
    $farmer = User::factory()->farmer()->verified()->create();
    $order = Order::factory()->create();
    OrderItem::factory()->for($order)->for(Product::factory()->forFarmer($farmer))->create();

    $this->actingAs($farmer)->patchJson(route('farmer.orders.status', $order), ['status' => 'shipped'])->assertSuccessful();

    Notification::assertSentTo($order->user, OrderStatusChangedNotification::class, fn ($notification) => $notification->status === OrderStatus::Shipped);
});

it('tells the farmer the result of the identity review', function () {
    $farmer = User::factory()->farmer()->create();

    $this->actingAs(User::factory()->admin()->create())->post(route('admin.verify', [$farmer, 'approved']));

    Notification::assertSentTo($farmer, FarmerVerificationReviewedNotification::class, fn ($notification) => $notification->status === VerificationStatus::Approved);
});

it('queues every mail instead of sending it during the request', function (string $notification) {
    expect(new ReflectionClass($notification))->implementsInterface(Illuminate\Contracts\Queue\ShouldQueue::class)->toBeTrue();
})->with([
    OrderPlacedNotification::class,
    NewOrderForFarmerNotification::class,
    OrderStatusChangedNotification::class,
    FarmerVerificationReviewedNotification::class,
]);
