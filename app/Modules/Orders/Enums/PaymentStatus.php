<?php

namespace App\Modules\Orders\Enums;

enum PaymentStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Refunded = 'refunded';
    case RefundFailed = 'refund_failed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pendiente',
            self::Paid => 'Pagado',
            self::Refunded => 'Reembolsado',
            self::RefundFailed => 'Reembolso fallido',
        };
    }
}
