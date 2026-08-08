<?php

namespace App\Enums;

enum AffiliateCommissionPeriodStatus: string
{
    case Draft = 'draft';
    case UnderReview = 'under_review';
    case Finalized = 'finalized';
    case Reopened = 'reopened';

    public function label(): string
    {
        return str($this->value)->replace('_', ' ')->title()->toString();
    }
}
