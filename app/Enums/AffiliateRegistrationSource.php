<?php

namespace App\Enums;

enum AffiliateRegistrationSource: string
{
    case SelfRegistration = 'self_registration';
    case CreatedByNandini = 'created_by_nandini';

    public function label(): string
    {
        return match ($this) {
            self::SelfRegistration => 'Self Registration',
            self::CreatedByNandini => 'Created by Nandini',
        };
    }
}
