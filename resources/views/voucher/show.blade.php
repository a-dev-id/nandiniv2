@php
$money = app(\App\Services\Voucher\MoneyFormatter::class);
$member = auth('member')->user();
$buyerName = old('recipient_name', $member?->full_name ?: $member?->name ?: '');
$buyerEmail = old('recipient_email', $member?->email ?: '');
$purchaseFor = old('purchase_for', 'self');
$defaultGiftMessage = 'Enjoy this indulgent escape together!';
$giftFrom = old('gift_from', $member?->full_name ?: $member?->name ?: '');
$priceSuffix = $money->priceTypeSuffix($voucher->price_type);
$unitLabel = $money->unitLabel($voucher->unit_type);
$galleryImages = collect([[
        'image' => $voucher->image,
        'image_alt' => $voucher->image_alt,
    ]])
    ->merge(collect($voucher->gallery_images ?? [])->map(
        fn ($item) => is_array($item)
            ? $item
            : ['image' => $item, 'image_alt' => null]
    ))
    ->filter(fn ($item) => filled($item['image'] ?? null))
    ->unique('image')
    ->values();
@endphp
@push('meta')
<title>{{ $voucher->meta_title ?: $voucher->title . ' | Nandini Voucher' }}</title>
<meta name="description" content="{{ $voucher->meta_description ?: $voucher->excerpt }}">
<link rel="canonical" href="{{ route('voucher.show', $voucher) }}">
@endpush

<x-layouts.app>
    <section class="bg-white px-6 pb-14 pt-36 md:pb-20">
        <div class="mx-auto grid max-w-6xl gap-10 lg:grid-cols-2">
            <div class="min-w-0">
                @if ($galleryImages->isNotEmpty())
                    <div class="voucher-gallery-wrap relative" data-voucher-gallery-wrap>
                        <div class="voucher-gallery" data-total="{{ $galleryImages->count() }}">
                            @foreach ($galleryImages as $index => $galleryImage)
                                <div>
                                    <img src="{{ asset('storage/' . $galleryImage['image']) }}" alt="{{ $galleryImage['image_alt'] ?: $voucher->image_alt ?: $voucher->title . ($index ? ' - image ' . ($index + 1) : '') }}" class="h-[30rem] w-full object-cover md:h-[38rem] lg:h-[44rem]">
                                </div>
                            @endforeach
                        </div>
                        @if ($galleryImages->count() > 1)
                            <button type="button" class="voucher-gallery-prev fold-carousel-arrow fold-image-carousel-arrow absolute left-0 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center bg-[#A88444] text-white transition hover:bg-[#A88444] md:h-12 md:w-12" aria-label="Previous image">
                                <svg class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                            </button>
                            <button type="button" class="voucher-gallery-next fold-carousel-arrow fold-image-carousel-arrow absolute right-0 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center bg-[#A88444] text-white transition hover:bg-[#A88444] md:h-12 md:w-12" aria-label="Next image">
                                <svg class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </button>
                        @endif
                    </div>
                @else
                    <div class="flex h-[30rem] items-center justify-center bg-[#F7F7F7] text-sm uppercase tracking-[0.08em] text-slate-500 md:h-[38rem] lg:h-[44rem]">Nandini Gift Voucher</div>
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
                <div class="mt-6">
                    @if ($voucher->has_discount)
                        <div class="mb-2 flex flex-wrap items-center gap-3">
                            <p class="text-sm tracking-[0.04em] text-slate-400 line-through">{{ $money->format($voucher->selling_price, $voucher->currency) }}</p>
                            <span class="bg-[#A88444]/10 px-2.5 py-1 text-xs font-semibold uppercase tracking-[0.08em] text-[#A88444]">Save {{ $voucher->discount_percentage }}%</span>
                        </div>
                    @endif
                    <p class="text-2xl font-semibold tracking-[0.06em] text-slate-700">{{ $money->format($voucher->discounted_price, $voucher->currency) }}{{ $priceSuffix }}</p>
                </div>
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
                    <div class="grid gap-5 sm:grid-cols-2" data-recipient-fields>
                        <div>
                            <label for="recipient_name" class="block text-xs uppercase tracking-[0.08em] text-slate-700" data-recipient-name-label>{{ $purchaseFor === 'self' ? 'Your Name' : 'Recipient Name' }}</label>
                            <input id="recipient_name" name="recipient_name" value="{{ $buyerName }}" data-self-name="{{ $member?->full_name ?: $member?->name }}" class="mt-2 w-full border border-slate-300 px-4 py-3 text-sm focus:border-[#A88444] focus:outline-none" required>
                        </div>
                        <div>
                            <label for="recipient_email" class="block text-xs uppercase tracking-[0.08em] text-slate-700" data-recipient-email-label>{{ $purchaseFor === 'self' ? 'Your Email' : 'Recipient Email' }}</label>
                            <input id="recipient_email" name="recipient_email" type="email" value="{{ $buyerEmail }}" data-self-email="{{ $member?->email }}" class="mt-2 w-full border border-slate-300 px-4 py-3 text-sm focus:border-[#A88444] focus:outline-none" required>
                        </div>
                    </div>
                    <div data-message-field>
                        <label for="personal_message" class="block text-xs uppercase tracking-[0.08em] text-slate-700">Message or Note</label>
                        <textarea id="personal_message" name="personal_message" rows="4" class="mt-2 w-full border border-slate-300 px-4 py-3 text-sm focus:border-[#A88444] focus:outline-none">{{ old('personal_message', $purchaseFor === 'gift' ? $defaultGiftMessage : '') }}</textarea>
                    </div>
                    <div class="hidden border border-slate-200 bg-[#F7F7F7] p-5" data-gift-summary>
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div>
                                <p class="text-xs uppercase tracking-[0.08em] text-slate-500">Gift voucher details</p>
                                <p class="mt-2 text-sm text-slate-700" data-gift-summary-recipient></p>
                                <p class="mt-1 text-sm leading-6 text-slate-600" data-gift-summary-message></p>
                            </div>
                            <button type="button" class="border border-[#A88444] px-4 py-2 text-xs font-medium uppercase tracking-[0.08em] text-[#A88444] transition hover:bg-[#A88444] hover:text-white" data-open-gift-modal>Edit gift</button>
                        </div>
                    </div>
                    <input type="hidden" name="gift_from" id="gift_from" value="{{ $giftFrom }}">
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

    <div class="fixed inset-0 z-[100] hidden overflow-y-auto bg-slate-950/60 px-4 py-6 sm:px-6" data-gift-modal aria-hidden="true">
        <div class="mx-auto min-h-full max-w-4xl bg-white p-6 shadow-2xl sm:p-10" role="dialog" aria-modal="true" aria-labelledby="gift-modal-title">
            <div class="flex items-center justify-between gap-4">
                <h2 id="gift-modal-title" class="text-xl uppercase text-slate-700 sm:text-2xl">Give as a gift</h2>
                <button type="button" class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-300 text-xl text-slate-500 transition hover:border-[#A88444] hover:text-[#A88444]" data-close-gift-modal aria-label="Close gift form">&times;</button>
            </div>

            <div class="mt-10 grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="gift_recipient_name" class="block text-xs uppercase tracking-[0.08em] text-slate-700">Gift to <span class="text-red-600">*</span></label>
                    <input id="gift_recipient_name" class="mt-2 w-full border border-slate-300 px-4 py-3 text-sm focus:border-[#A88444] focus:outline-none" value="{{ $purchaseFor === 'gift' ? $buyerName : '' }}">
                </div>
                <div>
                    <label for="gift_sender_name" class="block text-xs uppercase tracking-[0.08em] text-slate-700">Gift from (optional)</label>
                    <input id="gift_sender_name" class="mt-2 w-full border border-slate-300 px-4 py-3 text-sm focus:border-[#A88444] focus:outline-none" value="{{ $giftFrom }}">
                </div>
            </div>
            <div class="mt-5">
                <label for="gift_recipient_email" class="block text-xs uppercase tracking-[0.08em] text-slate-700">Recipient email <span class="text-red-600">*</span></label>
                <input id="gift_recipient_email" type="email" class="mt-2 w-full border border-slate-300 px-4 py-3 text-sm focus:border-[#A88444] focus:outline-none" value="{{ $purchaseFor === 'gift' ? $buyerEmail : '' }}">
            </div>
            <div class="mt-5">
                <label for="gift_message" class="block text-xs uppercase tracking-[0.08em] text-slate-700">Message</label>
                <textarea id="gift_message" rows="4" maxlength="800" class="mt-2 w-full border border-slate-300 px-4 py-3 text-sm leading-6 focus:border-[#A88444] focus:outline-none">{{ old('personal_message', $defaultGiftMessage) }}</textarea>
            </div>
            <p class="mt-5 hidden text-sm text-red-600" data-gift-error>Please enter the recipient name and a valid recipient email.</p>
            <div class="mt-10 flex flex-wrap items-center justify-between gap-4">
                <button type="button" class="border border-slate-300 px-6 py-3 text-xs font-medium uppercase tracking-[0.08em] text-slate-700 transition hover:border-[#A88444] hover:text-[#A88444]" data-open-gift-preview>Preview</button>
                <button type="button" class="border border-[#A88444] bg-[#A88444] px-6 py-3 text-xs font-medium uppercase tracking-[0.08em] text-white transition hover:border-[#B8945B] hover:bg-[#B8945B]" data-add-gift-to-cart>Add to Cart</button>
            </div>
        </div>
    </div>

    <div class="fixed inset-0 z-[110] hidden overflow-y-auto bg-slate-950/70 px-4 py-6 sm:px-6" data-gift-preview aria-hidden="true">
        <div class="mx-auto max-w-3xl bg-white p-5 shadow-2xl sm:p-8">
            <div class="flex justify-end">
                <button type="button" class="text-xs uppercase tracking-[0.08em] text-slate-600 hover:text-[#A88444]" data-close-gift-preview>&times; Close preview</button>
            </div>
            <div class="mt-6 overflow-hidden border border-slate-200 bg-white">
                @if ($voucher->image)
                    <img src="{{ asset('storage/' . $voucher->image) }}" alt="{{ $voucher->image_alt ?: $voucher->title }}" class="h-64 w-full object-cover sm:h-80">
                @endif
                <div class="px-6 py-10 text-center sm:px-12">
                    <p class="text-xs uppercase tracking-[0.18em] text-[#A88444]">Nandini Jungle by Hanging Gardens</p>
                    <h2 class="mt-5 text-2xl uppercase text-slate-700 sm:text-3xl">{{ $voucher->title }}</h2>
                    <p class="mt-4 text-xl font-semibold tracking-[0.06em] text-slate-700">{{ $money->format($voucher->discounted_price, $voucher->currency) }}{{ $priceSuffix }}</p>
                    <p class="mx-auto mt-6 max-w-xl text-sm italic leading-7 text-slate-600" data-preview-message></p>
                    <div class="mx-auto mt-10 max-w-xl border-t border-slate-200 pt-8 text-sm leading-7 text-slate-600">{{ $voucher->excerpt }}</div>
                    <div class="mx-auto mt-8 max-w-xl border-t border-slate-200 pt-8 text-left text-xs leading-6 text-slate-500">
                        <p><span class="font-semibold text-slate-700">Gift to:</span> <span data-preview-recipient></span></p>
                        <p class="mt-1"><span class="font-semibold text-slate-700">Gift from:</span> <span data-preview-sender></span></p>
                    </div>
                </div>
                <div class="border-t-4 border-[#A88444] bg-[#F7F7F7] px-6 py-5 text-center text-[10px] uppercase tracking-[0.12em] text-slate-500">Accommodation &nbsp; | &nbsp; Dining &nbsp; | &nbsp; Spa &nbsp; | &nbsp; Experiences</div>
            </div>
        </div>
    </div>

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
            const recipientFields = document.querySelector('[data-recipient-fields]');
            const messageField = document.querySelector('[data-message-field]');
            const giftSummary = document.querySelector('[data-gift-summary]');
            const giftModal = document.querySelector('[data-gift-modal]');
            const giftPreview = document.querySelector('[data-gift-preview]');
            const giftName = document.getElementById('gift_recipient_name');
            const giftEmail = document.getElementById('gift_recipient_email');
            const giftSender = document.getElementById('gift_sender_name');
            const giftMessage = document.getElementById('gift_message');
            const giftFromInput = document.getElementById('gift_from');
            const personalMessage = document.getElementById('personal_message');
            const defaultGiftMessage = @json($defaultGiftMessage);
            let selfNameValue = @json($purchaseFor === 'self' ? $buyerName : ($member?->full_name ?: $member?->name ?: ''));
            let selfEmailValue = @json($purchaseFor === 'self' ? $buyerEmail : ($member?->email ?: ''));

            function setDialogState(dialog, open) {
                if (!dialog) return;
                dialog.classList.toggle('hidden', !open);
                dialog.setAttribute('aria-hidden', open ? 'false' : 'true');
                document.documentElement.classList.toggle('overflow-hidden', open);
            }

            function updateGiftSummary() {
                const recipient = document.querySelector('[data-gift-summary-recipient]');
                const message = document.querySelector('[data-gift-summary-message]');

                if (recipient) recipient.textContent = giftName?.value ? `For ${giftName.value}` : 'Gift details not completed';
                if (message) message.textContent = giftMessage?.value || defaultGiftMessage;
            }

            function updatePreview() {
                const recipient = document.querySelector('[data-preview-recipient]');
                const sender = document.querySelector('[data-preview-sender]');
                const message = document.querySelector('[data-preview-message]');

                if (recipient) recipient.textContent = giftName?.value || 'Your recipient';
                if (sender) sender.textContent = giftSender?.value || 'A special someone';
                if (message) message.textContent = giftMessage?.value || defaultGiftMessage;
            }

            function saveGiftDetails() {
                const error = document.querySelector('[data-gift-error]');
                const isValid = Boolean(giftName?.value.trim() && giftEmail?.checkValidity() && giftEmail.value.trim());

                error?.classList.toggle('hidden', isValid);
                if (!isValid) return false;

                nameInput.value = giftName.value.trim();
                emailInput.value = giftEmail.value.trim();
                personalMessage.value = giftMessage.value.trim() || defaultGiftMessage;
                giftFromInput.value = giftSender.value.trim();
                updateGiftSummary();
                setDialogState(giftModal, false);
                return true;
            }

            function syncLabels() {
                const selected = document.querySelector('[data-voucher-purchase-for]:checked');
                const isSelf = selected && selected.value === 'self';

                if (nameLabel) {
                    nameLabel.textContent = isSelf ? 'Your Name' : 'Recipient Name';
                }

                if (emailLabel) {
                    emailLabel.textContent = isSelf ? 'Your Email' : 'Recipient Email';
                }

                recipientFields?.classList.toggle('hidden', !isSelf);
                messageField?.classList.toggle('hidden', !isSelf);
                giftSummary?.classList.toggle('hidden', isSelf);

                if (isSelf) {
                    if (nameInput) nameInput.value = selfNameValue;
                    if (emailInput) emailInput.value = selfEmailValue;
                } else {
                    if (!giftMessage.value.trim()) giftMessage.value = defaultGiftMessage;
                    updateGiftSummary();
                }
            }

            radios.forEach(function (radio) {
                radio.addEventListener('change', function () {
                    syncLabels();
                    if (radio.checked && radio.value === 'gift') setDialogState(giftModal, true);
                });
            });
            nameInput?.addEventListener('input', () => {
                if (document.querySelector('[data-voucher-purchase-for]:checked')?.value === 'self') selfNameValue = nameInput.value;
            });
            emailInput?.addEventListener('input', () => {
                if (document.querySelector('[data-voucher-purchase-for]:checked')?.value === 'self') selfEmailValue = emailInput.value;
            });

            document.querySelectorAll('[data-open-gift-modal]').forEach((button) => button.addEventListener('click', () => setDialogState(giftModal, true)));
            document.querySelectorAll('[data-close-gift-modal]').forEach((button) => button.addEventListener('click', () => setDialogState(giftModal, false)));
            document.querySelectorAll('[data-add-gift-to-cart]').forEach((button) => button.addEventListener('click', function () {
                if (saveGiftDetails()) nameInput.closest('form')?.requestSubmit();
            }));
            document.querySelectorAll('[data-open-gift-preview]').forEach((button) => button.addEventListener('click', function () {
                updatePreview();
                setDialogState(giftPreview, true);
            }));
            document.querySelectorAll('[data-close-gift-preview]').forEach((button) => button.addEventListener('click', () => {
                setDialogState(giftPreview, false);
                document.documentElement.classList.add('overflow-hidden');
            }));

            giftModal?.addEventListener('click', (event) => {
                if (event.target === giftModal) setDialogState(giftModal, false);
            });
            giftPreview?.addEventListener('click', (event) => {
                if (event.target === giftPreview) {
                    setDialogState(giftPreview, false);
                    document.documentElement.classList.add('overflow-hidden');
                }
            });
            document.addEventListener('keydown', (event) => {
                if (event.key !== 'Escape') return;
                if (giftPreview && !giftPreview.classList.contains('hidden')) {
                    setDialogState(giftPreview, false);
                    document.documentElement.classList.add('overflow-hidden');
                } else if (giftModal && !giftModal.classList.contains('hidden')) {
                    setDialogState(giftModal, false);
                }
            });

            syncLabels();
            updatePreview();
        });
    </script>
</x-layouts.app>
