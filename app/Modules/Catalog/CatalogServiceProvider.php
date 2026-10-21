<?php

namespace App\Modules\Catalog;

use App\Modules\Catalog\Models\Category;
use App\Modules\ModuleServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\View\View as ViewInstance;

class CatalogServiceProvider extends ModuleServiceProvider
{
    /**
     * Bootstrap the module.
     */
    public function boot(): void
    {
        parent::boot();

        View::composer('layouts.app', fn (ViewInstance $view) => $view->with('navCategories', Category::all()));
        View::composer('onboarding.user', fn (ViewInstance $view) => $view->with('categories', Category::all()));
    }
}
