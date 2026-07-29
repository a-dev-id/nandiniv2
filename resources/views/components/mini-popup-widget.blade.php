@php
use App\Models\MiniPopup;
use App\Services\Voucher\Cart\VoucherCartService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Js;
use Illuminate\Support\Str;

$miniPopups = Schema::hasTable('mini_popups')
? MiniPopup::query()
->active()
->ordered()
->get()
->map(function (MiniPopup $popup): array {
$image = trim((string) $popup->image);
$imageUrl = null;

if ($image !== '') {
$imageUrl = Str::startsWith($image, ['http://', 'https://'])
? $image
: asset('storage/' . ltrim($image, '/'));
}

return [
'title' => $popup->title,
'subtitle' => $popup->subtitle,
'description' => trim((string) $popup->description),
'imageUrl' => $imageUrl,
'imageAlt' => $popup->image_alt ?: $popup->title,
'buttonLabel' => \App\Support\DetailPageButtonLabel::resolve(
$popup->button_label,
$popup->button_route,
$popup->resolved_button_url,
),
'buttonUrl' => $popup->resolved_button_url,
];
})
->values()
->all()
: [];

$whatsappUrl = 'https://wa.me/6281236871170';
$showVoucherCartShortcut = ! config('features.disable_voucher_feature')
    && request()->routeIs('voucher.*')
    && \Illuminate\Support\Facades\Route::has('voucher.cart.index');
$voucherCartCount = $showVoucherCartShortcut ? app(VoucherCartService::class)->countUnits() : 0;
@endphp

<div x-data="{
        items: {{ Js::from($miniPopups) }},
        active: 0,
        minimized: false,
        whatsappTipVisible: true,
        storageKey: 'nandini-mini-popup-closed-date',
        todayKey() {
            return new Date().toISOString().slice(0, 10);
        },
        closePopup() {
            this.minimized = true;
            window.localStorage.setItem(this.storageKey, this.todayKey());
        },
        openPopup() {
            this.minimized = false;
            window.localStorage.removeItem(this.storageKey);
        },
        next() {
            if (this.items.length <= 1) return;
            this.active = (this.active + 1) % this.items.length;
        },
        prev() {
            if (this.items.length <= 1) return;
            this.active = (this.active - 1 + this.items.length) % this.items.length;
        },
        init() {
            if (window.localStorage.getItem(this.storageKey) === this.todayKey()) {
                this.minimized = true;
            }

            window.setInterval(() => {
                if (! this.minimized) {
                    this.next();
                }
            }, 6500);

            const cycleWhatsappTip = () => {
                this.whatsappTipVisible = true;

                window.setTimeout(() => {
                    this.whatsappTipVisible = false;

                    window.setTimeout(cycleWhatsappTip, 5000);
                }, 10000);
            };

            cycleWhatsappTip();
        }
    }" x-cloak>
    <template x-if="items.length > 0">
        <div>
            <div x-show="! minimized" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" class="fixed bottom-24 left-3 z-[65] w-[calc(100vw-1.5rem)] max-w-[560px] bg-white text-slate-700 shadow-2xl ring-1 ring-black/10 sm:bottom-5 sm:left-5 sm:w-[calc(100vw-2rem)]">
                <button type="button" class="absolute right-4 top-4 z-10 flex h-7 w-7 items-center justify-center text-slate-500 transition text-slate-700 tracking-[0.08em] font-medium" aria-label="Minimize offer popup" @click="closePopup()">
                    <span class="block h-0.5 w-5 rotate-45 bg-current"></span>
                    <span class="absolute block h-0.5 w-5 -rotate-45 bg-current"></span>
                </button>

                <div class="grid grid-cols-1 sm:min-h-[230px] sm:grid-cols-[170px_1fr]">
                    <div class="hidden bg-[#b1823b] sm:block">
                        <template x-if="items[active].imageUrl">
                            <img :src="items[active].imageUrl" :alt="items[active].imageAlt" class="h-full min-h-[230px] w-full object-cover" width="340" height="460" loading="lazy" decoding="async">
                        </template>
                    </div>

                    <div class="px-4 py-4 pr-11 sm:px-7 sm:py-7 sm:pr-12">
                        <p x-show="items[active].subtitle" class="mb-1.5 text-[9px] font-semibold uppercase leading-none text-[#b1823b] sm:mb-2 sm:text-sm" x-text="items[active].subtitle"></p>

                        <h3 class="text-base leading-snug uppercase text-slate-700 font-medium mb-3 sm:text-lg" x-text="items[active].title"></h3>

                        <div x-show="items[active].description" class="mt-2 text-[10px] leading-relaxed text-gray-600 sm:text-[14px] [&_p]:mb-1.5 sm:[&_p]:mb-2 [&_p:last-child]:mb-0 [&_ul]:space-y-1 sm:[&_ul]:space-y-2 [&_ul]:pl-4 sm:[&_ul]:pl-5 [&_ul]:list-disc [&_ol]:space-y-1 sm:[&_ol]:space-y-2 [&_ol]:pl-4 sm:[&_ol]:pl-5 [&_ol]:list-decimal [&_strong]:font-semibold [&_a]:text-[#b1823b] [&_a]:underline" x-html="items[active].description"></div>

                        <div class="mt-2 flex flex-wrap items-center justify-between gap-3 sm:gap-4">
                            <a x-show="items[active].buttonLabel" :href="items[active].buttonUrl" class="inline-flex min-w-0 flex-1 items-center justify-center bg-[#A88444] px-4 py-2.5 text-[9px] font-medium uppercase text-white transition hover:bg-[#B8945B] sm:min-w-[150px] sm:flex-none sm:px-5 sm:py-3 sm:text-sm tracking-[0.08em]" x-text="items[active].buttonLabel"></a>

                            <div x-show="items.length > 1" class="ml-auto flex items-center gap-2">
                                <button type="button" class="mini-popup-arrow flex h-7 w-7 items-center justify-center border border-[#A88444] text-[#A88444] transition tracking-[0.08em] font-medium" aria-label="Previous popup offer" @click="prev()">
                                    <span class="leading-none" aria-hidden="true">&lsaquo;</span>
                                </button>

                                <button type="button" class="mini-popup-arrow flex h-7 w-7 items-center justify-center border border-[#A88444] text-[#A88444] transition tracking-[0.08em] font-medium" aria-label="Next popup offer" @click="next()">
                                    <span class="leading-none" aria-hidden="true">&rsaquo;</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" x-show="minimized" x-transition class="fixed bottom-5 left-5 z-[66] flex h-14 w-14 items-center justify-center rounded-full bg-[#ef4444] text-white shadow-xl transition hover:bg-[#dc2626] tracking-[0.08em] font-medium" aria-label="Open offers popup" @click="openPopup()">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 7h18s-3 0-3-7" />
                    <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                </svg>
            </button>
        </div>
    </template>

    <div class="fixed bottom-5 right-4 z-[67] flex flex-col items-end gap-3 sm:right-5">
        @if ($showVoucherCartShortcut)
        <a href="{{ route('voucher.cart.index') }}" class="relative flex h-14 w-14 items-center justify-center rounded-full bg-[#A88444] text-white shadow-xl transition hover:bg-[#8f6b34] tracking-[0.08em] font-medium" style="position: relative;" aria-label="View voucher cart">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="9" cy="20" r="1.5" />
                <circle cx="18" cy="20" r="1.5" />
                <path d="M3 4h2l2.2 11.2a2 2 0 0 0 2 1.6h7.9a2 2 0 0 0 2-1.5L21 8H6.2" />
            </svg>

            @if ($voucherCartCount > 0)
            <span class="flex items-center justify-center rounded-full border-2 border-white bg-slate-900 text-[10px] font-bold leading-none text-white" style="position: absolute; top: -0.375rem; right: -0.375rem; min-width: 1.375rem; height: 1.375rem; padding: 0 0.3125rem; z-index: 1;">
                {{ $voucherCartCount > 99 ? '99+' : $voucherCartCount }}
            </span>
            @endif
        </a>
        @endif

        <div class="flex items-center gap-3">
            <div x-show="whatsappTipVisible" x-transition class="hidden rounded-xl bg-white px-4 py-3 text-xs leading-none text-slate-700 shadow-xl ring-1 ring-black/10 sm:block sm:text-sm">
                Hi, how may we assist you today?
            </div>

            <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="flex h-14 w-14 items-center justify-center rounded-full bg-[#25D366] text-white shadow-xl transition hover:bg-[#1ebe5d] tracking-[0.08em] font-medium" aria-label="Chat with us on WhatsApp">
                <svg class="h-7 w-7" viewBox="0 0 32 32" fill="currentColor" aria-hidden="true">
                    <path d="M16.04 3.2A12.77 12.77 0 0 0 5.15 22.6L3.2 29l6.57-1.86A12.76 12.76 0 1 0 16.04 3.2Zm0 23.18a10.54 10.54 0 0 1-5.38-1.47l-.39-.23-3.9 1.1 1.13-3.78-.25-.4a10.53 10.53 0 1 1 8.79 4.78Zm5.78-7.9c-.32-.16-1.88-.93-2.17-1.03-.29-.11-.5-.16-.72.16-.21.32-.82 1.03-1.01 1.24-.19.21-.37.24-.69.08-.32-.16-1.34-.49-2.55-1.57-.94-.84-1.58-1.88-1.76-2.2-.19-.32-.02-.49.14-.65.14-.14.32-.37.48-.56.16-.19.21-.32.32-.53.11-.21.05-.4-.03-.56-.08-.16-.72-1.73-.98-2.36-.26-.62-.52-.53-.72-.54h-.61c-.21 0-.56.08-.85.4-.29.32-1.11 1.09-1.11 2.65s1.14 3.07 1.3 3.28c.16.21 2.24 3.42 5.43 4.8.76.33 1.35.52 1.81.67.76.24 1.46.21 2.01.13.61-.09 1.88-.77 2.15-1.51.27-.74.27-1.38.19-1.51-.08-.13-.29-.21-.61-.37Z" />
                </svg>
            </a>
        </div>
    </div>
</div>
