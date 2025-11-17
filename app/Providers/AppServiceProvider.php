<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Product; 
use App\Policies\ProductPolicy;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Product::class => ProductPolicy::class,
    ];
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
   
    public function boot(): void
    {
        Gate::policy(Product::class, ProductPolicy::class);
    }
}
