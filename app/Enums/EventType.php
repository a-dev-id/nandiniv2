<?php

namespace App\Enums;

enum EventType: string
{
    case Regular = 'regular';
    case Weekly = 'weekly';
    case Monthly = 'monthly';
    case Yearly = 'yearly';

    public function label(): string
    {
        return match ($this) {
            self::Regular => 'Regular',
            self::Weekly => 'Weekly',
            self::Monthly => 'Monthly',
            self::Yearly => 'Yearly',
        };
    }

    public function isRecurring(): bool
    {
        return $this !== self::Regular;
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $type): array => [$type->value => $type->label()])
            ->all();
    }
}
