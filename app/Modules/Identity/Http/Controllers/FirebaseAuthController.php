<?php

namespace App\Modules\Identity\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Identity\Exceptions\InvalidFirebaseTokenException;
use App\Modules\Identity\Http\Requests\FirebaseAuthRequest;
use App\Modules\Identity\Models\User;
use App\Modules\Identity\Services\FirebaseTokenVerifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

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
    #[OA\Post(
        path: '/auth/firebase',
        summary: 'Login o registro con Google/Facebook',
        description: 'Recibe el ID token de Firebase, verifica su firma y abre la sesión web. Con isRegistering=false y un correo sin cuenta responde redirect_to_register; con isRegistering=true y una cuenta existente responde redirect_to_login.',
        security: [['csrf' => []]],
        tags: ['Autenticación'],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(required: ['idToken'], properties: [
            new OA\Property(property: 'idToken', type: 'string', maxLength: 4096, description: 'ID token devuelto por Firebase Auth'),
            new OA\Property(property: 'isRegistering', type: 'boolean', default: false),
        ])),
        responses: [
            new OA\Response(response: 200, description: 'Sesión iniciada o redirección sugerida', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'success', type: 'boolean'),
                new OA\Property(property: 'action', type: 'string', enum: ['login', 'register', 'redirect_to_register', 'redirect_to_login']),
                new OA\Property(property: 'redirect', type: 'string', format: 'uri'),
                new OA\Property(property: 'message', type: 'string', nullable: true),
                new OA\Property(property: 'user', nullable: true, properties: [
                    new OA\Property(property: 'id', type: 'integer'),
                    new OA\Property(property: 'name', type: 'string'),
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                ], type: 'object'),
            ])),
            new OA\Response(response: 401, description: 'Token inválido o falsificado', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 403, description: 'Cuenta suspendida', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 422, description: 'Correo no verificado o datos inválidos', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(ref: '#/components/responses/TooManyRequests', response: 429),
        ],
    )]
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
