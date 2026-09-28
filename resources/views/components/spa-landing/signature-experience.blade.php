@props([
    'settings' => null,
    'image' => null,
])

@php
    $eyebrow = $settings?->signature_eyebrow ?: 'A Unique Setting';
    $heading = $settings?->signature_heading ?: 'Spa on the River';
    $description = $settings?->signature_description ?: 'Our signature riverside spa brings wellness closer to the natural rhythm of the jungle. Surrounded by tropical greenery and the sound of flowing water, each treatment becomes a deeply immersive moment of calm, connection and renewal.';
    $linkLabel = $settings?->signature_link_label ?: 'Discover the Spa';
    $linkUrl = $settings?->signature_link_url ?: 'https://'.config('domains.main').'/spa-wellness';
@endphp

<section class="bg-white px-6 py-14 font-sans md:px-12 md:py-20 lg:px-6" aria-labelledby="spa-signature-title" data-gtm-section="signature_experiences">
    <div class="mx-auto grid max-w-7xl items-center gap-8 md:grid-cols-[minmax(0,2fr)_minmax(0,3fr)] md:gap-10 lg:gap-16">
        <div class="order-2 min-w-0 md:order-1 md:pr-2">
            @if ($eyebrow)<p class="mb-3 text-[10px] font-medium uppercase tracking-[.18em] text-[#A88444] sm:text-xs">{{ $eyebrow }}</p>@endif
            <h2 id="spa-signature-title" class="text-lg leading-snug font-medium text-slate-700 uppercase sm:text-xl">
                {!! nl2br(e($heading)) !!}
            </h2>
            @if ($description)<p class="mt-5 max-w-xl text-xs leading-relaxed text-slate-600 sm:text-sm">{{ $description }}</p>@endif
            @if ($linkLabel && $linkUrl)
                <a href="{{ $linkUrl }}" class="mt-6 inline-flex items-center gap-3 text-[10px] font-medium uppercase tracking-[.16em] text-[#A88444] transition hover:text-[#8f6b34] sm:text-xs">
                    <span>{{ $linkLabel }}</span><span aria-hidden="true">→</span>
                </a>
            @endif
        </div>

        <div class="order-1 aspect-[4/3] min-w-0 overflow-hidden bg-[#f3f4f5] md:order-2">
            @if ($image)
                <img src="{{ $image }}" alt="{{ $settings?->signature_image_alt }}" class="h-full w-full object-cover object-center transition duration-500 hover:scale-[1.02]" width="1200" height="900" loading="lazy" decoding="async">
            @endif
        </div>
    </div>
</section>
