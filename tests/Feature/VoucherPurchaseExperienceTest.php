<?php

namespace Tests\Feature;

use App\Models\Voucher;
use App\Services\Voucher\Cart\VoucherCartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoucherPurchaseExperienceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'domains.voucher' => 'voucher.nandinibali.test',
            'features.disable_voucher_feature' => false,
        ]);
    }

    public function test_discount_prompt_is_always_visible_and_carousel_arrows_are_image_aligned(): void
    {
        $voucher = Voucher::factory()->create(['is_featured' => true]);

        $this->get('http://voucher.nandinibali.test/voucher/' . $voucher->slug)
            ->assertOk()
            ->assertSee('Purchase more voucher to unlock an extra 10% off your cart.')
            ->assertSee('Gift Voucher')
            ->assertSee('A someone special')
            ->assertDontSee('data-preview-price', false);

        $this->get('http://voucher.nandinibali.test/')
            ->assertOk()
            ->assertSee('itemcarousel-prev fold-carousel-arrow fold-image-carousel-arrow home-mobile-image-arrow', false)
            ->assertSee('itemcarousel-next fold-carousel-arrow fold-image-carousel-arrow home-mobile-image-arrow', false);
    }

    public function test_checkout_collects_personal_information_phone_whatsapp_and_country(): void
    {
        $voucher = Voucher::factory()->create();
        app(VoucherCartService::class)->add($voucher, [
            'quantity' => 1,
            'purchase_for' => 'self',
        ]);

        $this->get('http://voucher.nandinibali.test/checkout')
            ->assertOk()
            ->assertSee('Input your personal information')
            ->assertSee('Phone/WhatsApp')
            ->assertSee('Select Country')
            ->assertSee('value="AF"', false)
            ->assertSee('Afghanistan')
            ->assertSee('value="ZW"', false)
            ->assertSee('Zimbabwe');

        $this->post('http://voucher.nandinibali.test/checkout', [
            'purchaser_first_name' => 'Test',
            'purchaser_last_name' => 'Guest',
            'purchaser_email' => 'guest@example.com',
            'purchaser_phone' => '+62 812 3456 7890',
        ])->assertSessionHasErrors('billing_country_code');
    }
}
