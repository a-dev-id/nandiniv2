<?php

namespace App\Services\Affiliate;

class AffiliateCodeGenerator
{
    private const PREFIX = 'NJHG';

    public function candidate(): string
    {
        return self::PREFIX.random_int(10000, 99999);
    }
}
