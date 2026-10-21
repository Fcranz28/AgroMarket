<?php

namespace App\Modules\Catalog;

use App\Modules\Catalog\Livewire\ProductCatalog;
use App\Modules\Catalog\Livewire\SearchBox;
use App\Modules\Catalog\Models\Category;
use App\Modules\ModuleServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\View\View as ViewInstance;
use Livewire\Component as LivewireComponent;

class CatalogServiceProvider extends ModuleServiceProvider
{
    /**
     * @var array<string, class-string<LivewireComponent>>
     */
    protected array $livewire = [
        'catalog.product-catalog' => ProductCatalog::class,
        'catalog.search-box' => SearchBox::class,
    ];

    /**
     * Bootstrap the module.
     */
    public function boot(): void
    {
        parent::boot();

        View::composer('onboarding.user', fn (ViewInstance $view) => $view->with('categories', Category::all()));
    }
}
