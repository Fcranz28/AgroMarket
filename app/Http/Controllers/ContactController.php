<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ContactController extends Controller
{
    /**
     * The contact form is delivered by Formspree directly from the browser.
     */
    public function show(): View
    {
        return view('contact');
    }
}
