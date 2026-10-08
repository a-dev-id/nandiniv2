@props([
    'settings' => null,
    'image' => null,
])

@php
    $quote = $settings?->guest_review_quote ?: 'The most peaceful and healing spa experience. The sound of the river, the jungle, and the care from the therapists made it truly special.';
    $label = $settings?->guest_review_label ?: 'Guest Experience';
@endphp

<section class="bg-white px-6 py-14 font-sans md:px-12 md:py-20 lg:px-6" aria-label="Guest spa experience" data-gtm-section="guest_reviews">
    <div class="mx-auto grid max-w-7xl items-center gap-10 lg:grid-cols-[minmax(0,.85fr)_minmax(0,1.15fr)] lg:gap-16">
        <div class="order-2 lg:order-1 lg:pl-8">
            <span class="block font-span text-5xl leading-none text-[#A88444]" aria-hidden="true">“</span>
            <blockquote class="mt-2 max-w-xl font-span text-lg italic leading-relaxed text-slate-600 [--heading-font-weight:400] [--heading-letter-spacing:.01em] sm:text-xl md:text-2xl">
                {{ $quote }}
            </blockquote>
            @if ($label)<p class="mt-6 text-[10px] font-medium uppercase tracking-[.18em] text-[#A88444] sm:text-xs">{{ $label }}</p>@endif
        </div>

        <div class="order-1 aspect-[4/3] overflow-hidden bg-[#ebe9e2] lg:order-2">
            @if ($image)
                <img src="{{ $image }}" alt="{{ $settings?->guest_review_image_alt ?: 'Spa experience at Nandini Jungle' }}" class="h-full w-full object-cover object-center" width="1400" height="1050" loading="lazy" decoding="async">
            @endif
        </div>
    </div>
</section>
