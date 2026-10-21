<?php

namespace App\Modules\Payments;

use App\Modules\ModuleServiceProvider;
use App\Modules\Orders\Events\OrderCancelled;
use App\Modules\Payments\Listeners\RefundCancelledOrder;
use App\Modules\Payments\Livewire\Checkout;
use App\Modules\Payments\Services\PaymentGateway;
use Livewire\Component as LivewireComponent;

class PaymentsServiceProvider extends ModuleServiceProvider
{
    /**
     * @var array<class-string, list<class-string>>
     */
    protected array $listen = [
        OrderCancelled::class => [RefundCancelledOrder::class],
    ];

    /**
     * @var array<string, class-string<LivewireComponent>>
     */
    protected array $livewire = [
        'payments.checkout' => Checkout::class,
    ];

    /**
     * Register the module services.
     */
    public function register(): void
    {
        $this->app->singleton(PaymentGateway::class, fn () => new PaymentGateway(
            config('services.stripe.secret'),
            config('services.stripe.currency'),
        ));
    }
}
