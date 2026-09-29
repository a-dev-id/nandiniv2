@props([
    'settings' => null,
    'image' => null,
])

<section class="relative flex min-h-[720px] items-end overflow-hidden bg-[#101713] font-sans text-white lg:min-h-screen" aria-labelledby="festive-hero-title" data-gtm-section="hero">
    @if ($image)
        <img src="{{ $image }}" alt="{{ $settings?->hero_image_alt }}" class="absolute inset-0 h-full w-full object-cover object-center" width="1920" height="1080" fetchpriority="high" decoding="async">
    @endif
    <div class="absolute inset-0 bg-[linear-gradient(90deg,rgba(0,0,0,.4)_0%,rgba(0,0,0,.18)_52%,rgba(0,0,0,.05)_100%)] max-md:bg-[linear-gradient(0deg,rgba(0,0,0,.48)_0%,rgba(0,0,0,.16)_65%,rgba(0,0,0,.05)_100%)]" aria-hidden="true"></div>
    <div class="relative z-10 w-full px-6 pb-16 pt-36 md:px-12 md:pb-20 lg:px-[clamp(64px,5vw,100px)] lg:pb-24 lg:pt-44">
        <div class="max-w-3xl">
            @if ($settings?->hero_eyebrow)
                <p class="mb-4 text-[10px] font-medium uppercase tracking-[.18em] text-[#d1b77d] sm:text-xs">{{ $settings->hero_eyebrow }}</p>
            @endif
            @if ($settings?->hero_heading)
                <h1 id="festive-hero-title" class="font-span text-4xl leading-[1.05] [--heading-font-weight:400] [--heading-letter-spacing:-.025em] sm:text-5xl">{!! nl2br(e($settings->hero_heading)) !!}</h1>
            @endif
            @if ($settings?->hero_subheading)
                <p class="mt-5 text-xs font-medium uppercase tracking-[.16em] text-white sm:text-sm">{{ $settings->hero_subheading }}</p>
            @endif
            @if ($settings?->hero_description)
                <p class="mt-4 max-w-2xl text-xs leading-relaxed text-white/85 sm:text-sm">{!! nl2br(e($settings->hero_description)) !!}</p>
            @endif
            <div class="mt-7 flex flex-col items-start gap-3 sm:flex-row sm:flex-wrap">
                @if ($settings?->hero_primary_cta_label && $settings?->hero_primary_cta_url)
                    <x-buttons.link-button :href="$settings->hero_primary_cta_url" variant="solid">{{ $settings->hero_primary_cta_label }}</x-buttons.link-button>
                @endif
                @if ($settings?->hero_secondary_cta_label && $settings?->hero_secondary_cta_url)
                    <x-buttons.link-button :href="$settings->hero_secondary_cta_url" variant="white-outline">{{ $settings->hero_secondary_cta_label }}</x-buttons.link-button>
                @endif
            </div>
        </div>
    </div>
</section>
