<?php

namespace App\Modules\Payments\Livewire;

use App\Modules\Identity\Models\Address;
use App\Modules\Identity\Models\User;
use App\Modules\Orders\Services\SessionCart;
use App\Modules\Payments\Actions\CompleteCheckout;
use App\Modules\Payments\Actions\CreateCheckoutIntent;
use App\Modules\Payments\Exceptions\CheckoutException;
use App\Modules\Payments\Http\Requests\Concerns\ValidatesCheckoutCustomer;
use App\Modules\Payments\Services\PaymentGateway;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Checkout page. Stripe's Payment Element runs in "deferred intent" mode:
 *
 *  1. The browser checks the card form (elements.submit()).
 *  2. prepare() validates the buyer data and creates the payment intent for the session cart.
 *  3. The browser confirms the card payment with that intent.
 *  4. complete() verifies the paid intent, places the order and empties the cart.
 *
 * @property-read Collection<int, Address> $addresses
 */
class Checkout extends Component
{
    use ValidatesCheckoutCustomer;

    public const NEW_ADDRESS = 'new';

    public string $guest_name = '';

    public string $guest_lastname = '';

    public string $guest_email = '';

    public string $document_type = 'DNI';

    public string $document_number = '';

    public string $phone = '';

    /**
     * Id of the saved address picked by a signed-in buyer, or "new" to type one.
     */
    public string $addressChoice = self::NEW_ADDRESS;

    public string $shipping_address = '';

    public bool $saveAddress = false;

    #[Locked]
    public ?string $paymentIntentId = null;

    public function mount(SessionCart $cart): void
    {
        if ($cart->isEmpty()) {
            session()->flash('status', 'Tu carrito está vacío.');
            $this->redirectRoute('products.index');

            return;
        }

        if ($user = $this->user()) {
            $this->guest_name = $user->name;
            $this->guest_email = $user->email;
            $this->phone = (string) $user->phone;
            $this->document_number = (string) $user->dni;
            $address = $this->addresses->firstWhere('is_default', true) ?? $this->addresses->first();
            $this->addressChoice = $address ? (string) $address->id : self::NEW_ADDRESS;
        }
    }

    /**
     * Saved addresses of the signed-in buyer.
     *
     * @return Collection<int, Address>
     */
    #[Computed]
    public function addresses(): Collection
    {
        return $this->user()?->addresses()->latest()->get() ?? collect();
    }

    /**
     * Validate the buyer data and create the payment intent. Returns the client secret the
     * browser confirms the card payment with, or null when something has to be fixed first.
     */
    public function prepare(SessionCart $cart, CreateCheckoutIntent $createIntent): ?string
    {
        $this->resolveShippingAddress();
        $this->validate();

        if ($cart->isEmpty()) {
            $this->addError('payment', 'Tu carrito está vacío.');

            return null;
        }

        try {
            $paymentIntent = $createIntent->execute($cart->toPaymentPayload(), Auth::id());
        } catch (CheckoutException $exception) {
            $this->addError('payment', $exception->getMessage());

            return null;
        }

        $this->paymentIntentId = $paymentIntent->id;

        return $paymentIntent->client_secret;
    }

    /**
     * Called by the browser once Stripe confirmed the payment of the prepared intent.
     */
    public function complete(SessionCart $cart, CompleteCheckout $completeCheckout): void
    {
        if ($this->paymentIntentId === null) {
            $this->addError('payment', 'El pago no se ha iniciado.');

            return;
        }

        $this->resolveShippingAddress();
        $customer = $this->validate();

        try {
            $order = $completeCheckout->execute($this->paymentIntentId, $cart->toPaymentPayload(), $customer, Auth::id());
        } catch (CheckoutException $exception) {
            $this->addError('payment', $exception->getMessage());

            return;
        }

        $cart->clear();
        $this->saveNewAddress();

        session()->flash('status', '¡Gracias por tu compra! Tu pedido fue registrado.');

        $this->redirect(Auth::check() ? route('orders.show', $order) : CompleteCheckout::guestInvoiceUrl($order));
    }

    /**
     * @return array<string, string>
     */
    protected function rules(): array
    {
        return $this->customerRules();
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return $this->customerMessages();
    }

    public function render(): View
    {
        return view('livewire.payments.checkout', [
            ...app(SessionCart::class)->summary(),
            'currency' => app(PaymentGateway::class)->currency(),
            'stripeKey' => config('services.stripe.key'),
        ]);
    }

    /**
     * A picked saved address wins over the typed one; it must belong to the buyer.
     */
    private function resolveShippingAddress(): void
    {
        if ($this->addressChoice === self::NEW_ADDRESS) {
            return;
        }

        $address = $this->addresses->firstWhere('id', (int) $this->addressChoice);

        if ($address) {
            $this->shipping_address = $address->address;
        } else {
            $this->addressChoice = self::NEW_ADDRESS;
        }
    }

    private function saveNewAddress(): void
    {
        $user = $this->user();

        if ($user && $this->addressChoice === self::NEW_ADDRESS && $this->saveAddress) {
            $user->addresses()->firstOrCreate(
                ['address' => $this->shipping_address],
                ['is_default' => $this->addresses->isEmpty()],
            );
        }
    }

    private function user(): ?User
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user;
    }
}
