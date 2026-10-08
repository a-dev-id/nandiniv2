@props([
    'settings' => null,
    'image' => null,
    'mobileImage' => null,
])

@php
    $eyebrow = $settings?->hero_eyebrow ?: 'WELLNESS AT NANDINI JUNGLE';
    $heading = $settings?->hero_heading ?: 'ESSENCE SPA IN UBUD, BALI';
    $subheading = $settings?->hero_subheading ?: 'Wellness in the Heart of Nature';
    $description = $settings?->hero_description ?: 'Experience deeply restorative spa rituals inspired by Bali, nature and the surrounding jungle. A serene sanctuary to rebalance your body, mind and soul.';
    $primaryLabel = $settings?->hero_primary_cta_label ?: 'BOOK A SPA EXPERIENCE';
    $primaryUrl = $settings?->hero_primary_cta_url ?: 'https://wa.me/6281236871170?text='.rawurlencode('Hello, I would like to book a spa experience at Nandini Jungle.');
    $secondaryLabel = $settings?->hero_secondary_cta_label ?: 'EXPLORE TREATMENTS';
    $secondaryUrl = $settings?->hero_secondary_cta_url ?: '#treatments';
    $videoId = $settings?->hero_video_id ?: (blank($settings?->hero_image) ? 'jafQbgUnfL4' : null);
@endphp

<section class="font-sans text-white [&_a:focus-visible]:outline-2 [&_a:focus-visible]:outline-offset-[5px] [&_a:focus-visible]:outline-[#d1b77d]" aria-labelledby="spa-hero-title" data-gtm-section="hero">
    <div class="relative aspect-[4/3] overflow-hidden bg-[#1a3028] md:aspect-auto md:min-h-[max(700px,100svh)] lg:min-h-[max(720px,100vh)]">
        @if ($videoId)
            <div class="absolute inset-0">
                <x-heroes.video-hero :video-id="$videoId" :poster="$image" poster-alt="Essence Spa at Nandini Jungle in Ubud, Bali" title="Essence Spa at Nandini Jungle video" :hide-mobile-overlay="true" />
            </div>
        @elseif ($image)
            <picture class="absolute inset-0 block h-full w-full">
                @if ($mobileImage)<source media="(max-width: 767px)" srcset="{{ $mobileImage }}">@endif
                <img src="{{ $image }}" alt="{{ $settings?->hero_image_alt ?: 'Essence Spa at Nandini Jungle in Ubud, Bali' }}" class="h-full w-full object-cover object-[65%_center] md:object-[center_58%]" width="1920" height="1080" fetchpriority="high" decoding="async">
            </picture>
        @endif
        <div class="absolute inset-0 hidden h-full w-full bg-[linear-gradient(90deg,rgba(0,0,0,.68)_0%,rgba(0,0,0,.48)_35%,rgba(0,0,0,.18)_65%,rgba(0,0,0,.12)_100%),linear-gradient(0deg,rgba(0,0,0,.35),transparent_50%)] md:block" aria-hidden="true"></div>
        <div class="relative flex h-full w-full items-center px-6 pt-20 pb-6 md:min-h-[inherit] md:px-12 md:pt-36 md:pb-12 lg:px-[clamp(64px,5vw,100px)]">
            <div class="hidden max-w-xl md:block">
                @if ($eyebrow)<p class="mb-4 text-[10px] font-medium tracking-[.18em] text-[#A88444] uppercase sm:text-xs">{{ $eyebrow }}</p>@endif
                <h1 id="spa-hero-title" class="mb-5 font-span text-4xl leading-[1.05] [--heading-font-weight:400] [--heading-letter-spacing:-.025em] sm:text-5xl">{!! nl2br(e($heading)) !!}</h1>
                @if ($subheading)<p class="mb-3 hidden text-xs leading-relaxed font-medium sm:block sm:text-sm">{{ $subheading }}</p>@endif
                @if ($description)<p class="mb-6 hidden max-w-lg text-xs leading-relaxed text-white/85 sm:block sm:text-sm">{!! nl2br(e($description)) !!}</p>@endif
                <div class="flex flex-wrap gap-3">
                    @if ($primaryLabel && $primaryUrl)<x-buttons.link-button :href="$primaryUrl" variant="solid">{{ $primaryLabel }}</x-buttons.link-button>@endif
                    @if ($secondaryLabel && $secondaryUrl)<x-buttons.link-button :href="$secondaryUrl" variant="white-outline">{{ $secondaryLabel }}</x-buttons.link-button>@endif
                </div>
            </div>
        </div>
    </div>
</section>
