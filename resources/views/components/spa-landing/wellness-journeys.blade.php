@props([
    'settings' => null,
    'sourceJourney' => null,
])

@php
    $eyebrow = $settings?->wellness_journeys_eyebrow ?: 'WELLNESS JOURNEYS';
    $heading = $settings?->wellness_journeys_heading ?: 'SACRED JUNGLE WELLNESS JOURNEYS';
    $description = $settings?->wellness_journeys_description ?: 'Reconnect with your inner self through immersive multi-day experiences, combining traditional Balinese therapy, natural healing and the serene beauty of Nandini Jungle.';
    $journeys = $settings?->wellness_journeys_items ?? [];

    if (blank($journeys)) {
        $journeys = [
            [
                'title' => '2-DAY BALINESE WELLNESS ESCAPE',
                'description' => 'A two-day journey to revive your energy through a curated blend of Balinese massage, herbal rituals and time in nature.',
                'image' => 'spas/hero/68d37345-f6e6-4f1d-a962-725cf049fe62.webp',
                'image_alt' => 'Balinese massage treatment surrounded by the Nandini jungle',
                'details_label' => 'MORE DETAILS',
                'details_url' => '/spa-wellness/2-day-balinese-wellness-escape',
                'book_label' => 'BOOK NOW',
                'book_url' => $settings?->reservation_url,
            ],
            [
                'title' => '3-DAY INNER HARMONY RETREAT',
                'description' => 'A three-day retreat to restore balance and reconnect with yourself through signature treatments, holistic therapies and mindful rituals.',
                'image' => 'spas/hero/b5490cd9-d622-4ce2-b483-992ef4ea0c3c.webp',
                'image_alt' => 'Jungle spa treatment beds prepared for an inner harmony retreat',
                'details_label' => 'MORE DETAILS',
                'details_url' => '/spa-wellness/3-day-inner-harmony-retreat',
                'book_label' => 'BOOK NOW',
                'book_url' => $settings?->reservation_url,
            ],
            [
                'title' => '4-DAY DEEP BALINESE WELLNESS IMMERSION',
                'description' => 'A four-day immersive experience designed for deep relaxation and renewal, with a combination of traditional therapies, wellness rituals and personalised care.',
                'image' => 'spas/hero/19d9f7d8-6a93-422d-8a13-b333a2384ff8.webp',
                'image_alt' => 'Flower bath ritual for a deep Balinese wellness immersion',
                'details_label' => 'MORE DETAILS',
                'details_url' => '/spa-wellness/4-day-deep-balinese-wellness-immersion',
                'book_label' => 'BOOK NOW',
                'book_url' => $settings?->reservation_url,
            ],
        ];
    }

    if (filled($sourceJourney)) {
        $sourcePath = parse_url($sourceJourney['details_url'] ?? '', PHP_URL_PATH);
        $alreadyIncluded = collect($journeys)->contains(function (array $journey) use ($sourcePath): bool {
            $journeyPath = parse_url($journey['details_url'] ?? '', PHP_URL_PATH);

            return filled($sourcePath)
                && rtrim((string) $journeyPath, '/') === rtrim((string) $sourcePath, '/');
        });

        if (! $alreadyIncluded) {
            $journeys[] = $sourceJourney;
        }
    }

    $mainBaseUrl = 'https://'.config('domains.main');
    $resolveLink = static function (?string $url) use ($mainBaseUrl): string {
        if (blank($url)) {
            return '#';
        }

        return str_starts_with($url, '/') ? $mainBaseUrl.$url : $url;
    };

    $resolveImage = static function (?string $path): ?string {
        if (blank($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, '/')) {
            return asset(ltrim($path, '/'));
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->url($path);
    };
@endphp

<section id="treatments" class="scroll-mt-20 bg-[#f3f4f5] px-6 py-14 font-sans md:py-20" aria-labelledby="spa-wellness-journeys-title" data-gtm-section="treatments">
    <div class="mx-auto max-w-7xl">
        <header class="mx-auto mb-9 max-w-4xl text-center md:mb-12">
            <p class="mb-3 text-[10px] font-medium uppercase tracking-[.18em] text-[#A88444] sm:text-xs">{{ $eyebrow }}</p>
            <h2 id="spa-wellness-journeys-title" class="text-lg leading-snug font-medium text-slate-700 uppercase sm:text-xl">
                {!! nl2br(e($heading)) !!}
            </h2>
            <p class="mx-auto mt-5 max-w-3xl text-xs leading-relaxed text-slate-600 sm:text-sm">
                {{ $description }}
            </p>
        </header>

        <div class="item-carousel-wrap relative -mx-3 lg:mx-0">
            <div class="itemcarousel-slick spa-wellness-journeys-carousel" data-slides-to-show="3" data-total="{{ count($journeys) }}">
                @foreach ($journeys as $index => $journey)
                    @php
                        $image = $resolveImage($journey['image'] ?? null);
                        $detailsUrl = $resolveLink($journey['details_url'] ?? null);
                        $bookUrl = $resolveLink($journey['book_url'] ?? $settings?->reservation_url);
                    @endphp
                    <article class="flex h-full w-full flex-col px-3" aria-labelledby="spa-wellness-journey-{{ $index }}">
                        <div class="spa-wellness-journey-image aspect-4/3 w-full shrink-0 overflow-hidden bg-[#f3f4f5]">
                            @if ($image)
                                <img src="{{ $image }}" alt="{{ $journey['image_alt'] ?? '' }}" class="h-full w-full object-cover object-center" width="1200" height="900" loading="lazy" decoding="async">
                            @endif
                        </div>
                        <div class="flex flex-1 flex-col border border-t-0 border-slate-200 bg-white px-5 pt-5 pb-6 sm:px-6">
                            <h3 id="spa-wellness-journey-{{ $index }}" class="mb-3 font-sans text-base leading-snug font-semibold text-slate-700 uppercase [--heading-letter-spacing:.06em] sm:text-lg">
                                {{ $journey['title'] ?? '' }}
                            </h3>
                            <p class="mb-5 flex-1 text-xs leading-relaxed text-slate-600 sm:text-sm">
                                {{ $journey['description'] ?? '' }}
                            </p>
                            <div class="fold-carousel-actions flex flex-wrap items-center gap-3">
                                @if (filled($journey['details_label'] ?? null) && $detailsUrl !== '#')
                                    <x-buttons.link-button :href="$detailsUrl" variant="outline" class="min-h-10 flex-1 px-4 text-xs sm:flex-none">
                                        {{ $journey['details_label'] }}
                                    </x-buttons.link-button>
                                @endif
                                @if (filled($journey['book_label'] ?? null) && $bookUrl !== '#')
                                    <x-buttons.link-button :href="$bookUrl" variant="solid" class="min-h-10 flex-1 px-4 text-xs sm:flex-none">
                                        {{ $journey['book_label'] }}
                                    </x-buttons.link-button>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            @if (count($journeys) > 1)
                <button type="button" class="itemcarousel-prev fold-carousel-arrow fold-image-carousel-arrow home-mobile-image-arrow absolute left-0 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center bg-[#A88444] text-white transition hover:bg-[#B8945B] md:h-12 md:w-12 lg:-left-6" aria-label="Previous wellness journey">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m15.75 19.5-7.5-7.5 7.5-7.5"/></svg>
                </button>
                <button type="button" class="itemcarousel-next fold-carousel-arrow fold-image-carousel-arrow home-mobile-image-arrow absolute right-0 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center bg-[#A88444] text-white transition hover:bg-[#B8945B] md:h-12 md:w-12 lg:-right-6" aria-label="Next wellness journey">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                </button>
            @endif
        </div>
    </div>
</section>
