<?php

namespace App\Services\Payments\Flywire;

class FlywireWebhookVerifier
{
    public function isValid(string $rawBody, ?string $receivedDigest): bool
    {
        $secret = (string) config('services.flywire.shared_secret');

        if ($secret === '' || ! is_string($receivedDigest) || $receivedDigest === '') {
            return false;
        }

        $expectedDigest = base64_encode(hash_hmac('sha256', $rawBody, $secret, true));

        return hash_equals($expectedDigest, $receivedDigest);
    }
}
