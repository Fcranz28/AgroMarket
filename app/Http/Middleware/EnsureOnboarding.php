<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureOnboarding
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Skip for Admins
            if ($user->isAdmin()) {
                return $next($request);
            }

            if (!$user->onboarding_completed) {
                // Allow access to onboarding routes and logout
                if ($request->routeIs('onboarding.*') || $request->routeIs('logout')) {
                    return $next($request);
                }
                
                return redirect()->route('onboarding.welcome');
            }
        }

        return $next($request);
    }
}
