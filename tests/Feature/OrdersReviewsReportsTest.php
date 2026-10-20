<?php

use App\Modules\Catalog\Models\Product;
use App\Modules\Catalog\Models\ProductUnit;
use App\Modules\Catalog\Models\Review;
use App\Modules\Identity\Models\User;
use App\Modules\Orders\Models\Order;
use App\Modules\Orders\Models\OrderItem;
use App\Services\PaymentGateway;

use function Pest\Laravel\mock;

beforeEach(function () {
    $this->withoutVite();

    $this->farmer = User::factory()->farmer()->verified()->create();
    $this->product = Product::factory()->forFarmer($this->farmer)->create(['stock' => 8]);
    $this->unit = ProductUnit::factory()->for($this->product)->create(['unit' => 'kg', 'stock' => 8]);
    $this->buyer = User::factory()->create();
    $this->order = Order::factory()->for($this->buyer)->create();
    OrderItem::factory()->for($this->order)->for($this->product)->create([
        'product_unit_id' => $this->unit->id,
        'quantity' => 2,
        'price' => 10,
    ]);
});

it('lets the farmer advance the status of an order with only their products', function () {
    $this->actingAs($this->farmer)
        ->patchJson(route('farmer.orders.status', $this->order), ['status' => 'shipped'])
        ->assertSuccessful();

    expect($this->order->fresh()->status)->toBe('shipped');
});

it('does not let a farmer change orders that include products of other farmers', function () {
    OrderItem::factory()->for($this->order)->create();

    $this->actingAs($this->farmer)
        ->patchJson(route('farmer.orders.status', $this->order), ['status' => 'delivered'])
        ->assertForbidden();

    expect($this->order->fresh()->status)->toBe('pending');
});

it('does not change orders that are already closed', function () {
    $this->order->update(['status' => 'delivered']);

    $this->actingAs($this->farmer)
        ->patchJson(route('farmer.orders.status', $this->order), ['status' => 'pending'])
        ->assertUnprocessable();
});

it('returns stock and refunds when an order is cancelled', function () {
    mock(PaymentGateway::class)->shouldReceive('refund')->once()->with($this->order->stripe_payment_intent_id);

    $this->actingAs($this->farmer)
        ->patchJson(route('farmer.orders.status', $this->order), ['status' => 'cancelled'])
        ->assertSuccessful();

    expect($this->order->fresh())
        ->status->toBe('cancelled')
        ->payment_status->toBe('refunded')
        ->and($this->unit->fresh()->stock)->toBe(10)
        ->and($this->product->fresh()->stock)->toBe(10);
});

it('lets buyers review products they bought, once', function () {
    $this->actingAs($this->buyer)
        ->post(route('reviews.store', $this->product), ['rating' => 5, 'comment' => 'Excelente'])
        ->assertSessionHasNoErrors();

    $this->actingAs($this->buyer)
        ->post(route('reviews.store', $this->product), ['rating' => 1])
        ->assertSessionHasErrors('rating');

    expect(Review::count())->toBe(1);
});

it('does not let users review products they did not buy', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('reviews.store', $this->product), ['rating' => 1])
        ->assertSessionHasErrors('rating');

    expect(Review::count())->toBe(0);
});

it('does not let farmers review their own products', function () {
    $this->actingAs($this->farmer)
        ->post(route('reviews.store', $this->product), ['rating' => 5])
        ->assertSessionHasErrors('rating');
});

it('lets buyers report products of their own orders', function () {
    $this->actingAs($this->buyer)->postJson(route('reports.store'), [
        'order_id' => $this->order->id,
        'product_id' => $this->product->id,
        'reason' => 'Producto en mal estado',
        'description' => 'Llegó malogrado',
    ])->assertSuccessful()->assertJsonMissingPath('report.user');
});

it('does not let users report orders of other customers', function () {
    $this->actingAs(User::factory()->create())->postJson(route('reports.store'), [
        'order_id' => $this->order->id,
        'product_id' => $this->product->id,
        'reason' => 'Spam',
        'description' => 'Reporte falso',
    ])->assertJsonValidationErrors('order_id');
});

it('does not let users report a product that is not in the order', function () {
    $this->actingAs($this->buyer)->postJson(route('reports.store'), [
        'order_id' => $this->order->id,
        'product_id' => Product::factory()->create()->id,
        'reason' => 'Spam',
        'description' => 'Reporte falso',
    ])->assertJsonValidationErrors('order_id');
});

it('shows real sales on the farmer dashboard', function () {
    $this->actingAs($this->farmer)
        ->get(route('farmer.dashboard'))
        ->assertSuccessful()
        ->assertViewHas('sales', 1)
        ->assertViewHas('revenue', 20.0);
});

it('keeps orders when the buyer account is deleted', function () {
    $this->buyer->delete();

    expect($this->order->fresh())->not->toBeNull()
        ->and($this->order->fresh()->user_id)->toBeNull();
});

it('searches name and description inside the selected category only', function () {
    $fruit = Product::factory()->create(['name' => 'Mango ciruelo']);
    Product::factory()->create(['name' => 'Papa', 'description' => 'Ideal con mango']);

    $response = $this->getJson('/api/search?q=mango&category='.$fruit->category->slug)->assertSuccessful();

    expect($response->json('products'))->toHaveCount(1)
        ->and($response->json('products.0.name'))->toBe('Mango ciruelo');
});

it('treats LIKE wildcards in the search term literally', function () {
    Product::factory()->create(['name' => 'Palta fuerte']);

    $this->getJson('/api/search?q=%25')->assertJsonCount(0, 'products');
    $this->getJson('/api/suggestions?q=__')->assertJsonCount(0, 'suggestions');
});

it('hides soft deleted products from the catalog api', function () {
    $this->product->delete();

    $this->getJson('/api/productos')->assertJsonMissing(['id' => $this->product->id]);
});
