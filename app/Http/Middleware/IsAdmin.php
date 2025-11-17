<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- Importante
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Revisa si el usuario está logueado Y si es admin
        // (La lógica es idéntica)
        if (Auth::check() && Auth::user()->is_admin) {
            // Si es admin, déjalo pasar
            return $next($request);
        }

        // 2. Si no es admin, redirígelo
        return redirect()->route('home');
    }
}