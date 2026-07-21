<?php

namespace Tests\Feature;

use App\Models\IssuedVoucher;
use App\Models\Voucher;
use App\Models\VoucherOrder;
use App\Services\Voucher\VoucherEmailService;
use App\Services\Voucher\VoucherRedemptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class VoucherRedemptionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_fully_redeem_an_active_voucher(): void
    {
        $voucher = $this->issuedVoucher();

        $this->mock(VoucherEmailService::class, function ($mock): void {
            $mock->shouldReceive('sendRedeemed')->once()->andReturn(true);
        });

        $redemption = app(VoucherRedemptionService::class)->redeem($voucher, [
            'department' => 'Front Office',
            'redemption_location' => 'Nandini Jungle by Hanging Gardens',
            'reference_number' => 'FO-1234',
            'notes' => 'Guest presented the voucher PDF.',
        ]);

        $this->assertSame(4000000, $redemption->amount);
        $this->assertSame(0, $redemption->balance_after);
        $this->assertSame('Front Office', $redemption->department);
        $this->assertSame('redeemed', $voucher->fresh()->status);
        $this->assertSame(0, $voucher->fresh()->remaining_value);
        $this->assertNotNull($voucher->fresh()->redeemed_at);
    }

    public function test_voucher_cannot_be_redeemed_before_its_valid_from_date(): void
    {
        $voucher = $this->issuedVoucher(['valid_from' => now()->addDay()]);

        $this->mock(VoucherEmailService::class, function ($mock): void {
            $mock->shouldNotReceive('sendRedeemed');
        });

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Voucher is not valid yet.');

        app(VoucherRedemptionService::class)->redeem($voucher, [
            'department' => 'Front Office',
            'redemption_location' => 'Nandini Jungle by Hanging Gardens',
        ]);
    }

    private function issuedVoucher(array $overrides = []): IssuedVoucher
    {
        $catalogueVoucher = Voucher::factory()->create([
            'selling_price' => 4000000,
            'face_value' => 4000000,
            'allow_partial_redemption' => false,
        ]);
        $order = VoucherOrder::query()->create([
            'order_number' => 'NDN-VCH-' . fake()->unique()->numerify('########'),
            'purchaser_first_name' => 'Test',
            'purchaser_last_name' => 'Guest',
            'purchaser_email' => 'guest@example.com',
            'billing_country_code' => 'ID',
            'currency' => 'IDR',
            'subtotal' => 4000000,
            'total_amount' => 4000000,
            'payment_status' => 'paid',
            'order_status' => 'completed',
        ]);
        $item = $order->items()->create([
            'voucher_id' => $catalogueVoucher->id,
            'voucher_title' => $catalogueVoucher->title,
            'voucher_type' => $catalogueVoucher->voucher_type,
            'quantity' => 1,
            'unit_price' => 4000000,
            'line_total' => 4000000,
            'currency' => 'IDR',
            'recipient_name' => 'Voucher Guest',
            'recipient_email' => 'guest@example.com',
            'voucher_snapshot' => ['allow_partial_redemption' => false],
        ]);

        return IssuedVoucher::query()->create(array_merge([
            'voucher_order_item_id' => $item->id,
            'voucher_id' => $catalogueVoucher->id,
            'voucher_code' => 'NDN-TEST-' . fake()->unique()->numerify('########'),
            'verification_token_hash' => hash('sha256', fake()->uuid()),
            'recipient_name' => 'Voucher Guest',
            'recipient_email' => 'guest@example.com',
            'title' => $catalogueVoucher->title,
            'original_value' => 4000000,
            'remaining_value' => 4000000,
            'currency' => 'IDR',
            'valid_from' => now()->subDay(),
            'expires_at' => now()->addYear(),
            'status' => 'active',
        ], $overrides));
    }
}
