<?php

namespace App\Contracts\Payments;

use App\Data\Payments\PaymentResult;
use App\Data\Payments\PaymentSessionResult;
use App\Models\VoucherOrder;

interface PaymentGateway
{
    public function createCheckoutSession(VoucherOrder $order): PaymentSessionResult;

    public function retrievePayment(string $paymentId): PaymentResult;

    public function expireCheckoutSession(string $sessionId): void;
}
