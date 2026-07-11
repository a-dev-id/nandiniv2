@php
$money = app(\App\Services\Voucher\MoneyFormatter::class);
$member = auth('member')->user();
$buyerName = old('recipient_name', $member?->full_name ?: $member?->name ?: '');
$buyerEmail = old('recipient_email', $member?->email ?: '');
$purchaseFor = old('purchase_for', 'self');
$priceSuffix = $money->priceTypeSuffix($voucher->price_type);
$unitLabel = $money->unitLabel($voucher->unit_type);
@endphp
@push('meta')
<title>{{ $voucher->meta_title ?: $voucher->title . ' | Nandini Voucher' }}</title>
<meta name="description" content="{{ $voucher->meta_description ?: $voucher->excerpt }}">
<link rel="canonical" href="{{ route('voucher.show', $voucher) }}">
@endpush

<x-layouts.app>
    <section class="bg-white px-6 pb-14 pt-36 md:pb-20">
        <div class="mx-auto grid max-w-6xl gap-10 lg:grid-cols-2">
            <div>
                @if ($voucher->image)
                    <img src="{{ asset('storage/' . $voucher->image) }}" alt="{{ $voucher->image_alt ?: $voucher->title }}" class="aspect-[4/3] w-full object-cover">
                @else
                    <div class="flex aspect-[4/3] items-center justify-center bg-[#F7F7F7] text-sm uppercase tracking-[0.08em] text-slate-500">Nandini Gift Voucher</div>
                @endif
            </div>
            <div>
                <nav class="text-xs uppercase tracking-[0.08em] text-slate-500">
                    <a href="{{ route('voucher.index') }}" class="hover:text-[#A88444]">Vouchers</a>
                    @if ($voucher->category)
                        <span class="mx-2">/</span>
                        <a href="{{ route('voucher.category.show', $voucher->category) }}" class="hover:text-[#A88444]">{{ $voucher->category->name }}</a>
                    @endif
                </nav>
                <h1 class="mt-5 text-2xl uppercase text-slate-700 sm:text-4xl">{{ $voucher->title }}</h1>
                <p class="mt-4 text-sm leading-7 text-slate-600">{{ $voucher->excerpt }}</p>
                <p class="mt-6 text-lg font-semibold uppercase tracking-[0.08em] text-slate-700">{{ $money->format($voucher->selling_price, $voucher->currency) }}{{ $priceSuffix }}</p>
                @if ($unitLabel)
                    <p class="mt-1 text-sm leading-6 text-slate-600">{{ $unitLabel }}</p>
                @endif

                <form method="POST" action="{{ route('voucher.cart.add', $voucher) }}" class="mt-8 space-y-5">
                    @csrf
                    <fieldset>
                        <legend class="block text-xs uppercase tracking-[0.08em] text-slate-700">Who is this voucher for?</legend>
                        <div class="mt-2 grid gap-3 sm:grid-cols-2">
                            <label class="flex cursor-pointer items-center gap-3 border border-slate-300 px-4 py-3 text-sm text-slate-700 transition has-[:checked]:border-[#A88444] has-[:checked]:bg-[#A88444]/10">
                                <input type="radio" name="purchase_for" value="self" class="h-4 w-4" {{ $purchaseFor === 'self' ? 'checked' : '' }} data-voucher-purchase-for>
                                For myself
                            </label>
                            <label class="flex cursor-pointer items-center gap-3 border border-slate-300 px-4 py-3 text-sm text-slate-700 transition has-[:checked]:border-[#A88444] has-[:checked]:bg-[#A88444]/10">
                                <input type="radio" name="purchase_for" value="gift" class="h-4 w-4" {{ $purchaseFor === 'gift' ? 'checked' : '' }} data-voucher-purchase-for>
                                Gift for someone else
                            </label>
                        </div>
                    </fieldset>

                    <div>
                        <label for="quantity" class="block text-xs uppercase tracking-[0.08em] text-slate-700">Quantity</label>
                        <input id="quantity" name="quantity" type="number" min="{{ $voucher->minimum_quantity }}" max="{{ $voucher->purchase_limit_per_order ?: $voucher->maximum_quantity ?: 99 }}" value="{{ old('quantity', $voucher->minimum_quantity) }}" class="mt-2 w-full border border-slate-300 px-4 py-3 text-sm focus:border-[#A88444] focus:outline-none">
                    </div>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="recipient_name" class="block text-xs uppercase tracking-[0.08em] text-slate-700" data-recipient-name-label>{{ $purchaseFor === 'self' ? 'Your Name' : 'Recipient Name' }}</label>
                            <input id="recipient_name" name="recipient_name" value="{{ $buyerName }}" data-self-name="{{ $member?->full_name ?: $member?->name }}" class="mt-2 w-full border border-slate-300 px-4 py-3 text-sm focus:border-[#A88444] focus:outline-none" required>
                        </div>
                        <div>
                            <label for="recipient_email" class="block text-xs uppercase tracking-[0.08em] text-slate-700" data-recipient-email-label>{{ $purchaseFor === 'self' ? 'Your Email' : 'Recipient Email' }}</label>
                            <input id="recipient_email" name="recipient_email" type="email" value="{{ $buyerEmail }}" data-self-email="{{ $member?->email }}" class="mt-2 w-full border border-slate-300 px-4 py-3 text-sm focus:border-[#A88444] focus:outline-none" required>
                        </div>
                    </div>
                    <div>
                        <label for="personal_message" class="block text-xs uppercase tracking-[0.08em] text-slate-700">Message or Note</label>
                        <textarea id="personal_message" name="personal_message" rows="4" class="mt-2 w-full border border-slate-300 px-4 py-3 text-sm focus:border-[#A88444] focus:outline-none">{{ old('personal_message') }}</textarea>
                    </div>
                    <input type="hidden" name="delivery_method" value="email">
                    @if ($errors->any())
                        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
                    @endif
                    <div class="flex flex-wrap gap-3">
                        <button type="submit" class="inline-flex items-center justify-center border border-[#A88444] bg-[#A88444] px-5 py-3 text-xs font-medium uppercase tracking-[0.08em] text-white transition hover:border-[#B8945B] hover:bg-[#B8945B] sm:text-sm">Add to Cart</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <section class="bg-[#F7F7F7] px-6 py-14 md:py-20">
        <div class="mx-auto grid max-w-6xl gap-8 md:grid-cols-3">
            <div class="md:col-span-2">
                <h2 class="text-xl uppercase text-slate-700">Details</h2>
                <div class="mt-4 space-y-5 text-sm leading-7 text-slate-600">{!! $voucher->description !!}</div>
            </div>
            <aside class="space-y-6">
                <div>
                    <h3 class="text-base uppercase text-slate-700">Validity</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $voucher->validity_type === 'days_after_issue' ? $voucher->validity_days . ' days after issue' : 'See voucher terms' }}</p>
                </div>
                <div>
                    <h3 class="text-base uppercase text-slate-700">Inclusions</h3>
                    <div class="mt-2 text-sm leading-6 text-slate-600">{!! $voucher->inclusions !!}</div>
                </div>
                <div>
                    <h3 class="text-base uppercase text-slate-700">Terms</h3>
                    <div class="mt-2 text-sm leading-6 text-slate-600">{!! $voucher->terms_conditions !!}</div>
                </div>
            </aside>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const radios = document.querySelectorAll('[data-voucher-purchase-for]');
            const nameInput = document.getElementById('recipient_name');
            const emailInput = document.getElementById('recipient_email');
            const nameLabel = document.querySelector('[data-recipient-name-label]');
            const emailLabel = document.querySelector('[data-recipient-email-label]');

            function syncLabels() {
                const selected = document.querySelector('[data-voucher-purchase-for]:checked');
                const isSelf = selected && selected.value === 'self';

                if (nameLabel) {
                    nameLabel.textContent = isSelf ? 'Your Name' : 'Recipient Name';
                }

                if (emailLabel) {
                    emailLabel.textContent = isSelf ? 'Your Email' : 'Recipient Email';
                }

                if (isSelf) {
                    if (nameInput && !nameInput.value && nameInput.dataset.selfName) {
                        nameInput.value = nameInput.dataset.selfName;
                    }

                    if (emailInput && !emailInput.value && emailInput.dataset.selfEmail) {
                        emailInput.value = emailInput.dataset.selfEmail;
                    }
                }
            }

            radios.forEach(function (radio) {
                radio.addEventListener('change', syncLabels);
            });

            syncLabels();
        });
    </script>
</x-layouts.app>
