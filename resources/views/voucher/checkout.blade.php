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
                <p class="mt-4 text-sm leading-6 text-slate-600">Already an Inner Circle member? <a href="{{ route('membership.login', ['redirect' => route('voucher.checkout.index')]) }}" class="text-[#A88444] underline">Sign in</a> before checkout to attach this order to your dashboard.</p>
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
                <h2 class="text-lg uppercase text-slate-700">Input your personal information</h2>
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
                        <label class="block text-xs uppercase tracking-[0.08em] text-slate-700">Phone/WhatsApp</label>
                        <input name="purchaser_phone" value="{{ old('purchaser_phone', $purchaserDefaults['phone'] ?? null) }}" class="mt-2 w-full border border-slate-300 px-4 py-3 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-[0.08em] text-slate-700">Country</label>
                        <select name="billing_country_code" class="mt-2 w-full border border-slate-300 bg-white px-4 py-3 text-sm" required>
                            <option value="">Select Country</option>
                            @foreach ($countries as $code => $country)
                                <option value="{{ $code }}" @selected(old('billing_country_code', $purchaserDefaults['country'] ?? null) === $code)>{{ $country }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <p class="text-sm leading-6 text-slate-600">By clicking “Pay Now,” you agree to the voucher terms and conditions and acknowledge that your information will be handled in accordance with our privacy policy.</p>
                <button class="inline-flex items-center justify-center border border-[#A88444] bg-[#A88444] px-5 py-3 text-xs uppercase tracking-[0.08em] text-white">Pay Now</button>
            </form>

            <aside class="border border-slate-200 bg-[#F7F7F7] p-6">
                <h2 class="text-lg uppercase text-slate-700">Order Summary</h2>
                <div class="mt-5 space-y-4">
                    @foreach ($cart['lines'] as $line)
                        <div class="border-b border-slate-300 pb-4">
                            <p class="text-sm font-semibold text-slate-700">{{ $line['voucher']->title }} x {{ $line['quantity'] }}</p>
                            @if (filled(data_get($line, 'price_option.label')))
                                <p class="mt-1 text-xs font-medium text-slate-700">Room: {{ data_get($line, 'price_option.label') }}</p>
                            @endif
                            <p class="mt-1 text-xs text-slate-600">
                                @if (($line['purchase_for'] ?? 'gift') === 'self')
                                    For yourself — purchaser details will be used.
                                @else
                                    Gift for {{ $line['recipient_name'] }}{{ filled($line['recipient_email']) ? ' <'.$line['recipient_email'].'>' : '' }}
                                @endif
                            </p>
                            <p class="mt-1 text-xs text-slate-600">{{ ($line['delivery_method'] ?? 'email') === 'print_at_resort' ? 'Print at resort (+ '.$money->format($line['delivery_fee'], $line['currency']).')' : 'Send to email' }}</p>
                            @if (filled($line['hotel_note'] ?? null))
                                <p class="mt-1 text-xs leading-5 text-slate-600">Hotel note: {{ $line['hotel_note'] }}</p>
                            @endif
                            <div class="mt-3 space-y-1 text-xs text-slate-600">
                                <div class="flex justify-between gap-3"><span>Voucher subtotal</span><span>{{ $money->format($line['voucher_subtotal'], $line['currency']) }}</span></div>
                                @if (($line['room_upgrade_total'] ?? 0) > 0)
                                    <div class="flex justify-between gap-3"><span>Room upgrade</span><span>+ {{ $money->format($line['room_upgrade_total'], $line['currency']) }}</span></div>
                                @endif
                                @if (($line['delivery_fee'] ?? 0) > 0)
                                    <div class="flex justify-between gap-3"><span>Additional charge</span><span>{{ $money->format($line['delivery_fee'], $line['currency']) }}</span></div>
                                @endif
                                @if ($line['additional_charges_apply'])
                                    <div class="flex justify-between gap-3"><span>Service ({{ $cart['service_charge_percentage'] }}%)</span><span>{{ $money->format($line['service_charge'], $line['currency']) }}</span></div>
                                    <div class="flex justify-between gap-3"><span>Tax ({{ $cart['tax_percentage'] }}%)</span><span>{{ $money->format($line['tax'], $line['currency']) }}</span></div>
                                @endif
                                <div class="flex justify-between gap-3 pt-1 font-semibold text-slate-700"><span>Line total</span><span>{{ $money->format($line['line_total'], $line['currency']) }}</span></div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-5 space-y-3 text-sm text-slate-600">
                    <div class="flex justify-between"><span>Subtotal</span><span>{{ $money->format($cart['subtotal'], $cart['currency']) }}</span></div>
                    @if ($cart['discount'] > 0)
                        <div class="flex justify-between gap-4 border-l-4 border-[#A88444] bg-[#A88444]/10 px-3 py-2.5 font-semibold text-[#8A682F]"><span>Extra 10% Discount</span><span>-{{ $money->format($cart['discount'], $cart['currency']) }}</span></div>
                    @endif
                </div>
                <div class="mt-4 flex justify-between border-t border-slate-300 pt-4 text-base font-semibold text-slate-700"><span>Total Payment</span><span>{{ $money->format($cart['total'], $cart['currency']) }}</span></div>
            </aside>
        </div>
    </section>
</x-layouts.app>
