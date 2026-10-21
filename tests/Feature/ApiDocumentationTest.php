<?php

use App\Modules\Identity\Models\User;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    Artisan::call('l5-swagger:generate');

    $this->spec = json_decode(file_get_contents(storage_path('api-docs/api-docs.json')), true);
});

it('documents every JSON endpoint', function (string $method, string $path) {
    expect($this->spec['paths'])->toHaveKey($path)
        ->and($this->spec['paths'][$path])->toHaveKey($method);
})->with([
    ['get', '/api/productos'],
    ['get', '/api/search'],
    ['get', '/api/suggestions'],
    ['post', '/payment/create-intent'],
    ['post', '/payment/process'],
    ['post', '/stripe/webhook'],
    ['post', '/auth/firebase'],
    ['get', '/addresses'],
    ['post', '/addresses'],
    ['delete', '/addresses/{address}'],
    ['post', '/reportes'],
    ['get', '/factura/{invoice}'],
    ['patch', '/agricultor/pedidos/{order}/status'],
    ['post', '/admin/usuarios/consultar-dni'],
    ['get', '/admin/reportes/{report}'],
    ['patch', '/admin/reportes/{report}/status'],
]);

it('only documents routes that exist', function () {
    $routes = collect(app('router')->getRoutes()->getRoutes())
        ->flatMap(fn ($route) => collect($route->methods())->map(fn ($method) => strtolower($method).' /'.ltrim($route->uri(), '/')));

    foreach ($this->spec['paths'] as $path => $operations) {
        foreach (array_keys($operations) as $method) {
            expect($routes)->toContain("{$method} {$path}");
        }
    }
});

it('only shows the documentation to admins outside the local environment', function () {
    $this->get('/api/documentation')->assertRedirect(route('login'));

    $this->actingAs(User::factory()->create())->get('/api/documentation')->assertForbidden();

    $this->actingAs(User::factory()->admin()->create())->get('/api/documentation')->assertSuccessful();
});
