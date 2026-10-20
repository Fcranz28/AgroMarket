<?php

namespace App\Modules\Identity\Services;

use App\Modules\Identity\Exceptions\InvalidFirebaseTokenException;
use Kreait\Firebase\JWT\IdTokenVerifier;
use Throwable;

class FirebaseTokenVerifier
{
    private ?IdTokenVerifier $verifier = null;

    public function __construct(private ?string $projectId) {}

    /**
     * Verify the signature, audience, issuer and expiry of a Firebase ID token.
     *
     * @return array{email?: string, email_verified?: bool, name?: string, picture?: string, sub: string, firebase?: array{sign_in_provider?: string}}
     *
     * @throws InvalidFirebaseTokenException
     */
    public function verify(string $idToken): array
    {
        if (blank($this->projectId)) {
            throw new InvalidFirebaseTokenException('Firebase project id is not configured.');
        }

        try {
            return $this->verifier()->verifyIdToken($idToken)->payload();
        } catch (Throwable $exception) {
            throw new InvalidFirebaseTokenException('Invalid Firebase ID token.', previous: $exception);
        }
    }

    private function verifier(): IdTokenVerifier
    {
        return $this->verifier ??= IdTokenVerifier::createWithProjectId($this->projectId);
    }
}
