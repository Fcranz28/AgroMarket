<?php

/*
 * Module boundaries. Dependencies only point "down" this graph:
 *
 *   Identity ← Catalog ← Orders ← Payments | Invoicing | Moderation
 *   Backoffice reads from every module and nothing depends on it.
 */

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

arch('nothing depends on the backoffice')
    ->expect('App\Modules\Backoffice')
    ->toOnlyBeUsedIn('App\Modules\Backoffice');
