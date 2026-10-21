<?php

namespace App\Modules\Backoffice\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Catalog\Models\Product;
use App\Modules\Identity\Enums\UserRole;
use App\Modules\Identity\Models\User;
use App\Modules\Orders\Enums\OrderStatus;
use App\Modules\Orders\Models\Order;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $pendingFarmers = User::pendingVerification()->count();
        $totalFarmers = User::farmers()->count();
        $totalUsers = User::where('role', UserRole::User)->count();

        // Mock data or real counts if models exist
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        // Calculate total revenue from completed orders
        $totalRevenue = Order::where('status', OrderStatus::Delivered)->sum('total');

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
