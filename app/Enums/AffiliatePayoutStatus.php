<?php

namespace App\Enums;

enum AffiliatePayoutStatus: string
{
    case Draft = 'draft';
    case Ready = 'ready';
    case Processing = 'processing';
    case Paid = 'paid';
    case Failed = 'failed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return str($this->value)->replace('_', ' ')->title()->toString();
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Paid, self::Cancelled], true);
    }
}
