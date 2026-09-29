@props([
    'settings' => null,
    'image' => null,
])

<section id="reserve" class="relative flex min-h-[440px] scroll-mt-20 items-center overflow-hidden bg-[#101713] px-6 py-16 text-center text-white md:px-12" aria-labelledby="festive-reservation-title" data-gtm-section="reservation_cta">
    @if ($image)
        <img src="{{ $image }}" alt="{{ $settings?->booking_cta_image_alt }}" class="absolute inset-0 h-full w-full object-cover object-center" width="1920" height="900" loading="lazy" decoding="async">
    @endif
    <div class="absolute inset-0 bg-[#030c08]/75" aria-hidden="true"></div>
    <div class="relative z-10 mx-auto max-w-3xl">
        @if ($settings?->booking_cta_eyebrow)
            <p class="mb-4 text-[10px] font-medium uppercase tracking-[.18em] text-[#d1b77d] sm:text-xs">{{ $settings->booking_cta_eyebrow }}</p>
        @endif
        @if ($settings?->booking_cta_heading)
            <h2 id="festive-reservation-title" class="text-xl font-medium uppercase leading-snug [--heading-letter-spacing:.15em] sm:text-2xl">{!! nl2br(e($settings->booking_cta_heading)) !!}</h2>
        @endif
        @if ($settings?->booking_cta_description)
            <p class="mx-auto my-4 max-w-2xl text-xs leading-relaxed text-white/85 sm:text-sm">{!! nl2br(e($settings->booking_cta_description)) !!}</p>
        @endif
        @if ($settings?->booking_cta_button_label && $settings?->booking_cta_button_url)
            <x-buttons.link-button :href="$settings->booking_cta_button_url" variant="solid">{{ $settings->booking_cta_button_label }}</x-buttons.link-button>
        @endif
    </div>
</section>
