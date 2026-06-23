@php
$logoUrl = asset('images/logo-njhg.png');
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Nandini Inner Circle Password</title>
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

                            <h1 style="margin:0;color:#0f1d33;font-family:Georgia,'Times New Roman',serif;font-size:29px;line-height:1.35;letter-spacing:4px;text-transform:uppercase;font-weight:normal;">
                                Reset Password
                            </h1>

                            <div style="width:72px;height:1px;background:#d8c6a8;margin:24px auto 0;"></div>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0 34px 38px;">
                            <p style="margin:0 0 18px;font-size:15px;line-height:1.75;color:#344054;">
                                Dear {{ $member->full_name ?: 'Member' }},
                            </p>

                            <p style="margin:0 0 22px;font-size:15px;line-height:1.75;color:#344054;">
                                We received a request to reset the password for your Nandini Inner Circle membership account.
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="width:100%;border-collapse:collapse;margin:0 0 26px;">
                                <tr>
                                    <td style="width:42%;padding:13px 0;border-bottom:1px solid #eee8df;color:#667085;font-size:12px;text-transform:uppercase;letter-spacing:1.4px;">Email</td>
                                    <td style="padding:13px 0;border-bottom:1px solid #eee8df;color:#344054;font-size:14px;text-align:right;">{{ $member->email }}</td>
                                </tr>
                                <tr>
                                    <td style="width:42%;padding:13px 0;border-bottom:1px solid #eee8df;color:#667085;font-size:12px;text-transform:uppercase;letter-spacing:1.4px;">Link Expires</td>
                                    <td style="padding:13px 0;border-bottom:1px solid #eee8df;color:#0f1d33;font-size:14px;font-weight:bold;text-align:right;">{{ $expiresInMinutes }} Minutes</td>
                                </tr>
                            </table>

                            <p style="margin:0 0 28px;font-size:15px;line-height:1.75;color:#344054;">
                                Please use the button below to create a new password. This link is valid for a limited time for your security.
                            </p>

                            <table role="presentation" cellspacing="0" cellpadding="0" align="center" style="margin:0 auto 28px;">
                                <tr>
                                    <td align="center" bgcolor="#a67c3d">
                                        <a href="{{ $resetUrl }}" style="display:inline-block;padding:15px 26px;color:#ffffff;background:#a67c3d;font-size:12px;font-weight:bold;letter-spacing:2.5px;line-height:1;text-decoration:none;text-transform:uppercase;">
                                            Reset Password
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 18px;font-size:14px;line-height:1.75;color:#667085;">
                                If you did not request a password reset, no further action is required.
                            </p>

                            <p style="margin:0 0 28px;font-size:15px;line-height:1.75;color:#344054;">
                                Warm regards,<br>
                                <strong>Nandini Jungle by Hanging Gardens</strong>
                            </p>

                            <div style="height:1px;background:#eee8df;margin:0 0 22px;"></div>

                            <p style="margin:0 0 10px;font-size:12px;line-height:1.7;color:#667085;">
                                If the button above does not work, copy and paste this URL into your browser:
                            </p>

                            <p style="margin:0;font-size:12px;line-height:1.7;color:#667085;word-break:break-all;">
                                <a href="{{ $resetUrl }}" style="color:#916b2c;text-decoration:underline;">
                                    {{ $resetUrl }}
                                </a>
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
