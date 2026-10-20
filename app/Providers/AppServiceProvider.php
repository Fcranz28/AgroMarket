<?php

namespace App\Providers;

use App\Models\Report;
use App\Modules\Catalog\Models\Product;
use App\Modules\Identity\Models\User;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Temporary until the Moderation module owns reports (M6).
        User::resolveRelationUsing('reportsReceived', fn (User $user) => $user->hasManyThrough(Report::class, Product::class));
    }
}
