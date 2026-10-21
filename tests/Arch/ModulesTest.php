<?php

/*
 * Module boundaries. Dependencies only point "down" this graph:
 *
 *   Identity ← Catalog ← Orders ← Payments | Invoicing | Moderation
 *   Backoffice reads from every module and nothing depends on it.
 */

arch('shared kernel does not depend on any module')
    ->expect('App\Modules\Shared')
    ->not->toUse([
        'App\Modules\Identity',
        'App\Modules\Catalog',
        'App\Modules\Orders',
        'App\Modules\Payments',
        'App\Modules\Invoicing',
        'App\Modules\Moderation',
        'App\Modules\Backoffice',
    ]);

arch('identity does not depend on other modules')
    ->expect('App\Modules\Identity')
    ->not->toUse([
        'App\Modules\Catalog',
        'App\Modules\Orders',
        'App\Modules\Payments',
        'App\Modules\Invoicing',
        'App\Modules\Moderation',
        'App\Modules\Backoffice',
    ]);

arch('catalog only depends on identity')
    ->expect('App\Modules\Catalog')
    ->not->toUse([
        'App\Modules\Orders',
        'App\Modules\Payments',
        'App\Modules\Invoicing',
        'App\Modules\Moderation',
        'App\Modules\Backoffice',
    ]);

arch('orders does not depend on the modules built on top of it')
    ->expect('App\Modules\Orders')
    ->not->toUse([
        'App\Modules\Payments',
        'App\Modules\Invoicing',
        'App\Modules\Moderation',
        'App\Modules\Backoffice',
    ]);

arch('payments does not depend on its sibling modules')
    ->expect('App\Modules\Payments')
    ->not->toUse([
        'App\Modules\Invoicing',
        'App\Modules\Moderation',
        'App\Modules\Backoffice',
    ]);

arch('invoicing does not depend on its sibling modules')
    ->expect('App\Modules\Invoicing')
    ->not->toUse([
        'App\Modules\Payments',
        'App\Modules\Moderation',
        'App\Modules\Backoffice',
    ]);

arch('moderation does not depend on its sibling modules')
    ->expect('App\Modules\Moderation')
    ->not->toUse([
        'App\Modules\Payments',
        'App\Modules\Invoicing',
        'App\Modules\Backoffice',
    ]);

arch('nothing depends on the backoffice')
    ->expect('App\Modules\Backoffice')
    ->toOnlyBeUsedIn('App\Modules\Backoffice');

arch('every module provider extends the module base provider')
    ->expect([
        'App\Modules\Identity\IdentityServiceProvider',
        'App\Modules\Catalog\CatalogServiceProvider',
        'App\Modules\Orders\OrdersServiceProvider',
        'App\Modules\Payments\PaymentsServiceProvider',
        'App\Modules\Invoicing\InvoicingServiceProvider',
        'App\Modules\Moderation\ModerationServiceProvider',
        'App\Modules\Backoffice\BackofficeServiceProvider',
    ])
    ->toExtend('App\Modules\ModuleServiceProvider');

test('domain code lives in modules, not in the legacy folders', function (string $folder) {
    $directory = __DIR__."/../../app/{$folder}";
    $phpFiles = is_dir($directory)
        ? iterator_to_array(new RegexIterator(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)), '/\.php$/'))
        : [];

    expect($phpFiles)->toBeEmpty();
})->with(['Models', 'Services', 'Policies', 'Support', 'Exceptions', 'Http/Requests']);
