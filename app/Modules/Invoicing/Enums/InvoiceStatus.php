<?php

namespace App\Modules\Invoicing\Enums;

enum InvoiceStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Success = 'success';
    case Error = 'error';

    public function isPending(): bool
    {
        return in_array($this, [self::Pending, self::Processing], true);
    }
}
