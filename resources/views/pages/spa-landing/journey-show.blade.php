@php
    $title = $journey['title'] ?? 'Wellness Journey';
    $description = $journey['description'] ?? '';
    $imageAlt = $journey['image_alt'] ?? $title;
    $buttonLabel = $journey['book_label'] ?? 'Book Now';
    $metaDescription = \Illuminate\Support\Str::limit(strip_tags((string) $description), 160, '');
@endphp

@push('meta')
<title>{{ $title }} | Essence Spa at Nandini Jungle</title>
<meta name="description" content="{{ $metaDescription }}">
<meta name="author" content="Nandini Jungle by Hanging Gardens">
<link rel="canonical" href="{{ url()->current() }}">
<meta property="og:type" content="article">
<meta property="og:title" content="{{ $title }} | Essence Spa at Nandini Jungle">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:site_name" content="Nandini Jungle by Hanging Gardens">
@if ($image)
<meta property="og:image" content="{{ $image }}">
<meta name="twitter:image" content="{{ $image }}">
@endif
<meta name="twitter:card" content="summary_large_image">
@endpush

<x-layouts.app>
    @if ($image)
        <x-heroes.image-hero :image-src="$image" :mobile-image-src-manual="$image" :alt-text="$imageAlt" />
    @endif

    <section class="bg-white px-6 py-14 font-sans md:px-12 md:py-20" data-gtm-section="wellness_journey_details">
        <div class="mx-auto max-w-4xl text-center">
            <p class="mb-3 text-[10px] font-medium uppercase tracking-[.18em] text-[#A88444] sm:text-xs">Wellness Journey</p>
            <h1 class="text-xl leading-snug font-medium text-slate-700 uppercase sm:text-2xl">{{ $title }}</h1>

            @if ($description)
                <p class="mx-auto mt-6 max-w-3xl text-xs leading-relaxed text-slate-600 sm:text-sm">{{ $description }}</p>
            @endif

            @if (filled($bookingUrl))
                <div class="mt-8">
                    <x-buttons.link-button :href="$bookingUrl" variant="solid">{{ $buttonLabel }}</x-buttons.link-button>
                </div>
            @endif
        </div>
    </section>
</x-layouts.app>
