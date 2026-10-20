<?php

namespace App\Services;

use Stripe\PaymentIntent;
use Stripe\Refund;
use Stripe\StripeClient;

class PaymentGateway
{
    private ?StripeClient $client = null;

    public function __construct(private ?string $secret, private string $currency) {}

    /**
     * @param  array<string, string|int|null>  $metadata
     */
    public function createIntent(int $amountCents, array $metadata): PaymentIntent
    {
        return $this->client()->paymentIntents->create([
            'amount' => $amountCents,
            'currency' => $this->currency,
            'automatic_payment_methods' => ['enabled' => true],
            'metadata' => $metadata,
        ]);
    }

    public function retrieveIntent(string $paymentIntentId): PaymentIntent
    {
        return $this->client()->paymentIntents->retrieve($paymentIntentId);
    }

    public function refund(string $paymentIntentId): Refund
    {
        return $this->client()->refunds->create(['payment_intent' => $paymentIntentId]);
    }

    public function currency(): string
    {
        return $this->currency;
    }

    private function client(): StripeClient
    {
        return $this->client ??= new StripeClient((string) $this->secret);
    }
}
