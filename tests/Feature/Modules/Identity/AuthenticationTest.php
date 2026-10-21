<?php

use App\Modules\Identity\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use PragmaRX\Google2FA\Google2FA;

use function Pest\Laravel\mock;

beforeEach(function () {
    $this->withoutVite();
});

it('renders the account pages', function (string $route) {
    $this->get(route($route))->assertSuccessful();
})->with(['login', 'register', 'password.request']);

it('sends each role to its home after logging in', function (callable $makeUser, string $home) {
    $user = $makeUser();

    $this->post(route('login'), ['email' => $user->email, 'password' => 'password'])
        ->assertRedirect(route($home));

    $this->assertAuthenticatedAs($user);
})->with([
    'admin' => [fn () => User::factory()->admin()->create(), 'admin.dashboard'],
    'farmer' => [fn () => User::factory()->farmer()->verified()->create(), 'farmer.dashboard'],
    'buyer' => [fn () => User::factory()->create(), 'home'],
    'pending onboarding' => [fn () => User::factory()->pendingOnboarding()->create(), 'onboarding.welcome'],
]);

it('rejects wrong credentials with a spanish message', function () {
    $user = User::factory()->create();

    $this->post(route('login'), ['email' => $user->email, 'password' => 'incorrecta'])
        ->assertSessionHasErrors(['email' => 'El email o la contraseña son incorrectos.']);

    $this->assertGuest();
});

it('blocks suspended accounts at login', function () {
    $user = User::factory()->banned()->create();

    $this->post(route('login'), ['email' => $user->email, 'password' => 'password'])
        ->assertSessionHasErrors(['email' => 'Tu cuenta ha sido suspendida.']);

    $this->assertGuest();
});

it('throttles repeated login attempts', function () {
    $user = User::factory()->create();

    foreach (range(1, 5) as $attempt) {
        $this->post(route('login'), ['email' => $user->email, 'password' => 'incorrecta']);
    }

    $this->post(route('login'), ['email' => $user->email, 'password' => 'incorrecta'])->assertTooManyRequests();
});

it('logs out', function () {
    $this->actingAs(User::factory()->create())->post(route('logout'))->assertRedirect('/');

    $this->assertGuest();
});

it('sends a password reset link and resets the password', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->post(route('password.email'), ['email' => $user->email])->assertSessionHas('status');

    Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user) {
        $this->get(route('password.reset', ['token' => $notification->token, 'email' => $user->email]))->assertSuccessful();

        $this->post(route('password.update'), [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'nueva-clave-segura',
            'password_confirmation' => 'nueva-clave-segura',
        ])->assertRedirect(route('login'));

        return true;
    });

    expect(Hash::check('nueva-clave-segura', $user->fresh()->password))->toBeTrue();
});

it('sends the verification e-mail when someone registers', function () {
    Event::fake([Registered::class]);

    $this->post('/register', [
        'name' => 'Nuevo Usuario',
        'email' => 'nuevo@example.com',
        'password' => 'secreto123',
        'password_confirmation' => 'secreto123',
    ]);

    Event::assertDispatched(Registered::class);
});

it('emails the verification link to unverified users', function () {
    Notification::fake();
    $user = User::factory()->unverified()->pendingOnboarding()->create();

    $this->actingAs($user)->get(route('onboarding.welcome'))->assertRedirect(route('verification.notice'));
    $this->get(route('verification.notice'))->assertSuccessful()->assertSee('Verifica tu correo');
    $this->post(route('verification.send'));

    Notification::assertSentTo($user, VerifyEmail::class);
});

it('changes the password from the account page', function () {
    $user = User::factory()->create(['password' => 'actual-123']);

    $this->actingAs($user)->put(route('user-password.update'), [
        'current_password' => 'actual-123',
        'password' => 'nueva-clave-segura',
        'password_confirmation' => 'nueva-clave-segura',
    ])->assertSessionHas('status', 'password-updated');

    expect(Hash::check('nueva-clave-segura', $user->fresh()->password))->toBeTrue();
});

it('rejects a wrong current password when changing it', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->put(route('user-password.update'), [
        'current_password' => 'incorrecta',
        'password' => 'nueva-clave-segura',
        'password_confirmation' => 'nueva-clave-segura',
    ])->assertSessionHasErrorsIn('updatePassword', ['current_password' => 'La contraseña actual no es correcta.']);
});

it('asks for the password before opening the security page', function () {
    $this->actingAs(User::factory()->create())->get(route('security'))->assertRedirect(route('password.confirm'));
});

it('enables two factor authentication and asks for the code at login', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->post(route('two-factor.enable'))
        ->assertSessionHas('status', 'two-factor-authentication-enabled');

    $user->refresh();
    $code = app(Google2FA::class)->getCurrentOtp(decrypt($user->two_factor_secret));

    $this->withSession(['auth.password_confirmed_at' => time()])
        ->post(route('two-factor.confirm'), ['code' => $code])
        ->assertSessionHas('status', 'two-factor-authentication-confirmed');

    $this->get(route('security'))->assertSuccessful()->assertSee('códigos de recuperación');

    $this->post(route('logout'));

    $this->post(route('login'), ['email' => $user->email, 'password' => 'password'])
        ->assertRedirect(route('two-factor.login'));
    $this->assertGuest();

    mock(TwoFactorAuthenticationProvider::class)->shouldReceive('verify')->andReturn(true);

    $this->post(route('two-factor.login'), ['code' => '123456'])->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticatedAs($user);
});
