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

    public function test_disabled_voucher_feature_redirects_public_routes_home(): void
    {
        config([
            'domains.main' => 'nandinibali.test',
            'domains.voucher' => 'voucher.nandinibali.test',
            'features.disable_voucher_feature' => true,
        ]);

        $this->get('http://voucher.nandinibali.test/')
            ->assertRedirect(route('home'));
    }

    public function test_disabled_voucher_feature_hides_navigation_links(): void
    {
        config(['features.disable_voucher_feature' => true]);

        $this->blade('<x-layouts.navbar />')
            ->assertDontSee('Gift Voucher');
    }

    public function test_disabled_voucher_feature_rejects_flywire_notifications(): void
    {
        config(['features.disable_voucher_feature' => true]);

        $this->postJson(route('api.flywire.notifications'))
            ->assertNotFound();
    }
}
