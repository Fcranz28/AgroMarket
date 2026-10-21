<?php

use App\Modules\Catalog\Models\Product;
use App\Modules\Orders\Services\SessionCart;

beforeEach(function () {
    $this->cart = app(SessionCart::class);
});

it('adds a product in its cheapest unit when no unit is given', function () {
    $product = Product::factory()->create();
    $product->syncUnits([
        ['unit' => 'saco', 'price' => '150.00', 'stock' => 5],
        ['unit' => 'kg', 'price' => '4.50', 'stock' => 40],
    ]);
    $kg = $product->units()->where('unit', 'kg')->sole();

    $result = $this->cart->add($product->id);

    expect($result['added'])->toBeTrue()
        ->and($this->cart->toPaymentPayload())->toBe([['id' => $product->id, 'unit_id' => $kg->id, 'cantidad' => 1]]);
});

it('keeps the same product in different units as separate lines', function () {
    $product = Product::factory()->create();
    $product->syncUnits([
        ['unit' => 'kg', 'price' => '4.50', 'stock' => 40],
        ['unit' => 'saco', 'price' => '150.00', 'stock' => 5],
    ]);
    [$kg, $sack] = $product->units()->orderBy('price')->get()->all();

    $this->cart->add($product->id, $kg->id, 2);
    $this->cart->add($product->id, $sack->id);
    $this->cart->add($product->id, $kg->id);

    $summary = $this->cart->summary();

    expect($summary['lines'])->toHaveCount(2)
        ->and($this->cart->count())->toBe(4)
        ->and($summary['total']->format())->toBe('S/. 163.50');
});

it('caps the quantity at the available stock', function () {
    $product = Product::factory()->create(['stock' => 3]);

    $this->cart->add($product->id, quantity: 2);
    $first = $this->cart->add($product->id, quantity: 5);
    $second = $this->cart->add($product->id);

    expect($first['added'])->toBeTrue()
        ->and($second)->toBe(['added' => false, 'message' => "No hay más stock de {$product->name}."])
        ->and($this->cart->count())->toBe(3);
});

it('does not add products without stock', function () {
    $product = Product::factory()->create(['stock' => 0]);

    expect($this->cart->add($product->id)['added'])->toBeFalse()
        ->and($this->cart->isEmpty())->toBeTrue();
});

it('rejects unknown products and units of other products', function () {
    $product = Product::factory()->create();
    $otherProduct = Product::factory()->create();
    $otherProduct->syncUnits([['unit' => 'kg', 'price' => '3.00', 'stock' => 10]]);

    expect($this->cart->add(999_999)['added'])->toBeFalse()
        ->and($this->cart->add($product->id, $otherProduct->units()->value('id'))['added'])->toBeFalse()
        ->and($this->cart->isEmpty())->toBeTrue();
});

it('updates, caps and removes lines', function () {
    $product = Product::factory()->create(['stock' => 5]);
    $this->cart->add($product->id);
    $key = $this->cart->summary()['lines']->first()['key'];

    $this->cart->update($key, 50);
    expect($this->cart->count())->toBe(5);

    $this->cart->update($key, 2);
    expect($this->cart->count())->toBe(2);

    $this->cart->update($key, 0);
    expect($this->cart->isEmpty())->toBeTrue();
});

it('always prices lines with the current catalog price', function () {
    $product = Product::factory()->create(['price' => '10.00', 'stock' => 10]);
    $this->cart->add($product->id, quantity: 2);

    $product->update(['price' => '12.00']);

    expect($this->cart->summary()['total']->format())->toBe('S/. 24.00');
});

it('drops lines whose product was deleted', function () {
    $kept = Product::factory()->create();
    $deleted = Product::factory()->create();
    $this->cart->add($kept->id);
    $this->cart->add($deleted->id);

    $deleted->delete();

    expect($this->cart->summary()['lines'])->toHaveCount(1)
        ->and($this->cart->toPaymentPayload())->toHaveCount(1);
});

it('empties the cart', function () {
    $this->cart->add(Product::factory()->create()->id);

    $this->cart->clear();

    expect($this->cart->isEmpty())->toBeTrue()
        ->and($this->cart->count())->toBe(0);
});
