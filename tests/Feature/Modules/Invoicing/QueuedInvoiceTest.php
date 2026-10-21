<?php

use App\Modules\Catalog\Models\Product;
use App\Modules\Invoicing\Enums\InvoiceStatus;
use App\Modules\Invoicing\Listeners\GenerateInvoiceForOrder;
use App\Modules\Invoicing\Models\Invoice;
use App\Modules\Orders\Actions\PlaceOrderAction;
use App\Modules\Orders\Events\OrderPlaced;
use App\Modules\Orders\Models\Order;
use App\Modules\Orders\Models\OrderItem;
use App\Modules\Orders\Services\CartPricingService;
use Illuminate\Events\CallQueuedListener;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

beforeEach(function () {
    Storage::fake('local');
    $this->withoutVite();
});

/**
 * @return array<string, string>
 */
function guestCustomer(): array
{
    return [
        'shipping_address' => 'Jr. Lima 100',
        'phone' => '987654321',
        'guest_name' => 'Ana',
        'guest_lastname' => 'Quispe',
        'guest_email' => 'ana@example.com',
        'document_type' => 'DNI',
        'document_number' => '12345678',
    ];
}

function placeGuestOrder(): Order
{
    $product = Product::factory()->create(['price' => '10.00', 'stock' => 5]);
    $priced = app(CartPricingService::class)->price([['id' => $product->id, 'cantidad' => 1]]);

    return app(PlaceOrderAction::class)->execute($priced['lines'], $priced['total_cents'], guestCustomer(), 'pi_queued', null);
}

it('announces a placed order once it is committed', function () {
    Event::fake([OrderPlaced::class]);

    $order = placeGuestOrder();

    Event::assertDispatched(OrderPlaced::class, fn (OrderPlaced $event) => $event->order->is($order));
});

it('generates the invoice on the invoices queue instead of during the request', function () {
    Queue::fake();

    $order = placeGuestOrder();

    Queue::assertPushedOn('invoices', CallQueuedListener::class, fn (CallQueuedListener $job) => $job->class === GenerateInvoiceForOrder::class);
    expect(Invoice::where('order_id', $order->id)->exists())->toBeFalse();
});

it('issues the invoice when the queued listener runs', function () {
    $order = OrderItem::factory()->create()->order;

    app(GenerateInvoiceForOrder::class)->handle(new OrderPlaced($order));

    expect($order->fresh()->invoice)
        ->not->toBeNull()
        ->status->toBe(InvoiceStatus::Success);
});

it('never issues a second invoice when the job is retried', function () {
    $order = OrderItem::factory()->create()->order;
    $listener = app(GenerateInvoiceForOrder::class);

    $listener->handle(new OrderPlaced($order));
    $listener->handle(new OrderPlaced($order));

    expect(Invoice::where('order_id', $order->id)->count())->toBe(1);
});

it('retries the invoice with backoff', function () {
    $listener = app(GenerateInvoiceForOrder::class);

    expect($listener->tries)->toBe(3)
        ->and($listener->backoff)->toBe([10, 60, 300]);
});

it('shows guests a waiting page until the worker issues the invoice', function () {
    $order = Order::factory()->guest()->create();
    $url = URL::temporarySignedRoute('orders.guest-invoice', now()->addHour(), $order);

    $this->get($url)
        ->assertStatus(202)
        ->assertSee('Estamos generando tu comprobante')
        ->assertSee('http-equiv="refresh"', false);
});

it('shows guests a signed download link once the invoice exists', function () {
    $order = Order::factory()->guest()->create();
    Invoice::factory()->for($order)->create(['status' => 'success']);
    $url = URL::temporarySignedRoute('orders.guest-invoice', now()->addHour(), $order);

    $this->get($url)
        ->assertSuccessful()
        ->assertSee('Descargar comprobante')
        ->assertDontSee('http-equiv="refresh"', false);
});

it('requires a valid signature for the guest receipt page', function () {
    $order = Order::factory()->guest()->create();

    $this->get(route('orders.guest-invoice', $order))->assertForbidden();
});

it('does not show the guest receipt page for orders of registered users', function () {
    $order = Order::factory()->create();

    $this->get(URL::temporarySignedRoute('orders.guest-invoice', now()->addHour(), $order))->assertForbidden();
});
