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
        // Mocking sales data for now
        $sales = 0; 
        $revenue = 0;

        return view('farmer.dashboard', compact('products', 'sales', 'revenue'));
    }
}
