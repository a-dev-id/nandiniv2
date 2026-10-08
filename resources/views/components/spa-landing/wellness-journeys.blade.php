@props([
    'settings' => null,
    'sourceJourney' => null,
])

@php
    $eyebrow = $settings?->wellness_journeys_eyebrow ?: 'WELLNESS JOURNEYS';
    $heading = $settings?->wellness_journeys_heading ?: 'SACRED JUNGLE WELLNESS JOURNEYS';
    $description = $settings?->wellness_journeys_description ?: 'Reconnect with your inner self through immersive multi-day experiences, combining traditional Balinese therapy, natural healing and the serene beauty of Nandini Jungle.';
    $reservationUrl = filled($settings?->reservation_url)
        ? trim((string) $settings->reservation_url)
        : 'https://wa.me/6281236871170';
    $journeys = \App\Support\SpaWellnessJourneys::items($settings);

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

        $path = parse_url($url, PHP_URL_PATH);

        if (filled($path) && str_starts_with((string) $path, '/spa-wellness/')) {
            return route('spa-landing.treatments.show', ['slug' => basename((string) $path)]);
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

<section id="wellness-journeys" class="scroll-mt-20 bg-[#f3f4f5] px-6 py-14 font-sans md:px-12 md:py-20 lg:px-6" aria-labelledby="spa-wellness-journeys-title" data-gtm-section="wellness_journeys">
    <div class="item-carousel-wrap relative mx-auto max-w-7xl">
        <header class="mb-9 flex flex-col gap-6 text-center md:mb-12 md:flex-row md:items-end md:justify-between md:text-left">
            <div class="max-w-4xl">
                <p class="mb-3 text-[10px] font-medium uppercase tracking-[.18em] text-[#A88444] sm:text-xs">{{ $eyebrow }}</p>
                <h2 id="spa-wellness-journeys-title" class="text-lg leading-snug font-medium text-slate-700 uppercase sm:text-xl">
                    {!! nl2br(e($heading)) !!}
                </h2>
                <p class="mt-5 max-w-3xl text-xs leading-relaxed text-slate-600 sm:text-sm">
                    {{ $description }}
                </p>
            </div>

            @if (count($journeys) > 1)
                <div class="flex shrink-0 gap-2" aria-label="Wellness journey carousel controls">
                    <button type="button" class="itemcarousel-prev fold-carousel-arrow flex size-11 items-center justify-center bg-[#A88444] text-white transition hover:bg-[#B8945B]" aria-label="Previous wellness journey">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m15.75 19.5-7.5-7.5 7.5-7.5"/></svg>
                    </button>
                    <button type="button" class="itemcarousel-next fold-carousel-arrow flex size-11 items-center justify-center bg-[#A88444] text-white transition hover:bg-[#B8945B]" aria-label="Next wellness journey">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                    </button>
                </div>
            @endif
        </header>

        <div class="-mx-3">
            <div class="itemcarousel-slick spa-wellness-journeys-carousel" data-slides-to-show="3" data-total="{{ count($journeys) }}">
                @foreach ($journeys as $index => $journey)
                    @php
                        $image = $resolveImage($journey['image'] ?? null);
                        $detailsUrl = $resolveLink($journey['details_url'] ?? null);
                        $bookUrl = $resolveLink($journey['book_url'] ?? $reservationUrl);
                    @endphp
                    <article class="group flex h-full w-full flex-col px-3" aria-labelledby="spa-wellness-journey-{{ $index }}">
                        <div class="spa-wellness-journey-image aspect-4/3 w-full shrink-0 overflow-hidden bg-[#e5e1d8]">
                            @if ($image)
                                <img src="{{ $image }}" alt="{{ $journey['image_alt'] ?? '' }}" class="h-full w-full object-cover object-center transition duration-700 ease-out group-hover:scale-[1.025]" width="1200" height="900" loading="lazy" decoding="async">
                            @endif
                        </div>
                        <div class="flex flex-1 flex-col border border-t-0 border-slate-200 bg-white px-5 pt-5 pb-6 sm:px-6">
                            <p class="mb-3 text-[9px] font-medium uppercase tracking-[.18em] text-[#A88444]">Wellness Journey</p>
                            <h3 id="spa-wellness-journey-{{ $index }}" class="font-sans text-base leading-snug font-semibold text-slate-700 uppercase [--heading-letter-spacing:.06em] sm:text-lg">
                                {{ $journey['title'] ?? '' }}
                            </h3>
                            <p class="mt-3 mb-5 flex-1 text-xs leading-relaxed text-slate-600 sm:text-sm">
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

        </div>
    </div>
</section>
