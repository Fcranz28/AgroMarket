<?php

namespace App\Modules\Orders\Livewire;

use App\Modules\Orders\Services\SessionCart;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Cart panel of the layout. Any page adds products by dispatching the "add-to-cart"
 * Livewire event, so the catalog does not depend on this module.
 */
class CartSidebar extends Component
{
    #[On('add-to-cart')]
    public function add(int $productId, ?int $unitId = null, int $quantity = 1): void
    {
        $result = $this->cart()->add($productId, $unitId, $quantity);

        $this->dispatch('notify', type: $result['added'] ? 'success' : 'warning', message: $result['message']);

        if ($result['added']) {
            $this->dispatch('cart-updated');
        }
    }

    public function increment(string $key, int $quantity): void
    {
        $this->cart()->update($key, $quantity + 1);
        $this->dispatch('cart-updated');
    }

    public function decrement(string $key, int $quantity): void
    {
        $this->cart()->update($key, $quantity - 1);
        $this->dispatch('cart-updated');
    }

    public function remove(string $key): void
    {
        $this->cart()->remove($key);
        $this->dispatch('cart-updated');
    }

    public function render(): View
    {
        return view('livewire.orders.cart-sidebar', $this->cart()->summary());
    }

    private function cart(): SessionCart
    {
        return app(SessionCart::class);
    }
}
