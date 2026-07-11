@php($money = app(\App\Services\Voucher\MoneyFormatter::class))
@push('meta')
<title>Voucher Verification | Nandini Jungle</title>
<meta name="robots" content="noindex,nofollow">
@endpush

<x-layouts.app>
    <section class="bg-[#F7F7F7] px-6 pb-14 pt-36 md:pb-20">
        <div class="mx-auto max-w-3xl text-center">
            <h1 class="text-2xl uppercase text-slate-700 sm:text-4xl">Voucher Verification</h1>
            <p class="mt-4 text-sm leading-7 text-slate-600">This page confirms voucher status without exposing purchaser or payment details.</p>
        </div>
    </section>
    <section class="bg-white px-6 py-14 md:py-20">
        <div class="mx-auto max-w-xl border border-slate-200 bg-[#F7F7F7] p-8">
            <h2 class="text-xl uppercase text-slate-700">{{ $issuedVoucher->title }}</h2>
            <dl class="mt-6 space-y-3 text-sm text-slate-600">
                <div class="flex justify-between gap-4"><dt>Status</dt><dd>{{ str_replace('_', ' ', $issuedVoucher->status) }}</dd></div>
                <div class="flex justify-between gap-4"><dt>Voucher Holder</dt><dd>{{ \Illuminate\Support\Str::before($issuedVoucher->recipient_name, ' ') }}</dd></div>
                <div class="flex justify-between gap-4"><dt>Valid From</dt><dd>{{ $issuedVoucher->valid_from?->format('d M Y') ?: '-' }}</dd></div>
                <div class="flex justify-between gap-4"><dt>Expires</dt><dd>{{ $issuedVoucher->expires_at?->format('d M Y') ?: 'Manual' }}</dd></div>
                <div class="flex justify-between gap-4"><dt>Original Value</dt><dd>{{ $money->format($issuedVoucher->original_value, $issuedVoucher->currency) }}</dd></div>
                <div class="flex justify-between gap-4"><dt>Remaining Value</dt><dd>{{ $money->format($issuedVoucher->remaining_value, $issuedVoucher->currency) }}</dd></div>
            </dl>
            @if ($issuedVoucher->terms_snapshot)
                <div class="mt-6 border-t border-slate-300 pt-5 text-sm leading-7 text-slate-600">{!! $issuedVoucher->terms_snapshot !!}</div>
            @endif
        </div>
    </section>
</x-layouts.app>
