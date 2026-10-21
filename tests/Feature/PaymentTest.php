<?php

use App\Modules\Catalog\Models\Product;
use App\Modules\Orders\Models\Order;
use App\Modules\Orders\Services\CartPricingService;
use App\Services\PaymentGateway;
use Illuminate\Support\Facades\Storage;
use Stripe\PaymentIntent;

use function Pest\Laravel\mock;

beforeEach(function () {
    Storage::fake('public');

    $this->product = Product::factory()->create(['price' => '12.50', 'stock' => 10]);
    $this->cart = [['id' => $this->product->id, 'cantidad' => 2]];
});

/**
 * @param  array<int, array{id: int, cantidad: int}>  $cart
 * @param  array<string, mixed>  $overrides
 */
function fakePaymentIntent(array $cart, array $overrides = []): PaymentIntent
{
    $priced = app(CartPricingService::class)->price($cart);

    return PaymentIntent::constructFrom([
        'id' => 'pi_test_123',
        'object' => 'payment_intent',
        'status' => 'succeeded',
        'amount' => $priced['total_cents'],
        'currency' => 'pen',
        'client_secret' => 'pi_test_123_secret',
        'metadata' => ['cart_hash' => $priced['hash']],
        ...$overrides,
    ]);
}

/**
 * @param  array<int, array{id: int, cantidad: int}>  $cart
 * @return array<string, mixed>
 */
function checkoutPayload(array $cart): array
{
    return [
        'payment_intent_id' => 'pi_test_123',
        'cart' => $cart,
        'shipping_address' => 'Av. Siempre Viva 123, Lima',
        'phone' => '987654321',
        'guest_name' => 'Ana',
        'guest_lastname' => 'Quispe',
        'guest_email' => 'ana@example.com',
        'document_type' => 'DNI',
        'document_number' => '12345678',
    ];
}

it('creates the payment intent with the server-side total', function () {
    mock(PaymentGateway::class)
        ->shouldReceive('createIntent')
        ->once()
        ->withArgs(fn (int $amount, array $metadata) => $amount === 2500 && filled($metadata['cart_hash']))
        ->andReturn(fakePaymentIntent($this->cart));

    $this->postJson(route('payment.create-intent'), ['cart' => $this->cart, 'amount' => 0.01])
        ->assertSuccessful()
        ->assertJson(['clientSecret' => 'pi_test_123_secret']);
});

it('rejects carts below the stripe minimum', function () {
    $cheap = Product::factory()->create(['price' => '1.00']);

    mock(PaymentGateway::class)->shouldNotReceive('createIntent');

    $this->postJson(route('payment.create-intent'), ['cart' => [['id' => $cheap->id, 'cantidad' => 1]]])
        ->assertBadRequest();
});

it('creates the order when the payment matches the cart', function () {
    $gateway = mock(PaymentGateway::class);
    $gateway->shouldReceive('retrieveIntent')->andReturn(fakePaymentIntent($this->cart));
    $gateway->shouldReceive('currency')->andReturn('pen');

    $this->postJson(route('payment.process'), checkoutPayload($this->cart))
        ->assertSuccessful()
        ->assertJson(['success' => true]);

    $order = Order::sole();

    expect((float) $order->total)->toBe(25.0)
        ->and($order->items)->toHaveCount(1)
        ->and((int) $this->product->fresh()->stock)->toBe(8);
});

it('rejects a payment intent whose amount does not match the cart', function () {
    $bigCart = [['id' => $this->product->id, 'cantidad' => 8]];

    $gateway = mock(PaymentGateway::class);
    $gateway->shouldReceive('retrieveIntent')->andReturn(fakePaymentIntent($this->cart));
    $gateway->shouldReceive('currency')->andReturn('pen');

    $this->postJson(route('payment.process'), checkoutPayload($bigCart))->assertUnprocessable();

    expect(Order::count())->toBe(0)
        ->and((int) $this->product->fresh()->stock)->toBe(10);
});

it('rejects a payment intent in another currency', function () {
    $gateway = mock(PaymentGateway::class);
    $gateway->shouldReceive('retrieveIntent')->andReturn(fakePaymentIntent($this->cart, ['currency' => 'usd']));
    $gateway->shouldReceive('currency')->andReturn('pen');

    $this->postJson(route('payment.process'), checkoutPayload($this->cart))->assertUnprocessable();

    expect(Order::count())->toBe(0);
});

it('does not accept the same payment intent twice', function () {
    $gateway = mock(PaymentGateway::class);
    $gateway->shouldReceive('retrieveIntent')->andReturn(fakePaymentIntent($this->cart));
    $gateway->shouldReceive('currency')->andReturn('pen');

    $this->postJson(route('payment.process'), checkoutPayload($this->cart))->assertSuccessful();
    $this->postJson(route('payment.process'), checkoutPayload($this->cart))->assertConflict();

    expect(Order::count())->toBe(1)
        ->and((int) $this->product->fresh()->stock)->toBe(8);
});

it('rejects payments that have not succeeded', function () {
    $gateway = mock(PaymentGateway::class);
    $gateway->shouldReceive('retrieveIntent')->andReturn(fakePaymentIntent($this->cart, ['status' => 'requires_payment_method']));

    $this->postJson(route('payment.process'), checkoutPayload($this->cart))->assertBadRequest();

    expect(Order::count())->toBe(0);
});

it('refunds the payment when stock ran out', function () {
    $this->product->update(['stock' => 1]);

    $gateway = mock(PaymentGateway::class);
    $gateway->shouldReceive('retrieveIntent')->andReturn(fakePaymentIntent($this->cart));
    $gateway->shouldReceive('currency')->andReturn('pen');
    $gateway->shouldReceive('refund')->once()->with('pi_test_123');

    $this->postJson(route('payment.process'), checkoutPayload($this->cart))->assertConflict();

    expect(Order::count())->toBe(0)
        ->and((int) $this->product->fresh()->stock)->toBe(1);
});

it('rejects unsigned webhooks', function () {
    config(['services.stripe.webhook_secret' => 'whsec_test']);

    $this->postJson(route('stripe.webhook'), ['type' => 'payment_intent.succeeded'])->assertBadRequest();
});

it('rejects webhooks when no secret is configured', function () {
    config(['services.stripe.webhook_secret' => null]);

    $this->postJson(route('stripe.webhook'), ['type' => 'payment_intent.succeeded'])->assertBadRequest();
});

it('accepts correctly signed webhooks', function () {
    config(['services.stripe.webhook_secret' => 'whsec_test']);
    $order = Order::factory()->unpaid()->create(['stripe_payment_intent_id' => 'pi_test_123']);

    $payload = json_encode([
        'id' => 'evt_test',
        'object' => 'event',
        'type' => 'payment_intent.succeeded',
        'data' => ['object' => ['id' => 'pi_test_123', 'object' => 'payment_intent']],
    ]);
    $timestamp = time();
    $signature = hash_hmac('sha256', "{$timestamp}.{$payload}", 'whsec_test');

    $this->call('POST', route('stripe.webhook'), [], [], [], [
        'HTTP_STRIPE_SIGNATURE' => "t={$timestamp},v1={$signature}",
        'CONTENT_TYPE' => 'application/json',
    ], $payload)->assertSuccessful();

    expect($order->fresh()->payment_status)->toBe('paid');
});
