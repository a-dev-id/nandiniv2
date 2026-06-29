<!DOCTYPE html>
<html lang="en">
@php
    $logoUrl = rtrim(config('app.url'), '/') . '/images/logo-njhg.png';
    $reserveTime = $inquiry->reserve_time
        ? \Illuminate\Support\Carbon::createFromFormat('H:i', $inquiry->reserve_time)->format('h:i A')
        : '-';
@endphp

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Inquiry</title>
</head>

<body style="margin:0;background:#f6f3ee;color:#243044;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f6f3ee;padding:28px 14px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:680px;background:#ffffff;border:1px solid #e5ddcf;">
                    <tr>
                        <td style="padding:32px 32px 22px;text-align:center;">
                            <img src="{{ $logoUrl }}" alt="Nandini Jungle by Hanging Gardens" width="150" style="display:block;width:150px;max-width:100%;height:auto;margin:0 auto 22px;border:0;">
                            <p style="margin:0 0 10px;color:#b8945b;font-size:12px;font-weight:bold;letter-spacing:3px;text-transform:uppercase;">Inquiry Received</p>
                            <h1 style="margin:0;color:#0f1d33;font-family:Georgia,'Times New Roman',serif;font-size:28px;line-height:1.35;letter-spacing:5px;text-transform:uppercase;">
                                {{ $inquiry->inquiry_title ?: 'Nandini Inquiry' }}
                            </h1>
                        </td>
                    </tr>

                    @if ($inquiry->inquiry_image)
                    <tr>
                        <td>
                            <img src="{{ $inquiry->inquiry_image }}" alt="{{ $inquiry->inquiry_title ?: 'Inquiry image' }}" width="680" style="display:block;width:100%;max-width:680px;height:auto;border:0;">
                        </td>
                    </tr>
                    @endif

                    <tr>
                        <td style="padding:28px 32px 8px;">
                            <p style="margin:0 0 18px;font-size:15px;line-height:1.7;color:#344054;">
                                Dear {{ $guestName }},
                            </p>

                            <p style="margin:0 0 22px;font-size:15px;line-height:1.7;color:#344054;">
                                Thank you for your inquiry. Our reservations team has received your request and will get back to you shortly.
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;margin:0 0 22px;">
                                <tr>
                                    <td colspan="2" style="padding:12px 0;border-bottom:1px solid #e8e1d6;color:#0f1d33;font-size:15px;font-weight:bold;letter-spacing:2px;text-transform:uppercase;">Inquiry Details</td>
                                </tr>
                                <tr>
                                    <td style="width:38%;padding:12px 0;border-bottom:1px solid #eee8df;color:#667085;font-size:13px;text-transform:uppercase;letter-spacing:1px;">Guest</td>
                                    <td style="padding:12px 0;border-bottom:1px solid #eee8df;color:#344054;font-size:14px;">{{ $guestName }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 0;border-bottom:1px solid #eee8df;color:#667085;font-size:13px;text-transform:uppercase;letter-spacing:1px;">Email</td>
                                    <td style="padding:12px 0;border-bottom:1px solid #eee8df;color:#344054;font-size:14px;">{{ $inquiry->email }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 0;border-bottom:1px solid #eee8df;color:#667085;font-size:13px;text-transform:uppercase;letter-spacing:1px;">Phone/WA</td>
                                    <td style="padding:12px 0;border-bottom:1px solid #eee8df;color:#344054;font-size:14px;">{{ $inquiry->phone_wa }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 0;border-bottom:1px solid #eee8df;color:#667085;font-size:13px;text-transform:uppercase;letter-spacing:1px;">Country</td>
                                    <td style="padding:12px 0;border-bottom:1px solid #eee8df;color:#344054;font-size:14px;">{{ $inquiry->country }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 0;border-bottom:1px solid #eee8df;color:#667085;font-size:13px;text-transform:uppercase;letter-spacing:1px;">Reserve Date</td>
                                    <td style="padding:12px 0;border-bottom:1px solid #eee8df;color:#344054;font-size:14px;">{{ optional($inquiry->reserve_date)->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 0;border-bottom:1px solid #eee8df;color:#667085;font-size:13px;text-transform:uppercase;letter-spacing:1px;">Reserve Time</td>
                                    <td style="padding:12px 0;border-bottom:1px solid #eee8df;color:#344054;font-size:14px;">
                                        {{ $reserveTime }}
                                        @if ($requiresLateStart)
                                        <span style="display:block;margin-top:4px;color:#8a642d;font-size:13px;">Dinner and night activities start after 16:00.</span>
                                        @endif
                                    </td>
                                </tr>
                                @if ($inquiry->note)
                                <tr>
                                    <td style="padding:12px 0;border-bottom:1px solid #eee8df;color:#667085;font-size:13px;text-transform:uppercase;letter-spacing:1px;">Note</td>
                                    <td style="padding:12px 0;border-bottom:1px solid #eee8df;color:#344054;font-size:14px;line-height:1.6;">{{ $inquiry->note }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="padding:12px 0;color:#667085;font-size:13px;text-transform:uppercase;letter-spacing:1px;">Source</td>
                                    <td style="padding:12px 0;color:#344054;font-size:14px;"><a href="{{ $sourceUrl }}" style="color:#b8945b;text-decoration:none;">View page</a></td>
                                </tr>
                            </table>

                            <p style="margin:0 0 24px;font-size:15px;line-height:1.7;color:#344054;">
                                Warm regards,<br>
                                Nandini Jungle by Hanging Gardens
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
