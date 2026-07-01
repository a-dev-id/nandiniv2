@props([
'items' => collect(),
'wrapperClass' => '',
'bottomPaddingClass' => 'pb-16 md:pb-28',
'innerPaddingClass' => 'lg:px-16',
'itemPaddingClass' => 'px-3',
'previousButtonClass' => 'left-2 lg:left-10',
'nextButtonClass' => 'right-2 lg:right-10',
'buttonPositionClass' => 'top-1/2',
'buttonAlignClass' => 'justify-start',
'showReserveButton' => true,
'routeName' => null,
'variant' => 'cards',
])

@once
@push('css')
<style>
    .item-carousel-description-collapsed {
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 3;
        overflow: hidden;
    }

</style>
@endpush
@endonce

<section class="{{ $bottomPaddingClass }} {{ $wrapperClass }}">
    <div class="item-carousel-wrap mx-auto {{ $innerPaddingClass }} relative">

        <div class="itemcarousel-slick">
            @foreach ($items as $item)
            <article class="{{ $itemPaddingClass }} h-full w-full flex">
                <div class="flex flex-col h-full w-full">

                    @php
                    $title = $item->title ?? $item->name ?? '';
                    $sectionImage = $item->images?->first();
                    $image = $item->card_image ?? $item->hero_image ?? $item->image ?? $sectionImage?->image ?? null;
                    $alt = $item->card_image_alt ?? $item->hero_image_alt ?? $item->image_alt ?? $sectionImage?->image_alt ?? ($title ?: 'Gallery image');

                    $url = '#';

                    if (! empty($item->show_url)) {
                    $url = $item->show_url;
                    } elseif ($routeName && \Illuminate\Support\Facades\Route::has($routeName)) {
                    $url = route($routeName, $item->slug);
                    }

                    $excerptSummary = html_entity_decode(strip_tags((string) ($item->excerpt ?? '')), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $descriptionSummary = html_entity_decode(strip_tags((string) ($item->description ?? '')), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $summary = trim($excerptSummary) !== '' ? $excerptSummary : $descriptionSummary;
                    $summary = trim((string) preg_replace('/\s+/', ' ', $summary));
                    $shortSummary = \Illuminate\Support\Str::words(
                    preg_replace('/\.{3,}/', '', $summary) ?: $summary,
                    24,
                    '...'
                    );

                    $reserveUrl = $item->resolved_button_url
                    ?? (! empty($item->booking_url)
                    ? \App\Support\MemberBookingVoucher::appendToUrl($item->booking_url)
                    : null);

                    $reserveLabel = $item instanceof \App\Models\Accommodation
                    ? 'Reserve'
                    : ($item->button_label ?: 'Reserve');
                    @endphp

                    @if ($variant === 'gallery')
                    <div class="group aspect-[4/3] md:aspect-[4/3] overflow-hidden bg-slate-100">
                        @if ($image)
                        <img src="{{ asset('storage/' . $image) }}" alt="{{ $alt }}" class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" width="640" height="480" loading="lazy" decoding="async" />
                        @endif
                    </div>
                    @else
                    <a href="{{ $url }}" class="block">
                        <div class="aspect-[4/3] md:aspect-4/3 overflow-hidden bg-slate-100 group">
                            @if ($image)
                            <img src="{{ asset('storage/' . $image) }}" alt="{{ $alt }}" class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" width="640" height="480" loading="lazy" decoding="async" />
                            @endif
                        </div>
                    </a>

                    <div class="flex flex-col grow border border-slate-200 border-t-0 bg-white px-6 py-6 sm:px-7">
                        <h3 class="text-base font-semibold leading-snug text-slate-700 uppercase mb-3 sm:text-lg">
                            <a href="{{ $url }}" class="transition hover:text-[#A88444]">
                                {{ $title }}
                            </a>
                        </h3>

                        @if ($summary)
                        <p x-data="{ expanded: false }" x-bind:aria-expanded="expanded.toString()" role="button" tabindex="0" class="mt-2 grow cursor-pointer text-xs leading-relaxed text-slate-600 sm:text-sm" @click="expanded = ! expanded" @keydown.enter.prevent="expanded = ! expanded" @keydown.space.prevent="expanded = ! expanded">
                            <span x-show="! expanded">{{ $shortSummary }}</span>
                            <span x-show="expanded">{{ $summary }}</span>
                        </p>
                        @endif

                        <div class="fold-carousel-actions mt-9 flex flex-wrap items-center {{ $buttonAlignClass }} gap-4">
                            <a href="{{ $url }}" class="inline-flex min-w-[120px] items-center justify-center border border-slate-700 px-4 py-2.5 text-xs font-medium uppercase text-slate-700 transition hover:border-[#B8945B] hover:bg-[#B8945B] hover:text-white tracking-[0.08em] sm:text-sm">
                                Explore More
                            </a>

                            @if ($showReserveButton && $reserveUrl)
                            <a href="{{ $reserveUrl }}" class="inline-flex min-w-[120px] items-center justify-center border border-[#A88444] bg-[#A88444] px-4 py-2.5 text-xs font-medium uppercase text-white transition hover:border-[#B8945B] hover:bg-[#B8945B] tracking-[0.08em] sm:text-sm">
                                {{ $reserveLabel }}
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif

                </div>
            </article>
            @endforeach
        </div>

        <button type="button" class="itemcarousel-prev fold-carousel-arrow fold-image-carousel-arrow absolute {{ $previousButtonClass }} {{ $buttonPositionClass }} -translate-y-1/2 w-10 h-10 md:w-12 md:h-12 bg-[#A88444] text-white flex items-center justify-center z-10 transition hover:bg-[#A88444] tracking-[0.08em] font-medium" aria-label="Previous">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"></path>
            </svg>
        </button>

        <button type="button" class="itemcarousel-next fold-carousel-arrow fold-image-carousel-arrow absolute {{ $nextButtonClass }} {{ $buttonPositionClass }} -translate-y-1/2 w-10 h-10 md:w-12 md:h-12 bg-[#A88444] text-white flex items-center justify-center z-10 transition hover:bg-[#A88444] tracking-[0.08em] font-medium" aria-label="Next">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"></path>
            </svg>
        </button>

    </div>
</section>
