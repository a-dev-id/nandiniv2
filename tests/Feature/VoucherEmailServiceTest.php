<?php

namespace Tests\Feature;

use App\Models\IssuedVoucher;
use App\Models\Voucher;
use App\Models\VoucherOrder;
use App\Models\VoucherRedemption;
use App\Services\MembershipEmailRelayService;
use App\Services\Voucher\VoucherEmailService;
use App\Services\Voucher\VoucherPdfService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class VoucherEmailServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_gift_delivery_and_separate_purchaser_confirmation_are_sent(): void
    {
        config([
            'mail.guest_bcc' => 'news@nandinibali.com,manager@nandinibali.com',
            'mail.voucher_purchase_cc' => 'reservation@nandinibali.com',
        ]);
        [$order, $voucher] = $this->voucherFixture('receiver@example.com', 'buyer@example.com', 'Buyer');

        $this->mock(VoucherPdfService::class, function ($mock) use ($voucher): void {
            $mock->shouldReceive('filename')->once()->with($voucher)->andReturn('voucher.pdf');
            $mock->shouldReceive('render')->once()->with($voucher)->andReturn('%PDF-sample');
        });

        $this->mock(MembershipEmailRelayService::class, function ($mock) use ($order, $voucher): void {
            $mock->shouldReceive('sendView')->once()->with(
                'emails.voucher.gift-delivery',
                Mockery::on(fn(array $data): bool => $data['voucher']->is($voucher) && $data['order']->is($order)),
                Mockery::on(fn(array $payload): bool => $payload['to'] === 'receiver@example.com'
                    && $payload['cc'] === []
                    && $payload['bcc'] === ['news@nandinibali.com', 'manager@nandinibali.com']
                    && $payload['attachments'][0]['filename'] === 'voucher.pdf'
                    && base64_decode($payload['attachments'][0]['content_base64'], true) === '%PDF-sample')
            )->andReturn($this->success());
            $mock->shouldReceive('sendView')->once()->with(
                'emails.voucher.purchase-success',
                Mockery::on(fn(array $data): bool => $data['voucher']->is($voucher)
                    && $data['order']->is($order)
                    && $data['isGift'] === true),
                Mockery::on(fn(array $payload): bool => $payload['to'] === 'buyer@example.com'
                    && $payload['cc'] === ['reservation@nandinibali.com']
                    && $payload['bcc'] === ['news@nandinibali.com', 'manager@nandinibali.com']
                    && $payload['attachments'][0]['filename'] === 'voucher.pdf')
            )->andReturn($this->success());
        });

        $this->assertTrue(app(VoucherEmailService::class)->sendIssued($voucher));
        $this->assertNotNull($voucher->fresh()->delivered_at);
    }

    public function test_redemption_email_keeps_purchaser_cc_and_env_bcc(): void
    {
        config(['mail.guest_bcc' => 'archive@nandinibali.com']);
        [$order, $voucher] = $this->voucherFixture('receiver@example.com', 'buyer@example.com', 'Buyer');
        $redemption = $voucher->redemptions()->create([
            'amount' => 500000,
            'balance_before' => 1000000,
            'balance_after' => 500000,
            'redeemed_at' => now(),
        ]);

        $this->mock(MembershipEmailRelayService::class, function ($mock) use ($order, $voucher, $redemption): void {
            $mock->shouldReceive('sendView')->once()->with(
                'emails.voucher.redeemed',
                Mockery::on(fn(array $data): bool => $data['voucher']->is($voucher)
                    && $data['order']->is($order)
                    && $data['redemption']->is($redemption)),
                Mockery::on(fn(array $payload): bool => $payload['to'] === 'receiver@example.com'
                    && $payload['cc'] === ['buyer@example.com']
                    && $payload['bcc'] === ['archive@nandinibali.com'])
            )->andReturn($this->success());
        });

        $this->assertTrue(app(VoucherEmailService::class)->sendRedeemed($redemption));
    }

    public function test_print_at_resort_delivery_ccs_the_configured_operational_recipients(): void
    {
        config([
            'mail.voucher_print_cc' => 'reservation@nandinibali.com,frontoffice@example.com',
            'mail.voucher_purchase_cc' => 'reservation@nandinibali.com',
            'mail.guest_bcc' => 'archive@nandinibali.com',
        ]);
        [$order, $voucher] = $this->voucherFixture('receiver@example.com', 'buyer@example.com', null);
        $voucher->forceFill(['metadata' => array_merge($voucher->metadata, [
            'delivery_method' => 'print_at_resort',
        ])])->save();

        $this->mock(VoucherPdfService::class, function ($mock) use ($voucher): void {
            $mock->shouldReceive('filename')->once()->with($voucher)->andReturn('voucher.pdf');
            $mock->shouldReceive('render')->once()->with($voucher)->andReturn('%PDF-sample');
        });

        $this->mock(MembershipEmailRelayService::class, function ($mock): void {
            $mock->shouldReceive('sendView')->once()->with(
                'emails.voucher.purchase-success',
                Mockery::type('array'),
                Mockery::on(fn(array $payload): bool => $payload['to'] === 'buyer@example.com'
                    && $payload['cc'] === ['reservation@nandinibali.com', 'frontoffice@example.com']
                    && $payload['bcc'] === ['archive@nandinibali.com'])
            )->andReturn($this->success());
        });

        $this->assertTrue(app(VoucherEmailService::class)->sendIssued($voucher));
    }

    public function test_empty_purchase_cc_disables_general_purchase_copy(): void
    {
        config([
            'mail.voucher_purchase_cc' => '',
            'mail.guest_bcc' => '',
        ]);
        [$order, $voucher] = $this->voucherFixture('receiver@example.com', 'buyer@example.com', 'Buyer');

        $this->mock(VoucherPdfService::class, function ($mock) use ($voucher): void {
            $mock->shouldReceive('filename')->once()->with($voucher)->andReturn('voucher.pdf');
            $mock->shouldReceive('render')->once()->with($voucher)->andReturn('%PDF-sample');
        });

        $this->mock(MembershipEmailRelayService::class, function ($mock): void {
            $mock->shouldReceive('sendView')->once()->with(
                'emails.voucher.gift-delivery',
                Mockery::type('array'),
                Mockery::on(fn(array $payload): bool => $payload['cc'] === [])
            )->andReturn($this->success());
            $mock->shouldReceive('sendView')->once()->with(
                'emails.voucher.purchase-success',
                Mockery::type('array'),
                Mockery::on(fn(array $payload): bool => $payload['to'] === 'buyer@example.com'
                    && $payload['cc'] === [])
            )->andReturn($this->success());
        });

        $this->assertTrue(app(VoucherEmailService::class)->sendIssued($voucher));
    }

    public function test_gift_pdf_uses_the_new_heading_anonymous_sender_and_no_price(): void
    {
        [, $voucher] = $this->voucherFixture('receiver@example.com', 'buyer@example.com', null);

        $html = view('pdf.voucher', ['voucher' => $voucher, 'imageUrl' => null])->render();

        $this->assertStringContainsString('Gift Voucher', $html);
        $this->assertStringContainsString('Nandini Jungle by Hanging Gardens', $html);
        $this->assertStringContainsString('A someone special', $html);
        $this->assertStringNotContainsString('IDR', $html);
        $this->assertStringNotContainsString('Original Value', $html);
        $this->assertStringNotContainsString('Remaining Value', $html);
    }

    public function test_notes_are_routed_to_the_correct_email_or_pdf(): void
    {
        [$order, $voucher] = $this->voucherFixture('receiver@example.com', 'buyer@example.com', 'Buyer');

        $voucher->forceFill(['metadata' => [
            'purchase_for' => 'self',
            'delivery_method' => 'email',
            'personal_message' => 'Private purchaser note.',
        ]])->save();

        $selfEmail = view('emails.voucher.purchase-success', compact('order', 'voucher'))->render();
        $selfPdf = view('pdf.voucher', compact('voucher'))->render();

        $this->assertStringContainsString('Private purchaser note.', $selfEmail);
        $this->assertStringNotContainsString('Private purchaser note.', $selfPdf);
        $this->assertStringNotContainsString('Gift from', $selfPdf);
        $this->assertStringNotContainsString('A someone special', $selfPdf);

        $voucher->forceFill(['metadata' => [
            'purchase_for' => 'gift',
            'delivery_method' => 'email',
            'gift_from' => 'Buyer',
            'personal_message' => 'A message for the voucher receiver.',
        ]])->save();

        $giftEmail = view('emails.voucher.gift-delivery', compact('order', 'voucher'))->render();
        $giftPdf = view('pdf.voucher', compact('voucher'))->render();

        $this->assertStringNotContainsString('A message for the voucher receiver.', $giftEmail);
        $this->assertStringContainsString('A message for the voucher receiver.', $giftPdf);

        $voucher->forceFill(['metadata' => [
            'purchase_for' => 'gift',
            'delivery_method' => 'print_at_resort',
            'gift_from' => 'Buyer',
            'personal_message' => 'Printed message for the receiver.',
            'hotel_note' => 'Please prepare the printed voucher before arrival.',
        ]])->save();

        $printEmail = view('emails.voucher.purchase-success', compact('order', 'voucher'))->render();
        $printPdf = view('pdf.voucher', compact('voucher'))->render();

        $this->assertStringContainsString('Please prepare the printed voucher before arrival.', $printEmail);
        $this->assertStringNotContainsString('Printed message for the receiver.', $printEmail);
        $this->assertStringContainsString('Printed message for the receiver.', $printPdf);
        $this->assertStringNotContainsString('Please prepare the printed voucher before arrival.', $printPdf);
    }

    public function test_voucher_email_previews_are_protected_and_render(): void
    {
        config(['services.mail_test_token' => 'preview-secret']);

        $this->get('/voucher-email-preview/wrong/purchase-success')->assertForbidden();
        $this->get('/voucher-email-preview/preview-secret/purchase-success')
            ->assertOk()
            ->assertSee('Purchase Confirmed')
            ->assertDontSee('IDR 1,000,000');
        $this->get('/voucher-email-preview/preview-secret/gift-delivery')
            ->assertOk()
            ->assertSee('A Gift for You');
        $this->get('/voucher-email-preview/preview-secret/redeemed')
            ->assertOk()
            ->assertSee('Voucher Redeemed')
            ->assertDontSee('Amount Used')
            ->assertDontSee('Remaining Value')
            ->assertDontSee('Location');
        $this->get('/voucher-email-preview/preview-secret/voucher-pdf')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    private function voucherFixture(string $recipientEmail, string $purchaserEmail, ?string $giftFrom): array
    {
        $catalogueVoucher = Voucher::factory()->create(['face_value' => 1000000]);
        $order = VoucherOrder::query()->create([
            'order_number' => 'NJV-EMAIL-' . fake()->unique()->numerify('####'),
            'purchaser_first_name' => 'Test',
            'purchaser_last_name' => 'Buyer',
            'purchaser_email' => $purchaserEmail,
            'billing_country_code' => 'ID',
            'currency' => 'IDR',
            'subtotal' => 1000000,
            'total_amount' => 1000000,
            'payment_status' => 'paid',
            'order_status' => 'completed',
        ]);
        $item = $order->items()->create([
            'voucher_id' => $catalogueVoucher->id,
            'voucher_title' => $catalogueVoucher->title,
            'voucher_type' => $catalogueVoucher->voucher_type,
            'quantity' => 1,
            'unit_price' => 1000000,
            'line_total' => 1000000,
            'currency' => 'IDR',
            'recipient_name' => 'Gift Receiver',
            'recipient_email' => $recipientEmail,
            'voucher_snapshot' => ['gift_from' => $giftFrom],
        ]);
        $voucher = IssuedVoucher::query()->create([
            'voucher_order_item_id' => $item->id,
            'voucher_id' => $catalogueVoucher->id,
            'voucher_code' => 'NJV-' . fake()->unique()->numerify('########'),
            'verification_token_hash' => hash('sha256', fake()->uuid()),
            'recipient_name' => 'Gift Receiver',
            'recipient_email' => $recipientEmail,
            'title' => $catalogueVoucher->title,
            'original_value' => 1000000,
            'remaining_value' => 1000000,
            'currency' => 'IDR',
            'status' => 'active',
            'metadata' => [
                'gift_from' => $giftFrom,
                'purchase_for' => 'gift',
                'personal_message' => 'Enjoy your stay!',
            ],
        ]);

        return [$order, $voucher];
    }

    private function success(): array
    {
        return ['success' => true, 'status' => 200, 'response' => null, 'error' => null];
    }
}
