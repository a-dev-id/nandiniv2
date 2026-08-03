@php($money = app(\App\Services\Voucher\MoneyFormatter::class))
@push('meta')
<title>Your Voucher Cart | Nandini Jungle</title>
<meta name="robots" content="noindex,nofollow">
@endpush

<x-layouts.app>
    <section class="bg-[#F7F7F7] px-6 pb-14 pt-36 md:pb-20">
        <div class="mx-auto max-w-6xl">
            <h1 class="text-2xl uppercase text-slate-700 sm:text-4xl">Your Voucher Cart</h1>
            @if (session('status'))
                <p class="mt-4 border border-[#A88444]/30 bg-white px-4 py-3 text-sm text-slate-700">{{ session('status') }}</p>
            @endif
        </div>
    </section>

    <section class="bg-white px-6 py-14 md:py-20">
        <div class="mx-auto max-w-6xl">
            @if ($cart['lines']->isEmpty())
                <div class="border border-slate-200 bg-[#F7F7F7] p-8 text-center">
                    <p class="text-sm text-slate-600">Your cart is empty.</p>
                    <a href="{{ route('voucher.index') }}" class="mt-5 inline-flex items-center justify-center border border-[#A88444] bg-[#A88444] px-5 py-3 text-xs uppercase tracking-[0.08em] text-white">Continue Shopping</a>
                </div>
            @else
                <div class="space-y-6">
                    @foreach ($cart['lines'] as $line)
                        <article class="border border-slate-200 p-5">
                            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_280px]">
                                <form method="POST" action="{{ route('voucher.cart.update', $line['key']) }}" class="grid gap-5 md:grid-cols-2">
                                    @csrf
                                    @method('PUT')
                                    <div class="md:col-span-2">
                                        <h2 class="text-lg uppercase text-slate-700">{{ $line['voucher']->title }}</h2>
                                        @if (filled(data_get($line, 'price_option.label')))
                                            <p class="mt-1 text-sm font-medium text-slate-700">Room: {{ data_get($line, 'price_option.label') }}</p>
                                        @endif
                                        <p class="mt-1 text-sm text-slate-600">
                                            {{ $money->format($line['base_unit_price'], $line['currency']) }}{{ $money->priceTypeSuffix($line['price_type']) }} each
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-xs uppercase tracking-[0.08em] text-slate-700">Quantity</label>
                                        <input name="quantity" type="number" value="{{ $line['quantity'] }}" min="1" class="mt-2 w-full border border-slate-300 px-4 py-3 text-sm">
                                    </div>
                                    @if ($line['voucher']->availablePriceOptions() !== [])
                                        <div>
                                            <label class="block text-xs uppercase tracking-[0.08em] text-slate-700">Room Type</label>
                                            <select name="price_option" class="mt-2 w-full border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700">
                                                @foreach ($line['voucher']->availablePriceOptions() as $option)
                                                    <option value="{{ $option['key'] }}" @selected($line['price_option_key'] === $option['key'])>
                                                        {{ $option['label'] }} (+{{ $money->format($option['additional_price'], $line['currency']) }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                    <input type="hidden" name="purchase_for" value="{{ $line['purchase_for'] ?? 'gift' }}">
                                    @if (($line['purchase_for'] ?? 'gift') === 'gift')
                                        <div>
                                            <label class="block text-xs uppercase tracking-[0.08em] text-slate-700">Recipient Name</label>
                                            <input name="recipient_name" value="{{ $line['recipient_name'] }}" class="mt-2 w-full border border-slate-300 px-4 py-3 text-sm" required>
                                        </div>
                                        <div class="{{ ($line['delivery_method'] ?? 'email') === 'print_at_resort' ? 'hidden' : '' }}" data-cart-recipient-email>
                                            <label class="block text-xs uppercase tracking-[0.08em] text-slate-700">Recipient Email</label>
                                            <input name="recipient_email" type="email" value="{{ $line['recipient_email'] }}" class="mt-2 w-full border border-slate-300 px-4 py-3 text-sm" {{ ($line['delivery_method'] ?? 'email') === 'email' ? 'required' : '' }}>
                                        </div>
                                    @endif
                                    @if (($line['purchase_for'] ?? 'gift') === 'gift')
                                        <div>
                                            <label class="block text-xs uppercase tracking-[0.08em] text-slate-700">Delivery Option</label>
                                            <select name="delivery_method" class="mt-2 w-full border border-slate-300 bg-white px-4 py-3 text-sm" data-cart-delivery-method>
                                                <option value="email" {{ ($line['delivery_method'] ?? 'email') === 'email' ? 'selected' : '' }}>Send to email</option>
                                                <option value="print_at_resort" {{ ($line['delivery_method'] ?? 'email') === 'print_at_resort' ? 'selected' : '' }}>Print at resort (+ IDR 100,000)</option>
                                            </select>
                                        </div>
                                    @else
                                        <input type="hidden" name="delivery_method" value="email">
                                    @endif
                                    <div class="md:col-span-2">
                                        <label class="block text-xs uppercase tracking-[0.08em] text-slate-700">Message or Note</label>
                                        <textarea name="personal_message" rows="3" class="mt-2 w-full border border-slate-300 px-4 py-3 text-sm" placeholder="Add a message or note for this voucher">{{ $line['personal_message'] }}</textarea>
                                        <input type="hidden" name="gift_from" value="{{ $line['gift_from'] ?? '' }}">
                                    </div>
                                    @if (($line['purchase_for'] ?? 'gift') === 'gift')
                                        <div class="md:col-span-2 {{ ($line['delivery_method'] ?? 'email') === 'print_at_resort' ? '' : 'hidden' }}" data-cart-hotel-note>
                                            <label class="block text-xs uppercase tracking-[0.08em] text-slate-700">Note for the Hotel Team</label>
                                            <textarea name="hotel_note" rows="3" class="mt-2 w-full border border-slate-300 px-4 py-3 text-sm" placeholder="Used when print at resort is selected">{{ $line['hotel_note'] ?? '' }}</textarea>
                                        </div>
                                    @endif
                                    <div class="flex flex-wrap gap-3 md:col-span-2">
                                        <button class="inline-flex items-center justify-center border border-[#A88444] bg-[#A88444] px-4 py-2.5 text-xs uppercase tracking-[0.08em] text-white">Update</button>
                                        @if (($line['purchase_for'] ?? 'gift') === 'gift')
                                            <button
                                                type="button"
                                                class="inline-flex items-center justify-center border border-slate-700 px-4 py-2.5 text-xs uppercase tracking-[0.08em] text-slate-700 transition hover:border-[#A88444] hover:text-[#A88444]"
                                                data-cart-voucher-preview
                                                data-title="{{ $line['voucher']->title }}"
                                                data-price="{{ $money->format($line['base_unit_price'], $line['currency']) }}{{ $money->priceTypeSuffix($line['price_type']) }}"
                                                data-excerpt="{{ $line['voucher']->excerpt }}"
                                            >Preview</button>
                                        @endif
                                    </div>
                                </form>
                                <div class="flex flex-col justify-between bg-[#F7F7F7] p-5">
                                    <div>
                                        <p class="text-xs uppercase tracking-[0.08em] text-slate-500">Line Total</p>
                                        <div class="mt-3 space-y-2 text-xs text-slate-600">
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
                                        </div>
                                        <p class="mt-3 border-t border-slate-300 pt-3 text-lg font-semibold text-slate-700">{{ $money->format($line['line_total'], $line['currency']) }}</p>
                                    </div>
                                    <form method="POST" action="{{ route('voucher.cart.remove', $line['key']) }}" class="mt-6">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-xs uppercase tracking-[0.08em] text-slate-600 underline">Remove</button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-8 ml-auto max-w-sm border border-slate-200 bg-[#F7F7F7] p-6">
                    <div class="flex justify-between text-sm text-slate-600"><span>Subtotal</span><span>{{ $money->format($cart['subtotal'], $cart['currency']) }}</span></div>
                    @if ($cart['discount'] > 0)
                        <div class="mt-3 flex justify-between gap-4 border-l-4 border-[#A88444] bg-[#A88444]/10 px-3 py-2.5 text-sm font-semibold text-[#8A682F]"><span>{{ $cart['global_discount_active'] ? 'Extra 10% Discount' : 'Discount' }}</span><span>-{{ $money->format($cart['discount'], $cart['currency']) }}</span></div>
                    @endif
                    <div class="mt-4 flex justify-between border-t border-slate-300 pt-4 text-base font-semibold text-slate-700"><span>Total Payment</span><span>{{ $money->format($cart['total'], $cart['currency']) }}</span></div>
                    <a href="{{ route('voucher.checkout.index') }}" class="mt-6 inline-flex w-full items-center justify-center border border-[#A88444] bg-[#A88444] px-5 py-3 text-xs uppercase tracking-[0.08em] text-white">Proceed to Checkout</a>
                    <a href="{{ route('voucher.index') }}" class="mt-3 inline-flex w-full items-center justify-center border border-slate-700 px-5 py-3 text-xs uppercase tracking-[0.08em] text-slate-700">Continue Shopping</a>
                </div>
            @endif
        </div>
    </section>

    <div class="fixed inset-0 z-[110] hidden overflow-y-auto bg-slate-950/70 px-4 py-6 sm:px-6" data-cart-preview-modal aria-hidden="true">
        <div class="mx-auto max-w-3xl bg-white p-5 shadow-2xl sm:p-8" role="dialog" aria-modal="true" aria-labelledby="cart-preview-title">
            <div class="flex justify-end">
                <button type="button" class="text-xs uppercase tracking-[0.08em] text-slate-600 hover:text-[#A88444]" data-close-cart-preview>&times; Close preview</button>
            </div>
            <div class="mt-6 bg-[#F5F0E7] p-3 sm:p-5">
                <div class="relative border border-[#B8945B] bg-[#FFFCF7] px-6 py-12 text-center outline outline-1 outline-offset-[-7px] outline-[#D8C6A8] sm:px-12 sm:py-16">
                    <span class="absolute left-5 top-5 h-8 w-8 border-l border-t border-[#B8945B]" aria-hidden="true"></span><span class="absolute right-5 top-5 h-8 w-8 border-r border-t border-[#B8945B]" aria-hidden="true"></span>
                    <span class="absolute bottom-5 left-5 h-8 w-8 border-b border-l border-[#B8945B]" aria-hidden="true"></span><span class="absolute bottom-5 right-5 h-8 w-8 border-b border-r border-[#B8945B]" aria-hidden="true"></span>
                    <p class="font-serif text-xs uppercase tracking-[0.34em] text-[#A88444]">Nandini Jungle by Hanging Gardens</p>
                    <p class="mt-5 text-[10px] uppercase tracking-[0.32em] text-slate-500">Gift Voucher</p>
                    <h2 id="cart-preview-title" class="mx-auto mt-5 max-w-xl font-serif text-2xl font-normal uppercase leading-snug tracking-[0.12em] text-[#17233A] sm:text-3xl" data-cart-preview-title></h2>
                    <div class="mx-auto mt-6 h-px w-20 bg-[#B8945B]"></div>
                    <p class="mx-auto mt-7 max-w-lg font-serif text-base italic leading-8 text-slate-600" data-cart-preview-message></p>
                    <p class="mx-auto mt-8 max-w-xl border-y border-[#D8C6A8] py-7 text-sm leading-7 text-slate-600" data-cart-preview-excerpt></p>
                    <div class="mx-auto mt-8 grid max-w-xl gap-6 text-left text-xs sm:grid-cols-2">
                        <p><span class="block text-[9px] uppercase tracking-[0.2em] text-[#A88444]">Gift to</span><span class="mt-2 block font-serif text-base text-[#17233A]" data-cart-preview-recipient></span></p>
                        <p class="sm:text-right"><span class="block text-[9px] uppercase tracking-[0.2em] text-[#A88444]">Gift from</span><span class="mt-2 block font-serif text-base text-[#17233A]" data-cart-preview-sender></span></p>
                    </div>
                    <p class="mt-10 text-[9px] uppercase tracking-[0.24em] text-slate-400">An invitation to experience the beauty of Bali</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.querySelector('[data-cart-preview-modal]');

            document.querySelectorAll('[data-cart-delivery-method]').forEach(function (select) {
                const form = select.closest('form');
                const hotelNote = form?.querySelector('[data-cart-hotel-note]');
                const recipientEmailField = form?.querySelector('[data-cart-recipient-email]');
                const recipientEmailInput = recipientEmailField?.querySelector('[name="recipient_email"]');

                function updateDeliveryFields() {
                    const isPrintAtResort = select.value === 'print_at_resort';
                    hotelNote?.classList.toggle('hidden', !isPrintAtResort);
                    recipientEmailField?.classList.toggle('hidden', isPrintAtResort);
                    if (recipientEmailInput) recipientEmailInput.required = !isPrintAtResort;
                }

                select.addEventListener('change', updateDeliveryFields);
                updateDeliveryFields();
            });

            function closePreview() {
                modal?.classList.add('hidden');
                modal?.setAttribute('aria-hidden', 'true');
                document.documentElement.classList.remove('overflow-hidden');
            }

            document.querySelectorAll('[data-cart-voucher-preview]').forEach(function (button) {
                button.addEventListener('click', function () {
                    const form = button.closest('form');
                    document.querySelector('[data-cart-preview-title]').textContent = button.dataset.title || '';
                    document.querySelector('[data-cart-preview-excerpt]').textContent = button.dataset.excerpt || '';
                    document.querySelector('[data-cart-preview-recipient]').textContent = form?.querySelector('[name="recipient_name"]')?.value || '';
                    document.querySelector('[data-cart-preview-sender]').textContent = form?.querySelector('[name="gift_from"]')?.value || 'A someone special';
                    document.querySelector('[data-cart-preview-message]').textContent = form?.querySelector('[name="personal_message"]')?.value || '';
                    modal?.classList.remove('hidden');
                    modal?.setAttribute('aria-hidden', 'false');
                    document.documentElement.classList.add('overflow-hidden');
                });
            });

            document.querySelectorAll('[data-close-cart-preview]').forEach((button) => button.addEventListener('click', closePreview));
            modal?.addEventListener('click', (event) => {
                if (event.target === modal) closePreview();
            });
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && modal && !modal.classList.contains('hidden')) closePreview();
            });
        });
    </script>
</x-layouts.app>
