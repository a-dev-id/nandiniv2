<?php

namespace App\Enums;

enum AffiliatePaymentMethod: string
{
    case Wise = 'wise';
    case BankTransfer = 'bank_transfer';

    public function label(): string
    {
        return match ($this) {
            self::Wise => 'Wise',
            self::BankTransfer => 'Bank Transfer',
        };
    }
}
