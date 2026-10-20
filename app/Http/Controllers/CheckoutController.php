<?php

namespace App\Http\Controllers;

class CheckoutController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return view('checkout.index', compact('user'));
    }
}
