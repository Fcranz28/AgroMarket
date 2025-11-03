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
        // Aquí iría la lógica para guardar el mensaje o enviar correo
        return back()->with('status', 'Mensaje enviado');
    }
}
