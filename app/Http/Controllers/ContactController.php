<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function show()
    {
        return view('contact');
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'message' => 'required|string|min:10',
        ]);

        // Aquí iría la lógica para enviar correo
        // Mail::to('admin@agromarket.com')->send(new ContactForm($validated));

        return back()->with('status', 'Mensaje enviado correctamente. Nos pondremos en contacto contigo pronto.');
    }
}
