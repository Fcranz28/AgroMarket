<?php

use App\Modules\Identity\Exceptions\InvalidFirebaseTokenException;
use App\Modules\Identity\Models\User;
use App\Modules\Identity\Services\FirebaseTokenVerifier;

use function Pest\Laravel\mock;

/**
 * @param  array<string, mixed>  $claims
 */
function fakeFirebaseClaims(array $claims): void
{
    mock(FirebaseTokenVerifier::class)
        ->shouldReceive('verify')
        ->andReturn([
            'sub' => 'firebase-uid',
            'email_verified' => true,
            'firebase' => ['sign_in_provider' => 'google.com'],
            ...$claims,
        ]);
}

it('rejects a forged or invalid token', function () {
    $admin = User::factory()->admin()->create();

    mock(FirebaseTokenVerifier::class)
        ->shouldReceive('verify')
        ->andThrow(new InvalidFirebaseTokenException('Invalid Firebase ID token.'));

    $this->postJson(route('auth.firebase'), ['idToken' => 'forged.token.value'])
        ->assertUnauthorized();

    $this->assertGuest();
    expect($admin->fresh()->remember_token)->toBe($admin->remember_token);
});

it('really verifies the token signature when no mock is bound', function () {
    config(['services.firebase.project_id' => 'agromarket-test']);
    app()->forgetInstance(FirebaseTokenVerifier::class);

    $forgedPayload = rtrim(strtr(base64_encode(json_encode(['email' => 'admin@agromarket.com'])), '+/', '-_'), '=');

    $this->postJson(route('auth.firebase'), ['idToken' => "eyJhbGciOiJub25lIn0.{$forgedPayload}.signature"])
        ->assertUnauthorized();

    $this->assertGuest();
});

it('logs in an existing user with a verified token', function () {
    $user = User::factory()->create(['email' => 'cliente@example.com']);
    fakeFirebaseClaims(['email' => 'cliente@example.com']);

    $this->postJson(route('auth.firebase'), ['idToken' => 'valid'])
        ->assertSuccessful()
        ->assertJson(['success' => true, 'action' => 'login', 'redirect' => route('home')]);

    $this->assertAuthenticatedAs($user);
});

it('registers a new user with a verified token', function () {
    fakeFirebaseClaims(['email' => 'nuevo@example.com', 'name' => 'Nuevo Usuario']);

    $this->postJson(route('auth.firebase'), ['idToken' => 'valid', 'isRegistering' => true])
        ->assertSuccessful()
        ->assertJson(['action' => 'register', 'redirect' => route('onboarding.welcome')]);

    $user = User::where('email', 'nuevo@example.com')->firstOrFail();

    expect($user->role)->toBe(User::ROLE_USER)
        ->and($user->email_verified_at)->not->toBeNull()
        ->and($user->onboarding_completed)->toBeFalsy();
    $this->assertAuthenticatedAs($user);
});

it('rejects tokens without a verified email', function () {
    User::factory()->create(['email' => 'cliente@example.com']);
    fakeFirebaseClaims(['email' => 'cliente@example.com', 'email_verified' => false]);

    $this->postJson(route('auth.firebase'), ['idToken' => 'valid'])->assertUnprocessable();

    $this->assertGuest();
});

it('rejects banned users', function () {
    User::factory()->banned()->create(['email' => 'baneado@example.com']);
    fakeFirebaseClaims(['email' => 'baneado@example.com']);

    $this->postJson(route('auth.firebase'), ['idToken' => 'valid'])->assertForbidden();

    $this->assertGuest();
});

it('asks unknown users to register first', function () {
    fakeFirebaseClaims(['email' => 'desconocido@example.com']);

    $this->postJson(route('auth.firebase'), ['idToken' => 'valid'])
        ->assertSuccessful()
        ->assertJson(['action' => 'redirect_to_register']);

    $this->assertGuest();
    expect(User::where('email', 'desconocido@example.com')->exists())->toBeFalse();
});

it('requires an id token', function () {
    $this->postJson(route('auth.firebase'), [])->assertJsonValidationErrors('idToken');
});
