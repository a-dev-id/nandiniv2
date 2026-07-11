<?php

namespace App\Data\Payments;

class PaymentSessionResult
{
    public function __construct(
        public readonly string $sessionId,
        public readonly ?string $redirectUrl,
        public readonly array $raw = [],
    ) {
    }
}
