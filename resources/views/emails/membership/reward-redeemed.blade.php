@php
$logoUrl = rtrim(config('app.url'), '/') . '/images/logo-njhg.png';
$expiresAt = $redemption->expires_at?->format('d F Y') ?? '-';
$redeemedAt = $redemption->created_at?->format('d F Y') ?? now()->format('d F Y');
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Reward Redemption Confirmation</title>
</head>

<body style="margin:0;padding:0;background:#f6f3ee;color:#243044;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="width:100%;background:#f6f3ee;margin:0;padding:32px 14px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="width:100%;max-width:640px;background:#ffffff;border:1px solid #e5ddcf;border-collapse:collapse;">
                    <tr>
                        <td align="center" style="padding:38px 32px 26px;text-align:center;">
                            <img src="{{ $logoUrl }}" alt="Nandini Jungle by Hanging Gardens" width="170" style="display:block;width:170px;max-width:100%;height:auto;margin:0 auto 24px;border:0;">

                            <p style="margin:0 0 10px;color:#b8945b;font-size:12px;font-weight:bold;letter-spacing:3px;text-transform:uppercase;line-height:1.5;">
                                Nandini Inner Circle
                            </p>

                            <h1 style="margin:0;color:#0f1d33;font-family:Georgia,'Times New Roman',serif;font-size:27px;line-height:1.35;letter-spacing:4px;text-transform:uppercase;font-weight:normal;">
                                Reward Redeemed
                            </h1>

                            <div style="width:72px;height:1px;background:#d8c6a8;margin:24px auto 0;"></div>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0 34px 38px;">
                            <p style="margin:0 0 18px;font-size:15px;line-height:1.75;color:#344054;">
                                Dear {{ $member->full_name ?: 'Member' }},
                            </p>

                            <p style="margin:0 0 18px;font-size:15px;line-height:1.75;color:#344054;">
                                Thank you for redeeming your Nandini Inner Circle points. Your reward request has been received and is now pending confirmation from our team.
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="width:100%;border-collapse:collapse;margin:0 0 26px;">
                                <tr>
                                    <td style="width:42%;padding:13px 0;border-bottom:1px solid #eee8df;color:#667085;font-size:12px;text-transform:uppercase;letter-spacing:1.4px;">Reward</td>
                                    <td style="padding:13px 0;border-bottom:1px solid #eee8df;color:#344054;font-size:14px;text-align:right;">{{ $redemption->reward_name }}</td>
                                </tr>
                                <tr>
                                    <td style="width:42%;padding:13px 0;border-bottom:1px solid #eee8df;color:#667085;font-size:12px;text-transform:uppercase;letter-spacing:1.4px;">Redemption Code</td>
                                    <td style="padding:13px 0;border-bottom:1px solid #eee8df;color:#0f1d33;font-size:14px;font-weight:bold;text-align:right;">{{ $redemption->redemption_code }}</td>
                                </tr>
                                <tr>
                                    <td style="width:42%;padding:13px 0;border-bottom:1px solid #eee8df;color:#667085;font-size:12px;text-transform:uppercase;letter-spacing:1.4px;">Points Used</td>
                                    <td style="padding:13px 0;border-bottom:1px solid #eee8df;color:#344054;font-size:14px;text-align:right;">{{ number_format((int) $redemption->points_used, 0) }}</td>
                                </tr>
                                <tr>
                                    <td style="width:42%;padding:13px 0;border-bottom:1px solid #eee8df;color:#667085;font-size:12px;text-transform:uppercase;letter-spacing:1.4px;">Redeemed On</td>
                                    <td style="padding:13px 0;border-bottom:1px solid #eee8df;color:#344054;font-size:14px;text-align:right;">{{ $redeemedAt }}</td>
                                </tr>
                                <tr>
                                    <td style="width:42%;padding:13px 0;border-bottom:1px solid #eee8df;color:#667085;font-size:12px;text-transform:uppercase;letter-spacing:1.4px;">Valid Until</td>
                                    <td style="padding:13px 0;border-bottom:1px solid #eee8df;color:#344054;font-size:14px;text-align:right;">{{ $expiresAt }}</td>
                                </tr>
                                @if ($redeemDate || $redeemTime)
                                <tr>
                                    <td style="width:42%;padding:13px 0;border-bottom:1px solid #eee8df;color:#667085;font-size:12px;text-transform:uppercase;letter-spacing:1.4px;">Preferred Time</td>
                                    <td style="padding:13px 0;border-bottom:1px solid #eee8df;color:#344054;font-size:14px;text-align:right;">{{ $redeemDate ?: '-' }} {{ $redeemTime ? 'at ' . $redeemTime : '' }}</td>
                                </tr>
                                @endif
                            </table>

                            @if ($specialRequest)
                            <p style="margin:0 0 18px;font-size:15px;line-height:1.75;color:#344054;">
                                Special request: {{ $specialRequest }}
                            </p>
                            @endif

                            <p style="margin:0 0 18px;font-size:15px;line-height:1.75;color:#344054;">
                                Please present your redemption code to our team when enjoying your reward. Experience redemption is subject to availability, and your voucher is valid for one month from the redemption date.
                            </p>

                            <table role="presentation" cellspacing="0" cellpadding="0" align="center" style="margin:0 auto 28px;">
                                <tr>
                                    <td align="center" bgcolor="#a88444">
                                        <a href="{{ $thankYouUrl }}" style="display:inline-block;padding:15px 26px;color:#ffffff;background:#a88444;font-size:12px;font-weight:bold;letter-spacing:2.5px;line-height:1;text-decoration:none;text-transform:uppercase;">
                                            View Redemption
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0;font-size:15px;line-height:1.75;color:#344054;">
                                Warm regards,<br>
                                <strong>Nandini Jungle by Hanging Gardens</strong>
                            </p>
                        </td>
                    </tr>
                </table>

                <p style="margin:24px 0 0;font-size:12px;line-height:1.7;color:#9a8f80;text-align:center;">
                    &copy; {{ date('Y') }} Nandini Jungle by Hanging Gardens. All rights reserved.
                </p>
            </td>
        </tr>
    </table>
</body>

</html>
