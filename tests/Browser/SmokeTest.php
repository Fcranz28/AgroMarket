<?php

use App\Modules\Catalog\Models\Product;

beforeEach(function () {
    Product::factory()->count(3)->create();
});

it('loads the public pages without JavaScript errors', function () {
    $pages = visit(['/', '/productos', '/buscar?q=a', '/nosotros', '/contacto', '/login', '/register']);

    $pages->assertNoJavaScriptErrors();
});
