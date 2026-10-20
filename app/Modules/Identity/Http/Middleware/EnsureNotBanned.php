<?php

namespace App\Modules\Identity\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureNotBanned
{
    /**
     * Log out suspended users on any request, not only on role-protected routes.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->is_banned) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Tu cuenta ha sido suspendida.'], 403);
            }

            return redirect()->route('login')->withErrors(['email' => 'Tu cuenta ha sido suspendida.']);
        }

        return $next($request);
    }
}
