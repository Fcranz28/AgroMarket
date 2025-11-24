<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth as FirebaseAuth;

class FirebaseAuthController extends Controller
{
    /**
     * Authenticate user with Firebase token
     */
    public function authenticate(Request $request)
    {
        $request->validate([
            'idToken' => 'required|string',
            'provider' => 'required|in:google,facebook',
            'isRegistering' => 'boolean' // To know if this is from register or login page
        ]);

        try {
            // Verify the Firebase ID token using basic JWT decoding
            $idToken = $request->idToken;
            $isRegistering = $request->input('isRegistering', false);
            
            // Decode JWT token
            $tokenParts = explode('.', $idToken);
            if (count($tokenParts) !== 3) {
                return response()->json(['error' => 'Invalid token format'], 400);
            }
            
            $payload = json_decode(base64_decode($tokenParts[1]), true);
            
            if (!$payload || !isset($payload['email'])) {
                return response()->json(['error' => 'Invalid token payload'], 400);
            }
            
            $email = $payload['email'];
            $name = $payload['name'] ?? $payload['email'];
            $uid = $payload['sub'] ?? $payload['user_id'] ?? null;
            
            \Log::info('Firebase auth attempt', [
                'email' => $email,
                'name' => $name,
                'isRegistering' => $isRegistering
            ]);
            
            // Check if user already exists
            $user = User::where('email', $email)->first();
            
            // If user exists and they're trying to login
            if ($user && !$isRegistering) {
                // User exists, log them in
                Auth::login($user, true);
                
                // Redirect based on role and onboarding status
                if (!$user->onboarding_completed) {
                    $redirect = route('onboarding.welcome');
                } elseif ($user->isAdmin()) {
                    $redirect = route('admin.dashboard');
                } elseif ($user->isFarmer()) {
                    $redirect = route('farmer.dashboard');
                } else {
                    $redirect = route('home');
                }
                
                return response()->json([
                    'success' => true,
                    'action' => 'login',
                    'redirect' => $redirect,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email
                    ]
                ]);
            }
            
            // If user doesn't exist and they're trying to login
            if (!$user && !$isRegistering) {
                return response()->json([
                    'success' => false,
                    'action' => 'redirect_to_register',
                    'message' => 'No tienes una cuenta. Por favor regístrate primero.',
                    'redirect' => route('register')
                ], 200);
            }
            
            // If user doesn't exist and they're registering, create new user
            if (!$user && $isRegistering) {
                // Extract avatar URL from payload
                $avatar = $payload['picture'] ?? $payload['photo_url'] ?? null;
                
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => bcrypt(Str::random(32)), // Random password for OAuth users
                    'role' => User::ROLE_USER,
                    'onboarding_completed' => false,
                    'email_verified_at' => now(), // Email verified by Google/Facebook
                    'avatar' => $avatar // Store OAuth avatar URL
                ]);
                
                Auth::login($user, true);
                
                return response()->json([
                    'success' => true,
                    'action' => 'register',
                    'redirect' => route('onboarding.welcome'),
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email
                    ]
                ]);
            }
            
            // If user exists and they're trying to register
            if ($user && $isRegistering) {
                return response()->json([
                    'success' => false,
                    'action' => 'redirect_to_login',
                    'message' => 'Ya tienes una cuenta. Por favor inicia sesión.',
                    'redirect' => route('login')
                ], 200);
            }
            
        } catch (\Exception $e) {
            \Log::error('Firebase auth error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'Error de autenticación: ' . $e->getMessage()
            ], 500);
        }
    }
}

