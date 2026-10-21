<?php

use App\Modules\Catalog\Models\Category;
use App\Modules\Catalog\Models\Product;
use App\Modules\Catalog\Models\ProductUnit;
use App\Modules\Identity\Models\User;
use App\Modules\Orders\Models\Order;
use App\Modules\Orders\Services\CartPricingService;
use App\Services\InvoiceService;
use App\Services\PaymentGateway;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Stripe\PaymentIntent;

use function Pest\Laravel\mock;

beforeEach(function () {
    Storage::fake('public');
    Storage::fake('local');

    $this->product = Product::factory()->create(['price' => '4.50', 'unit' => 'kg', 'stock' => 25]);
    $this->kilo = ProductUnit::factory()->for($this->product)->create(['unit' => 'kg', 'price' => '4.50', 'stock' => 20]);
    $this->sack = ProductUnit::factory()->for($this->product)->create(['unit' => 'saco', 'price' => '180.00', 'stock' => 5]);
});

it('charges the price of the selected unit', function () {
    $priced = app(CartPricingService::class)->price([
        ['id' => $this->product->id, 'unit_id' => $this->sack->id, 'cantidad' => 2],
        ['id' => $this->product->id, 'unit_id' => $this->kilo->id, 'cantidad' => 3],
    ]);

    expect($priced['total_cents'])->toBe(36000 + 1350)
        ->and($priced['lines'])->toHaveCount(2);
});

it('uses the cheapest unit when the catalog adds a product without unit', function () {
    $priced = app(CartPricingService::class)->price([
        ['id' => $this->product->id, 'cantidad' => 2],
    ]);

    expect($priced['total_cents'])->toBe(900)
        ->and($priced['lines'][0]['product_unit_id'])->toBe($this->kilo->id);
});

it('merges repeated lines of the same unit', function () {
    $priced = app(CartPricingService::class)->price([
        ['id' => $this->product->id, 'unit_id' => $this->sack->id, 'cantidad' => 1],
        ['id' => $this->product->id, 'unit_id' => $this->sack->id, 'cantidad' => 1],
    ]);

    expect($priced['lines'])->toHaveCount(1)
        ->and($priced['lines'][0]['quantity'])->toBe(2);
});

it('rejects units of another product', function () {
    $otherUnit = ProductUnit::factory()->create();

    app(CartPricingService::class)->price([
        ['id' => $this->product->id, 'unit_id' => $otherUnit->id, 'cantidad' => 1],
    ]);
})->throws(ValidationException::class);

it('takes the stock from the purchased unit', function () {
    $cart = [['id' => $this->product->id, 'unit_id' => $this->sack->id, 'cantidad' => 2]];
    $priced = app(CartPricingService::class)->price($cart);

    $gateway = mock(PaymentGateway::class);
    $gateway->shouldReceive('currency')->andReturn('pen');
    $gateway->shouldReceive('retrieveIntent')->andReturn(PaymentIntent::constructFrom([
        'id' => 'pi_units',
        'status' => 'succeeded',
        'amount' => $priced['total_cents'],
        'currency' => 'pen',
        'metadata' => ['cart_hash' => $priced['hash']],
    ]));

    $this->postJson(route('payment.process'), [
        'payment_intent_id' => 'pi_units',
        'cart' => $cart,
        'shipping_address' => 'Jr. Lima 100',
        'phone' => '987654321',
        'guest_name' => 'Luis',
        'guest_lastname' => 'Huamán',
        'guest_email' => 'luis@example.com',
        'document_type' => 'DNI',
        'document_number' => '87654321',
    ])->assertSuccessful();

    $item = Order::sole()->items()->sole();

    expect($this->sack->fresh()->stock)->toBe(3)
        ->and($this->kilo->fresh()->stock)->toBe(20)
        ->and($this->product->fresh()->stock)->toBe(23)
        ->and($item->unit)->toBe('saco')
        ->and($item->product_unit_id)->toBe($this->sack->id)
        ->and((float) $item->price)->toBe(180.0);
});

it('issues invoices with IGV included in the published price', function () {
    $order = Order::factory()->create(['total' => 118.00]);
    $order->items()->create(['product_id' => $this->product->id, 'quantity' => 1, 'price' => 118.00, 'unit' => 'kg']);

    $invoice = app(InvoiceService::class)->generateInvoice($order);
    $payload = $invoice->api_request;

    expect($payload['mtoImpVenta'])->toEqual(118.0)
        ->and($payload['mtoOperGravadas'])->toEqual(100.0)
        ->and($payload['mtoIGV'])->toEqual(18.0)
        ->and((float) $invoice->total)->toBe(118.0)
        ->and((float) $invoice->subtotal)->toBe(100.0);
});

it('keeps unit ids when a farmer edits a product', function () {
    $farmer = $this->product->user;

    $this->actingAs($farmer)->put(route('dashboard.productos.update', $this->product), [
        'name' => 'Papa nativa',
        'description' => 'Cosecha de la semana',
        'category_id' => $this->product->category_id,
        'unit' => ['kg', 'saco'],
        'stock' => [30, 4],
        'price' => [5, 190],
    ])->assertRedirect(route('dashboard.productos.index'));

    $product = $this->product->fresh();

    expect($this->kilo->fresh()->stock)->toBe(30)
        ->and((float) $this->sack->fresh()->price)->toBe(190.0)
        ->and($product->stock)->toBe(34)
        ->and((float) $product->price)->toBe(5.0)
        ->and($product->name)->toBe('Papa nativa');
});

it('creates a product with its units and images', function () {
    $farmer = User::factory()->farmer()->verified()->create();

    $this->actingAs($farmer)->post(route('dashboard.productos.store'), [
        'name' => 'Quinua blanca',
        'description' => 'Quinua orgánica de Puno',
        'category_id' => Category::factory()->create()->id,
        'unit' => ['kg', 'arroba'],
        'stock' => [50, 10],
        'price' => [12, 140],
        'image' => [UploadedFile::fake()->image('quinua.jpg'), UploadedFile::fake()->image('quinua2.jpg')],
    ])->assertRedirect(route('dashboard.productos.index'));

    $product = $farmer->products()->sole();

    expect($product->units)->toHaveCount(2)
        ->and($product->images)->toHaveCount(2)
        ->and($product->image_path)->not->toBeNull()
        ->and($product->stock)->toBe(60)
        ->and((float) $product->price)->toBe(12.0);
});

it('rejects negative stock and invalid prices', function () {
    $farmer = User::factory()->farmer()->verified()->create();

    $this->actingAs($farmer)->post(route('dashboard.productos.store'), [
        'name' => 'Maíz',
        'description' => 'Maíz morado',
        'category_id' => Category::factory()->create()->id,
        'unit' => ['kg'],
        'stock' => [-5],
        'price' => ['gratis'],
        'image' => [UploadedFile::fake()->image('maiz.jpg')],
    ])->assertSessionHasErrors(['stock.0', 'price.0']);

    expect($farmer->products()->count())->toBe(0);
});

it('rejects units without stock or price', function () {
    $farmer = User::factory()->farmer()->verified()->create();

    $this->actingAs($farmer)->post(route('dashboard.productos.store'), [
        'name' => 'Maíz',
        'description' => 'Maíz morado',
        'category_id' => Category::factory()->create()->id,
        'unit' => ['kg', 'saco'],
        'stock' => [5],
        'price' => [3],
        'image' => [UploadedFile::fake()->image('maiz.jpg')],
    ])->assertSessionHasErrors('unit');
});

it('does not let unverified farmers create products', function () {
    $farmer = User::factory()->farmer()->create();

    $this->actingAs($farmer)->post(route('dashboard.productos.store'), [])->assertForbidden();
});
