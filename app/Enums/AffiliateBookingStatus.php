<?php

namespace App\Enums;

enum AffiliateBookingStatus: string
{
    case Confirmed = 'confirmed';
    case InHouse = 'in_house';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case NoShow = 'no_show';
    case Refunded = 'refunded';
    case Unknown = 'unknown';

    public function label(): string
    {
        return match ($this) {
            self::NoShow => 'No-show',
            default => str($this->value)->replace('_', ' ')->title()->toString(),
        };
    }

    public function isIneligible(): bool
    {
        return in_array($this, [self::Cancelled, self::NoShow, self::Refunded], true);
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Confirmed => 'info',
            self::InHouse => 'primary',
            self::Completed => 'success',
            self::Cancelled, self::NoShow => 'danger',
            self::Refunded, self::Unknown => 'gray',
        };
    }
}
