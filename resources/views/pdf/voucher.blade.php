<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 22px; }
        body { margin: 0; background: #f5f0e7; color: #344054; font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .outer { border: 1px solid #b8945b; padding: 6px; }
        .inner { position: relative; min-height: 745px; border: 1px solid #d8c6a8; padding: 34px 42px 26px; background: #fffcf7; }
        .brand { color: #a88444; font-family: DejaVu Serif, serif; font-size: 10px; letter-spacing: 3px; text-align: center; text-transform: uppercase; }
        .voucher-label { margin-top: 18px; color: #667085; font-size: 8px; letter-spacing: 3px; text-align: center; text-transform: uppercase; }
        .title { max-width: 600px; margin: 18px auto 0; color: #17233a; font-family: DejaVu Serif, serif; font-size: 25px; font-weight: normal; letter-spacing: 2px; line-height: 1.3; text-align: center; text-transform: uppercase; }
        .gold-rule { width: 72px; margin: 18px auto 20px; border-top: 1px solid #b8945b; }
        .message { margin: 0 auto 20px; color: #596273; font-family: DejaVu Serif, serif; font-size: 13px; font-style: italic; line-height: 1.65; text-align: center; }
        .description { margin: 0 auto 20px; padding: 15px 20px; border-top: 1px solid #d8c6a8; border-bottom: 1px solid #d8c6a8; font-size: 12px; line-height: 1.65; text-align: center; }
        .description p { margin: 0 0 6px; }
        .details { width: 100%; margin: 0 0 18px; border-collapse: collapse; }
        .details td { padding: 8px 0; border-bottom: 1px solid #eee8df; }
        .label { width: 38%; color: #a88444; font-size: 9px; letter-spacing: 1.4px; text-transform: uppercase; }
        .value { color: #17233a; font-size: 12px; text-align: right; }
        .code { font-family: DejaVu Serif, serif; font-size: 15px; letter-spacing: 1px; }
        .gift { width: 100%; margin: 0 0 17px; border-collapse: collapse; }
        .gift td { width: 50%; padding: 5px 0; vertical-align: top; }
        .gift .right { text-align: right; }
        .gift-label { display: block; margin-bottom: 5px; color: #a88444; font-size: 9px; letter-spacing: 1.5px; text-transform: uppercase; }
        .gift-value { color: #17233a; font-family: DejaVu Serif, serif; font-size: 13px; }
        .terms { padding-top: 12px; border-top: 1px solid #d8c6a8; color: #596273; font-size: 11px; line-height: 1.5; }
        .terms h2 { margin: 0 0 4px; color: #17233a; font-family: DejaVu Serif, serif; font-size: 9px; font-weight: normal; letter-spacing: 1px; text-transform: uppercase; }
        .terms ul { margin: 0 0 7px; padding-left: 15px; }
        .terms li { margin-bottom: 3px; }
        .footer { margin-top: 13px; color: #9a8f80; font-size: 9px; letter-spacing: 1.8px; text-align: center; text-transform: uppercase; }
    </style>
</head>
<body>
@php($isGiftVoucher = data_get($voucher->metadata, 'purchase_for') === 'gift')
<div class="outer">
    <div class="inner">
        <div class="brand">Nandini Jungle by Hanging Gardens</div>
        <div class="voucher-label">Gift Voucher</div>
        <h1 class="title">{{ $voucher->title }}</h1>
        <div class="gold-rule"></div>

        @if ($isGiftVoucher && filled(data_get($voucher->metadata, 'personal_message')))
            <div class="message">&ldquo;{{ data_get($voucher->metadata, 'personal_message') }}&rdquo;</div>
        @endif

        @if ($voucher->description_snapshot)
            <div class="description">{!! strip_tags($voucher->description_snapshot, '<p><br><strong><b><em><i><ul><ol><li>') !!}</div>
        @endif

        <table class="details">
            <tr><td class="label">Voucher Code</td><td class="value code">{{ $voucher->voucher_code }}</td></tr>
            <tr><td class="label">Valid From</td><td class="value">{{ $voucher->valid_from?->format('d F Y') ?? 'Immediately' }}</td></tr>
            <tr><td class="label">Valid Until</td><td class="value">{{ $voucher->expires_at?->format('d F Y') ?? 'No expiry date' }}</td></tr>
        </table>

        <table class="gift">
            <tr>
                <td @if (! $isGiftVoucher) colspan="2" @endif><span class="gift-label">Gift to</span><span class="gift-value">{{ $voucher->recipient_name }}</span></td>
                @if ($isGiftVoucher)
                    <td class="right"><span class="gift-label">Gift from</span><span class="gift-value">{{ data_get($voucher->metadata, 'gift_from') ?: 'A someone special' }}</span></td>
                @endif
            </tr>
        </table>

        <div class="terms">
            <h2>Usage Terms</h2>
            <ul>
                <li>The voucher is valid for 12 months from the date of purchase.</li>
                <li>Advance reservation is required. Contact reservation@nandinibali.com or WhatsApp +62 812 3687 1170.</li>
                <li>The voucher is non-refundable, non-transferable, and cannot be exchanged for cash.</li>
                <li>The voucher cannot be combined with other promotions unless otherwise stated. Blackout dates may apply.</li>
            </ul>
            <h2>Payment Terms</h2>
            <ul><li>All payments are non-refundable once successfully completed.</li></ul>
        </div>

        <div class="footer">An invitation to experience the beauty of Bali</div>
    </div>
</div>
</body>
</html>
