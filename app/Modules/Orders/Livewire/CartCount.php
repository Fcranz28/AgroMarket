<?php

namespace App\Modules\Orders\Livewire;

use App\Modules\Orders\Services\SessionCart;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Badge with the number of units in the cart.
 */
class CartCount extends Component
{
    public string $variant = 'desktop';

    #[On('cart-updated')]
    public function refreshCount(): void {}

    public function render(): View
    {
        return view('livewire.orders.cart-count', ['count' => app(SessionCart::class)->count()]);
    }
}
