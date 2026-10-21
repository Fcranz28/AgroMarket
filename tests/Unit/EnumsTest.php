<?php

use App\Modules\Invoicing\Enums\InvoiceStatus;
use App\Modules\Orders\Enums\OrderStatus;
use App\Modules\Orders\Enums\PaymentStatus;

it('marks only delivered and cancelled orders as final', function (OrderStatus $status, bool $isFinal) {
    expect($status->isFinal())->toBe($isFinal);
})->with([
    [OrderStatus::Pending, false],
    [OrderStatus::Processing, false],
    [OrderStatus::Shipped, false],
    [OrderStatus::Delivered, true],
    [OrderStatus::Cancelled, true],
]);

it('keeps the stored values of the existing database rows', function () {
    expect(OrderStatus::Cancelled->value)->toBe('cancelled')
        ->and(PaymentStatus::RefundFailed->value)->toBe('refund_failed')
        ->and(InvoiceStatus::Success->value)->toBe('success');
});

it('has spanish labels for the interface', function () {
    expect(OrderStatus::Shipped->label())->toBe('Enviado')
        ->and(PaymentStatus::Refunded->label())->toBe('Reembolsado');
});

it('treats pending and processing invoices as pending', function () {
    expect(InvoiceStatus::Processing->isPending())->toBeTrue()
        ->and(InvoiceStatus::Success->isPending())->toBeFalse();
});
