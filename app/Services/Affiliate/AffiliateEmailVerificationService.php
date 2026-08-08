<?php

namespace App\Services\Affiliate;

use App\Models\Affiliate;
use App\Services\MembershipEmailRelayService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class AffiliateEmailVerificationService
{
    public function __construct(private readonly MembershipEmailRelayService $relay) {}

    public function send(Affiliate $affiliate): bool
    {
        $verificationUrl = URL::temporarySignedRoute(
            'affiliate.verification.verify',
            now()->addHours(24),
            [
                'affiliate' => $affiliate->getKey(),
                'hash' => sha1($affiliate->email),
            ],
        );

        $result = $this->relay->sendView('emails.affiliate.verify-email', [
            'affiliate' => $affiliate,
            'verificationUrl' => $verificationUrl,
        ], [
            'to' => $affiliate->email,
            'subject' => 'Verify Your Nandini Partner Circle Email',
        ]);

        if (! $result['success']) {
            Log::warning('Affiliate verification email could not be sent through relay.', [
                'affiliate_id' => $affiliate->getKey(),
                'email' => $affiliate->email,
                'relay_response' => $result,
            ]);
        }

        return $result['success'];
    }
}
