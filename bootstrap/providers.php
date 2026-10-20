<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Modules\Identity\IdentityServiceProvider::class,
    App\Modules\Catalog\CatalogServiceProvider::class,
    App\Modules\Orders\OrdersServiceProvider::class,
    App\Modules\Payments\PaymentsServiceProvider::class,
    App\Modules\Invoicing\InvoicingServiceProvider::class,
    App\Modules\Moderation\ModerationServiceProvider::class,
    App\Modules\Backoffice\BackofficeServiceProvider::class,
];
