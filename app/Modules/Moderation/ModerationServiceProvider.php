<?php

namespace App\Modules\Moderation;

use App\Modules\Catalog\Models\Product;
use App\Modules\Identity\Models\User;
use App\Modules\Moderation\Models\Report;
use App\Modules\ModuleServiceProvider;

class ModerationServiceProvider extends ModuleServiceProvider
{
    /**
     * Bootstrap the module.
     */
    public function boot(): void
    {
        parent::boot();

        User::resolveRelationUsing('reportsReceived', fn (User $user) => $user->hasManyThrough(Report::class, Product::class));
    }
}
