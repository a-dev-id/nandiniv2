@props([
    'settings' => null,
    'image' => null,
    'mobileImage' => null,
])

@php
    $eyebrow = $settings?->hero_eyebrow ?: 'WELLNESS AT NANDINI JUNGLE';
    $heading = $settings?->hero_heading ?: 'ESSENCE SPA';
    $subheading = $settings?->hero_subheading ?: 'Wellness in the Heart of Nature';
    $description = $settings?->hero_description ?: 'Experience deeply restorative spa rituals inspired by Bali, nature and the surrounding jungle. A serene sanctuary to rebalance your body, mind and soul.';
    $primaryLabel = $settings?->hero_primary_cta_label ?: 'BOOK A SPA EXPERIENCE';
    $primaryUrl = $settings?->hero_primary_cta_url ?: 'https://wa.me/6281236871170?text='.rawurlencode('Hello, I would like to book a spa experience at Nandini Jungle.');
    $secondaryLabel = $settings?->hero_secondary_cta_label ?: 'EXPLORE TREATMENTS';
    $secondaryUrl = $settings?->hero_secondary_cta_url ?: route('spa.index');
    $videoId = $settings?->hero_video_id;
@endphp

<section class="relative min-h-[760px] overflow-hidden bg-[#173126] font-sans text-white md:min-h-[80svh] lg:h-screen lg:min-h-0" aria-labelledby="spa-hero-title" data-gtm-section="hero">
    @if ($videoId)
        <x-heroes.video-hero :video-id="$videoId" :poster="$image" background />
    @elseif ($image)
        <picture class="absolute inset-0 block h-full w-full">
            @if ($mobileImage)<source media="(max-width: 767px)" srcset="{{ $mobileImage }}">@endif
            <img src="{{ $image }}" alt="{{ $settings?->hero_image_alt }}" class="h-full w-full object-cover object-center md:object-[center_58%]" width="1920" height="1080" fetchpriority="high" decoding="async">
        </picture>
    @endif
    <div class="absolute inset-0 bg-[linear-gradient(90deg,rgba(0,0,0,.72)_0%,rgba(0,0,0,.48)_48%,rgba(0,0,0,.12)_100%)] max-md:bg-[linear-gradient(0deg,rgba(0,0,0,.78)_0%,rgba(0,0,0,.42)_58%,rgba(0,0,0,.14)_100%)]" aria-hidden="true"></div>
    <div class="relative flex min-h-[760px] items-center px-6 py-24 md:min-h-[80svh] md:px-12 md:py-28 lg:h-full lg:min-h-0 lg:px-[clamp(64px,5vw,100px)] lg:py-0">
        <div class="max-w-2xl">
            @if ($eyebrow)<p class="mb-4 text-[10px] font-medium uppercase tracking-[.18em] text-[#d1b77d] sm:text-xs">{{ $eyebrow }}</p>@endif
            <h1 id="spa-hero-title" class="mb-5 font-span text-4xl leading-[1.05] [--heading-font-weight:400] [--heading-letter-spacing:-.025em] sm:text-5xl">{!! nl2br(e($heading)) !!}</h1>
            @if ($subheading)<p class="mb-3 text-xs leading-relaxed font-medium text-white sm:text-sm">{{ $subheading }}</p>@endif
            @if ($description)<p class="mb-6 max-w-lg text-xs leading-relaxed text-white/85 sm:text-sm">{!! nl2br(e($description)) !!}</p>@endif
            <div class="flex flex-col items-start gap-3 sm:flex-row sm:flex-wrap">
                @if ($primaryLabel && $primaryUrl)<x-buttons.link-button :href="$primaryUrl" variant="solid">{{ $primaryLabel }}</x-buttons.link-button>@endif
                @if ($secondaryLabel && $secondaryUrl)<x-buttons.link-button :href="$secondaryUrl" variant="white-outline">{{ $secondaryLabel }}</x-buttons.link-button>@endif
            </div>
        </div>
    </div>
</section>
