@props([
    'settings' => null,
    'image' => null,
])

@php
    $quote = $settings?->guest_review_quote ?: 'The most peaceful and healing spa experience. The sound of the river, the jungle, and the care from the therapists made it truly special.';
    $label = $settings?->guest_review_label ?: 'Guest Experience';
@endphp

<section class="bg-white px-6 py-14 font-sans md:px-12 md:py-20 lg:px-6" aria-label="Guest spa experience" data-gtm-section="guest_reviews">
    <div class="mx-auto grid max-w-7xl items-center gap-10 lg:grid-cols-2 lg:gap-16">
        <div>
            <span class="block font-span text-5xl leading-none text-[#A88444]" aria-hidden="true">“</span>
            <blockquote class="mt-2 font-serif text-lg italic leading-relaxed text-slate-600 sm:text-xl md:text-2xl">
                {{ $quote }}
            </blockquote>
            @if ($label)<p class="mt-6 text-[10px] font-medium uppercase tracking-[.18em] text-[#A88444] sm:text-xs">{{ $label }}</p>@endif
        </div>

        <div class="aspect-video overflow-hidden bg-[#f3f4f5]">
            @if ($image)
                <img src="{{ $image }}" alt="{{ $settings?->guest_review_image_alt }}" class="h-full w-full object-cover object-center" width="1600" height="900" loading="lazy" decoding="async">
            @endif
        </div>
    </div>
</section>
