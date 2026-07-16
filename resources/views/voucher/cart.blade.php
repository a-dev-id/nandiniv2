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
                            <div class="grid gap-6 lg:grid-cols-[1fr_220px]">
                                <form method="POST" action="{{ route('voucher.cart.update', $line['key']) }}" class="grid gap-5 md:grid-cols-2">
                                    @csrf
                                    @method('PUT')
                                    <div class="md:col-span-2">
                                        <h2 class="text-lg uppercase text-slate-700">{{ $line['voucher']->title }}</h2>
                                        <p class="mt-1 text-sm text-slate-600">
                                            {{ $money->format($line['unit_price'], $line['currency']) }}{{ $money->priceTypeSuffix($line['price_type'] ?? null) }} each
                                        </p>
                                    </div>
                                    <div>
                                        <label class="block text-xs uppercase tracking-[0.08em] text-slate-700">Quantity</label>
                                        <input name="quantity" type="number" value="{{ $line['quantity'] }}" min="1" class="mt-2 w-full border border-slate-300 px-4 py-3 text-sm">
                                    </div>
                                    <div>
                                        <input type="hidden" name="purchase_for" value="{{ $line['purchase_for'] ?? 'gift' }}">
                                        <label class="block text-xs uppercase tracking-[0.08em] text-slate-700">{{ ($line['purchase_for'] ?? 'gift') === 'self' ? 'Your Name' : 'Recipient Name' }}</label>
                                        <input name="recipient_name" value="{{ $line['recipient_name'] }}" class="mt-2 w-full border border-slate-300 px-4 py-3 text-sm" required>
                                    </div>
                                    <div>
                                        <label class="block text-xs uppercase tracking-[0.08em] text-slate-700">{{ ($line['purchase_for'] ?? 'gift') === 'self' ? 'Your Email' : 'Recipient Email' }}</label>
                                        <input name="recipient_email" type="email" value="{{ $line['recipient_email'] }}" class="mt-2 w-full border border-slate-300 px-4 py-3 text-sm" required>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-xs uppercase tracking-[0.08em] text-slate-700">Message or Note</label>
                                        <textarea name="personal_message" rows="3" class="mt-2 w-full border border-slate-300 px-4 py-3 text-sm">{{ $line['personal_message'] }}</textarea>
                                        <input type="hidden" name="gift_from" value="{{ $line['gift_from'] ?? '' }}">
                                        <input type="hidden" name="delivery_method" value="email">
                                    </div>
                                    <div class="flex flex-wrap gap-3 md:col-span-2">
                                        <button class="inline-flex items-center justify-center border border-[#A88444] bg-[#A88444] px-4 py-2.5 text-xs uppercase tracking-[0.08em] text-white">Update</button>
                                        <button
                                            type="button"
                                            class="inline-flex items-center justify-center border border-slate-700 px-4 py-2.5 text-xs uppercase tracking-[0.08em] text-slate-700 transition hover:border-[#A88444] hover:text-[#A88444]"
                                            data-cart-voucher-preview
                                            data-title="{{ $line['voucher']->title }}"
                                            data-price="{{ $money->format($line['unit_price'], $line['currency']) }}{{ $money->priceTypeSuffix($line['price_type'] ?? null) }}"
                                            data-image="{{ $line['voucher']->image ? asset('storage/' . $line['voucher']->image) : '' }}"
                                            data-excerpt="{{ $line['voucher']->excerpt }}"
                                        >Preview</button>
                                    </div>
                                </form>
                                <div class="flex flex-col justify-between bg-[#F7F7F7] p-5">
                                    <div>
                                        <p class="text-xs uppercase tracking-[0.08em] text-slate-500">Line Total</p>
                                        <p class="mt-2 text-lg font-semibold text-slate-700">{{ $money->format($line['line_total'], $line['currency']) }}{{ $money->priceTypeSuffix($line['price_type'] ?? null) }}</p>
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
                    <div class="mt-3 flex justify-between text-sm text-slate-600"><span>Discount</span><span>{{ $money->format($cart['discount'], $cart['currency']) }}</span></div>
                    <div class="mt-4 flex justify-between border-t border-slate-300 pt-4 text-base font-semibold text-slate-700"><span>Total</span><span>{{ $money->format($cart['total'], $cart['currency']) }}</span></div>
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
            <div class="mt-6 overflow-hidden border border-slate-200 bg-white">
                <img src="" alt="" class="hidden h-64 w-full object-cover sm:h-80" data-cart-preview-image>
                <div class="px-6 py-10 text-center sm:px-12">
                    <p class="text-xs uppercase tracking-[0.18em] text-[#A88444]">Nandini Jungle by Hanging Gardens</p>
                    <h2 id="cart-preview-title" class="mt-5 text-2xl uppercase text-slate-700 sm:text-3xl" data-cart-preview-title></h2>
                    <p class="mt-4 text-xl font-semibold tracking-[0.06em] text-slate-700" data-cart-preview-price></p>
                    <p class="mx-auto mt-6 max-w-xl text-sm italic leading-7 text-slate-600" data-cart-preview-message></p>
                    <p class="mx-auto mt-10 max-w-xl border-t border-slate-200 pt-8 text-sm leading-7 text-slate-600" data-cart-preview-excerpt></p>
                    <div class="mx-auto mt-8 max-w-xl border-t border-slate-200 pt-8 text-left text-xs leading-6 text-slate-500">
                        <p><span class="font-semibold text-slate-700">Gift to:</span> <span data-cart-preview-recipient></span></p>
                        <p class="mt-1"><span class="font-semibold text-slate-700">Gift from:</span> <span data-cart-preview-sender></span></p>
                    </div>
                </div>
                <div class="border-t-4 border-[#A88444] bg-[#F7F7F7] px-6 py-5 text-center text-[10px] uppercase tracking-[0.12em] text-slate-500">Accommodation &nbsp; | &nbsp; Dining &nbsp; | &nbsp; Spa &nbsp; | &nbsp; Experiences</div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.querySelector('[data-cart-preview-modal]');
            const image = document.querySelector('[data-cart-preview-image]');

            function closePreview() {
                modal?.classList.add('hidden');
                modal?.setAttribute('aria-hidden', 'true');
                document.documentElement.classList.remove('overflow-hidden');
            }

            document.querySelectorAll('[data-cart-voucher-preview]').forEach(function (button) {
                button.addEventListener('click', function () {
                    const form = button.closest('form');
                    const imageUrl = button.dataset.image || '';
                    document.querySelector('[data-cart-preview-title]').textContent = button.dataset.title || '';
                    document.querySelector('[data-cart-preview-price]').textContent = button.dataset.price || '';
                    document.querySelector('[data-cart-preview-excerpt]').textContent = button.dataset.excerpt || '';
                    document.querySelector('[data-cart-preview-recipient]').textContent = form?.querySelector('[name="recipient_name"]')?.value || '';
                    document.querySelector('[data-cart-preview-sender]').textContent = form?.querySelector('[name="gift_from"]')?.value || 'A special someone';
                    document.querySelector('[data-cart-preview-message]').textContent = form?.querySelector('[name="personal_message"]')?.value || '';
                    image.src = imageUrl;
                    image.alt = button.dataset.title || '';
                    image.classList.toggle('hidden', !imageUrl);
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
