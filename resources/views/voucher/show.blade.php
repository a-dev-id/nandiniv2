@php
$money = app(\App\Services\Voucher\MoneyFormatter::class);
$member = auth('member')->user();
$buyerName = old('recipient_name', $member?->full_name ?: $member?->name ?: '');
$buyerEmail = old('recipient_email', $member?->email ?: '');
$purchaseFor = old('purchase_for', 'self');
$defaultGiftMessage = '';
$giftFrom = old('gift_from', $member?->full_name ?: $member?->name ?: '');
$giftDeliveryMethod = old('delivery_method', 'email');
$priceSuffix = $money->priceTypeSuffix($voucher->price_type);
$unitLabel = $money->unitLabel($voucher->unit_type);
$priceOptions = $voucher->availablePriceOptions();
$termsSections = app(\App\Services\Voucher\VoucherTermsFormatter::class)->sections($voucher->terms_conditions);
$selectedPriceOptionKey = old('price_option');
$selectedPriceOption = collect($priceOptions)->firstWhere('key', $selectedPriceOptionKey);
$selectedOriginalPrice = $voucher->originalPriceForOption();
$selectedDiscountedPrice = $voucher->discountedPriceForOption();
$shareUrl = route('voucher.show', $voucher);
$shareImagePath = $voucher->preview_image;
$shareImageUrl = $shareImagePath ? Storage::disk('public')->url($shareImagePath) : asset('images/logo-njhg.png');
$encodedShareUrl = rawurlencode($shareUrl);
$encodedShareText = rawurlencode($voucher->title . ' | Nandini Jungle by Hanging Gardens');
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
<meta property="og:type" content="product">
<meta property="og:title" content="{{ $voucher->meta_title ?: $voucher->title . ' | Nandini Voucher' }}">
<meta property="og:description" content="{{ $voucher->meta_description ?: $voucher->excerpt }}">
<meta property="og:url" content="{{ $shareUrl }}">
<meta property="og:image" content="{{ $shareImageUrl }}">
<meta property="og:image:alt" content="{{ $voucher->image_alt ?: $voucher->title }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $voucher->meta_title ?: $voucher->title . ' | Nandini Voucher' }}">
<meta name="twitter:description" content="{{ $voucher->meta_description ?: $voucher->excerpt }}">
<meta name="twitter:image" content="{{ $shareImageUrl }}">
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
                            <img src="{{ Storage::disk('public')->url($galleryImage['image']) }}" alt="{{ $galleryImage['image_alt'] ?: $voucher->image_alt ?: $voucher->title . ($index ? ' - image ' . ($index + 1) : '') }}" class="h-[30rem] w-full object-cover md:h-[38rem] lg:h-[44rem]">
                        </div>
                        @endforeach
                    </div>
                    @if ($galleryImages->count() > 1)
                    <button type="button" class="voucher-gallery-prev fold-carousel-arrow fold-image-carousel-arrow absolute left-0 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center bg-[#A88444] text-white transition hover:bg-[#A88444] md:h-12 md:w-12" aria-label="Previous image">
                        <svg class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button type="button" class="voucher-gallery-next fold-carousel-arrow fold-image-carousel-arrow absolute right-0 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center bg-[#A88444] text-white transition hover:bg-[#A88444] md:h-12 md:w-12" aria-label="Next image">
                        <svg class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
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
                <div class="mt-4 flex items-center gap-1 border-y border-slate-200 py-2.5" aria-label="Share this voucher">
                    <span class="mr-2 text-xs uppercase tracking-[0.08em] text-slate-500">Share:</span>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ $encodedShareUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex h-7 w-7 items-center justify-center text-slate-600 transition hover:text-[#A88444]" aria-label="Share {{ $voucher->title }} on Facebook" title="Share on Facebook">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M13.5 22v-9h3l.45-3.5H13.5V7.26c0-1.01.28-1.7 1.73-1.7H17V2.43c-.31-.04-1.37-.13-2.61-.13-2.58 0-4.35 1.58-4.35 4.47V9.5H7.12V13h2.92v9h3.46Z" />
                        </svg>
                    </a>
                    <a href="https://twitter.com/intent/tweet?text={{ $encodedShareText }}&amp;url={{ $encodedShareUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex h-7 w-7 items-center justify-center text-slate-600 transition hover:text-[#A88444]" aria-label="Share {{ $voucher->title }} on Twitter" title="Share on Twitter">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M18.9 2H22l-6.77 7.74L23.2 22h-6.24l-4.89-6.39L6.49 22H3.38l7.24-8.28L2.98 2h6.4l4.42 5.84L18.9 2Zm-1.09 17.84h1.72L8.44 4.05H6.6l11.21 15.79Z" />
                        </svg>
                    </a>
                    <a href="https://wa.me/?text={{ $encodedShareText }}%20{{ $encodedShareUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex h-7 w-7 items-center justify-center text-slate-600 transition hover:text-[#A88444]" aria-label="Share {{ $voucher->title }} on WhatsApp" title="Share on WhatsApp">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.5 11.6a8.5 8.5 0 0 1-12.57 7.46L3.5 20.5l1.44-4.31A8.5 8.5 0 1 1 20.5 11.6Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.6 7.8c.25-.55.5-.56.73-.57h.62c.2 0 .4.07.5.35l.75 1.8c.08.25.04.43-.1.64l-.56.72c-.17.2-.3.39-.12.7.18.3.8 1.28 1.73 2.07 1.19 1.05 2.19 1.37 2.5 1.52.3.15.48.13.66-.08l.84-.98c.2-.23.4-.2.68-.1l1.72.81c.3.15.5.22.57.35.07.12.07.72-.17 1.41-.23.7-1.36 1.34-1.87 1.42-.48.08-1.1.12-1.77-.1-.41-.13-.94-.31-1.62-.6-.29-.13-5.04-1.86-6.87-6.45-.5-1.25.51-2.75.74-2.91Z" />
                        </svg>
                    </a>
                </div>
                <div class="mt-4 text-sm leading-7 text-slate-600 [&_p]:mb-4 [&_p:last-child]:mb-0 [&_ul]:list-disc [&_ul]:pl-5 [&_ol]:list-decimal [&_ol]:pl-5 [&_li]:mb-1">
                    @if (filled($voucher->description))
                    {!! $voucher->description !!}
                    @else
                    <p>{{ $voucher->excerpt }}</p>
                    @endif
                </div>
                <div class="mt-6">
                    @if ($voucher->has_discount)
                    <div class="mb-2 flex flex-wrap items-center gap-3">
                        <p class="text-sm tracking-[0.04em] text-slate-400 line-through">{{ $money->format($selectedOriginalPrice, $voucher->currency) }}</p>
                        <span class="bg-[#A88444]/10 px-2.5 py-1 text-xs font-semibold uppercase tracking-[0.08em] text-[#A88444]">Save {{ $voucher->discount_percentage }}%</span>
                    </div>
                    @endif
                    <p class="text-2xl font-semibold tracking-[0.06em] text-slate-700">{{ $money->format($selectedDiscountedPrice, $voucher->currency) }}{{ $priceSuffix }}</p>
                </div>
                @if ($unitLabel)
                <p class="mt-1 text-sm leading-6 text-slate-600">{{ $unitLabel }}</p>
                @endif

                <form method="POST" action="{{ route('voucher.cart.add', $voucher) }}" class="mt-8 space-y-5">
                    @csrf
                    @if ($priceOptions !== [])
                    <div>
                        <label for="price_option" class="block text-xs uppercase tracking-[0.08em] text-slate-700">Room Type</label>
                        <select id="price_option" name="price_option" class="mt-2 w-full border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 focus:border-[#A88444] focus:outline-none" data-voucher-price-option>
                            <option value="" @selected($selectedPriceOption === null)>Choose another room type</option>
                            @foreach ($priceOptions as $option)
                                <option
                                    value="{{ $option['key'] }}"
                                    @selected(($selectedPriceOption['key'] ?? null) === $option['key'])
                                >{{ $option['label'] }}{{ $option['additional_price'] > 0 ? ' (+'.$money->format($option['additional_price'], $voucher->currency).')' : '' }}</option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-xs leading-5 text-slate-500">Optional. Choose a different room category to add its upgrade price.</p>
                    </div>
                    @endif
                    <fieldset>
                        <legend class="block text-xs uppercase tracking-[0.08em] text-slate-700">Who is this voucher for?</legend>
                        <div class="mt-2 grid gap-3 sm:grid-cols-2">
                            <label class="flex cursor-pointer items-center gap-3 border border-slate-300 px-4 py-3 text-sm text-slate-700 transition has-[:checked]:border-[#A88444] has-[:checked]:bg-[#A88444]/10">
                                <input type="radio" name="purchase_for" value="self" class="h-4 w-4" {{ $purchaseFor==='self' ? 'checked' : '' }} data-voucher-purchase-for>
                                For myself
                            </label>
                            <label class="flex cursor-pointer items-center gap-3 border border-slate-300 px-4 py-3 text-sm text-slate-700 transition has-[:checked]:border-[#A88444] has-[:checked]:bg-[#A88444]/10">
                                <input type="radio" name="purchase_for" value="gift" class="h-4 w-4" {{ $purchaseFor==='gift' ? 'checked' : '' }} data-voucher-purchase-for>
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
                            <input id="recipient_name" name="recipient_name" value="{{ $buyerName }}" data-self-name="{{ $member?->full_name ?: $member?->name }}" class="mt-2 w-full border border-slate-300 px-4 py-3 text-sm focus:border-[#A88444] focus:outline-none">
                        </div>
                        <div>
                            <label for="recipient_email" class="block text-xs uppercase tracking-[0.08em] text-slate-700" data-recipient-email-label>{{ $purchaseFor === 'self' ? 'Your Email' : 'Recipient Email' }}</label>
                            <input id="recipient_email" name="recipient_email" type="email" value="{{ $buyerEmail }}" data-self-email="{{ $member?->email }}" class="mt-2 w-full border border-slate-300 px-4 py-3 text-sm focus:border-[#A88444] focus:outline-none">
                        </div>
                    </div>
                    <div data-message-field>
                        <label for="personal_message" class="block text-xs uppercase tracking-[0.08em] text-slate-700">Message or Note</label>
                        <textarea id="personal_message" name="personal_message" rows="4" class="mt-2 w-full border border-slate-300 px-4 py-3 text-sm focus:border-[#A88444] focus:outline-none" placeholder="Add a message or note for this voucher">{{ old('personal_message', $purchaseFor === 'gift' ? $defaultGiftMessage : '') }}</textarea>
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
                    <input type="hidden" name="delivery_method" id="delivery_method" value="{{ $giftDeliveryMethod }}">
                    <input type="hidden" name="hotel_note" id="hotel_note" value="{{ old('hotel_note') }}">
                    @if ($errors->any())
                    <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
                    @endif
                    <div class="flex flex-wrap gap-3">
                        <button type="submit" class="inline-flex items-center justify-center border border-[#A88444] bg-[#A88444] px-5 py-3 text-xs font-medium uppercase tracking-[0.08em] text-white transition hover:border-[#B8945B] hover:bg-[#B8945B] sm:text-sm">Add to Cart</button>
                        <div class="relative flex max-w-sm items-center rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-xs leading-5 text-slate-600 shadow-md sm:text-sm" role="status">
                            <span class="absolute -left-1.5 top-1/2 h-3 w-3 -translate-y-1/2 rotate-45 border-b border-l border-slate-200 bg-white" aria-hidden="true"></span>
                            Purchase more vouchers to unlock an extra 10% off your cart.
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </section>

    <div class="fixed inset-0 z-[100] hidden overflow-y-auto bg-slate-950/60 px-4 py-4 sm:px-6 sm:py-6" data-gift-modal aria-hidden="true">
        <div class="mx-auto max-w-3xl bg-white p-5 shadow-2xl sm:p-7" role="dialog" aria-modal="true" aria-labelledby="gift-modal-title">
            <div class="flex items-center justify-between gap-4">
                <h2 id="gift-modal-title" class="text-xl uppercase text-slate-700 sm:text-2xl">Give as a gift</h2>
                <button type="button" class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-300 text-xl text-slate-500 transition hover:border-[#A88444] hover:text-[#A88444]" data-close-gift-modal aria-label="Close gift form">&times;</button>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="gift_delivery_method" class="block text-xs uppercase tracking-[0.08em] text-slate-700">Gift voucher delivery option</label>
                    <select id="gift_delivery_method" class="mt-2 w-full border border-slate-300 bg-white px-4 py-2.5 text-sm focus:border-slate-300 focus:outline-none">
                        <option value="email" {{ $giftDeliveryMethod==='email' ? 'selected' : '' }}>Send to email</option>
                        <option value="print_at_resort" {{ $giftDeliveryMethod==='print_at_resort' ? 'selected' : '' }}>Print at resort (+ IDR 100,000)</option>
                    </select>
                </div>
                <div>
                    <label for="gift_recipient_name" class="block text-xs uppercase tracking-[0.08em] text-slate-700">Gift to <span class="text-red-600">*</span></label>
                    <input id="gift_recipient_name" required class="mt-2 w-full border border-slate-300 px-4 py-2.5 text-sm focus:border-[#A88444] focus:outline-none" value="{{ $purchaseFor === 'gift' ? $buyerName : '' }}" placeholder="Enter the recipient's name">
                </div>
                <div>
                    <label for="gift_sender_name" class="block text-xs uppercase tracking-[0.08em] text-slate-700">Gift from (optional)</label>
                    <input id="gift_sender_name" class="mt-2 w-full border border-slate-300 px-4 py-2.5 text-sm focus:border-slate-300 focus:outline-none" value="{{ $giftFrom }}" placeholder="Enter your name">
                </div>
            </div>
            <div class="mt-4" data-email-delivery-fields>
                <label for="gift_recipient_email" class="block text-xs uppercase tracking-[0.08em] text-slate-700">Recipient email <span class="text-red-600">*</span></label>
                <input id="gift_recipient_email" type="email" required class="mt-2 w-full border border-slate-300 px-4 py-2.5 text-sm focus:border-[#A88444] focus:outline-none" value="{{ $purchaseFor === 'gift' ? $buyerEmail : '' }}" placeholder="Enter the recipient's email address">
            </div>
            <div class="mt-4">
                <label for="gift_message" class="block text-xs uppercase tracking-[0.08em] text-slate-700">Message in the Voucher</label>
                <textarea id="gift_message" rows="3" maxlength="800" class="mt-2 w-full border border-slate-300 px-4 py-2.5 text-sm leading-6 focus:border-slate-300 focus:outline-none" placeholder="Write a personal message for the voucher recipient">{{ old('personal_message', $defaultGiftMessage) }}</textarea>
            </div>
            <div class="mt-4 hidden border border-[#A88444]/30 bg-[#A88444]/10 p-4" data-print-delivery-fields>
                <p class="text-sm leading-6 text-slate-700">An additional IDR 100,000 printing charge will be added to your order.</p>
                <label for="gift_hotel_note" class="mt-4 block text-xs uppercase tracking-[0.08em] text-slate-700">Note for the hotel team</label>
                <textarea id="gift_hotel_note" rows="4" maxlength="1000" class="mt-2 w-full border border-slate-300 bg-white px-4 py-2.5 text-sm leading-6 focus:border-slate-300 focus:outline-none" placeholder="Add printing, collection, or special preparation instructions">{{ old('hotel_note') }}</textarea>
            </div>
            <p class="mt-3 hidden text-sm text-red-600" data-gift-error>Please complete the required gift delivery details.</p>
            <div class="mt-6 flex flex-wrap items-center justify-end gap-3">
                <button type="button" class="border border-slate-300 px-5 py-2.5 text-xs font-medium uppercase tracking-[0.08em] text-slate-700 transition hover:border-[#A88444] hover:text-[#A88444]" data-open-gift-preview>Preview</button>
                <button type="button" class="border border-[#A88444] bg-[#A88444] px-5 py-2.5 text-xs font-medium uppercase tracking-[0.08em] text-white transition hover:border-[#B8945B] hover:bg-[#B8945B]" data-add-gift-to-cart>Add to Cart</button>
            </div>
        </div>
    </div>

    <div class="fixed inset-0 z-[110] hidden overflow-y-auto bg-slate-950/70 px-4 py-6 sm:px-6" data-gift-preview aria-hidden="true">
        <div class="mx-auto max-w-3xl bg-white p-5 shadow-2xl sm:p-8">
            <div class="flex justify-end">
                <button type="button" class="text-xs uppercase tracking-[0.08em] text-slate-600 hover:text-[#A88444]" data-close-gift-preview>&times; Close preview</button>
            </div>
            <div class="mt-6 bg-[#F5F0E7] p-3 sm:p-5">
                <div class="relative border border-[#B8945B] bg-[#FFFCF7] px-6 py-12 text-center outline outline-1 outline-offset-[-7px] outline-[#D8C6A8] sm:px-12 sm:py-16">
                    <span class="absolute left-5 top-5 h-8 w-8 border-l border-t border-[#B8945B]" aria-hidden="true"></span>
                    <span class="absolute right-5 top-5 h-8 w-8 border-r border-t border-[#B8945B]" aria-hidden="true"></span>
                    <span class="absolute bottom-5 left-5 h-8 w-8 border-b border-l border-[#B8945B]" aria-hidden="true"></span>
                    <span class="absolute bottom-5 right-5 h-8 w-8 border-b border-r border-[#B8945B]" aria-hidden="true"></span>
                    <p class="font-serif text-xs uppercase tracking-[0.34em] text-[#A88444]">Nandini Jungle by Hanging Gardens</p>
                    <p class="mt-5 text-[10px] uppercase tracking-[0.32em] text-slate-500">Gift Voucher</p>
                    <h2 class="mx-auto mt-5 max-w-xl font-serif text-2xl font-normal uppercase leading-snug tracking-[0.12em] text-[#17233A] sm:text-3xl">{{ $voucher->title }}</h2>
                    <div class="mx-auto mt-6 h-px w-20 bg-[#B8945B]"></div>
                    <p class="mx-auto mt-7 max-w-lg font-serif text-base italic leading-8 text-slate-600" data-preview-message></p>
                    <div class="mx-auto mt-8 max-w-xl border-y border-[#D8C6A8] py-7 text-sm leading-7 text-slate-600 [&_p]:mb-4 [&_p:last-child]:mb-0 [&_ul]:list-disc [&_ul]:pl-5 [&_ol]:list-decimal [&_ol]:pl-5 [&_li]:mb-1">
                        @if (filled($voucher->description))
                        {!! $voucher->description !!}
                        @else
                        <p>{{ $voucher->excerpt }}</p>
                        @endif
                    </div>
                    <div class="mx-auto mt-8 grid max-w-xl gap-6 text-left text-xs sm:grid-cols-2">
                        <p><span class="block text-[9px] uppercase tracking-[0.2em] text-[#A88444]">Gift to</span><span class="mt-2 block font-serif text-base text-[#17233A]" data-preview-recipient></span></p>
                        <p class="sm:text-right"><span class="block text-[9px] uppercase tracking-[0.2em] text-[#A88444]">Gift from</span><span class="mt-2 block font-serif text-base text-[#17233A]" data-preview-sender></span></p>
                    </div>
                    <p class="mt-10 text-[9px] uppercase tracking-[0.24em] text-slate-400">An invitation to experience the beauty of Bali</p>
                </div>
            </div>
        </div>
    </div>

    <section class="bg-[#F7F7F7] px-6 py-14 md:py-20">
        <div class="mx-auto grid max-w-6xl gap-10 md:grid-cols-2 md:gap-12">
            @foreach ($termsSections as $section)
                <div @class(['md:col-span-2' => count($termsSections) === 1])>
                    <h2 class="text-xl uppercase text-slate-700">{{ $section['title'] }}</h2>
                    <div class="mt-4 text-sm leading-6 text-slate-600 [&_a]:transition [&_a:hover]:text-[#A88444] [&_a:hover]:underline [&_li]:mb-2 [&_ol]:list-decimal [&_ol]:pl-5 [&_p]:mb-3 [&_ul]:list-disc [&_ul]:pl-5">
                        {!! $section['html'] !!}
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const radios = document.querySelectorAll('[data-voucher-purchase-for]');
            const voucherForm = document.getElementById('recipient_name')?.closest('form');
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
            const giftDeliveryMethod = document.getElementById('gift_delivery_method');
            const deliveryMethodInput = document.getElementById('delivery_method');
            const giftHotelNote = document.getElementById('gift_hotel_note');
            const hotelNoteInput = document.getElementById('hotel_note');
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
                if (message) message.textContent = giftDeliveryMethod?.value === 'print_at_resort'
                    ? 'Print at resort (+ IDR 100,000)'
                    : (giftMessage?.value || defaultGiftMessage);
            }

            function updateGiftDeliveryFields() {
                const isPrint = giftDeliveryMethod?.value === 'print_at_resort';
                document.querySelectorAll('[data-email-delivery-fields]').forEach((field) => field.classList.toggle('hidden', isPrint));
                document.querySelector('[data-print-delivery-fields]')?.classList.toggle('hidden', !isPrint);
                if (giftEmail) giftEmail.required = !isPrint;
            }

            function updatePreview() {
                const recipient = document.querySelector('[data-preview-recipient]');
                const sender = document.querySelector('[data-preview-sender]');
                const message = document.querySelector('[data-preview-message]');

                if (recipient) recipient.textContent = giftName?.value || 'Your recipient';
                if (sender) sender.textContent = giftSender?.value || 'A someone special';
                if (message) message.textContent = giftMessage?.value || defaultGiftMessage;
            }

            function saveGiftDetails() {
                const error = document.querySelector('[data-gift-error]');
                const isPrint = giftDeliveryMethod?.value === 'print_at_resort';
                const nameIsValid = Boolean(giftName?.value.trim());
                const emailIsValid = isPrint || Boolean(giftEmail?.value.trim() && giftEmail.checkValidity());
                const isValid = nameIsValid && emailIsValid;

                error?.classList.toggle('hidden', isValid);
                giftName?.classList.toggle('border-red-500', !nameIsValid);
                giftName?.setAttribute('aria-invalid', nameIsValid ? 'false' : 'true');
                giftEmail?.classList.toggle('border-red-500', !emailIsValid);
                giftEmail?.setAttribute('aria-invalid', emailIsValid ? 'false' : 'true');
                if (!isValid) {
                    (nameIsValid ? giftEmail : giftName)?.focus();
                    return false;
                }

                nameInput.value = giftName.value.trim();
                emailInput.value = isPrint ? '' : giftEmail.value.trim();
                emailInput.required = !isPrint;
                personalMessage.value = giftMessage.value.trim() || defaultGiftMessage;
                giftFromInput.value = giftSender.value.trim();
                deliveryMethodInput.value = giftDeliveryMethod.value;
                hotelNoteInput.value = isPrint ? giftHotelNote.value.trim() : '';
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

                recipientFields?.classList.add('hidden');
                messageField?.classList.toggle('hidden', !isSelf);
                giftSummary?.classList.toggle('hidden', isSelf);

                if (isSelf) {
                    if (nameInput) {
                        nameInput.value = '';
                        nameInput.required = false;
                    }
                    if (emailInput) {
                        emailInput.value = '';
                        emailInput.required = false;
                    }
                    if (deliveryMethodInput) deliveryMethodInput.value = 'email';
                } else {
                    if (deliveryMethodInput && giftDeliveryMethod) deliveryMethodInput.value = giftDeliveryMethod.value;
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
            giftDeliveryMethod?.addEventListener('change', function () {
                updateGiftDeliveryFields();
                updateGiftSummary();
            });
            [giftName, giftEmail].forEach((input) => input?.addEventListener('input', function () {
                input.classList.remove('border-red-500');
                input.setAttribute('aria-invalid', 'false');
                document.querySelector('[data-gift-error]')?.classList.add('hidden');
            }));
            document.querySelectorAll('[data-close-gift-modal]').forEach((button) => button.addEventListener('click', () => setDialogState(giftModal, false)));
            document.querySelectorAll('[data-add-gift-to-cart]').forEach((button) => button.addEventListener('click', function () {
                if (saveGiftDetails()) nameInput.closest('form')?.requestSubmit();
            }));
            voucherForm?.addEventListener('submit', function (event) {
                const isGift = document.querySelector('[data-voucher-purchase-for]:checked')?.value === 'gift';
                if (isGift && !saveGiftDetails()) {
                    event.preventDefault();
                    setDialogState(giftModal, true);
                }
            });
            document.querySelectorAll('[data-open-gift-preview]').forEach((button) => button.addEventListener('click', function () {
                updatePreview();
                setDialogState(giftPreview, true);
            }));
            document.querySelectorAll('[data-close-gift-preview]').forEach((button) => button.addEventListener('click', () => {
                setDialogState(giftPreview, false);
                document.documentElement.classList.add('overflow-hidden');
            }));

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
                }
            });

            syncLabels();
            updateGiftDeliveryFields();
            updatePreview();
        });
    </script>
</x-layouts.app>
