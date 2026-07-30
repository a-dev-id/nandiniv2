@php
    $money = app(\App\Services\Voucher\MoneyFormatter::class);
    $awaitingPayment = ! in_array($order->payment_status, ['paid', 'failed', 'cancelled'], true);
@endphp
@push('meta')
<title>Voucher Order {{ $order->order_number }} | Nandini Jungle</title>
<meta name="robots" content="noindex,nofollow">
@endpush

<x-layouts.app>
    <section class="bg-[#F7F7F7] px-6 pb-14 pt-36 md:pb-20">
        <div class="mx-auto max-w-6xl">
            <h1 class="text-2xl uppercase text-slate-700 sm:text-4xl">Thank You</h1>
            <p class="mt-4 text-sm leading-6 text-slate-600">Order {{ $order->order_number }}</p>
            @if ($awaitingPayment && session('status'))
                <p class="mt-4 border border-[#A88444]/30 bg-white px-4 py-3 text-sm text-slate-700">{{ session('status') }}</p>
            @endif
            @if ($awaitingPayment)
                <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border border-[#A88444]/30 bg-white px-4 py-3 text-sm text-slate-700" data-payment-status-poll data-refresh-seconds="10" aria-live="polite">
                    <p>Waiting for payment confirmation. Checking again in <span class="font-semibold text-[#A88444]" data-payment-countdown>10</span> seconds.</p>
                    @if (filled(config('services.flywire.api_key')))
                        <form method="POST" action="{{ route('voucher.order.check-payment', array_filter(['orderNumber' => $order->order_number, 'token' => request('token')])) }}">
                            @csrf
                            <button type="submit" class="text-xs font-medium uppercase tracking-[0.08em] text-[#A88444] underline underline-offset-4">Check now</button>
                        </form>
                    @endif
                </div>
            @elseif ($order->payment_status === 'paid')
                <p class="mt-4 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">Payment confirmed. Your voucher is ready.</p>
            @endif
        </div>
    </section>

    <section class="bg-white px-6 py-14 md:py-20">
        <div class="mx-auto grid max-w-6xl gap-8 lg:grid-cols-[1fr_320px]">
            <div class="space-y-6">
                @foreach ($order->items as $item)
                    <article class="border border-slate-200 p-5">
                        <h2 class="text-lg uppercase text-slate-700">{{ $item->voucher_title }}</h2>
                        @if (filled(data_get($item->voucher_snapshot, 'price_option.label')))
                            <p class="mt-2 text-sm font-medium text-slate-700">Room: {{ data_get($item->voucher_snapshot, 'price_option.label') }}</p>
                        @endif
                        <p class="mt-2 text-sm text-slate-600">Voucher holder: {{ $item->recipient_name }} &lt;{{ $item->recipient_email }}&gt;</p>
                        <p class="mt-2 text-sm text-slate-600">Quantity: {{ $item->quantity }}</p>
                        <p class="mt-2 text-sm text-slate-700">{{ $money->format($item->line_total, $item->currency) }}</p>
                        @if ($item->issuedVouchers->isNotEmpty())
                            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                @foreach ($item->issuedVouchers as $issued)
                                    <div class="bg-[#F7F7F7] p-4 text-sm text-slate-600">
                                        <p class="font-semibold text-slate-700">{{ $issued->voucher_code }}</p>
                                        <p>Status: {{ str_replace('_', ' ', $issued->status) }}</p>
                                        <p>Expires: {{ $issued->expires_at?->format('d M Y') ?: 'Manual' }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="mt-4 bg-[#F7F7F7] p-4 text-sm text-slate-600">Vouchers will appear here after your payment is confirmed.</p>
                        @endif
                    </article>
                @endforeach
            </div>
            <aside class="h-fit border border-slate-200 bg-[#F7F7F7] p-6">
                <h2 class="text-lg uppercase text-slate-700">Status</h2>
                <dl class="mt-5 space-y-3 text-sm text-slate-600">
                    <div class="flex justify-between gap-4"><dt>Payment</dt><dd class="text-right">{{ str_replace('_', ' ', $order->payment_status) }}</dd></div>
                    <div class="flex justify-between gap-4"><dt>Order</dt><dd class="text-right">{{ str_replace('_', ' ', $order->order_status) }}</dd></div>
                    <div class="flex justify-between gap-4"><dt>Payment Reference</dt><dd class="text-right">{{ $order->flywire_payment_reference ?: '-' }}</dd></div>
                    <div class="flex justify-between gap-4 border-t border-slate-300 pt-3 font-semibold text-slate-700"><dt>Total</dt><dd>{{ $money->format($order->total_amount, $order->currency) }}</dd></div>
                </dl>
                @auth('member')
                    <a href="{{ route('membership.dashboard') }}#my-vouchers" class="mt-6 inline-flex w-full items-center justify-center border border-[#A88444] bg-[#A88444] px-5 py-3 text-xs uppercase tracking-[0.08em] text-white">My Vouchers</a>
                @endauth
            </aside>
        </div>
    </section>

    @if ($awaitingPayment)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const poll = document.querySelector('[data-payment-status-poll]');
                const countdown = poll?.querySelector('[data-payment-countdown]');
                const refreshSeconds = Number.parseInt(poll?.dataset.refreshSeconds || '10', 10);
                let secondsRemaining = refreshSeconds;

                function refreshStatus() {
                    window.location.reload();
                }

                window.setInterval(function () {
                    if (document.hidden) return;

                    secondsRemaining -= 1;
                    if (countdown) countdown.textContent = String(Math.max(0, secondsRemaining));
                    if (secondsRemaining <= 0) refreshStatus();
                }, 1000);
            });
        </script>
    @endif
</x-layouts.app>
