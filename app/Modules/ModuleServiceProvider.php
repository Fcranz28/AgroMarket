<?php

namespace App\Modules;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Component as LivewireComponent;
use Livewire\Livewire;
use ReflectionClass;

/**
 * Base provider for a module in app/Modules/<Module>.
 *
 * A module keeps its routes in routes/web.php and routes/api.php (same URIs and names the
 * application exposes), its own migrations in Database/Migrations, its event listeners
 * in $listen and its Livewire components in $livewire. Policies are declared on the
 * models with #[UsePolicy].
 */
abstract class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Event listeners of the module.
     *
     * @var array<class-string, list<class-string>>
     */
    protected array $listen = [];

    /**
     * Livewire components of the module, by name (e.g. "orders.cart-sidebar").
     *
     * @var array<string, class-string<LivewireComponent>>
     */
    protected array $livewire = [];

    /**
     * Bootstrap the module.
     */
    public function boot(): void
    {
        $this->bootRoutes();
        $this->bootListeners();
        $this->bootLivewireComponents();
        $this->loadMigrationsFrom($this->modulePath('Database/Migrations'));
    }

    /**
     * Absolute path inside the module directory.
     */
    protected function modulePath(string $path = ''): string
    {
        $directory = dirname((new ReflectionClass(static::class))->getFileName());

        return $path === '' ? $directory : $directory.DIRECTORY_SEPARATOR.$path;
    }

    private function bootRoutes(): void
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        if (is_file($webRoutes = $this->modulePath('routes/web.php'))) {
            Route::middleware('web')->group($webRoutes);
        }

        if (is_file($apiRoutes = $this->modulePath('routes/api.php'))) {
            Route::middleware('api')->prefix('api')->group($apiRoutes);
        }
    }

    private function bootListeners(): void
    {
        foreach ($this->listen as $event => $listeners) {
            foreach ($listeners as $listener) {
                Event::listen($event, $listener);
            }
        }
    }

    private function bootLivewireComponents(): void
    {
        foreach ($this->livewire as $name => $component) {
            Livewire::component($name, $component);
        }
    }
}
