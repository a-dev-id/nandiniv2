<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 24px; }
        body { margin: 0; color: #344054; font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        .frame { border: 1px solid #d8c6a8; padding: 0 34px 28px; }
        .hero { width: calc(100% + 68px); height: 230px; margin: 0 -34px 30px; object-fit: cover; }
        .voucher-label { margin-top: 30px; text-align: center; color: #0f1d33; font-family: DejaVu Serif, serif; font-size: 14px; letter-spacing: 4px; text-transform: uppercase; }
        .brand { margin-top: 7px; text-align: center; color: #b8945b; font-size: 9px; font-weight: bold; letter-spacing: 2.5px; text-transform: uppercase; }
        .title { margin: 22px 0 8px; text-align: center; color: #0f1d33; font-family: DejaVu Serif, serif; font-size: 27px; font-weight: normal; letter-spacing: 2px; text-transform: uppercase; }
        .rule { width: 72px; margin: 18px auto 22px; border-top: 1px solid #d8c6a8; }
        .message { margin: 0 0 22px; padding: 14px 18px; border-left: 3px solid #b8945b; background: #f8f5ef; font-family: DejaVu Serif, serif; font-size: 14px; font-style: italic; line-height: 1.6; text-align: center; }
        .copy { margin: 0 auto 22px; padding: 0 12px; line-height: 1.65; text-align: center; }
        .copy p { margin: 0 0 8px; }
        .details { width: 100%; margin: 0 0 20px; border-collapse: collapse; }
        .details td { padding: 9px 0; border-bottom: 1px solid #eee8df; }
        .label { width: 38%; color: #667085; font-size: 9px; letter-spacing: 1px; text-transform: uppercase; }
        .value { text-align: right; }
        .code { color: #0f1d33; font-size: 16px; font-weight: bold; letter-spacing: 1px; }
        .gift { margin: 18px 0; padding-top: 14px; border-top: 1px solid #eee8df; line-height: 1.8; }
        .gift strong { color: #243044; }
        .terms { margin-top: 16px; padding-top: 14px; border-top: 1px solid #eee8df; font-size: 9px; line-height: 1.45; }
        .terms h2 { margin: 0 0 5px; color: #243044; font-family: DejaVu Serif, serif; font-size: 11px; }
        .terms ul { margin: 0 0 10px; padding-left: 17px; }
        .terms li { margin-bottom: 3px; }
        .footer { margin-top: 18px; color: #9a8f80; font-size: 8px; letter-spacing: 1px; text-align: center; text-transform: uppercase; }
    </style>
</head>
<body>
<div class="frame">
    @if ($imageUrl)
        <img class="hero" src="{{ $imageUrl }}" alt="">
    @endif

    <div class="voucher-label">Gift Voucher</div>
    <div class="brand">Nandini Jungle by Hanging Gardens</div>
    <h1 class="title">{{ $voucher->title }}</h1>
    <div class="rule"></div>

    @if (filled(data_get($voucher->metadata, 'personal_message')))
        <div class="message">
            &ldquo;{{ data_get($voucher->metadata, 'personal_message') }}&rdquo;<br>
            <span style="font-size:10px;font-style:normal;">- {{ data_get($voucher->metadata, 'gift_from') ?: 'A someone special' }}</span>
        </div>
    @endif

    @if ($voucher->description_snapshot)
        <div class="copy">{!! strip_tags($voucher->description_snapshot, '<p><br><strong><b><em><i><ul><ol><li>') !!}</div>
    @endif

    <table class="details">
        <tr><td class="label">Voucher Code</td><td class="value code">{{ $voucher->voucher_code }}</td></tr>
        <tr><td class="label">Valid From</td><td class="value">{{ $voucher->valid_from?->format('d F Y') ?? 'Immediately' }}</td></tr>
        <tr><td class="label">Valid Until</td><td class="value">{{ $voucher->expires_at?->format('d F Y') ?? 'No expiry date' }}</td></tr>
    </table>

    <div class="gift">
        <div><strong>Gift to:</strong> {{ $voucher->recipient_name }}</div>
        <div><strong>Gift from:</strong> {{ data_get($voucher->metadata, 'gift_from') ?: 'A someone special' }}</div>
    </div>

    <div class="terms">
        <h2>Usage Terms</h2>
        <ul>
            <li>The voucher is valid for 12 months from the date of purchase.</li>
            <li>Advance reservation is required. To redeem your voucher, please contact our Reservations Team via email at reservation@nandinibali.com or WhatsApp at +62 812 3687 1170.</li>
            <li>The voucher is non-refundable, non-transferable, and cannot be exchanged for cash, either in whole or in part.</li>
            <li>The voucher cannot be used in conjunction with any other promotions, discounts, special offers, or packages unless otherwise stated.</li>
            <li>Blackout dates may apply.</li>
        </ul>
        <h2>Payment Terms</h2>
        <ul><li>Please note that all payments are non-refundable once successfully completed.</li></ul>
    </div>

    <div class="footer">Present this voucher when redeeming your Nandini experience</div>
</div>
</body>
</html>
