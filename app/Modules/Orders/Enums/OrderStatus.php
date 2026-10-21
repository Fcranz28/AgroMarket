<?php

namespace App\Modules\Orders\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pendiente',
            self::Processing => 'En proceso',
            self::Shipped => 'Enviado',
            self::Delivered => 'Entregado',
            self::Cancelled => 'Cancelado',
        };
    }

    /**
     * Color of the status badge (x-ui.badge).
     */
    public function badgeColor(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Processing => 'info',
            self::Shipped => 'brand',
            self::Delivered => 'success',
            self::Cancelled => 'danger',
        };
    }

    /**
     * Steps of the delivery timeline, in order. A cancelled order is not part of it.
     *
     * @return list<self>
     */
    public static function trackingSteps(): array
    {
        return [self::Pending, self::Processing, self::Shipped, self::Delivered];
    }

    /**
     * Delivered or cancelled orders can no longer change.
     */
    public function isFinal(): bool
    {
        return in_array($this, [self::Delivered, self::Cancelled], true);
    }
}
