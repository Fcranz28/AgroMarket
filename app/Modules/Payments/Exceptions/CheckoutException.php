<?php

namespace App\Modules\Payments\Exceptions;

use RuntimeException;

/**
 * A checkout step that cannot go on. The message is safe to show to the shopper and the
 * status is the HTTP status the JSON API answers with.
 */
class CheckoutException extends RuntimeException
{
    public function __construct(string $message, public readonly int $status)
    {
        parent::__construct($message);
    }
}
