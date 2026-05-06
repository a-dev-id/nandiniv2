@push('meta')
@php
$metaTitle = $accommodation->meta_title ?: $accommodation->title;

$metaDescription = $accommodation->meta_description
?: \Illuminate\Support\Str::limit(strip_tags($accommodation->excerpt ?: $accommodation->description ?: ''), 160, '');

$metaImage = $accommodation->hero_image
?: $accommodation->card_image
?: $accommodation->hero_mobile_image;
@endphp

<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">

<meta name="author" content="Nandini Jungle by Hanging Gardens">
<link rel="canonical" href="{{ url()->current() }}">

<meta property="og:type" content="article">
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:site_name" content="Nandini Jungle by Hanging Gardens">

@if (! empty($metaImage))
<meta property="og:image" content="{{ asset('storage/' . $metaImage) }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta name="twitter:image" content="{{ asset('storage/' . $metaImage) }}">
@endif

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
@endpush

<x-layouts.app>
    {{-- Hero --}}
    <section class="relative w-full h-[460px] sm:h-[560px] lg:h-[760px] overflow-hidden bg-slate-100">
        @if ($accommodation->hero_image || $accommodation->hero_mobile_image)
        <picture class="absolute inset-0 w-full h-full">
            @if ($accommodation->hero_mobile_image)
            <source media="(max-width: 767px)" srcset="{{ asset('storage/' . $accommodation->hero_mobile_image) }}">
            @endif

            <img src="{{ asset('storage/' . ($accommodation->hero_image ?: $accommodation->hero_mobile_image)) }}" alt="{{ $accommodation->hero_image_alt ?: $accommodation->title }}" class="absolute inset-0 w-full h-full object-cover object-center">
        </picture>
        @elseif ($accommodation->card_image)
        <img src="{{ asset('storage/' . $accommodation->card_image) }}" alt="{{ $accommodation->card_image_alt ?: $accommodation->title }}" class="absolute inset-0 w-full h-full object-cover object-center">
        @endif

        <div class="absolute inset-0 bg-black/20"></div>
    </section>

    {{-- Intro --}}
    <section class="py-14 md:py-20 px-6 text-center bg-white">
        <div class="max-w-5xl mx-auto">
            <h1 class="text-2xl sm:text-3xl md:text-4xl leading-snug tracking-[0.15em] md:tracking-[0.25em] uppercase text-slate-800 mb-6 md:mb-8 font-medium">
                {{ $accommodation->title }}
            </h1>

            @if ($accommodation->excerpt)
            <p class="max-w-4xl mx-auto text-[15px] md:text-base leading-7 text-slate-700">
                {{ $accommodation->excerpt }}
            </p>
            @endif

            @if ($accommodation->description)
            <div class="mt-8 max-w-4xl mx-auto text-[15px] md:text-base leading-7 text-slate-700
                    [&_p]:mb-5
                    [&_p:last-child]:mb-0
                    [&_ul]:list-disc
                    [&_ul]:pl-6
                    [&_ul]:mb-5
                    [&_ol]:list-decimal
                    [&_ol]:pl-6
                    [&_ol]:mb-5
                    [&_li]:mb-2
                    [&_strong]:text-slate-900">
                {!! $accommodation->description !!}
            </div>
            @endif
        </div>
    </section>

    {{-- Features + Gallery --}}
    <x-sections.accommodation-features-gallery :accommodation="$accommodation" />

    {{-- Related Accommodations --}}
    @if ($relatedAccommodations->isNotEmpty())
    <section class="pt-10 md:pt-18 bg-white">
        <div class="px-6 mb-10 text-center">
            <h2 class="text-xl sm:text-2xl md:text-3xl tracking-[0.18em] uppercase text-slate-800 font-medium">
                You May Also Like
            </h2>
        </div>

        <x-sections.item-carousel :items="$relatedAccommodations" />
    </section>
    @endif
</x-layouts.app>