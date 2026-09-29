<?php

namespace Tests\Unit;

use App\Services\Affiliate\AffiliateCodeGenerator;
use PHPUnit\Framework\TestCase;

class AffiliateCodeGeneratorTest extends TestCase
{
    public function test_it_generates_the_njhg_prefix_followed_by_five_digits(): void
    {
        $generator = new AffiliateCodeGenerator;

        foreach (range(1, 25) as $iteration) {
            $this->assertMatchesRegularExpression('/\ANJHG\d{5}\z/', $generator->candidate());
        }
    }
}
