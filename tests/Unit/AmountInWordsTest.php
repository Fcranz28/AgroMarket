<?php

use App\Support\AmountInWords;

it('converts integers to spanish words', function (int $number, string $words) {
    expect(AmountInWords::integer($number))->toBe($words);
})->with([
    [0, 'CERO'],
    [1, 'UNO'],
    [15, 'QUINCE'],
    [21, 'VEINTIUNO'],
    [45, 'CUARENTA Y CINCO'],
    [100, 'CIEN'],
    [101, 'CIENTO UNO'],
    [999, 'NOVECIENTOS NOVENTA Y NUEVE'],
    [1000, 'MIL'],
    [1001, 'MIL UNO'],
    [1500, 'MIL QUINIENTOS'],
    [21000, 'VEINTIUN MIL'],
    [100000, 'CIEN MIL'],
    [251300, 'DOSCIENTOS CINCUENTA Y UN MIL TRESCIENTOS'],
    [1000000, 'UN MILLON'],
    [2345678, 'DOS MILLONES TRESCIENTOS CUARENTA Y CINCO MIL SEISCIENTOS SETENTA Y OCHO'],
]);

it('builds the invoice legend with cents', function () {
    expect(AmountInWords::soles(1500.5))->toBe('SON MIL QUINIENTOS CON 50/100 SOLES')
        ->and(AmountInWords::soles(0.999))->toBe('SON UNO CON 00/100 SOLES')
        ->and(AmountInWords::soles(25.07))->toBe('SON VEINTICINCO CON 07/100 SOLES');
});

it('rejects amounts out of range', function () {
    AmountInWords::integer(-1);
})->throws(InvalidArgumentException::class);
