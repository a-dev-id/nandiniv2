@push('meta')
    <title>Affiliate Payment Details | Nandini Partner Circle</title>
    <meta name="description" content="Securely manage your affiliate payout method, preferred currency, and payment account details for Nandini Partner Circle commissions.">
@endpush

@php
    $method = old('payment_method', $profile?->payment_method?->value ?? 'wise');
    $bankCountry = old('bank_country', $profile?->bank_country);
    $countries = \App\Support\InquiryOptions::countries();

    if ($bankCountry && ! array_key_exists($bankCountry, $countries)) {
        $countries = [$bankCountry => $bankCountry] + $countries;
    }
@endphp

<x-layouts.affiliate title="Payment Details | Nandini Partner Circle">
    <section class="bg-slate-50 px-5 py-12 sm:px-8 sm:py-16 lg:px-12 lg:py-20">
        <div class="mx-auto max-w-4xl">
            <p class="text-xs font-medium uppercase tracking-[0.12em] text-[#A88444] sm:text-sm">Secure payout profile</p>
            <h1 class="mt-3 text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">Payment details</h1>
            <p class="mt-4 max-w-2xl text-xs leading-relaxed text-gray-600 sm:text-sm">Choose how you prefer to receive externally processed commission payouts. Your sensitive details are encrypted and available only to authorized Finance staff.</p>

            @if (session('status'))
                <div role="status" class="mt-7 border-l-4 border-emerald-500 bg-emerald-50 px-5 py-4 text-sm leading-6 text-emerald-900">{{ session('status') }}</div>
            @endif

            @if ($profile)
                <div class="mt-8 border border-slate-200 bg-white px-5 py-5 sm:px-7">
                    <p class="text-xs font-medium uppercase tracking-[0.08em] text-slate-500">Saved payment method</p>
                    <p class="mt-2 text-base font-medium text-slate-950">{{ $profile->maskedDetails() }}</p>
                    @if ($profile->payment_method === \App\Enums\AffiliatePaymentMethod::BankTransfer && filled($profile->bank_name))
                        <p class="mt-2 text-xs leading-relaxed text-gray-600 sm:text-sm">Bank name: {{ $profile->bank_name }}</p>
                    @endif
                </div>
            @endif

            <form method="POST" action="{{ route('affiliate.payment-details.update') }}" class="mt-8 border border-slate-200 bg-white px-5 py-7 sm:px-8 sm:py-9" x-data="{ method: @js($method) }">
                @csrf
                @method('PUT')

                <div>
                    <label for="payment_method" class="block text-sm font-medium text-slate-800">Payment method</label>
                    <select id="payment_method" name="payment_method" x-model="method" class="mt-2 w-full border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#A88444] focus:outline-none" required>
                        <option value="wise">Wise</option>
                        <option value="bank_transfer">Bank Transfer</option>
                    </select>
                    @error('payment_method') <p class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>

                <div class="mt-6">
                    <label for="account_holder_name" class="block text-sm font-medium text-slate-800">Account-holder name</label>
                    <input id="account_holder_name" name="account_holder_name" value="{{ old('account_holder_name') }}" autocomplete="name" maxlength="191" required class="mt-2 w-full border border-slate-300 px-4 py-3 text-sm text-slate-900 focus:border-[#A88444] focus:outline-none">
                    @error('account_holder_name') <p class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>

                <div class="mt-6">
                    <label for="preferred_currency" class="block text-sm font-medium text-slate-800">Preferred currency</label>
                    <select id="preferred_currency" name="preferred_currency" class="mt-2 w-full border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#A88444] focus:outline-none" required>
                        @foreach (\App\Enums\AffiliatePreferredCurrency::cases() as $currency)
                            <option value="{{ $currency->value }}" @selected(old('preferred_currency', $profile?->preferred_currency?->value ?? 'IDR') === $currency->value)>{{ $currency->value }}</option>
                        @endforeach
                    </select>
                    @error('preferred_currency') <p class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>

                <div x-show="method === 'wise'" class="mt-6">
                    <label for="wise_email" class="block text-sm font-medium text-slate-800">Wise email</label>
                    <input id="wise_email" name="wise_email" type="email" value="{{ old('wise_email') }}" autocomplete="email" maxlength="191" :required="method === 'wise'" class="mt-2 w-full border border-slate-300 px-4 py-3 text-sm text-slate-900 focus:border-[#A88444] focus:outline-none">
                    @error('wise_email') <p class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>

                <div x-show="method === 'bank_transfer'" class="mt-6 grid gap-6 sm:grid-cols-2">
                    @foreach ([
                        'bank_name' => 'Bank name',
                        'bank_account_name' => 'Bank account name',
                        'bank_account_number' => 'Bank account number',
                        'swift_bic' => 'SWIFT / BIC (optional)',
                    ] as $field => $label)
                        <div @class(['sm:col-span-2' => $field === 'bank_account_number'])>
                            <label for="{{ $field }}" class="block text-sm font-medium text-slate-800">{{ $label }}</label>
                            <input id="{{ $field }}" name="{{ $field }}" value="{{ old($field) }}" maxlength="{{ $field === 'bank_account_number' ? 80 : 191 }}" :required="method === 'bank_transfer' && @js($field !== 'swift_bic')" class="mt-2 w-full border border-slate-300 px-4 py-3 text-sm text-slate-900 focus:border-[#A88444] focus:outline-none">
                            @error($field) <p class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
                        </div>
                    @endforeach

                    <div>
                        <label for="bank_country" class="block text-sm font-medium text-slate-800">Bank country</label>
                        <select id="bank_country" name="bank_country" :required="method === 'bank_transfer'" class="mt-2 w-full border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-[#A88444] focus:outline-none">
                            <option value="">Select bank country</option>
                            @foreach ($countries as $value => $label)
                                <option value="{{ $value }}" @selected($bankCountry === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('bank_country') <p class="mt-2 text-sm text-red-700">{{ $message }}</p> @enderror
                    </div>
                </div>

                <button type="submit" class="mt-8 inline-flex min-h-12 items-center justify-center border border-[#A88444] bg-[#A88444] px-6 py-3 text-sm font-medium uppercase tracking-[0.08em] text-white transition hover:border-[#B8945B] hover:bg-[#B8945B] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#A88444]">Save payment details</button>
            </form>
        </div>
    </section>
</x-layouts.affiliate>
