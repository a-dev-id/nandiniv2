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

        $this->assertSame(3000000, $cart['total']);
        $this->assertSame(1500000, $cart['lines']->first()['unit_price']);
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

        $this->assertSame(4000000, $cart['subtotal']);
        $this->assertSame(1000000, $cart['discount']);
        $this->assertSame(3000000, $cart['total']);
        $this->assertSame(1500000, $cart['lines']->first()['unit_price']);
    }
}
