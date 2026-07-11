<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoucherRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_voucher_homepage_uses_voucher_domain(): void
    {
        config(['domains.voucher' => 'voucher.nandinibali.test']);

        $this->get('http://voucher.nandinibali.test/')->assertOk();
        $this->assertSame('http://voucher.nandinibali.test', route('voucher.index'));
    }
}
