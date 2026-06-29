@props([
'accommodation' => null,
'reverse' => false,
'boxed' => true,
])

@php
$componentId = 'accommodation-gallery-' . ($accommodation?->id ?? uniqid());

$features = $accommodation?->features ?? collect();

$galleryImages = collect();

if ($accommodation?->activeImages?->isNotEmpty()) {
$galleryImages = $accommodation->activeImages->map(function ($image) use ($accommodation) {
return [
'src' => asset('storage/' . $image->image),
'alt' => $image->image_alt ?: $accommodation->title,
];
});
}

if ($galleryImages->isEmpty()) {
if ($accommodation?->hero_image) {
$galleryImages->push([
'src' => asset('storage/' . $accommodation->hero_image),
'alt' => $accommodation->hero_image_alt ?: $accommodation->title,
]);
}

if ($accommodation?->card_image && $accommodation->card_image !== $accommodation->hero_image) {
$galleryImages->push([
'src' => asset('storage/' . $accommodation->card_image),
'alt' => $accommodation->card_image_alt ?: $accommodation->title,
]);
}
}

$wrapper = $boxed
? 'w-full max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8'
: 'w-full';

$gridOrderFeatures = $reverse
? 'order-2 lg:order-2'
: 'order-2 lg:order-1';

$gridOrderGallery = $reverse
? 'order-1 lg:order-1'
: 'order-1 lg:order-2';

$firstImage = $galleryImages->first();

$thumbnailImages = $galleryImages;

if ($galleryImages->count() > 1 && $galleryImages->count() < 6) { $thumbnailImages=collect(); while ($thumbnailImages->count() < 6) { $thumbnailImages=$thumbnailImages->merge($galleryImages);
        }

        $thumbnailImages = $thumbnailImages->take(6)->values();
        }

        $directBookingUrl = $accommodation?->booking_url;
        $secondaryButtonLabel = trim((string) ($accommodation?->button_label ?? ''));
        $secondaryButtonUrl = trim((string) ($accommodation?->button_url ?? ''));
        $normalizedSecondaryButtonLabel = strtolower(trim(preg_replace('/\s+/', ' ', $secondaryButtonLabel)));
        $isRoomFlightButton = $normalizedSecondaryButtonLabel === 'room + flight'
        || str_contains($secondaryButtonUrl, 'reserve-online.net/DPSearch')
        || str_contains($secondaryButtonUrl, 'ovs.tour-list.com/DPSearch');

        if ($isRoomFlightButton) {
            $secondaryButtonLabel = $secondaryButtonLabel !== '' ? $secondaryButtonLabel : 'Room + Flight';
            $secondaryButtonUrl = $accommodation?->room_flight_url ?: $secondaryButtonUrl;
        }
        @endphp

        @if ($accommodation)
        <section class="py-14 md:py-28 w-full bg-[#F7F7F7]">
            <div class="{{ $wrapper }}">
                <div class="grid grid-cols-1 lg:grid-cols-12 items-center gap-10 lg:gap-16">

                    {{-- Features --}}
                    <div class="lg:col-span-5 {{ $gridOrderFeatures }}">
                        <div class="px-4 sm:px-8 md:px-10 lg:px-12">
                            <h2 class="text-lg leading-snug uppercase text-slate-700 font-medium mb-3 sm:text-xl">
                                Features
                            </h2>

                            @if ($features->isNotEmpty())
                            <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">
                                @foreach ($features as $feature)
                                <div class="flex items-center gap-3">
                                    @if ($feature->icon_image)
                                    <img src="{{ asset('storage/' . $feature->icon_image) }}" alt="{{ $feature->label }}" class="w-6 h-6 object-contain shrink-0 brightness-0 opacity-80" loading="lazy">
                                    @endif

                                    <span class="text-xs leading-6 text-slate-700 sm:text-sm">
                                        {{ $feature->label }}
                                    </span>
                                </div>
                                @endforeach
                            </div>
                            @endif

                            @if ($directBookingUrl || ($secondaryButtonLabel !== '' && $secondaryButtonUrl !== ''))
                            <div class="mt-10 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                                @if ($directBookingUrl)
                                <x-buttons.link-button :href="$directBookingUrl" variant="solid">
                                    Book Now
                                </x-buttons.link-button>
                                @endif

                                @if ($secondaryButtonLabel !== '' && $secondaryButtonUrl !== '')
                                <x-buttons.link-button :href="$secondaryButtonUrl" variant="white-gold">
                                    {{ $secondaryButtonLabel }}
                                </x-buttons.link-button>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Gallery --}}
                    <div class="lg:col-span-7 {{ $gridOrderGallery }}">
                        @if ($galleryImages->isNotEmpty())
                        <div id="{{ $componentId }}" class="w-full">
                            {{-- Main Image --}}
                            <div class="relative aspect-[4/3] md:aspect-[16/9] overflow-hidden bg-slate-100">
                                <img data-main-image src="{{ $firstImage['src'] }}" alt="{{ $firstImage['alt'] }}" class="w-full h-full object-cover transition-opacity duration-300" loading="lazy">
                            </div>

                            @if ($thumbnailImages->count() > 1)
                            {{-- Thumbnail Slider --}}
                            <div class="relative mt-2 px-10">
                                <button type="button" data-gallery-prev class="absolute left-0 top-1/2 -translate-y-1/2 z-10 w-8 h-8 bg-black text-white flex items-center justify-center hover:bg-[#B8945B] transition tracking-[0.08em] font-medium" aria-label="Previous image">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                                    </svg>
                                </button>

                                <div data-thumbnail-track class="flex gap-3 overflow-x-auto scroll-smooth [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                                    @foreach ($thumbnailImages as $index => $image)
                                    <button type="button" data-gallery-thumb data-index="{{ $index }}" data-src="{{ $image['src'] }}" data-alt="{{ $image['alt'] }}" class="shrink-0 w-24 sm:w-28 md:w-32 aspect-[4/3] overflow-hidden bg-slate-100 border {{ $index === 0 ? 'border-slate-800' : 'border-transparent' }} tracking-[0.08em] font-medium" aria-label="Show image {{ $index + 1 }}">
                                        <img src="{{ $image['src'] }}" alt="{{ $image['alt'] }}" class="w-full h-full object-cover" loading="lazy">
                                    </button>
                                    @endforeach
                                </div>

                                <button type="button" data-gallery-next class="absolute right-0 top-1/2 -translate-y-1/2 z-10 w-8 h-8 bg-black text-white flex items-center justify-center hover:bg-[#B8945B] transition tracking-[0.08em] font-medium" aria-label="Next image">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                    </svg>
                                </button>
                            </div>
                            @endif
                        </div>

                        @if ($galleryImages->count() > 1)
                        <script>
                            (() => {
                        const root = document.getElementById(@json($componentId));

                        if (!root) return;

                        const mainImage = root.querySelector('[data-main-image]');
                        const thumbs = Array.from(root.querySelectorAll('[data-gallery-thumb]'));
                        const thumbnailTrack = root.querySelector('[data-thumbnail-track]');
                        const prevButton = root.querySelector('[data-gallery-prev]');
                        const nextButton = root.querySelector('[data-gallery-next]');

                        let currentIndex = 0;
                        let interval = null;

                        const centerActiveThumbnail = (activeThumb) => {
                            if (!thumbnailTrack || !activeThumb) return;

                            const trackWidth = thumbnailTrack.clientWidth;
                            const thumbLeft = activeThumb.offsetLeft;
                            const thumbWidth = activeThumb.clientWidth;

                            thumbnailTrack.scrollTo({
                                left: thumbLeft - (trackWidth / 2) + (thumbWidth / 2),
                                behavior: 'smooth',
                            });
                        };

                        const showImage = (index, shouldCenterThumb = true) => {
                            if (!thumbs.length) return;

                            currentIndex = (index + thumbs.length) % thumbs.length;

                            const activeThumb = thumbs[currentIndex];

                            mainImage.style.opacity = '0';

                            setTimeout(() => {
                                mainImage.src = activeThumb.dataset.src;
                                mainImage.alt = activeThumb.dataset.alt;
                                mainImage.style.opacity = '1';
                            }, 180);

                            thumbs.forEach((thumb) => {
                                thumb.classList.remove('border-slate-800');
                                thumb.classList.add('border-transparent');
                            });

                            activeThumb.classList.remove('border-transparent');
                            activeThumb.classList.add('border-slate-800');

                            if (shouldCenterThumb) {
                                centerActiveThumbnail(activeThumb);
                            }
                        };

                        const nextImage = (shouldCenterThumb = true) => showImage(currentIndex + 1, shouldCenterThumb);
                        const prevImage = (shouldCenterThumb = true) => showImage(currentIndex - 1, shouldCenterThumb);

                        const startAutoSlide = () => {
                            stopAutoSlide();

                            interval = setInterval(() => {
                                nextImage(false);
                            }, 4000);
                        };

                        const stopAutoSlide = () => {
                            if (interval) {
                                clearInterval(interval);
                                interval = null;
                            }
                        };

                        thumbs.forEach((thumb) => {
                            thumb.addEventListener('click', () => {
                                showImage(Number(thumb.dataset.index), true);
                                startAutoSlide();
                            });
                        });

                        nextButton?.addEventListener('click', () => {
                            nextImage(true);
                            startAutoSlide();
                        });

                        prevButton?.addEventListener('click', () => {
                            prevImage(true);
                            startAutoSlide();
                        });

                        root.addEventListener('mouseenter', stopAutoSlide);
                        root.addEventListener('mouseleave', startAutoSlide);

                        startAutoSlide();
                    })();
                        </script>
                        @endif
                        @endif
                    </div>

                </div>
            </div>
        </section>
        @endif
