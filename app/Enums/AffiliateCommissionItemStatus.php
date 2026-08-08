<?php

namespace App\Enums;

enum AffiliateCommissionItemStatus: string
{
    case PendingReview = 'pending_review';
    case Approved = 'approved';
    case Held = 'held';
    case Excluded = 'excluded';
    case IncludedInPayout = 'included_in_payout';
    case Paid = 'paid';

    public function label(): string
    {
        return match ($this) {
            self::PendingReview => 'Pending Validation',
            self::Approved => 'Approved',
            self::Held => 'On Hold',
            self::Excluded => 'Not Eligible',
            self::IncludedInPayout => 'Included in Payout',
            self::Paid => 'Paid',
        };
    }

    public function isFinanciallyLocked(): bool
    {
        return in_array($this, [self::IncludedInPayout, self::Paid], true);
    }
}
