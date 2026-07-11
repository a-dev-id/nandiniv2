@php($money = app(\App\Services\Voucher\MoneyFormatter::class))
@push('meta')
<title>Voucher Checkout | Nandini Jungle</title>
<meta name="robots" content="noindex,nofollow">
@endpush

<x-layouts.app>
    <section class="bg-[#F7F7F7] px-6 pb-14 pt-36 md:pb-20">
        <div class="mx-auto max-w-6xl">
            <h1 class="text-2xl uppercase text-slate-700 sm:text-4xl">Checkout</h1>
            @guest('member')
                <p class="mt-4 text-sm leading-6 text-slate-600">Already an Inner Circle member? <a href="{{ route('membership.login') }}" class="text-[#A88444] underline">Sign in</a> before checkout to attach this order to your dashboard.</p>
            @endguest
        </div>
    </section>

    <section class="bg-white px-6 py-14 md:py-20">
        <div class="mx-auto grid max-w-6xl gap-8 lg:grid-cols-[1fr_360px]">
            <form method="POST" action="{{ route('voucher.checkout.store') }}" class="space-y-5">
                @csrf
                @if ($errors->any())
                    <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
                @endif
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs uppercase tracking-[0.08em] text-slate-700">First Name</label>
                        <input name="purchaser_first_name" value="{{ old('purchaser_first_name', $purchaserDefaults['first_name'] ?? null) }}" class="mt-2 w-full border border-slate-300 px-4 py-3 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-[0.08em] text-slate-700">Last Name</label>
                        <input name="purchaser_last_name" value="{{ old('purchaser_last_name', $purchaserDefaults['last_name'] ?? null) }}" class="mt-2 w-full border border-slate-300 px-4 py-3 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-[0.08em] text-slate-700">Email</label>
                        <input name="purchaser_email" type="email" value="{{ old('purchaser_email', $purchaserDefaults['email'] ?? null) }}" class="mt-2 w-full border border-slate-300 px-4 py-3 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-[0.08em] text-slate-700">Phone</label>
                        <input name="purchaser_phone" value="{{ old('purchaser_phone', $purchaserDefaults['phone'] ?? null) }}" class="mt-2 w-full border border-slate-300 px-4 py-3 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-[0.08em] text-slate-700">Billing Country</label>
                        <input name="billing_country_code" value="{{ old('billing_country_code', 'ID') }}" maxlength="2" class="mt-2 w-full border border-slate-300 px-4 py-3 text-sm uppercase" required>
                    </div>
                </div>
                <label class="flex gap-3 text-sm leading-6 text-slate-600"><input type="checkbox" name="terms" value="1" class="mt-1" required> I accept the voucher terms and conditions.</label>
                <label class="flex gap-3 text-sm leading-6 text-slate-600"><input type="checkbox" name="privacy" value="1" class="mt-1" required> I accept the privacy policy.</label>
                <button class="inline-flex items-center justify-center border border-[#A88444] bg-[#A88444] px-5 py-3 text-xs uppercase tracking-[0.08em] text-white">Pay Securely with Flywire</button>
            </form>

            <aside class="border border-slate-200 bg-[#F7F7F7] p-6">
                <h2 class="text-lg uppercase text-slate-700">Order Summary</h2>
                <div class="mt-5 space-y-4">
                    @foreach ($cart['lines'] as $line)
                        <div class="border-b border-slate-300 pb-4">
                            <p class="text-sm font-semibold text-slate-700">{{ $line['voucher']->title }} x {{ $line['quantity'] }}</p>
                            <p class="mt-1 text-xs text-slate-600">
                                {{ ($line['purchase_for'] ?? 'gift') === 'self' ? 'For yourself' : 'Gift for' }}
                                {{ $line['recipient_name'] }} &lt;{{ $line['recipient_email'] }}&gt;
                            </p>
                            <p class="mt-2 text-sm text-slate-700">{{ $money->format($line['line_total'], $line['currency']) }}{{ $money->priceTypeSuffix($line['price_type'] ?? null) }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="mt-5 flex justify-between text-base font-semibold text-slate-700"><span>Total</span><span>{{ $money->format($cart['total'], $cart['currency']) }}</span></div>
            </aside>
        </div>
    </section>
</x-layouts.app>
