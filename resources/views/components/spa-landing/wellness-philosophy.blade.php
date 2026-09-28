@props([
    'settings' => null,
    'image' => null,
])

@php
    $eyebrow = $settings?->wellness_philosophy_eyebrow ?: 'OUR WELLNESS PHILOSOPHY';
    $heading = $settings?->wellness_philosophy_heading ?: "A SACRED PAUSE\nIN THE JUNGLE";
    $description = $settings?->wellness_philosophy_description ?: 'At Nandini Jungle, wellness is a harmonious journey of body, mind and spirit, inspired by Balinese traditions and the healing power of nature. Our spa experiences invite you to slow down, reconnect and embrace a deeper sense of wellbeing.';
@endphp

<section class="bg-white px-6 py-14 font-sans md:px-12 md:py-20 lg:px-6" aria-labelledby="spa-wellness-philosophy-title" data-gtm-section="introduction">
    <div class="mx-auto grid max-w-7xl items-center gap-8 md:grid-cols-[minmax(0,2fr)_minmax(0,3fr)] md:gap-10 lg:gap-16">
        <div class="order-2 min-w-0 md:order-1 md:pr-2">
            <p class="mb-3 text-[10px] font-medium uppercase tracking-[.18em] text-[#A88444] sm:text-xs">{{ $eyebrow }}</p>
            <h2 id="spa-wellness-philosophy-title" class="mb-3 text-lg leading-snug font-medium text-slate-700 uppercase sm:text-xl">
                {!! nl2br(e($heading)) !!}
            </h2>
            <p class="max-w-xl text-xs leading-relaxed text-slate-600 sm:text-sm">
                {{ $description }}
            </p>
        </div>

        <div class="order-1 aspect-[4/3] min-w-0 overflow-hidden bg-[#f3f4f5] md:order-2">
            @if ($image)
                <img src="{{ $image }}" alt="{{ $settings?->wellness_philosophy_image_alt }}" class="h-full w-full object-cover object-center" width="1200" height="900" loading="lazy" decoding="async">
            @endif
        </div>
    </div>
</section>
