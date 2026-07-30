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

        $this->get('http://voucher.nandinibali.test/voucher/'.$voucher->slug)
            ->assertOk()
            ->assertSee('Purchase more vouchers to unlock an extra 10% off your cart.')
            ->assertSee('Gift Voucher')
            ->assertSee('A someone special')
            ->assertDontSee('data-preview-price', false);

        $this->get('http://voucher.nandinibali.test/')
            ->assertOk()
            ->assertSee('itemcarousel-prev fold-carousel-arrow fold-image-carousel-arrow home-mobile-image-arrow', false)
            ->assertSee('itemcarousel-next fold-carousel-arrow fold-image-carousel-arrow home-mobile-image-arrow', false);
    }

    public function test_room_voucher_displays_the_configured_price_option_dropdown(): void
    {
        $voucher = Voucher::factory()->create([
            'selling_price' => 7438017,
            'price_options' => [
                ['key' => 'jungle-view', 'label' => 'Jungle View Villa', 'additional_price' => 0],
                ['key' => 'sunrise-view', 'label' => 'Sunrise View Villa', 'additional_price' => 330579],
                ['key' => 'panoramic-view', 'label' => 'Panoramic Jungle View Villa', 'additional_price' => 661157],
            ],
        ]);

        $this->get('http://voucher.nandinibali.test/voucher/'.$voucher->slug)
            ->assertOk()
            ->assertSee('IDR 7,438,017++')
            ->assertSee('Room Type')
            ->assertDontSee('Room Type (Optional)')
            ->assertSee('name="price_option"', false)
            ->assertDontSee('name="price_option" required', false)
            ->assertDontSee('data-selected-price=', false)
            ->assertSee('Choose another room type')
            ->assertSee('value="" selected', false)
            ->assertSee('Jungle View Villa')
            ->assertSee('Sunrise View Villa (+IDR 330,579)')
            ->assertSee('Panoramic Jungle View Villa (+IDR 661,157)');

        app(VoucherCartService::class)->add($voucher, [
            'quantity' => 1,
            'price_option' => 'sunrise-view',
            'purchase_for' => 'self',
            'delivery_method' => 'email',
        ]);

        $this->get('http://voucher.nandinibali.test/cart')
            ->assertOk()
            ->assertSee('IDR 7,438,017++ each')
            ->assertSee('Room upgrade')
            ->assertSee('+ IDR 330,579')
            ->assertSee('IDR 9,400,002');
    }

    public function test_voucher_page_displays_the_terms_saved_in_filament(): void
    {
        $voucher = Voucher::factory()->create([
            'terms_conditions' => '<h3>Usage Terms</h3><ul><li>The voucher is valid for stay period between 01 November – 19 December 2026.</li><li>Blackout dates may apply.</li></ul><h3>Payment Terms</h3><ul><li>All payments are non-refundable once completed.</li></ul>',
        ]);

        $this->get('http://voucher.nandinibali.test/voucher/'.$voucher->slug)
            ->assertOk()
            ->assertSee('Usage Terms')
            ->assertSee('The voucher is valid for stay period between 01 November – 19 December 2026.')
            ->assertSee('Payment Terms')
            ->assertSee('All payments are non-refundable once completed.')
            ->assertDontSee('The voucher is valid for 12 months from the date of purchase.');
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
