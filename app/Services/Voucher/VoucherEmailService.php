<?php

namespace App\Services\Voucher;

use App\Models\IssuedVoucher;
use App\Models\VoucherRedemption;
use App\Services\MembershipEmailRelayService;
use Illuminate\Support\Facades\Log;

class VoucherEmailService
{
    public function __construct(
        private readonly MembershipEmailRelayService $relay,
        private readonly VoucherPdfService $pdf,
    ) {
    }

    public function sendIssued(IssuedVoucher $voucher): bool
    {
        try {
            return $this->deliverIssued($voucher);
        } catch (\Throwable $exception) {
            Log::warning('Issued voucher email could not be prepared or sent.', [
                'issued_voucher_id' => $voucher->id,
                'recipient' => $voucher->recipient_email,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    private function deliverIssued(IssuedVoucher $voucher): bool
    {
        $voucher->loadMissing('orderItem.order');
        $order = $voucher->orderItem->order;
        $isPrintAtResort = data_get($voucher->metadata, 'delivery_method') === 'print_at_resort';
        $isGift = ! $isPrintAtResort && (strcasecmp($voucher->recipient_email, $order->purchaser_email) !== 0
            || filled(data_get($voucher->metadata, 'gift_from')));
        $emailRecipient = $isPrintAtResort ? $order->purchaser_email : $voucher->recipient_email;

        $result = $this->relay->sendView(
            $isGift ? 'emails.voucher.gift-delivery' : 'emails.voucher.purchase-success',
            compact('voucher', 'order', 'isGift'),
            [
                'to' => $emailRecipient,
                'cc' => $isPrintAtResort
                    ? $this->printCc()
                    : ($isGift ? [$order->purchaser_email] : []),
                'bcc' => $this->bcc(),
                'subject' => $isGift
                    ? 'A Nandini Gift Voucher for You'
                    : 'Your Nandini Voucher Purchase Is Confirmed',
                'attachments' => [[
                    'filename' => $this->pdf->filename($voucher),
                    'content_type' => 'application/pdf',
                    'content_base64' => base64_encode($this->pdf->render($voucher)),
                ]],
            ]
        );

        if ($result['success']) {
            $voucher->forceFill(['delivered_at' => now()])->save();

            return true;
        }

        Log::warning('Issued voucher email could not be sent through relay.', [
            'issued_voucher_id' => $voucher->id,
            'recipient' => $voucher->recipient_email,
            'relay_response' => $result,
        ]);

        return false;
    }

    public function sendRedeemed(VoucherRedemption $redemption): bool
    {
        try {
            return $this->deliverRedeemed($redemption);
        } catch (\Throwable $exception) {
            Log::warning('Voucher redemption email could not be prepared or sent.', [
                'voucher_redemption_id' => $redemption->id,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    private function deliverRedeemed(VoucherRedemption $redemption): bool
    {
        $redemption->loadMissing('issuedVoucher.orderItem.order');
        $voucher = $redemption->issuedVoucher;
        $order = $voucher->orderItem->order;

        $result = $this->relay->sendView('emails.voucher.redeemed', compact('voucher', 'order', 'redemption'), [
            'to' => $voucher->recipient_email,
            'cc' => strcasecmp($voucher->recipient_email, $order->purchaser_email) !== 0
                ? [$order->purchaser_email]
                : [],
            'bcc' => $this->bcc(),
            'subject' => 'Your Nandini Voucher Has Been Redeemed',
        ]);

        if (! $result['success']) {
            Log::warning('Voucher redemption email could not be sent through relay.', [
                'voucher_redemption_id' => $redemption->id,
                'issued_voucher_id' => $voucher->id,
                'relay_response' => $result,
            ]);
        }

        return $result['success'];
    }

    /** @return array<int, string> */
    private function bcc(): array
    {
        return collect(explode(',', (string) config('mail.guest_bcc')))
            ->map(fn(string $recipient): string => trim($recipient))
            ->filter()
            ->values()
            ->all();
    }

    /** @return array<int, string> */
    private function printCc(): array
    {
        return collect(explode(',', (string) config('mail.voucher_print_cc')))
            ->map(fn(string $recipient): string => trim($recipient))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
