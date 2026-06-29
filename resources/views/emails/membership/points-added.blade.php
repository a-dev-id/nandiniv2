@php
$logoUrl = rtrim(config('app.url'), '/') . '/images/logo-njhg.png';
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Points Have Been Added to Your Account</title>
</head>

<body style="margin:0;padding:0;background:#f6f3ee;color:#243044;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="width:100%;background:#f6f3ee;margin:0;padding:32px 14px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="width:100%;max-width:640px;background:#ffffff;border:1px solid #e5ddcf;border-collapse:collapse;">
                    <tr>
                        <td align="center" style="padding:38px 32px 26px;text-align:center;">
                            <img src="{{ $logoUrl }}" alt="Nandini Jungle by Hanging Gardens" width="170" style="display:block;width:170px;max-width:100%;height:auto;margin:0 auto 24px;border:0;">

                            <p style="margin:0 0 10px;color:#b8945b;font-size:12px;font-weight:bold;letter-spacing:3px;text-transform:uppercase;line-height:1.5;">Nandini Inner Circle</p>

                            <h1 style="margin:0;color:#0f1d33;font-family:Georgia,'Times New Roman',serif;font-size:27px;line-height:1.35;letter-spacing:4px;text-transform:uppercase;font-weight:normal;">Points Update</h1>

                            <div style="width:72px;height:1px;background:#d8c6a8;margin:24px auto 0;"></div>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0 34px 38px;">

                            <p style="margin:0 0 18px;font-size:15px;line-height:1.75;color:#344054;">Dear {{ $member->full_name ?: 'Member' }},</p>

                            <p style="margin:0 0 18px;font-size:15px;line-height:1.75;color:#344054;">
                                Congratulations! Your recent stay has been credited to your Nandini Inner Circle membership account, and your points balance has been successfully updated.
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="width:100%;border-collapse:collapse;margin:0 0 26px;">
                                <tr>
                                    <td style="width:42%;padding:13px 0;border-bottom:1px solid #eee8df;color:#667085;font-size:12px;text-transform:uppercase;letter-spacing:1.4px;">Previous Tier</td>
                                    <td style="padding:13px 0;border-bottom:1px solid #eee8df;color:#344054;font-size:14px;text-align:right;">{{ $previousTierLabel }}</td>
                                </tr>
                                <tr>
                                    <td style="width:42%;padding:13px 0;border-bottom:1px solid #eee8df;color:#667085;font-size:12px;text-transform:uppercase;letter-spacing:1.4px;">Previous Points</td>
                                    <td style="padding:13px 0;border-bottom:1px solid #eee8df;color:#344054;font-size:14px;text-align:right;">{{ number_format($previousPoints, 0) }}</td>
                                </tr>
                                <tr>
                                    <td style="width:42%;padding:13px 0;border-bottom:1px solid #eee8df;color:#667085;font-size:12px;text-transform:uppercase;letter-spacing:1.4px;">Points Added</td>
                                    <td style="padding:13px 0;border-bottom:1px solid #eee8df;color:#344054;font-size:14px;text-align:right;">{{ number_format($pointsAdded, 0) }}</td>
                                </tr>
                                <tr>
                                    <td style="width:42%;padding:13px 0;border-bottom:1px solid #eee8df;color:#667085;font-size:12px;text-transform:uppercase;letter-spacing:1.4px;">New Tier</td>
                                    <td style="padding:13px 0;border-bottom:1px solid #eee8df;color:#344054;font-size:14px;text-align:right;">{{ $newTierLabel }}</td>
                                </tr>
                                <tr>
                                    <td style="width:42%;padding:13px 0;border-bottom:1px solid #eee8df;color:#667085;font-size:12px;text-transform:uppercase;letter-spacing:1.4px;">New Points Balance</td>
                                    <td style="padding:13px 0;border-bottom:1px solid #eee8df;color:#344054;font-size:14px;text-align:right;">{{ number_format($totalPoints, 0) }}</td>
                                </tr>
                            </table>

                            <p style="margin:0 0 18px;font-size:15px;line-height:1.75;color:#344054;">
                                Your updated membership status and points balance are now available in your account. You can also view your booking history and browse available rewards and experiences through your membership dashboard.
                            </p>

                            <p style="margin:0 0 24px;font-size:15px;line-height:1.75;color:#344054;">
                                Thank you for being part of Nandini Inner Circle. We look forward to welcoming you back to Nandini Jungle by Hanging Gardens for another memorable stay.
                            </p>

                            <table role="presentation" cellspacing="0" cellpadding="0" align="center" style="margin:0 auto 28px;">
                                <tr>
                                    <td align="center" bgcolor="#a88444">
                                        <a href="{{ $dashboardUrl }}" style="display:inline-block;padding:15px 26px;color:#ffffff;background:#a88444;font-size:12px;font-weight:bold;letter-spacing:2.5px;line-height:1;text-decoration:none;text-transform:uppercase;">View Your Dashboard</a>
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

                <p style="margin:24px 0 0;font-size:12px;line-height:1.7;color:#9a8f80;text-align:center;">&copy; {{ date('Y') }} Nandini Jungle by Hanging Gardens. All rights reserved.</p>
            </td>
        </tr>
    </table>
</body>

</html>
