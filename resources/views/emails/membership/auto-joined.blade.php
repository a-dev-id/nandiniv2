<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Nandini Inner Circle</title>
</head>

<body style="margin:0;background:#f6f3ee;color:#243044;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f6f3ee;padding:28px 14px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;background:#ffffff;border:1px solid #e5ddcf;">
                    <tr>
                        <td style="padding:34px 32px;text-align:center;">
                            <img src="https://nandinibali.com/images/nandini-logo.png" alt="Nandini Jungle by Hanging Gardens" width="150" style="display:block;width:150px;max-width:100%;height:auto;margin:0 auto 22px;border:0;">

                            <p style="margin:0 0 10px;color:#b8945b;font-size:12px;font-weight:bold;letter-spacing:3px;text-transform:uppercase;">Inner Circle</p>
                            <h1 style="margin:0;color:#0f1d33;font-family:Georgia,'Times New Roman',serif;font-size:28px;line-height:1.35;letter-spacing:4px;text-transform:uppercase;">
                                Welcome to Nandini Inner Circle
                            </h1>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0 32px 34px;">
                            <p style="margin:0 0 18px;font-size:15px;line-height:1.7;color:#344054;">
                                Dear {{ $member->full_name ?: 'Guest' }},
                            </p>

                            <p style="margin:0 0 22px;font-size:15px;line-height:1.7;color:#344054;">
                                Thank you for booking with Nandini Jungle by Hanging Gardens. We have created your Inner Circle membership account using your booking email address.
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;margin:0 0 24px;">
                                <tr>
                                    <td style="padding:12px 0;border-bottom:1px solid #eee8df;color:#667085;font-size:13px;text-transform:uppercase;letter-spacing:1px;">Email</td>
                                    <td style="padding:12px 0;border-bottom:1px solid #eee8df;color:#344054;font-size:14px;">{{ $member->email }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 0;border-bottom:1px solid #eee8df;color:#667085;font-size:13px;text-transform:uppercase;letter-spacing:1px;">Temporary Password</td>
                                    <td style="padding:12px 0;border-bottom:1px solid #eee8df;color:#0f1d33;font-size:16px;font-weight:bold;letter-spacing:1px;">{{ $temporaryPassword }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 0;border-bottom:1px solid #eee8df;color:#667085;font-size:13px;text-transform:uppercase;letter-spacing:1px;">Reservation</td>
                                    <td style="padding:12px 0;border-bottom:1px solid #eee8df;color:#344054;font-size:14px;">{{ $reservationId }}</td>
                                </tr>
                                @if ($roomName)
                                <tr>
                                    <td style="padding:12px 0;border-bottom:1px solid #eee8df;color:#667085;font-size:13px;text-transform:uppercase;letter-spacing:1px;">Room</td>
                                    <td style="padding:12px 0;border-bottom:1px solid #eee8df;color:#344054;font-size:14px;">{{ $roomName }}</td>
                                </tr>
                                @endif
                                @if ($checkinDate || $checkoutDate)
                                <tr>
                                    <td style="padding:12px 0;border-bottom:1px solid #eee8df;color:#667085;font-size:13px;text-transform:uppercase;letter-spacing:1px;">Stay Date</td>
                                    <td style="padding:12px 0;border-bottom:1px solid #eee8df;color:#344054;font-size:14px;">{{ $checkinDate ?: '-' }} to {{ $checkoutDate ?: '-' }}</td>
                                </tr>
                                @endif
                            </table>

                            <p style="margin:0 0 22px;font-size:15px;line-height:1.7;color:#344054;">
                                Please sign in with this password, which is based on your booking number. For your security, you will be asked to create a new password after your first login.
                            </p>

                            <table role="presentation" cellspacing="0" cellpadding="0" style="margin:0 0 26px;">
                                <tr>
                                    <td style="background:#b8945b;">
                                        <a href="{{ $loginUrl }}" style="display:inline-block;padding:14px 24px;color:#ffffff;font-size:12px;font-weight:bold;letter-spacing:2px;text-decoration:none;text-transform:uppercase;">
                                            Sign In
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0;font-size:15px;line-height:1.7;color:#344054;">
                                Warm regards,<br>
                                Nandini Inner Circle
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
