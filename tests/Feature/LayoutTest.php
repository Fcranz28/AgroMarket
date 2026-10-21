<?php

use App\Modules\Catalog\Models\Category;
use App\Modules\Identity\Models\User;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

beforeEach(function () {
    $this->withoutVite();
});

it('shows the navigation, search and login links to guests', function () {
    $category = Category::factory()->create(['name' => 'Frutas']);

    $this->get(route('home'))
        ->assertSuccessful()
        ->assertSee('id="searchForm"', false)
        ->assertSee('value="'.$category->slug.'"', false)
        ->assertSee(route('login'))
        ->assertSee(route('register'))
        ->assertSee(route('products.index'))
        ->assertDontSee(route('logout'))
        ->assertDontSee('Agregar nuevo producto');
});

it('keeps the hooks the cart and autocomplete scripts rely on', function () {
    $this->get(route('home'))
        ->assertSee('id="openCart"', false)
        ->assertSee('cart-button-mobile', false)
        ->assertSee('id="cartSidebar"', false)
        ->assertSee('id="searchSuggestions"', false)
        ->assertSee('id="searchInput"', false);
});

it('shows the user menu to authenticated buyers', function () {
    $buyer = User::factory()->create(['name' => 'Lucía']);

    $this->actingAs($buyer)
        ->get(route('home'))
        ->assertSee('Hola, Lucía')
        ->assertSee(route('orders.index'))
        ->assertSee(route('security'))
        ->assertSee(route('logout'))
        ->assertDontSee(route('admin.dashboard'))
        ->assertDontSee(route('farmer.dashboard'));
});

it('links each role to its panel and shows the add product button', function (Closure $makeUser, string $panelRoute) {
    $user = $makeUser();

    $this->actingAs($user)
        ->get(route('home'))
        ->assertSee(route($panelRoute))
        ->assertSee('Agregar nuevo producto');
})->with([
    'admin' => [fn () => User::factory()->admin()->create(), 'admin.dashboard'],
    'verified farmer' => [fn () => User::factory()->farmer()->verified()->create(), 'farmer.dashboard'],
]);

it('hides the add product button from unverified farmers', function () {
    $this->actingAs(User::factory()->farmer()->create())
        ->get(route('home'))
        ->assertSee(route('farmer.dashboard'))
        ->assertDontSee('Agregar nuevo producto');
});

it('marks the current section as active', function () {
    $html = $this->get(route('about'))->getContent();

    expect($html)->toMatch('/href="'.preg_quote(route('about'), '/').'"\s+class="[^"]*text-brand/');
});

it('renders a button as a link when given an href', function () {
    $html = Blade::render('<x-ui.button href="/demo" variant="secondary">Ir</x-ui.button>');

    expect($html)->toContain('<a href="/demo"')->toContain('border-line')->not->toContain('<button');
});

it('renders a submit button with the danger variant', function () {
    $html = Blade::render('<x-ui.button type="submit" variant="danger">Borrar</x-ui.button>');

    expect($html)->toContain('type="submit"')->toContain('bg-danger');
});

it('shows the validation error of an input and keeps the old value', function () {
    $errors = (new ViewErrorBag)->put('default', new MessageBag(['email' => ['El correo no es válido.']]));
    request()->setLaravelSession(session()->driver());
    session()->flashInput(['email' => 'malo@']);

    view()->share('errors', $errors);

    $html = Blade::render('<x-ui.input name="email" label="Correo" />');

    expect($html)
        ->toContain('El correo no es válido.')
        ->toContain('aria-invalid="true"')
        ->toContain('value="malo@"')
        ->toContain('<label for="email"');
});

it('never echoes the old value of a password input', function () {
    request()->setLaravelSession(session()->driver());
    session()->flashInput(['password' => 'secreto']);

    view()->share('errors', new ViewErrorBag);

    $html = Blade::render('<x-ui.input name="password" type="password" />');

    expect($html)->not->toContain('secreto');
});
