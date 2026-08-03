<?php

namespace Tests\Feature;

use App\Models\Voucher;
use App\Services\Voucher\Cart\VoucherCartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoucherCartTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_uses_database_price_and_ignores_browser_price(): void
    {
        $voucher = Voucher::factory()->create(['selling_price' => 1500000]);

        $service = app(VoucherCartService::class);
        $service->add($voucher, [
            'quantity' => 2,
            'unit_price' => 1,
            'purchase_for' => 'self',
            'recipient_name' => 'Recipient',
            'recipient_email' => 'recipient@example.com',
            'delivery_method' => 'email',
        ]);

        $cart = $service->refresh();

        $this->assertSame(363000, $cart['cart_discount']);
        $this->assertSame(300000, $cart['service_charge']);
        $this->assertSame(330000, $cart['tax']);
        $this->assertSame(3267000, $cart['total']);
        $this->assertSame(1500000, $cart['lines']->first()['unit_price']);
    }

    public function test_cart_uses_the_selected_database_room_upgrade_price(): void
    {
        $voucher = Voucher::factory()->create([
            'selling_price' => 1000000,
            'price_options' => [
                ['key' => 'jungle-view', 'label' => 'Jungle View Villa', 'additional_price' => 0],
                ['key' => 'sunrise-view', 'label' => 'Sunrise View Villa', 'additional_price' => 300000],
            ],
        ]);

        app(VoucherCartService::class)->add($voucher, [
            'quantity' => 1,
            'price_option' => 'sunrise-view',
            'purchase_for' => 'self',
            'delivery_method' => 'email',
        ]);

        $line = app(VoucherCartService::class)->refresh()['lines']->first();

        $this->assertSame('Sunrise View Villa', $line['price_option']['label']);
        $this->assertSame(300000, $line['price_option']['additional_price']);
        $this->assertSame(1300000, $line['unit_price']);
        $this->assertSame(1000000, $line['base_unit_price']);
        $this->assertSame(300000, $line['room_upgrade_unit_price']);
        $this->assertSame(1000000, $line['voucher_subtotal']);
        $this->assertSame(300000, $line['room_upgrade_total']);
        $this->assertSame(130000, $line['service_charge']);
        $this->assertSame(143000, $line['tax']);
        $this->assertSame(1573000, $line['line_total']);
    }

    public function test_cart_rejects_a_room_option_that_is_not_configured_on_the_voucher(): void
    {
        $voucher = Voucher::factory()->create([
            'price_options' => [
                ['key' => 'jungle-view', 'label' => 'Jungle View Villa', 'additional_price' => 0],
            ],
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The selected room option is not available.');

        app(VoucherCartService::class)->add($voucher, [
            'quantity' => 1,
            'price_option' => 'tampered-option',
            'purchase_for' => 'self',
            'delivery_method' => 'email',
        ]);
    }

    public function test_cart_uses_the_base_price_when_room_selection_is_omitted(): void
    {
        $voucher = Voucher::factory()->create([
            'selling_price' => 1000000,
            'price_options' => [
                ['key' => 'jungle-view', 'label' => 'Jungle View Villa', 'additional_price' => 0],
                ['key' => 'sunrise-view', 'label' => 'Sunrise View Villa', 'additional_price' => 300000],
            ],
        ]);

        app(VoucherCartService::class)->add($voucher, [
            'quantity' => 1,
            'purchase_for' => 'self',
            'delivery_method' => 'email',
        ]);

        $line = app(VoucherCartService::class)->refresh()['lines']->first();

        $this->assertNull($line['price_option']);
        $this->assertNull($line['price_option_key']);
        $this->assertSame(1000000, $line['unit_price']);
        $this->assertSame(0, $line['room_upgrade_total']);
    }

    public function test_inactive_voucher_cannot_be_added(): void
    {
        $voucher = Voucher::factory()->create(['is_active' => false]);

        $this->expectException(\InvalidArgumentException::class);

        app(VoucherCartService::class)->add($voucher, [
            'quantity' => 1,
            'purchase_for' => 'gift',
            'recipient_name' => 'Recipient',
            'recipient_email' => 'recipient@example.com',
            'delivery_method' => 'email',
        ]);
    }

    public function test_cart_uses_discounted_database_price(): void
    {
        $voucher = Voucher::factory()->create([
            'selling_price' => 2000000,
            'discount_percentage' => 25,
        ]);

        app(VoucherCartService::class)->add($voucher, [
            'quantity' => 2,
            'purchase_for' => 'self',
            'recipient_name' => 'Recipient',
            'recipient_email' => 'recipient@example.com',
            'delivery_method' => 'email',
        ]);

        $cart = app(VoucherCartService::class)->refresh();

        $this->assertSame(3630000, $cart['subtotal']);
        $this->assertSame(363000, $cart['discount']);
        $this->assertSame(363000, $cart['cart_discount']);
        $this->assertSame(300000, $cart['service_charge']);
        $this->assertSame(330000, $cart['tax']);
        $this->assertSame(3267000, $cart['total']);
        $this->assertSame(1500000, $cart['lines']->first()['unit_price']);
    }

    public function test_print_at_resort_adds_flat_delivery_fee_and_preserves_hotel_note(): void
    {
        $voucher = Voucher::factory()->create(['selling_price' => 1500000]);

        app(VoucherCartService::class)->add($voucher, [
            'quantity' => 2,
            'purchase_for' => 'gift',
            'recipient_name' => 'Recipient',
            'delivery_method' => 'print_at_resort',
            'hotel_note' => 'Please prepare it before check-in.',
        ]);

        $cart = app(VoucherCartService::class)->refresh();
        $line = $cart['lines']->first();

        $this->assertSame(3000000, $line['base_line_total']);
        $this->assertSame(100000, $line['delivery_fee']);
        $this->assertSame(3100000, $line['pre_tax_line_total']);
        $this->assertSame(3751000, $line['line_total']);
        $this->assertSame(375100, $cart['cart_discount']);
        $this->assertSame(310000, $cart['service_charge']);
        $this->assertSame(341000, $cart['tax']);
        $this->assertSame(3375900, $cart['total']);
        $this->assertSame('Please prepare it before check-in.', $line['hotel_note']);
        $this->assertSame('', $line['recipient_email']);
    }

    public function test_cart_discount_is_removed_when_cart_is_cleared(): void
    {
        $voucher = Voucher::factory()->create(['selling_price' => 1000000]);
        $cartService = app(VoucherCartService::class);

        $cartService->add($voucher, [
            'quantity' => 2,
            'purchase_for' => 'self',
            'recipient_name' => 'Guest',
            'recipient_email' => 'guest@example.com',
            'delivery_method' => 'email',
        ]);

        $this->assertSame(242000, $cartService->refresh()['cart_discount']);

        $cartService->clear();
        $cart = $cartService->refresh();

        $this->assertSame(0, $cart['cart_discount']);
        $this->assertSame(0, $cart['discount']);
        $this->assertSame(0, $cart['total']);
    }

    public function test_single_cart_item_does_not_receive_global_discount(): void
    {
        $voucher = Voucher::factory()->create(['selling_price' => 1000000]);

        app(VoucherCartService::class)->add($voucher, [
            'quantity' => 1,
            'purchase_for' => 'self',
            'recipient_name' => 'Guest',
            'recipient_email' => 'guest@example.com',
            'delivery_method' => 'email',
        ]);

        $cart = app(VoucherCartService::class)->refresh();

        $this->assertFalse($cart['global_discount_active']);
        $this->assertSame(0, $cart['cart_discount']);
        $this->assertSame(1000000, $cart['lines']->first()['unit_price']);
        $this->assertSame(1210000, $cart['total']);
    }

    public function test_net_price_does_not_add_service_charge_or_tax(): void
    {
        $voucher = Voucher::factory()->create([
            'selling_price' => 100,
            'price_type' => 'net',
        ]);

        app(VoucherCartService::class)->add($voucher, [
            'quantity' => 1,
            'purchase_for' => 'self',
            'delivery_method' => 'email',
        ]);

        $cart = app(VoucherCartService::class)->refresh();
        $line = $cart['lines']->first();

        $this->assertFalse($line['additional_charges_apply']);
        $this->assertSame(0, $line['service_charge']);
        $this->assertSame(0, $line['tax']);
        $this->assertSame(100, $line['line_total']);
        $this->assertSame(100, $cart['total']);

        $this->get(route('voucher.cart.index'))
            ->assertOk()
            ->assertSee('IDR 100 Net each')
            ->assertDontSee('IDR 100++ each')
            ->assertDontSee('Service (10%)')
            ->assertDontSee('Tax (11%)');

        $this->get(route('voucher.checkout.index'))
            ->assertOk()
            ->assertDontSee('Service (10%)')
            ->assertDontSee('Tax (11%)');
    }

    public function test_self_purchase_always_uses_email_delivery(): void
    {
        $voucher = Voucher::factory()->create(['selling_price' => 1000000]);

        app(VoucherCartService::class)->add($voucher, [
            'quantity' => 1,
            'purchase_for' => 'self',
            'delivery_method' => 'print_at_resort',
            'hotel_note' => 'Attempted print delivery.',
        ]);

        $line = app(VoucherCartService::class)->refresh()['lines']->first();

        $this->assertSame('email', $line['delivery_method']);
        $this->assertSame(0, $line['delivery_fee']);
        $this->assertSame('', $line['hotel_note']);
        $this->assertSame('', $line['recipient_name']);
        $this->assertSame('', $line['recipient_email']);
    }

    public function test_self_purchase_can_be_added_without_name_or_email(): void
    {
        $voucher = Voucher::factory()->create(['selling_price' => 1000000]);

        $response = $this->post(route('voucher.cart.add', $voucher), [
            'quantity' => 1,
            'purchase_for' => 'self',
            'personal_message' => 'A note for my voucher.',
            'delivery_method' => 'email',
        ]);

        $response->assertRedirect(route('voucher.cart.index'));
        $response->assertSessionHasNoErrors();
        $this->assertSame(1, app(VoucherCartService::class)->countUnits());
    }

    public function test_email_gift_cannot_be_added_without_required_recipient_details(): void
    {
        $voucher = Voucher::factory()->create(['selling_price' => 1000000]);

        $response = $this->post(route('voucher.cart.add', $voucher), [
            'quantity' => 1,
            'purchase_for' => 'gift',
            'delivery_method' => 'email',
        ]);

        $response->assertSessionHasErrors(['recipient_name', 'recipient_email']);
        $this->assertSame(0, app(VoucherCartService::class)->countUnits());
    }

    public function test_email_gift_cannot_be_added_with_an_invalid_recipient_email(): void
    {
        $voucher = Voucher::factory()->create(['selling_price' => 1000000]);

        $response = $this->post(route('voucher.cart.add', $voucher), [
            'quantity' => 1,
            'purchase_for' => 'gift',
            'recipient_name' => 'Recipient',
            'recipient_email' => 'not-an-email',
            'delivery_method' => 'email',
        ]);

        $response->assertSessionHasErrors(['recipient_email']);
        $this->assertSame(0, app(VoucherCartService::class)->countUnits());
    }
}
