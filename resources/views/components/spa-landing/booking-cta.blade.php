@props([
    'settings' => null,
    'image' => null,
])

@php
    $eyebrow = $settings?->booking_cta_eyebrow ?: 'Your Wellness Journey Awaits';
    $heading = $settings?->booking_cta_heading ?: 'Book Your Spa Experience';
    $description = $settings?->booking_cta_description ?: 'Step away from the everyday and reconnect with nature through a restorative Nandini Jungle Spa experience.';
    $buttonLabel = $settings?->booking_cta_button_label ?: 'BOOK NOW';
    $buttonUrl = $settings?->booking_cta_button_url
        ?: $settings?->reservation_url
        ?: 'https://wa.me/6281236871170?text='.rawurlencode('Hello, I would like to book a spa experience at Nandini Jungle.');
@endphp

<section class="relative isolate flex min-h-[440px] items-center overflow-hidden bg-[#142c24] px-6 py-20 text-center font-sans text-white md:min-h-[500px] md:px-12 md:py-24" aria-labelledby="spa-booking-cta-title" data-gtm-section="booking_cta">
    @if ($image)
        <img src="{{ $image }}" alt="{{ $settings?->booking_cta_image_alt }}" class="absolute inset-0 -z-20 h-full w-full object-cover object-center" width="1920" height="1080" loading="lazy" decoding="async">
    @endif
    <div class="absolute inset-0 -z-10 bg-[linear-gradient(180deg,rgba(7,20,15,.4),rgba(7,20,15,.76))]" aria-hidden="true"></div>

    <div class="mx-auto w-full max-w-3xl border-y border-white/25 py-10 md:py-12">
        @if ($eyebrow)<p class="mb-3 text-[10px] font-medium uppercase tracking-[.18em] text-[#e2cca0] sm:text-xs">{{ $eyebrow }}</p>@endif
        <h2 id="spa-booking-cta-title" class="text-lg leading-snug font-medium text-white uppercase sm:text-xl">
            {!! nl2br(e($heading)) !!}
        </h2>
        @if ($description)<p class="mx-auto mt-5 max-w-2xl text-xs leading-relaxed text-white/85 sm:text-sm">{{ $description }}</p>@endif
        @if ($buttonLabel && $buttonUrl)
            <div class="mt-8"><x-buttons.link-button :href="$buttonUrl" variant="solid">{{ $buttonLabel }}</x-buttons.link-button></div>
        @endif
    </div>
</section>
