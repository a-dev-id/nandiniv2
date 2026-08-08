<?php

namespace App\Services\Affiliate\Finance;

use App\Enums\AffiliatePaymentMethod;
use App\Models\AffiliatePaymentProfile;

class PaymentDetailMasker
{
    public function profile(AffiliatePaymentProfile $profile): string
    {
        return match ($profile->payment_method) {
            AffiliatePaymentMethod::Wise => 'Wise · '.$this->email($profile->wise_email),
            AffiliatePaymentMethod::BankTransfer => 'Bank Transfer · '.$this->account($profile->bank_account_number),
        };
    }

    public function email(?string $email): string
    {
        if (blank($email) || ! str_contains($email, '@')) {
            return 'Not provided';
        }

        [$name, $domain] = explode('@', $email, 2);

        return mb_substr($name, 0, 1).'***@'.$domain;
    }

    public function account(?string $number): string
    {
        if (blank($number)) {
            return 'Not provided';
        }

        $last = mb_substr(preg_replace('/\s+/', '', $number), -4);

        return '******'.$last;
    }
}
