<?php

namespace App\Modules\Orders\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Orders\Services\SessionCart;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(SessionCart $cart): View|RedirectResponse
    {
        $summary = $cart->summary();

        if ($summary['lines']->isEmpty()) {
            return to_route('products.index')->with('status', 'Tu carrito está vacío.');
        }

        return view('checkout.index', [
            'user' => auth()->user(),
            'lines' => $summary['lines'],
            'total' => $summary['total'],
            'paymentCart' => $cart->toPaymentPayload(),
        ]);
    }
}
