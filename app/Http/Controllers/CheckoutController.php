<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('checkout.index', compact('user'));
    }
}
