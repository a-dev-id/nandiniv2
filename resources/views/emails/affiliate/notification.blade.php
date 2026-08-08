@php
$logoUrl = 'https://nandinibali.com/images/logo-njhg.png';
$paragraphs = $paragraphs ?? [];
$details = $details ?? [];
$actionLabel = $actionLabel ?? null;
$actionUrl = $actionUrl ?? null;
$footerNote = $footerNote ?? null;
$greeting = $greeting ?? ($affiliate->name ?: 'Partner');
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
</head>
<body style="margin:0;padding:0;background:#f6f3ee;color:#243044;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="width:100%;background:#f6f3ee;margin:0;padding:32px 14px;">
        <tr><td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="width:100%;max-width:640px;background:#ffffff;border:1px solid #e5ddcf;border-collapse:collapse;">
                <tr><td align="center" style="padding:38px 32px 26px;text-align:center;">
                    <img src="{{ $logoUrl }}" alt="Nandini Jungle by Hanging Gardens" width="170" style="display:block;width:170px;max-width:100%;height:auto;margin:0 auto 24px;border:0;">
                    <p style="margin:0 0 10px;color:#b8945b;font-size:12px;font-weight:bold;letter-spacing:3px;text-transform:uppercase;line-height:1.5;">{{ $eyebrow }}</p>
                    <h1 style="margin:0;color:#0f1d33;font-family:Georgia,'Times New Roman',serif;font-size:29px;line-height:1.35;letter-spacing:4px;text-transform:uppercase;font-weight:normal;">{{ $heading }}</h1>
                    <div style="width:72px;height:1px;background:#d8c6a8;margin:24px auto 0;"></div>
                </td></tr>
                <tr><td style="padding:0 34px 38px;">
                    <p style="margin:0 0 18px;font-size:15px;line-height:1.75;color:#344054;">Dear {{ $greeting }},</p>

                    @foreach ($paragraphs as $paragraph)
                        <p style="margin:0 0 18px;font-size:15px;line-height:1.75;color:#344054;">{{ $paragraph }}</p>
                    @endforeach

                    @if ($details)
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="width:100%;margin:8px 0 28px;border-collapse:collapse;border:1px solid #eee8df;">
                            @foreach ($details as $label => $value)
                                <tr>
                                    <td style="width:34%;padding:12px 14px;border-bottom:1px solid #eee8df;color:#667085;font-size:13px;line-height:1.5;">{{ $label }}</td>
                                    <td style="padding:12px 14px;border-bottom:1px solid #eee8df;color:#344054;font-size:13px;font-weight:bold;line-height:1.5;word-break:break-word;">{{ $value }}</td>
                                </tr>
                            @endforeach
                        </table>
                    @endif

                    @if ($actionLabel && $actionUrl)
                        <table role="presentation" cellspacing="0" cellpadding="0" align="center" style="margin:0 auto 28px;"><tr><td align="center" bgcolor="#a88444">
                            <a href="{{ $actionUrl }}" style="display:inline-block;padding:15px 26px;color:#ffffff;background:#a88444;font-size:12px;font-weight:bold;letter-spacing:2.5px;line-height:1;text-decoration:none;text-transform:uppercase;">{{ $actionLabel }}</a>
                        </td></tr></table>
                    @endif

                    @if ($footerNote)
                        <p style="margin:0 0 18px;font-size:14px;line-height:1.75;color:#667085;">{{ $footerNote }}</p>
                    @endif

                    <p style="margin:0;font-size:15px;line-height:1.75;color:#344054;">Warm regards,<br><strong>Nandini Jungle by Hanging Gardens</strong></p>

                    @if ($actionUrl)
                        <div style="height:1px;background:#eee8df;margin:28px 0 22px;"></div>
                        <p style="margin:0 0 10px;font-size:12px;line-height:1.7;color:#667085;">If the button above does not work, copy and paste this URL into your browser:</p>
                        <p style="margin:0;font-size:12px;line-height:1.7;color:#667085;word-break:break-all;"><a href="{{ $actionUrl }}" style="color:#916b2c;text-decoration:underline;">{{ $actionUrl }}</a></p>
                    @endif
                </td></tr>
            </table>
            <p style="margin:24px 0 0;font-size:12px;line-height:1.7;color:#9a8f80;text-align:center;">&copy; {{ date('Y') }} Nandini Jungle by Hanging Gardens. All rights reserved.</p>
        </td></tr>
    </table>
</body>
</html>
