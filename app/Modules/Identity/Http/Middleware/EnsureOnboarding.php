<?php

namespace App\Modules\Identity\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

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

            if (! $user->onboarding_completed) {
                // Allow the onboarding itself and the account routes it may need
                if ($request->routeIs('onboarding.*', 'logout', 'verification.*', 'password.*', 'two-factor.*')) {
                    return $next($request);
                }

                return redirect()->route('onboarding.welcome');
            }
        }

        return $next($request);
    }
}
