<?php

use App\Modules\Shared\Money;

it('parses decimal amounts without floating point errors', function (string|int|float $amount, int $cents) {
    expect(Money::fromDecimal($amount)->cents())->toBe($cents);
})->with([
    ['12.50', 1250],
    ['12.5', 1250],
    ['0.10', 10],
    ['1250', 125000],
    ['12.505', 1251],
    ['-3.20', -320],
    [7, 700],
    [0.1 + 0.2, 30],
]);

it('formats amounts', function () {
    expect(Money::fromCents(125050)->toDecimal())->toBe('1250.50')
        ->and(Money::fromCents(125050)->format())->toBe('S/. 1,250.50')
        ->and(Money::fromCents(-5)->toDecimal())->toBe('-0.05')
        ->and(json_encode(['total' => Money::fromCents(990)]))->toBe('{"total":"9.90"}');
});

it('does arithmetic in cents', function () {
    $price = Money::fromDecimal('4.50');

    expect($price->multiply(3)->cents())->toBe(1350)
        ->and($price->add(Money::fromCents(50))->cents())->toBe(500)
        ->and($price->subtract(Money::fromCents(50))->cents())->toBe(400);
});

it('splits the IGV included in a price so base plus IGV is always the price', function (int $cents, int $base, int $igv) {
    $amount = Money::fromCents($cents);

    expect($amount->withoutIgv()->cents())->toBe($base)
        ->and($amount->igv()->cents())->toBe($igv)
        ->and($amount->withoutIgv()->add($amount->igv())->equals($amount))->toBeTrue();
})->with([
    [11800, 10000, 1800],
    [1000, 847, 153],
    [1, 1, 0],
    [999999, 847457, 152542],
]);

it('rejects amounts that are not numbers', function () {
    Money::fromDecimal('doce');
})->throws(InvalidArgumentException::class);
