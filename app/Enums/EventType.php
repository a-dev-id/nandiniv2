<?php

namespace App\Enums;

enum EventType: string
{
    case Regular = 'regular';

    public function label(): string
    {
        return 'One-time';
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $type): array => [$type->value => $type->label()])
            ->all();
    }
}
