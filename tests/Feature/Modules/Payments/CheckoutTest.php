<?php

use App\Modules\Catalog\Models\Product;
use App\Modules\Identity\Models\User;
use App\Modules\Orders\Models\Order;
use App\Modules\Orders\Services\CartPricingService;
use App\Modules\Orders\Services\SessionCart;
use App\Modules\Payments\Livewire\Checkout;
use App\Modules\Payments\Services\PaymentGateway;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Stripe\PaymentIntent;

use function Pest\Laravel\mock;

beforeEach(function () {
    $this->withoutVite();
    Storage::fake('public');

    $this->product = Product::factory()->create(['name' => 'Quinua blanca', 'price' => '11.80', 'stock' => 10]);
    app(SessionCart::class)->add($this->product->id, quantity: 2);
});

/**
 * Payment intent Stripe would return for the current session cart.
 *
 * @param  array<string, mixed>  $overrides
 */
function sessionCartIntent(array $overrides = []): PaymentIntent
{
    $priced = app(CartPricingService::class)->price(app(SessionCart::class)->toPaymentPayload());

    return PaymentIntent::constructFrom([
        'id' => 'pi_checkout_1',
        'object' => 'payment_intent',
        'status' => 'succeeded',
        'amount' => $priced['total_cents'],
        'currency' => 'pen',
        'client_secret' => 'pi_checkout_1_secret_abc',
        'metadata' => ['cart_hash' => $priced['hash']],
        ...$overrides,
    ]);
}

/**
 * @return array<string, string>
 */
function buyerData(): array
{
    return [
        'guest_name' => 'Ana',
        'guest_lastname' => 'Quispe',
        'guest_email' => 'ana@example.com',
        'document_type' => 'DNI',
        'document_number' => '12345678',
        'phone' => '987654321',
        'shipping_address' => 'Av. Siempre Viva 123, Lima',
    ];
}

function fillBuyer(\Livewire\Features\SupportTesting\Testable $component): \Livewire\Features\SupportTesting\Testable
{
    foreach (buyerData() as $field => $value) {
        $component->set($field, $value);
    }

    return $component;
}

it('sends shoppers with an empty cart back to the catalog', function () {
    app(SessionCart::class)->clear();

    $this->get(route('checkout.index'))->assertRedirect(route('products.index'));
});

it('shows the order summary and the payment form', function () {
    $this->get(route('checkout.index'))
        ->assertSuccessful()
        ->assertSeeLivewire(Checkout::class)
        ->assertSee('Quinua blanca')
        ->assertSee('S/. 23.60')
        ->assertSee('S/. 20.00')
        ->assertSee('S/. 3.60')
        ->assertSee('amount: 2360', false);
});

it('prefills the data of a signed-in buyer and picks the default address', function () {
    $buyer = User::factory()->create(['name' => 'Lucía', 'phone' => '999888777', 'dni' => '87654321']);
    $buyer->addresses()->create(['address' => 'Jr. Lima 1', 'is_default' => false]);
    $default = $buyer->addresses()->create(['address' => 'Av. Arequipa 200', 'is_default' => true]);

    Livewire::actingAs($buyer)->test(Checkout::class)
        ->assertSet('guest_name', 'Lucía')
        ->assertSet('guest_email', $buyer->email)
        ->assertSet('phone', '999888777')
        ->assertSet('document_number', '87654321')
        ->assertSet('addressChoice', (string) $default->id)
        ->assertSee('Av. Arequipa 200');
});

it('validates the buyer data before creating the payment intent', function () {
    mock(PaymentGateway::class)->shouldNotReceive('createIntent')->shouldReceive('currency')->andReturn('pen');

    Livewire::test(Checkout::class)
        ->set('guest_email', 'no-es-correo')
        ->set('document_number', '12-34')
        ->call('prepare')
        ->assertReturned(null)
        ->assertHasErrors([
            'guest_name' => 'required',
            'guest_email' => 'email',
            'document_number' => 'alpha_num',
            'shipping_address' => 'required',
        ])
        ->assertSet('paymentIntentId', null);
});

it('creates the payment intent for the session cart', function () {
    $gateway = mock(PaymentGateway::class);
    $gateway->shouldReceive('currency')->andReturn('pen');
    $gateway->shouldReceive('createIntent')
        ->once()
        ->withArgs(fn (int $amount, array $metadata) => $amount === 2360 && $metadata['cart_hash'] === sessionCartIntent()->metadata['cart_hash'])
        ->andReturn(sessionCartIntent());

    fillBuyer(Livewire::test(Checkout::class))
        ->call('prepare')
        ->assertHasNoErrors()
        ->assertReturned('pi_checkout_1_secret_abc')
        ->assertSet('paymentIntentId', 'pi_checkout_1');
});

it('rejects carts below the card minimum', function () {
    app(SessionCart::class)->clear();
    app(SessionCart::class)->add(Product::factory()->create(['price' => '1.50', 'stock' => 5])->id);
    mock(PaymentGateway::class)->shouldNotReceive('createIntent')->shouldReceive('currency')->andReturn('pen');

    fillBuyer(Livewire::test(Checkout::class))
        ->call('prepare')
        ->assertReturned(null)
        ->assertHasErrors('payment');
});

it('does not complete a payment that was never prepared', function () {
    mock(PaymentGateway::class)->shouldNotReceive('retrieveIntent')->shouldReceive('currency')->andReturn('pen');

    fillBuyer(Livewire::test(Checkout::class))
        ->call('complete')
        ->assertHasErrors('payment');

    expect(Order::count())->toBe(0);
});

it('places the order, empties the cart and opens the order of a signed-in buyer', function () {
    $buyer = User::factory()->create();
    $gateway = mock(PaymentGateway::class);
    $gateway->shouldReceive('currency')->andReturn('pen');
    $gateway->shouldReceive('createIntent')->andReturn(sessionCartIntent());
    $gateway->shouldReceive('retrieveIntent')->with('pi_checkout_1')->andReturn(sessionCartIntent());

    $component = fillBuyer(Livewire::actingAs($buyer)->test(Checkout::class))
        ->set('saveAddress', true)
        ->call('prepare')
        ->call('complete');

    $order = Order::sole();

    $component->assertHasNoErrors()->assertRedirect(route('orders.show', $order));

    expect($order->user_id)->toBe($buyer->id)
        ->and((float) $order->total)->toBe(23.6)
        ->and($order->shipping_address)->toBe('Av. Siempre Viva 123, Lima')
        ->and($this->product->fresh()->stock)->toBe(8)
        ->and(app(SessionCart::class)->isEmpty())->toBeTrue()
        ->and($buyer->addresses()->pluck('address')->all())->toBe(['Av. Siempre Viva 123, Lima']);
});

it('sends guests to the signed link of their receipt', function () {
    $gateway = mock(PaymentGateway::class);
    $gateway->shouldReceive('currency')->andReturn('pen');
    $gateway->shouldReceive('createIntent')->andReturn(sessionCartIntent());
    $gateway->shouldReceive('retrieveIntent')->andReturn(sessionCartIntent());

    $component = fillBuyer(Livewire::test(Checkout::class))->call('prepare')->call('complete');

    $redirect = $component->effects['redirect'] ?? '';

    expect($redirect)->toContain('signature=')
        ->and(Order::sole()->user_id)->toBeNull();
});

it('uses a saved address only if it belongs to the buyer', function () {
    $buyer = User::factory()->create();
    $own = $buyer->addresses()->create(['address' => 'Calle Propia 9', 'is_default' => true]);
    $foreign = User::factory()->create()->addresses()->create(['address' => 'Calle Ajena 5', 'is_default' => true]);
    $gateway = mock(PaymentGateway::class);
    $gateway->shouldReceive('currency')->andReturn('pen');
    $gateway->shouldReceive('createIntent')->andReturn(sessionCartIntent());

    $component = fillBuyer(Livewire::actingAs($buyer)->test(Checkout::class))->set('shipping_address', '');

    $component->set('addressChoice', (string) $own->id)->call('prepare')->assertSet('shipping_address', 'Calle Propia 9');

    $component->set('shipping_address', '')
        ->set('addressChoice', (string) $foreign->id)
        ->call('prepare')
        ->assertSet('addressChoice', Checkout::NEW_ADDRESS)
        ->assertHasErrors(['shipping_address' => 'required']);
});

it('keeps the cart when the payment does not match it', function () {
    $gateway = mock(PaymentGateway::class);
    $gateway->shouldReceive('currency')->andReturn('pen');
    $gateway->shouldReceive('createIntent')->andReturn(sessionCartIntent());
    $gateway->shouldReceive('retrieveIntent')->andReturn(sessionCartIntent(['amount' => 100]));

    fillBuyer(Livewire::test(Checkout::class))
        ->call('prepare')
        ->call('complete')
        ->assertHasErrors('payment')
        ->assertNoRedirect();

    expect(Order::count())->toBe(0)
        ->and(app(SessionCart::class)->isEmpty())->toBeFalse();
});
