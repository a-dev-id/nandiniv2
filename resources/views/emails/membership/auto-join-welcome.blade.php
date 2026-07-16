@php
$logoUrl = rtrim(config('app.url'), '/') . '/images/logo-njhg.png';
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Nandini Inner Circle</title>
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
                                Welcome
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
                                @if ($manuallyCreated ?? false)
                                    Your Nandini Inner Circle membership has been created. You can sign in using the details below.
                                @else
                                    Thank you for booking with Nandini Jungle by Hanging Gardens. Your Inner Circle membership has been created with the email address used for your reservation.
                                @endif
                            </p>

                            <p style="margin:0 0 22px;font-size:15px;line-height:1.75;color:#344054;">
                                Through your member profile, you can view your booking, follow your points, explore available rewards, and keep track of your membership history.
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="width:100%;border-collapse:collapse;margin:0 0 26px;">
                                <tr>
                                    <td style="width:42%;padding:13px 0;border-bottom:1px solid #eee8df;color:#667085;font-size:12px;text-transform:uppercase;letter-spacing:1.4px;">Email</td>
                                    <td style="padding:13px 0;border-bottom:1px solid #eee8df;color:#344054;font-size:14px;text-align:right;">{{ $member->email }}</td>
                                </tr>
                                @if (($manuallyCreated ?? false) && filled($temporaryPassword ?? null))
                                <tr>
                                    <td style="width:42%;padding:13px 0;border-bottom:1px solid #eee8df;color:#667085;font-size:12px;text-transform:uppercase;letter-spacing:1.4px;">Temporary Password</td>
                                    <td style="padding:13px 0;border-bottom:1px solid #eee8df;color:#344054;font-size:14px;text-align:right;">{{ $temporaryPassword }}</td>
                                </tr>
                                @endif
                                @if ($bookingNumber)
                                <tr>
                                    <td style="width:42%;padding:13px 0;border-bottom:1px solid #eee8df;color:#667085;font-size:12px;text-transform:uppercase;letter-spacing:1.4px;">Booking</td>
                                    <td style="padding:13px 0;border-bottom:1px solid #eee8df;color:#344054;font-size:14px;text-align:right;">{{ $bookingNumber }}</td>
                                </tr>
                                @endif
                                @if ($roomName)
                                <tr>
                                    <td style="width:42%;padding:13px 0;border-bottom:1px solid #eee8df;color:#667085;font-size:12px;text-transform:uppercase;letter-spacing:1.4px;">Room</td>
                                    <td style="padding:13px 0;border-bottom:1px solid #eee8df;color:#344054;font-size:14px;text-align:right;">{{ $roomName }}</td>
                                </tr>
                                @endif
                                @if ($checkinDate || $checkoutDate)
                                <tr>
                                    <td style="width:42%;padding:13px 0;border-bottom:1px solid #eee8df;color:#667085;font-size:12px;text-transform:uppercase;letter-spacing:1.4px;">Stay Date</td>
                                    <td style="padding:13px 0;border-bottom:1px solid #eee8df;color:#344054;font-size:14px;text-align:right;">{{ $checkinDate ?: '-' }} to {{ $checkoutDate ?: '-' }}</td>
                                </tr>
                                @endif
                            </table>

                            <p style="margin:0 0 28px;font-size:15px;line-height:1.75;color:#344054;">
                                @if ($manuallyCreated ?? false)
                                    For your security, you will be asked to change this temporary password after your first sign-in.
                                @else
                                    Because this account was created automatically from your booking, please set or reset your password before signing in.
                                @endif
                            </p>

                            <table role="presentation" cellspacing="0" cellpadding="0" align="center" style="margin:0 auto 22px;">
                                <tr>
                                    <td align="center" bgcolor="#a88444">
                                        <a href="{{ ($manuallyCreated ?? false) ? $loginUrl : $passwordResetUrl }}" style="display:inline-block;padding:15px 26px;color:#ffffff;background:#a88444;font-size:12px;font-weight:bold;letter-spacing:2.5px;line-height:1;text-decoration:none;text-transform:uppercase;">
                                            {{ ($manuallyCreated ?? false) ? 'Sign In' : 'Set Your Password' }}
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 28px;font-size:13px;line-height:1.75;color:#667085;text-align:center;">
                                You may also visit the member sign-in page here:<br>
                                <a href="{{ $loginUrl }}" style="color:#916b2c;text-decoration:underline;">{{ $loginUrl }}</a>
                            </p>

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
