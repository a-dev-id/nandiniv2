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
    @php
    $heroImage = $accommodation->hero_image
    ?: $accommodation->hero_mobile_image
    ?: $accommodation->card_image;

    $heroMobileImage = $accommodation->hero_mobile_image
    ?: $accommodation->hero_image
    ?: $accommodation->card_image;

    $heroAlt = $accommodation->hero_image_alt
    ?: $accommodation->card_image_alt
    ?: $accommodation->title;
    @endphp

    @if ($heroImage)
    <x-heroes.image-hero
        :image-src="asset('storage/' . $heroImage)"
        :mobile-image-src-manual="$heroMobileImage ? asset('storage/' . $heroMobileImage) : asset('storage/' . $heroImage)"
        :alt-text="$heroAlt"
    />
    @endif

    {{-- Intro --}}
    <section class="py-14 md:py-20 px-6 text-center bg-white">
        <div class="max-w-5xl mx-auto">
            <h1 class="text-xl leading-snug uppercase text-slate-700 font-medium mb-3 sm:text-2xl">
                {{ $accommodation->title }}
            </h1>

            @if ($accommodation->description)
            <div class="text-xs leading-relaxed text-slate-700 max-w-2xl sm:max-w-3xl md:max-w-5xl mx-auto [&_p]:mb-5 [&_p:last-child]:mb-0 [&_ul]:list-disc [&_ul]:pl-6 [&_ul]:mb-5 [&_ol]:list-decimal [&_ol]:pl-6 [&_ol]:mb-5 [&_li]:mb-2 sm:text-sm">
                {!! $accommodation->description !!}
            </div>
            @elseif ($accommodation->excerpt)
            <p class="text-xs leading-relaxed text-gray-600 max-w-2xl sm:max-w-3xl md:max-w-5xl mx-auto sm:text-sm">
                {{ $accommodation->excerpt }}
            </p>
            @endif
        </div>
    </section>

    {{-- Features + Gallery --}}
    <x-sections.accommodation-features-gallery :accommodation="$accommodation" />

    {{-- Related Accommodations --}}
    @if ($relatedAccommodations->isNotEmpty())
    <section class="pt-14 md:pt-20 bg-white">
        <div class="px-6 mb-10 text-center">
            <h2 class="text-lg leading-snug uppercase text-slate-700 font-medium mb-3 sm:text-xl">
                You May Also Like
            </h2>
        </div>

        <x-sections.item-carousel :items="$relatedAccommodations" />
    </section>
    @endif
</x-layouts.app>
