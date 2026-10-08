@props([
    'settings' => null,
    'image' => null,
])

@php
    $eyebrow = $settings?->signature_eyebrow ?: 'A Unique Setting';
    $heading = $settings?->signature_heading ?: 'Spa on the River';
    $description = $settings?->signature_description ?: 'Our signature riverside spa brings wellness closer to the natural rhythm of the jungle. Surrounded by tropical greenery and the sound of flowing water, each treatment becomes a deeply immersive moment of calm, connection and renewal.';
    $linkLabel = $settings?->signature_link_label ?: 'Book Now';
    $linkUrl = $settings?->signature_link_url ?: 'https://wa.me/6281236871170?text='.rawurlencode('Hello, I would like to book the Spa on the River experience at Nandini Jungle.');
@endphp

<section class="bg-white px-6 py-14 font-sans md:px-12 md:py-20 lg:px-6" aria-labelledby="spa-signature-title" data-gtm-section="signature_experiences">
    <div class="mx-auto grid max-w-7xl items-stretch gap-0 lg:grid-cols-[minmax(0,3fr)_minmax(360px,2fr)]">
        <div class="order-1 min-h-[360px] overflow-hidden bg-[#f3f4f5] lg:min-h-[560px]">
            @if ($image)
                <img src="{{ $image }}" alt="{{ $settings?->signature_image_alt ?: 'Spa on the River at Nandini Jungle' }}" class="h-full w-full object-cover object-center" width="1800" height="1200" loading="lazy" decoding="async">
            @endif
        </div>

        <div class="order-2 flex min-w-0 items-center bg-[#f3f4f5] px-6 py-12 sm:px-10 lg:px-14 lg:py-16">
            <div class="max-w-lg">
            @if ($eyebrow)<p class="mb-3 text-[10px] font-medium uppercase tracking-[.18em] text-[#A88444] sm:text-xs">{{ $eyebrow }}</p>@endif
            <h2 id="spa-signature-title" class="text-lg leading-snug font-medium text-slate-700 uppercase sm:text-xl">
                {!! nl2br(e($heading)) !!}
            </h2>
            @if ($description)<p class="mt-5 text-xs leading-relaxed text-slate-600 sm:text-sm">{{ $description }}</p>@endif
            @if ($linkLabel && $linkUrl)
                <div class="mt-7"><x-buttons.link-button :href="$linkUrl" variant="solid">{{ $linkLabel }}</x-buttons.link-button></div>
            @endif
            </div>
        </div>
    </div>
</section>
