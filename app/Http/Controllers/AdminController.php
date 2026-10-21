<?php

namespace App\Http\Controllers;

use App\Modules\Catalog\Models\Product;
use App\Modules\Identity\Models\User;
use App\Modules\Orders\Models\Order;

class AdminController extends Controller
{
    public function index()
    {
        $pendingFarmers = User::pendingVerification()->count();
        $totalFarmers = User::farmers()->count();
        $totalUsers = User::where('role', User::ROLE_USER)->count();

        // Mock data or real counts if models exist
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        // Calculate total revenue from completed orders
        $totalRevenue = Order::where('status', 'delivered')->sum('total');

        return view('admin.dashboard', compact(
            'pendingFarmers',
            'totalFarmers',
            'totalUsers',
            'totalProducts',
            'totalOrders',
            'totalRevenue'
        ));
    }
}
