@php($termsSections = app(\App\Services\Voucher\VoucherTermsFormatter::class)->sections($voucher->terms_snapshot))
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="width:100%;border-collapse:collapse;margin:28px 0 26px;background:#f7f7f7;">
@foreach ($termsSections as $section)
@php($sectionPadding = $loop->count === 1 ? '24px 22px' : ($loop->first ? '24px 22px 10px' : '16px 22px 24px'))
<tr>
<td style="padding:{{ $sectionPadding }};vertical-align:top;">
<h2 style="margin:0 0 12px;color:#0f1d33;font-family:Georgia,'Times New Roman',serif;font-size:16px;font-weight:normal;letter-spacing:2px;line-height:1.4;text-transform:uppercase;">{{ $section['title'] }}</h2>
<div style="margin:0;color:#344054;font-size:13px;line-height:1.65;">{!! strip_tags($section['html'], '<p><br><strong><b><em><i><ul><ol><li><a>') !!}</div>
</td>
</tr>
@endforeach
</table>
