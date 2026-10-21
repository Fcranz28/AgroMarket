<?php

use App\Modules\Catalog\Livewire\ProductCatalog;
use App\Modules\Catalog\Livewire\SearchBox;
use App\Modules\Catalog\Models\Category;
use App\Modules\Catalog\Models\Product;
use Livewire\Livewire;

beforeEach(function () {
    $this->withoutVite();

    $this->fruits = Category::factory()->create(['name' => 'Frutas', 'slug' => 'frutas']);
    $this->grains = Category::factory()->create(['name' => 'Granos', 'slug' => 'granos']);

    Product::factory()->for($this->fruits)->create(['name' => 'Mango Kent', 'price' => '6.50']);
    Product::factory()->for($this->fruits)->create(['name' => 'Palta Hass', 'price' => '9.90']);
    Product::factory()->for($this->grains)->create(['name' => 'Quinua blanca', 'price' => '12.00', 'description' => 'Grano andino']);
});

it('renders the catalog on the products and search pages', function (string $route) {
    $this->get(route($route))->assertSuccessful()->assertSeeLivewire(ProductCatalog::class);
})->with(['products.index', 'search']);

it('lists every product by default', function () {
    Livewire::test(ProductCatalog::class)
        ->assertSee(['Mango Kent', 'Palta Hass', 'Quinua blanca'])
        ->assertSee('Todos los productos')
        ->assertSee('3 productos');
});

it('filters by category', function () {
    Livewire::test(ProductCatalog::class)
        ->call('selectCategory', 'granos')
        ->assertSee('Quinua blanca')
        ->assertDontSee('Mango Kent')
        ->assertSee('1 producto');
});

it('ignores unknown categories', function () {
    Livewire::withQueryParams(['category' => 'no-existe'])
        ->test(ProductCatalog::class)
        ->assertSee(['Mango Kent', 'Quinua blanca']);
});

it('searches by name and description from the query string', function () {
    Livewire::withQueryParams(['q' => 'andino'])
        ->test(ProductCatalog::class)
        ->assertSee('Resultados para “andino”')
        ->assertSee('Quinua blanca')
        ->assertDontSee('Palta Hass');
});

it('treats LIKE wildcards in the search literally', function () {
    Livewire::test(ProductCatalog::class)
        ->set('q', '%')
        ->assertSee('Ningún producto');
});

it('sorts by price', function (string $sort, array $expectedOrder) {
    Livewire::test(ProductCatalog::class)
        ->set('sort', $sort)
        ->assertSeeInOrder($expectedOrder);
})->with([
    'cheapest first' => ['price-asc', ['Mango Kent', 'Palta Hass', 'Quinua blanca']],
    'most expensive first' => ['price-desc', ['Quinua blanca', 'Palta Hass', 'Mango Kent']],
]);

it('paginates the results', function () {
    Product::factory()->count(ProductCatalog::PER_PAGE)->for($this->grains)->create();

    Livewire::test(ProductCatalog::class)
        ->assertSee(ProductCatalog::PER_PAGE + 3 .' productos')
        ->tap(fn ($component) => expect($component->instance()->products->count())->toBe(ProductCatalog::PER_PAGE))
        ->call('nextPage')
        ->tap(fn ($component) => expect($component->instance()->products->count())->toBe(3));
});

it('clears the filters', function () {
    Livewire::withQueryParams(['q' => 'mango', 'category' => 'frutas'])
        ->test(ProductCatalog::class)
        ->call('resetFilters')
        ->assertSet('q', '')
        ->assertSet('category', 'all')
        ->assertSee('Quinua blanca');
});

it('suggests products once the term has two characters', function () {
    Livewire::test(SearchBox::class)
        ->set('q', 'p')
        ->assertDontSee('Palta Hass')
        ->set('q', 'pa')
        ->assertSee('Palta Hass')
        ->assertSee(route('search', ['q' => 'pa', 'category' => 'all']));
});

it('keeps the current search in the header box', function () {
    $this->get(route('search', ['q' => 'quinua', 'category' => 'granos']))
        ->assertSee('value="quinua"', false);
});
