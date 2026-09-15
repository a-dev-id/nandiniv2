@props([
    'settings' => null,
    'image' => null,
    'mobileImage' => null,
])

@php
    $eyebrow = $settings?->hero_eyebrow ?: 'WELLNESS AT NANDINI JUNGLE';
    $heading = $settings?->hero_heading ?: "RESTORE IN THE HEART\nOF THE JUNGLE";
    $description = $settings?->hero_description ?: 'Experience deeply restorative spa rituals inspired by Bali, nature and the surrounding jungle. A serene sanctuary to rebalance your body, mind and soul.';
    $primaryLabel = $settings?->hero_primary_cta_label ?: 'BOOK A SPA EXPERIENCE';
    $primaryUrl = $settings?->hero_primary_cta_url ?: 'https://wa.me/6281236871170?text='.rawurlencode('Hello, I would like to book a spa experience at Nandini Jungle.');
    $secondaryLabel = $settings?->hero_secondary_cta_label ?: 'EXPLORE TREATMENTS';
    $secondaryUrl = $settings?->hero_secondary_cta_url ?: route('spa.index');
@endphp

<section class="relative min-h-[760px] overflow-hidden bg-[#173126] font-sans text-white md:min-h-[80svh] lg:min-h-[max(760px,82vh)]" aria-labelledby="spa-hero-title">
    @if ($image)
        <picture class="absolute inset-0 block h-full w-full">
            @if ($mobileImage)<source media="(max-width: 767px)" srcset="{{ $mobileImage }}">@endif
            <img src="{{ $image }}" alt="{{ $settings?->hero_image_alt }}" class="h-full w-full object-cover object-center md:object-[center_58%]" width="1920" height="1080" fetchpriority="high" decoding="async">
        </picture>
    @endif
    <div class="absolute inset-0 bg-black/35" aria-hidden="true"></div>
    <div class="relative flex min-h-[760px] items-center px-6 pb-14 pt-28 md:min-h-[80svh] md:px-12 md:pb-20 md:pt-36 lg:min-h-[max(760px,82vh)] lg:px-[clamp(64px,5vw,100px)]">
        <div class="max-w-2xl">
            @if ($eyebrow)<p class="mb-4 text-[10px] font-medium uppercase tracking-[.18em] text-[#d1b77d] sm:text-xs">{{ $eyebrow }}</p>@endif
            <h1 id="spa-hero-title" class="mb-6 font-span text-4xl leading-[1.05] [--heading-font-weight:400] [--heading-letter-spacing:-.025em] sm:text-5xl lg:text-6xl">{!! nl2br(e($heading)) !!}</h1>
            @if ($description)<p class="max-w-xl text-sm leading-relaxed text-white/90 sm:text-base">{!! nl2br(e($description)) !!}</p>@endif
            <div class="mt-8 flex flex-col items-start gap-3 sm:flex-row sm:flex-wrap">
                @if ($primaryLabel && $primaryUrl)<x-buttons.link-button :href="$primaryUrl" variant="solid">{{ $primaryLabel }}</x-buttons.link-button>@endif
                @if ($secondaryLabel && $secondaryUrl)<x-buttons.link-button :href="$secondaryUrl" variant="white-outline">{{ $secondaryLabel }}</x-buttons.link-button>@endif
            </div>
        </div>
    </div>
</section>
