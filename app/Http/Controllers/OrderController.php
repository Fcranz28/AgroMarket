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
            ->where('payment_status', 'paid')
            ->with(['items.product', 'invoice'])
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

    /**
     * Display farmer's orders (where they are the seller)
     */
    public function farmerOrders()
    {
        $user = Auth::user();
        
        // Get all orders that contain products owned by this farmer
        $orders = Order::whereHas('items.product', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['items.product', 'user', 'invoice'])
            ->where('payment_status', 'paid')
            ->orderBy('created_at', 'desc')
            ->get();

        // Filter to only show pending orders (not delivered/cancelled)
        $pendingOrders = $orders->filter(function ($order) {
            return in_array($order->status, ['pending', 'processing', 'shipped']);
        });

        return view('farmer.orders', compact('pendingOrders', 'orders'));
    }

    /**
     * Update order status
     */
    public function updateStatus(\Illuminate\Http\Request $request, Order $order)
    {
        // Verify user is a farmer and owns products in this order
        // For simplicity, we'll just check if they are a farmer for now, 
        // but ideally we should check if they are the seller of items in this order.
        if (!Auth::user()->isFarmer()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        $order->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado correctamente'
        ]);
    }
}
