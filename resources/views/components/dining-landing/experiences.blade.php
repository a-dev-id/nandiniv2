@props([
    'exclude' => null,
    'settings' => null,
    'headingId' => 'dining-experiences-title',
    'backgroundClass' => 'bg-white',
])

@php
    $cmsCards = \Illuminate\Support\Facades\Schema::hasTable('dining_experiences')
        ? \App\Models\DiningExperience::query()
            ->published()
            ->inDisplayOrder()
            ->get()
        : collect();

    $cards = $cmsCards->map(fn ($item) => [
            'key' => match ($item->slug) {
                'wild-ginger-restaurant' => 'restaurant',
                'bar-and-lounge' => 'bar',
                'afternoon-tea' => 'tea',
                'wine-cellar-experience' => 'wine',
                'romantic-dining' => 'romantic',
                default => (string) $item->id,
            },
            'slug' => $item->slug,
            'title' => $item->display_title,
            'description' => $item->short_description,
            'cta' => $item->card_cta_label,
            'alt' => $item->card_image_alt,
            'image' => $item->card_image,
        ])
        ->values();

    $cards = $cards->reject(fn ($card) => $exclude && $card['slug'] === $exclude)->values();
@endphp

<section class="{{ $backgroundClass }} px-6 py-14 font-sans md:py-20" aria-labelledby="{{ $headingId }}">
    <div class="mx-auto max-w-[1800px]">
        <header class="mb-8 text-center">
            <p class="mb-3 text-[10px] font-medium tracking-[.18em] text-[#A88444] uppercase sm:text-xs">{{ $settings?->experiences_eyebrow }}</p>
            <h2 id="{{ $headingId }}" class="text-lg leading-snug font-medium text-slate-700 uppercase sm:text-xl">{!! nl2br(e($settings?->experiences_heading)) !!}</h2>
        </header>

        <div class="item-carousel-wrap relative -mx-3 lg:mx-0">
            <div class="itemcarousel-slick dining-experiences-carousel" data-slides-to-show="5">
            @foreach ($cards as $card)
                @php
                    $image = $card['image'];
                    if ($image && ! str_starts_with($image, 'http') && \Illuminate\Support\Facades\Storage::disk('public')->exists($image)) {
                        $image = asset('storage/'.$image);
                    }
                @endphp
                <article class="group flex h-full w-full flex-col px-3" aria-labelledby="dining-experience-{{ $card['key'] }}">
                    <div class="dining-experience-image aspect-4/3 w-full shrink-0 overflow-hidden bg-white">
                        @if ($image)
                            <img src="{{ asset($image) }}" alt="{{ $card['alt'] }}" class="h-full w-full object-cover object-center transition-transform duration-500 lg:motion-safe:group-hover:scale-[1.025]" width="800" height="600" loading="lazy" decoding="async">
                        @endif
                    </div>
                    <div class="flex flex-1 flex-col border border-t-0 border-slate-200 bg-white px-6 pt-5 pb-6">
                        <h3 id="dining-experience-{{ $card['key'] }}" class="mb-3 text-base leading-snug font-semibold text-slate-700 uppercase sm:text-lg">{{ $card['title'] }}</h3>
                        <p class="mb-6 text-xs leading-relaxed text-slate-600 sm:text-sm">{{ $card['description'] }}</p>
                        @if (filled($card['slug']) && filled($card['cta']))
                        <a href="{{ route('dining-landing.experiences.show', ['experience' => $card['slug']]) }}" class="mt-auto inline-flex min-h-8 items-center gap-2 self-start text-[11px] leading-[1.5] font-semibold tracking-[.04em] text-[#8f6b34] uppercase transition-colors duration-300 hover:text-[#20271f] focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#8f6b34]">
                            <span>{{ $card['cta'] }}</span>
                            <span class="shrink-0 transition-transform duration-300 lg:motion-safe:group-hover:translate-x-1" aria-hidden="true">&rarr;</span>
                        </a>
                        @endif
                    </div>
                </article>
            @endforeach
            </div>
            <button type="button" class="itemcarousel-prev fold-carousel-arrow fold-image-carousel-arrow home-mobile-image-arrow absolute left-0 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center bg-[#A88444] text-white transition hover:bg-[#B8945B] md:h-12 md:w-12 lg:-left-6" aria-label="Previous dining experience"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m15.75 19.5-7.5-7.5 7.5-7.5"/></svg></button>
            <button type="button" class="itemcarousel-next fold-carousel-arrow fold-image-carousel-arrow home-mobile-image-arrow absolute right-0 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center bg-[#A88444] text-white transition hover:bg-[#B8945B] md:h-12 md:w-12 lg:-right-6" aria-label="Next dining experience"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg></button>
        </div>
    </div>
</section>
