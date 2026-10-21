<?php

namespace App\Modules\Orders\Http\Controllers;

use App\Http\Controllers\Controller;
class CheckoutController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return view('checkout.index', compact('user'));
    }
}
