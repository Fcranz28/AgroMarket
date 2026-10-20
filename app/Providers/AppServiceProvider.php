<?php

namespace App\Providers;

use App\Services\FirebaseTokenVerifier;
use App\Services\PaymentGateway;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(FirebaseTokenVerifier::class, fn () => new FirebaseTokenVerifier(
            config('services.firebase.project_id'),
        ));

        $this->app->singleton(PaymentGateway::class, fn () => new PaymentGateway(
            config('services.stripe.secret'),
            config('services.stripe.currency'),
        ));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
