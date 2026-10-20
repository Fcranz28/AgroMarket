<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidFirebaseTokenException;
use App\Http\Requests\FirebaseAuthRequest;
use App\Models\User;
use App\Services\FirebaseTokenVerifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FirebaseAuthController extends Controller
{
    /**
     * Sign-in providers whose e-mail address is trusted without the email_verified claim.
     *
     * @var list<string>
     */
    private const TRUSTED_EMAIL_PROVIDERS = ['facebook.com'];

    /**
     * Authenticate a user with a verified Firebase ID token.
     */
    public function authenticate(FirebaseAuthRequest $request, FirebaseTokenVerifier $verifier): JsonResponse
    {
        try {
            $claims = $verifier->verify($request->validated('idToken'));
        } catch (InvalidFirebaseTokenException $exception) {
            Log::warning('Firebase token rejected', ['reason' => $exception->getPrevious()?->getMessage() ?? $exception->getMessage()]);

            return response()->json(['error' => 'No se pudo verificar tu sesión. Intenta nuevamente.'], 401);
        }

        $email = $claims['email'] ?? null;
        $provider = $claims['firebase']['sign_in_provider'] ?? null;
        $emailIsTrusted = ($claims['email_verified'] ?? false) === true
            || in_array($provider, self::TRUSTED_EMAIL_PROVIDERS, true);

        if (! is_string($email) || ! $emailIsTrusted) {
            return response()->json(['error' => 'Tu cuenta no tiene un correo verificado.'], 422);
        }

        $isRegistering = $request->boolean('isRegistering');
        $user = User::where('email', $email)->first();

        if ($user && $isRegistering) {
            return response()->json([
                'success' => false,
                'action' => 'redirect_to_login',
                'message' => 'Ya tienes una cuenta. Por favor inicia sesión.',
                'redirect' => route('login'),
            ]);
        }

        if (! $user && ! $isRegistering) {
            return response()->json([
                'success' => false,
                'action' => 'redirect_to_register',
                'message' => 'No tienes una cuenta. Por favor regístrate primero.',
                'redirect' => route('register'),
            ]);
        }

        if ($user?->is_banned) {
            return response()->json(['error' => 'Tu cuenta ha sido suspendida.'], 403);
        }

        $user ??= User::create([
            'name' => $claims['name'] ?? $email,
            'email' => $email,
            'password' => Str::random(40),
            'onboarding_completed' => false,
            'avatar' => $claims['picture'] ?? null,
        ])->forceFill(['email_verified_at' => now()]);

        $user->save();

        Auth::login($user, true);
        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'action' => $isRegistering ? 'register' : 'login',
            'redirect' => $this->redirectFor($user),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    private function redirectFor(User $user): string
    {
        return match (true) {
            ! $user->onboarding_completed => route('onboarding.welcome'),
            $user->isAdmin() => route('admin.dashboard'),
            $user->isFarmer() => route('farmer.dashboard'),
            default => route('home'),
        };
    }
}
