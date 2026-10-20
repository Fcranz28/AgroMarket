<?php

namespace App\Support;

use InvalidArgumentException;

class AmountInWords
{
    private const UNITS = [
        '', 'UNO', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE',
        'DIEZ', 'ONCE', 'DOCE', 'TRECE', 'CATORCE', 'QUINCE', 'DIECISEIS', 'DIECISIETE', 'DIECIOCHO', 'DIECINUEVE',
        'VEINTE', 'VEINTIUNO', 'VEINTIDOS', 'VEINTITRES', 'VEINTICUATRO', 'VEINTICINCO', 'VEINTISEIS', 'VEINTISIETE', 'VEINTIOCHO', 'VEINTINUEVE',
    ];

    private const TENS = ['', '', '', 'TREINTA', 'CUARENTA', 'CINCUENTA', 'SESENTA', 'SETENTA', 'OCHENTA', 'NOVENTA'];

    private const HUNDREDS = [
        '', 'CIENTO', 'DOSCIENTOS', 'TRESCIENTOS', 'CUATROCIENTOS', 'QUINIENTOS',
        'SEISCIENTOS', 'SETECIENTOS', 'OCHOCIENTOS', 'NOVECIENTOS',
    ];

    /**
     * Invoice legend for an amount in soles, e.g. "SON MIL QUINIENTOS CON 50/100 SOLES".
     */
    public static function soles(float $amount): string
    {
        $cents = (int) round($amount * 100);

        return sprintf('SON %s CON %02d/100 SOLES', self::integer(intdiv($cents, 100)), $cents % 100);
    }

    /**
     * Spanish words for a non-negative integer below one billion.
     */
    public static function integer(int $number): string
    {
        if ($number < 0 || $number >= 1_000_000_000) {
            throw new InvalidArgumentException("Amount out of range: {$number}");
        }

        if ($number === 0) {
            return 'CERO';
        }

        $millions = intdiv($number, 1_000_000);
        $thousands = intdiv($number % 1_000_000, 1_000);
        $rest = $number % 1_000;

        $words = [];

        if ($millions > 0) {
            $words[] = $millions === 1 ? 'UN MILLON' : self::belowThousand($millions, true).' MILLONES';
        }

        if ($thousands > 0) {
            $words[] = $thousands === 1 ? 'MIL' : self::belowThousand($thousands, true).' MIL';
        }

        if ($rest > 0) {
            $words[] = self::belowThousand($rest, false);
        }

        return implode(' ', $words);
    }

    /**
     * @param  bool  $apocope  Use "UN"/"VEINTIUN" before MIL and MILLONES.
     */
    private static function belowThousand(int $number, bool $apocope): string
    {
        if ($number === 100) {
            return 'CIEN';
        }

        $words = array_filter([
            self::HUNDREDS[intdiv($number, 100)],
            self::belowHundred($number % 100, $apocope),
        ]);

        return implode(' ', $words);
    }

    private static function belowHundred(int $number, bool $apocope): string
    {
        if ($number < 30) {
            $word = self::UNITS[$number];
        } else {
            $unit = $number % 10;
            $word = self::TENS[intdiv($number, 10)].($unit > 0 ? ' Y '.self::UNITS[$unit] : '');
        }

        return $apocope ? preg_replace('/UNO$/', 'UN', $word) : $word;
    }
}
