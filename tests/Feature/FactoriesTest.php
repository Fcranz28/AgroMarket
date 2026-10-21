<?php

use App\Models\Invoice;
use App\Models\OrderItem;
use App\Modules\Catalog\Models\ProductUnit;
use App\Modules\Identity\Models\User;

it('creates users with role states', function () {
    expect(User::factory()->admin()->create()->isAdmin())->toBeTrue()
        ->and(User::factory()->farmer()->create()->isVerified())->toBeFalse()
        ->and(User::factory()->farmer()->verified()->create()->isVerified())->toBeTrue()
        ->and(User::factory()->banned()->create()->is_banned)->toBeTrue();
});

it('creates related marketplace records', function () {
    $item = OrderItem::factory()->create();
    $unit = ProductUnit::factory()->create();
    $invoice = Invoice::factory()->create();

    expect($item->product->user->isFarmer())->toBeTrue()
        ->and($item->order->uuid)->not->toBeEmpty()
        ->and($unit->product)->not->toBeNull()
        ->and($invoice->order)->not->toBeNull();
});
