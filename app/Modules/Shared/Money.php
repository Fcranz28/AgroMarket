<?php

namespace App\Modules\Shared;

use InvalidArgumentException;
use JsonSerializable;
use Stringable;

/**
 * An amount of soles kept as integer cents, so prices, totals and the IGV breakdown never
 * go through floating point arithmetic. Published prices include IGV (18%).
 */
final class Money implements JsonSerializable, Stringable
{
    public const IGV_RATE_PERCENT = 18;

    private function __construct(private readonly int $cents) {}

    public static function fromCents(int $cents): self
    {
        return new self($cents);
    }

    /**
     * From a decimal amount as stored in the database ("12.50"), or an int/float amount.
     */
    public static function fromDecimal(string|int|float $amount): self
    {
        if (is_int($amount)) {
            return new self($amount * 100);
        }

        $amount = is_float($amount) ? sprintf('%.2f', $amount) : trim($amount);

        if (! preg_match('/^(-?)(\d+)(?:\.(\d+))?$/', $amount, $parts)) {
            throw new InvalidArgumentException("Invalid money amount: {$amount}");
        }

        $decimals = str_pad(substr($parts[3] ?? '', 0, 3), 3, '0');
        $cents = (int) $parts[2] * 100 + intdiv((int) $decimals + 5, 10);

        return new self($parts[1] === '-' ? -$cents : $cents);
    }

    public static function zero(): self
    {
        return new self(0);
    }

    public function cents(): int
    {
        return $this->cents;
    }

    public function add(self $other): self
    {
        return new self($this->cents + $other->cents);
    }

    public function subtract(self $other): self
    {
        return new self($this->cents - $other->cents);
    }

    public function multiply(int $quantity): self
    {
        return new self($this->cents * $quantity);
    }

    /**
     * Taxable base of an amount that includes IGV (rounded to the cent).
     */
    public function withoutIgv(): self
    {
        return new self(intdiv($this->cents * 100 * 2 + (100 + self::IGV_RATE_PERCENT), (100 + self::IGV_RATE_PERCENT) * 2));
    }

    /**
     * IGV contained in an amount that includes it; base + IGV always equals the amount.
     */
    public function igv(): self
    {
        return $this->subtract($this->withoutIgv());
    }

    /**
     * Decimal string for the database and the tax provider, e.g. "12.50".
     */
    public function toDecimal(): string
    {
        $sign = $this->cents < 0 ? '-' : '';
        $absolute = abs($this->cents);

        return sprintf('%s%d.%02d', $sign, intdiv($absolute, 100), $absolute % 100);
    }

    public function toFloat(): float
    {
        return $this->cents / 100;
    }

    /**
     * Amount for people, e.g. "S/. 1,250.50".
     */
    public function format(): string
    {
        return 'S/. '.number_format($this->toFloat(), 2);
    }

    public function equals(self $other): bool
    {
        return $this->cents === $other->cents;
    }

    public function jsonSerialize(): string
    {
        return $this->toDecimal();
    }

    public function __toString(): string
    {
        return $this->toDecimal();
    }
}
