<?php

namespace App\Modules\Identity;

use App\Modules\Identity\Services\FirebaseTokenVerifier;
use App\Modules\ModuleServiceProvider;

class IdentityServiceProvider extends ModuleServiceProvider
{
    /**
     * Register the module services.
     */
    public function register(): void
    {
        $this->app->singleton(FirebaseTokenVerifier::class, fn () => new FirebaseTokenVerifier(
            config('services.firebase.project_id'),
        ));
    }
}
