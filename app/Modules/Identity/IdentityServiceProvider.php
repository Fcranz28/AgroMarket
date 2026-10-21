<?php

namespace App\Modules\Identity;

use App\Modules\Identity\Actions\Fortify\ResetUserPassword;
use App\Modules\Identity\Actions\Fortify\UpdateUserPassword;
use App\Modules\Identity\Http\Responses\LoginResponse;
use App\Modules\Identity\Livewire\AddressBook;
use App\Modules\Identity\Models\User;
use App\Modules\Identity\Services\FirebaseTokenVerifier;
use App\Modules\ModuleServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse as TwoFactorLoginResponseContract;
use Laravel\Fortify\Fortify;
use Livewire\Component as LivewireComponent;

class IdentityServiceProvider extends ModuleServiceProvider
{
    /**
     * @var array<string, class-string<LivewireComponent>>
     */
    protected array $livewire = [
        'identity.address-book' => AddressBook::class,
    ];

    /**
     * Register the module services.
     */
    public function register(): void
    {
        $this->app->singleton(FirebaseTokenVerifier::class, fn () => new FirebaseTokenVerifier(
            config('services.firebase.project_id'),
        ));

        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);
        $this->app->singleton(TwoFactorLoginResponseContract::class, LoginResponse::class);
    }

    /**
     * Bootstrap the module.
     */
    public function boot(): void
    {
        parent::boot();

        $this->configureFortify();
        $this->configureRateLimiting();
    }

    /**
     * Fortify handles login, logout, password reset, e-mail verification, password changes
     * and two-factor authentication with the views of this module.
     */
    private function configureFortify(): void
    {
        Fortify::loginView('auth.login');
        Fortify::requestPasswordResetLinkView('auth.forgot-password');
        Fortify::resetPasswordView(fn (Request $request) => view('auth.reset-password', ['request' => $request]));
        Fortify::verifyEmailView('auth.verify-email');
        Fortify::confirmPasswordView('auth.confirm-password');
        Fortify::twoFactorChallengeView('auth.two-factor-challenge');

        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);

        Fortify::authenticateUsing(function (Request $request): ?User {
            $user = User::where('email', $request->input('email'))->first();

            if (! $user || ! Hash::check((string) $request->input('password'), $user->password)) {
                return null;
            }

            if ($user->is_banned) {
                throw ValidationException::withMessages(['email' => 'Tu cuenta ha sido suspendida.']);
            }

            return $user;
        });
    }

    private function configureRateLimiting(): void
    {
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower((string) $request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', fn (Request $request) => Limit::perMinute(5)->by($request->session()->get('login.id')));
    }
}
