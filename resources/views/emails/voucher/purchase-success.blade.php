@php
    $logoUrl = rtrim(config('app.url'), '/') . '/images/logo-njhg.png';
    $isPrintAtResort = data_get($voucher->metadata, 'delivery_method') === 'print_at_resort';
    $isGiftPurchase = (bool) ($isGift ?? data_get($voucher->metadata, 'purchase_for') === 'gift');
    $emailNote = $isPrintAtResort
        ? data_get($voucher->metadata, 'hotel_note')
        : (data_get($voucher->metadata, 'purchase_for', 'self') === 'self'
            ? data_get($voucher->metadata, 'personal_message')
            : null);
    $emailNoteLabel = $isPrintAtResort ? 'Note for the Resort Team' : 'Your Note';
@endphp
<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Voucher Purchase Confirmed</title></head>
<body style="margin:0;padding:0;background:#f6f3ee;color:#243044;font-family:Arial,Helvetica,sans-serif;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="width:100%;background:#f6f3ee;padding:32px 14px;"><tr><td align="center">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;background:#fff;border:1px solid #e5ddcf;border-collapse:collapse;">
<tr><td align="center" style="padding:38px 32px 26px;"><img src="{{ $logoUrl }}" alt="Nandini Jungle by Hanging Gardens" width="170" style="display:block;width:170px;max-width:100%;height:auto;margin:0 auto 24px;border:0;"><p style="margin:0 0 10px;color:#b8945b;font-size:12px;font-weight:bold;letter-spacing:3px;text-transform:uppercase;line-height:1.5;">Nandini Gift Vouchers</p><h1 style="margin:0;color:#0f1d33;font-family:Georgia,'Times New Roman',serif;font-size:27px;line-height:1.35;letter-spacing:4px;text-transform:uppercase;font-weight:normal;">Purchase Confirmed</h1><div style="width:72px;height:1px;background:#d8c6a8;margin:24px auto 0;"></div></td></tr>
<tr><td style="padding:0 34px 38px;"><p style="margin:0 0 18px;font-size:15px;line-height:1.75;color:#344054;">Dear {{ $order->purchaser_first_name }},</p><p style="margin:0 0 20px;font-size:15px;line-height:1.75;color:#344054;">@if ($isGiftPurchase) Thank you for your purchase. Your gift voucher for {{ $voucher->recipient_name }} is confirmed, and your purchase copy is attached as a PDF. @else Thank you for your purchase. Your Nandini voucher is ready and attached to this email as a PDF. @endif</p>
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;margin:0 0 26px;">
@foreach (['Voucher' => $voucher->title, 'Voucher Code' => $voucher->voucher_code, 'Order' => $order->order_number, 'Valid Until' => $voucher->expires_at?->format('d F Y') ?? 'No expiry date'] as $label => $value)
<tr><td style="width:42%;padding:13px 0;border-bottom:1px solid #eee8df;color:#667085;font-size:12px;text-transform:uppercase;letter-spacing:1.4px;">{{ $label }}</td><td style="padding:13px 0;border-bottom:1px solid #eee8df;color:#344054;font-size:14px;text-align:right;{{ $label === 'Voucher Code' ? 'font-weight:bold;color:#0f1d33;' : '' }}">{{ $value }}</td></tr>
@endforeach
</table>
@if ($isPrintAtResort)
<div style="margin:0 0 24px;padding:18px 20px;background:#f8f5ef;border-left:3px solid #b8945b;color:#344054;">
<p style="margin:0 0 8px;color:#b8945b;font-size:11px;font-weight:bold;letter-spacing:1.5px;text-transform:uppercase;">Resort Printing Requested</p>
<p style="margin:0;font-size:15px;line-height:1.7;">This voucher has been selected for resort printing.</p>
</div>
@endif
@if (filled($emailNote))
<div style="margin:0 0 24px;padding:18px 20px;background:#f8f5ef;border-left:3px solid #b8945b;color:#344054;">
<p style="margin:0 0 8px;color:#b8945b;font-size:11px;font-weight:bold;letter-spacing:1.5px;text-transform:uppercase;">{{ $emailNoteLabel }}</p>
<p style="margin:0;font-size:15px;line-height:1.7;white-space:pre-line;">{{ $emailNote }}</p>
</div>
@endif
<p style="margin:0 0 18px;font-size:15px;line-height:1.75;color:#344054;">Please present the voucher code or attached PDF when redeeming your experience.</p>
@include('emails.voucher.partials.purchase-terms')
<p style="margin:0;font-size:15px;line-height:1.75;color:#344054;">Warm regards,<br><strong>Nandini Jungle by Hanging Gardens</strong></p></td></tr>
</table><p style="margin:24px 0 0;font-size:12px;line-height:1.7;color:#9a8f80;text-align:center;">&copy; {{ date('Y') }} Nandini Jungle by Hanging Gardens. All rights reserved.</p>
</td></tr></table></body></html>
