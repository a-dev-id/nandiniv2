@php
$logoUrl = asset('images/logo-njhg.png');
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Verify Your Nandini Inner Circle Email</title>
</head>

<body style="margin: 0; padding: 0; background-color: #F7F7F7; font-family: Arial, Helvetica, sans-serif; color: #0f172a;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="width: 100%; background-color: #F7F7F7; margin: 0; padding: 0;">
        <tr>
            <td align="center" style="padding: 42px 20px 24px;">
                <img src="{{ $logoUrl }}" alt="Nandini Jungle by Hanging Gardens" style="display: block; max-width: 180px; width: 180px; height: auto; margin: 0 auto;">
            </td>
        </tr>

        <tr>
            <td align="center" style="padding: 0 20px 48px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="width: 100%; max-width: 640px; background-color: #ffffff; margin: 0 auto; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 48px 42px 44px;">

                            <p style="margin: 0 0 12px; font-size: 12px; line-height: 1.6; letter-spacing: 4px; text-transform: uppercase; color: #A67C3D; text-align: center;">
                                Nandini Inner Circle
                            </p>

                            <h1 style="margin: 0; font-family: Georgia, 'Times New Roman', serif; font-size: 30px; line-height: 1.25; letter-spacing: 4px; text-transform: uppercase; color: #0f172a; text-align: center; font-weight: normal;">
                                Verify Email
                            </h1>

                            <div style="width: 72px; height: 1px; background-color: #94a3b8; margin: 24px auto 34px;"></div>

                            <p style="margin: 0 0 22px; font-size: 16px; line-height: 1.8; color: #334155;">
                                Dear {{ $member->full_name ?: 'Member' }},
                            </p>

                            <p style="margin: 0 0 18px; font-size: 15px; line-height: 1.8; color: #334155;">
                                Thank you for joining Nandini Inner Circle.
                            </p>

                            <p style="margin: 0 0 32px; font-size: 15px; line-height: 1.8; color: #334155;">
                                Please verify your email address to activate your membership account and continue your journey with Nandini Jungle by Hanging Gardens.
                            </p>

                            <table role="presentation" cellspacing="0" cellpadding="0" align="center" style="margin: 0 auto 34px;">
                                <tr>
                                    <td align="center" bgcolor="#A67C3D">
                                        <a href="{{ $verificationUrl }}" style="display: inline-block; padding: 16px 28px; font-size: 12px; line-height: 1; letter-spacing: 3px; text-transform: uppercase; color: #ffffff; text-decoration: none; font-weight: bold; background-color: #A67C3D;">
                                            Verify Email Address
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 18px; font-size: 14px; line-height: 1.8; color: #64748b;">
                                This verification link will expire in 24 hours.
                            </p>

                            <p style="margin: 0 0 28px; font-size: 14px; line-height: 1.8; color: #64748b;">
                                If you did not create this account, no further action is required.
                            </p>

                            <p style="margin: 0; font-size: 15px; line-height: 1.8; color: #334155;">
                                Warm regards,<br>
                                <strong>Nandini Jungle by Hanging Gardens</strong>
                            </p>

                            <div style="height: 1px; background-color: #e2e8f0; margin: 36px 0 24px;"></div>

                            <p style="margin: 0 0 10px; font-size: 12px; line-height: 1.7; color: #64748b;">
                                If the button above does not work, copy and paste this URL into your browser:
                            </p>

                            <p style="margin: 0; font-size: 12px; line-height: 1.7; color: #64748b; word-break: break-all;">
                                <a href="{{ $verificationUrl }}" style="color: #A67C3D; text-decoration: underline;">
                                    {{ $verificationUrl }}
                                </a>
                            </p>

                        </td>
                    </tr>
                </table>

                <p style="margin: 28px 0 0; font-size: 12px; line-height: 1.7; color: #94a3b8; text-align: center;">
                    © {{ date('Y') }} Nandini Jungle by Hanging Gardens. All rights reserved.
                </p>
            </td>
        </tr>
    </table>
</body>

</html>