<?php

namespace App\Data\Payments;

class PaymentResult
{
    public function __construct(
        public readonly string $paymentId,
        public readonly string $status,
        public readonly array $raw = [],
    ) {
    }
}
