@props(['settings' => null])

@php
    $uploadedImage = $settings?->reservation_cta_background_image;
    $backgroundImage = $uploadedImage && \Illuminate\Support\Facades\Storage::disk('public')->exists($uploadedImage)
        ? asset('storage/'.$uploadedImage)
        : (str_starts_with((string) $uploadedImage, 'http') || str_starts_with((string) $uploadedImage, '/') ? asset($uploadedImage) : null);
@endphp

<section class="relative isolate flex min-h-[420px] items-center overflow-hidden bg-[#142c24] px-6 py-16 font-sans text-white md:min-h-[460px]" aria-labelledby="dining-reservation-cta-title">
    @if ($backgroundImage)
    <picture class="absolute inset-0 -z-20">
        <source media="(max-width: 767px)" srcset="{{ $backgroundImage }}">
        <img src="{{ $backgroundImage }}" alt="{{ $settings?->reservation_cta_background_image_alt }}" class="h-full w-full object-cover object-center max-md:object-[center_55%]" width="1920" height="900" loading="lazy" decoding="async">
    </picture>
    @endif
    <div class="absolute inset-0 -z-10 bg-black/50" aria-hidden="true"></div>

    <div class="mx-auto w-full max-w-[760px] text-center">
        <h2 id="dining-reservation-cta-title" class="text-lg leading-snug font-medium text-white uppercase sm:text-xl">{!! nl2br(e($settings?->reservation_cta_heading)) !!}</h2>
        <p class="mx-auto mt-4 max-w-2xl text-xs leading-relaxed text-white/85 sm:text-sm">
            {!! nl2br(e($settings?->reservation_cta_description)) !!}
        </p>

        <div class="mx-auto mt-7 flex justify-center">
            @if (filled($settings?->reservation_cta_label) && filled($settings?->reservation_cta_url))
                <x-buttons.link-button :href="$settings->reservation_cta_url" variant="solid" class="w-full sm:w-auto">{{ $settings->reservation_cta_label }}</x-buttons.link-button>
            @endif
        </div>
    </div>
</section>
