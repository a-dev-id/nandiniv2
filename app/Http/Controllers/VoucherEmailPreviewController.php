<?php

namespace App\Http\Controllers;

use App\Models\IssuedVoucher;
use App\Models\VoucherOrder;
use App\Models\VoucherOrderItem;
use App\Models\VoucherRedemption;
use App\Services\Voucher\VoucherPdfService;
use Illuminate\Http\Response;
use Illuminate\View\View;

class VoucherEmailPreviewController extends Controller
{
    public function __invoke(string $token, ?string $template = null): Response|View
    {
        abort_unless($this->validToken($token), 403, 'Invalid mail preview token.');

        $previews = $this->previews();

        if ($template === null) {
            return response($this->indexHtml($token, $previews));
        }

        abort_unless(array_key_exists($template, $previews) || $template === 'voucher-pdf', 404);

        if ($template === 'voucher-pdf') {
            $voucher = $this->sampleData()['voucher'];
            $pdf = app(VoucherPdfService::class);

            return response($pdf->render($voucher), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $pdf->filename($voucher) . '"',
            ]);
        }

        return view($previews[$template]['view'], $previews[$template]['data']);
    }

    private function validToken(string $token): bool
    {
        $expected = (string) config('services.mail_test_token');

        return $expected !== '' && hash_equals($expected, $token);
    }

    private function previews(): array
    {
        ['order' => $order, 'voucher' => $voucher, 'redemption' => $redemption] = $this->sampleData();

        return [
            'purchase-success' => [
                'title' => 'Voucher Purchase Success',
                'subject' => 'Your Nandini Voucher Purchase Is Confirmed',
                'view' => 'emails.voucher.purchase-success',
                'data' => compact('order', 'voucher') + ['isGift' => false],
            ],
            'gift-delivery' => [
                'title' => 'Gift Voucher Delivery',
                'subject' => 'A Nandini Gift Voucher for You',
                'view' => 'emails.voucher.gift-delivery',
                'data' => compact('order', 'voucher') + ['isGift' => true],
            ],
            'redeemed' => [
                'title' => 'Voucher Redeemed',
                'subject' => 'Your Nandini Voucher Has Been Redeemed',
                'view' => 'emails.voucher.redeemed',
                'data' => compact('order', 'voucher', 'redemption'),
            ],
        ];
    }

    private function sampleData(): array
    {
        $order = new VoucherOrder([
            'order_number' => 'NVC-20260716-PREVIEW',
            'purchaser_first_name' => 'Angga',
            'purchaser_last_name' => 'Gardens',
            'purchaser_email' => 'purchaser@example.com',
            'currency' => 'IDR',
            'total_amount' => 2500000,
            'payment_status' => 'paid',
        ]);
        $order->id = 1;

        $item = new VoucherOrderItem(['voucher_order_id' => 1]);
        $item->id = 1;
        $item->setRelation('order', $order);

        $voucher = new IssuedVoucher([
            'voucher_order_item_id' => 1,
            'voucher_code' => 'NJV-PREVIEW-2026',
            'recipient_name' => 'Nandini Guest',
            'recipient_email' => 'recipient@example.com',
            'title' => 'Romantic Jungle Escape',
            'description_snapshot' => '<p>Enjoy an unforgettable escape surrounded by the lush Payangan rainforest.</p>',
            'terms_snapshot' => '<p>Advance reservation is required and the voucher is subject to availability.</p>',
            'original_value' => 2500000,
            'remaining_value' => 1500000,
            'currency' => 'IDR',
            'issued_at' => now(),
            'valid_from' => now(),
            'expires_at' => now()->addYear(),
            'status' => 'partially_redeemed',
            'metadata' => [
                'gift_from' => 'Angga',
                'purchase_for' => 'gift',
                'personal_message' => 'Enjoy this indulgent escape together!',
                'verification_url' => route('voucher.verify', ['token' => 'preview-token']),
            ],
        ]);
        $voucher->id = 1;
        $voucher->setRelation('orderItem', $item);

        $redemption = new VoucherRedemption([
            'issued_voucher_id' => 1,
            'redemption_location' => 'Nandini Jungle Resort',
            'department' => 'Front Office',
            'reference_number' => 'RED-PREVIEW-001',
            'amount' => 1000000,
            'balance_before' => 2500000,
            'balance_after' => 1500000,
            'redeemed_at' => now(),
        ]);
        $redemption->id = 1;
        $redemption->setRelation('issuedVoucher', $voucher);

        return compact('order', 'voucher', 'redemption');
    }

    private function indexHtml(string $token, array $previews): string
    {
        $items = collect($previews)
            ->map(function (array $preview, string $slug) use ($token): string {
                $url = route('voucher-email-preview.show', compact('token') + ['template' => $slug]);

                return '<li><a href="' . e($url) . '" target="_blank">' . e($preview['title']) . '</a><span>' . e($preview['subject']) . '</span></li>';
            })
            ->push('<li><a href="' . e(route('voucher-email-preview.show', compact('token') + ['template' => 'voucher-pdf'])) . '" target="_blank">Voucher PDF</a><span>PDF attachment preview</span></li>')
            ->implode('');

        return '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Voucher Email Previews</title><style>body{margin:0;background:#f6f3ee;color:#172033;font-family:Arial,sans-serif}main{max-width:860px;margin:0 auto;padding:48px 22px}h1{font-family:Georgia,serif;font-size:30px;letter-spacing:3px;text-transform:uppercase;font-weight:400;margin:0 0 12px}p{color:#556070;line-height:1.7;margin:0 0 28px}ul{list-style:none;margin:0;padding:0;border:1px solid #e5ddcf;background:#fff}li{display:flex;gap:18px;justify-content:space-between;align-items:center;padding:18px 20px;border-bottom:1px solid #eee8df}li:last-child{border-bottom:0}a{color:#916b2c;text-transform:uppercase;letter-spacing:2px;font-size:13px;font-weight:700;text-decoration:none}span{color:#667085;font-size:13px;text-align:right}@media(max-width:640px){li{display:block}span{display:block;text-align:left;margin-top:8px}}</style></head><body><main><h1>Voucher Email Previews</h1><p>Sample previews only. No emails are sent.</p><ul>' . $items . '</ul></main></body></html>';
    }
}
