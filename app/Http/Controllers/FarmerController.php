<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FarmerController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if (!$user->isVerified()) {
            return view('farmer.pending');
        }

        $products = $user->products()->count();
        $sales = 0; // Placeholder until Order logic is implemented
        $revenue = 0; // Placeholder

        $recentProducts = $user->products()->latest()->take(5)->get();

        return view('farmer.dashboard', compact('products', 'sales', 'revenue', 'recentProducts'));
    }
}
