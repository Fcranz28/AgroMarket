<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display user's orders, profile and addresses
     */
    public function index()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)
            ->with(['items.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('orders.index', compact('user', 'orders'));
    }

    /**
     * Show specific order
     */
    public function show(Order $order)
    {
        // Make sure user owns this order
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load(['items.product']);

        return view('orders.show', compact('order'));
    }
}
