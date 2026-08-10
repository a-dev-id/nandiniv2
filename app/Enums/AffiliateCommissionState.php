<?php

namespace App\Enums;

enum AffiliateCommissionState: string
{
    case Estimated = 'estimated';
    case PendingValidation = 'pending_validation';
    case Ineligible = 'ineligible';
    case CalculationUnavailable = 'calculation_unavailable';

    public function label(): string
    {
        return match ($this) {
            self::Estimated => 'Estimated',
            self::PendingValidation => 'Stay completed — pending validation',
            self::Ineligible => 'Not eligible',
            self::CalculationUnavailable => 'Pending calculation',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Estimated => 'info',
            self::PendingValidation => 'warning',
            self::Ineligible => 'danger',
            self::CalculationUnavailable => 'gray',
        };
    }
}
