<?php

use App\Modules\Catalog\Models\Product;
use App\Modules\Identity\Models\Address;
use App\Modules\Identity\Models\User;
use App\Modules\Orders\Models\Order;
use App\Modules\Orders\Models\OrderItem;

it('lets farmers manage only their own products', function () {
    $farmer = User::factory()->farmer()->verified()->create();
    $own = Product::factory()->forFarmer($farmer)->create();
    $other = Product::factory()->create();

    expect($farmer->can('update', $own))->toBeTrue()
        ->and($farmer->can('delete', $own))->toBeTrue()
        ->and($farmer->can('update', $other))->toBeFalse()
        ->and($farmer->can('delete', $other))->toBeFalse();
});

it('lets only verified farmers and admins publish products', function () {
    expect(User::factory()->farmer()->verified()->create()->can('create', Product::class))->toBeTrue()
        ->and(User::factory()->admin()->create()->can('create', Product::class))->toBeTrue()
        ->and(User::factory()->farmer()->create()->can('create', Product::class))->toBeFalse()
        ->and(User::factory()->create()->can('create', Product::class))->toBeFalse();
});

it('shows orders to their buyer and to the farmers that sell in them', function () {
    $farmer = User::factory()->farmer()->verified()->create();
    $order = Order::factory()->create();
    OrderItem::factory()->for($order)->for(Product::factory()->forFarmer($farmer))->create();

    expect($order->user->can('view', $order))->toBeTrue()
        ->and(User::factory()->create()->can('view', $order))->toBeFalse()
        ->and($farmer->can('viewAsSeller', $order))->toBeTrue()
        ->and($farmer->can('updateStatus', $order))->toBeTrue()
        ->and(User::factory()->farmer()->verified()->create()->can('viewAsSeller', $order))->toBeFalse();
});

it('does not show guest orders through the buyer policy', function () {
    $order = Order::factory()->guest()->create();

    expect(User::factory()->create()->can('view', $order))->toBeFalse();
});

it('lets users delete only their own addresses', function () {
    $user = User::factory()->create();
    $own = Address::create(['user_id' => $user->id, 'address' => 'Jr. Lima 100']);
    $other = Address::create(['user_id' => User::factory()->create()->id, 'address' => 'Av. Grau 200']);

    expect($user->can('delete', $own))->toBeTrue()
        ->and($user->can('delete', $other))->toBeFalse();
});

it('lets admins ban and verify only the right accounts', function () {
    $admin = User::factory()->admin()->create();

    expect($admin->can('ban', User::factory()->create()))->toBeTrue()
        ->and($admin->can('ban', User::factory()->admin()->create()))->toBeFalse()
        ->and($admin->can('ban', $admin))->toBeFalse()
        ->and($admin->can('verify', User::factory()->farmer()->create()))->toBeTrue()
        ->and($admin->can('verify', User::factory()->create()))->toBeFalse();
});
