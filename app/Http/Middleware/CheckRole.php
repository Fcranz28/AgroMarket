<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();

        if ($user->is_banned) {
            Auth::logout();
            return redirect('login')->with('error', 'Tu cuenta ha sido suspendida.');
        }

        if ($role == 'admin' && $user->isAdmin()) {
            return $next($request);
        }

        if ($role == 'farmer' && $user->isFarmer()) {
            return $next($request);
        }
        
        // Allow admin to access farmer routes if needed, or handle strictly
        if ($role == 'farmer' && $user->isAdmin()) {
             return $next($request);
        }

        abort(403, 'No tienes permiso para acceder a esta página.');
    }
}
