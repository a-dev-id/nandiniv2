@push('meta')
@php
$metaTitle = $spa->meta_title ?: $spa->title;

$metaDescription = $spa->meta_description
?: \Illuminate\Support\Str::limit(strip_tags($spa->excerpt ?: $spa->description ?: ''), 160, '');

$metaImage = $spa->hero_image
?? $spa->card_image
?? $page->hero_image
?? null;
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
    $heroImage = $spa->hero_image
    ?? $spa->card_image
    ?? $page->hero_image
    ?? null;

    $heroMobileImage = $spa->hero_mobile_image
    ?? $spa->hero_image
    ?? $spa->card_image
    ?? $page->hero_mobile_image
    ?? $heroImage;

    $heroAlt = $spa->hero_image_alt
    ?? $spa->card_image_alt
    ?? $spa->title;

    $buttonLabel = $spa->button_label ?: 'Book Now';

    $buttonUrl = html_entity_decode(
    $spa->booking_url,
    ENT_QUOTES | ENT_HTML5,
    'UTF-8'
    );

    $validStartDate = $spa->valid_start_date;
    $validEndDate = $spa->valid_end_date;
    @endphp

    {{-- Hero Image --}}
    @if ($heroImage)
    <section class="relative w-full">
        <picture>
            @if ($heroMobileImage)
            <source media="(max-width: 767px)" srcset="{{ asset('storage/' . $heroMobileImage) }}">
            @endif

            <img src="{{ asset('storage/' . $heroImage) }}" alt="{{ $heroAlt }}" class="w-full h-[65vh] md:h-[85vh] object-cover object-center">
        </picture>
    </section>
    @endif

    {{-- Offer Content --}}
    <section class="py-14 md:py-20 px-6">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-2xl sm:text-3xl md:text-4xl leading-snug tracking-[0.15em] md:tracking-[0.25em] uppercase text-slate-800 mb-6 font-medium">
                {{ $spa->title }}
            </h1>

            @if (! empty($spa->subtitle))
            <p class="text-lg md:text-xl text-slate-700 mb-8 uppercase tracking-[0.15em]">
                {{ $spa->subtitle }}
            </p>
            @endif

            @if ($validStartDate || $validEndDate)
            <div class="mb-10 text-slate-800">
                <p class="text-[15px] leading-relaxed">
                    Stay Period
                </p>

                <p class="mt-1 text-lg md:text-xl font-bold tracking-wide">
                    @if ($validStartDate && $validEndDate)
                    {{ $validStartDate->format('j F Y') }} – {{ $validEndDate->format('j F Y') }}
                    @elseif ($validStartDate)
                    From {{ $validStartDate->format('j F Y') }}
                    @elseif ($validEndDate)
                    Until {{ $validEndDate->format('j F Y') }}
                    @endif
                </p>
            </div>
            @endif

            @if (! empty($spa->description))
            <div class="prose prose-slate max-w-none mx-auto text-slate-800 leading-relaxed
                [&_p]:mb-4
                [&_p]:min-h-6
                [&_p:last-child]:mb-0
                [&_strong]:font-semibold
                [&_strong]:text-slate-900
                [&_ul]:my-6
                [&_ol]:my-6
                [&_li]:mb-1
                [&_h2]:mt-10
                [&_h2]:mb-4
                [&_h2]:text-xl
                [&_h2]:font-semibold
                [&_h3]:mt-8
                [&_h3]:mb-3
                [&_h3]:text-lg
                [&_h3]:font-semibold">
                {!! $spa->description !!}
            </div>
            @elseif (! empty($spa->excerpt))
            <p class="text-slate-800 leading-relaxed">
                {{ $spa->excerpt }}
            </p>
            @endif

            @if (! empty($buttonUrl))
            <div class="mt-10 mb-8">
                <x-buttons.link-button href="{{ $buttonUrl }}" variant="solid">
                    {{ $buttonLabel }}
                </x-buttons.link-button>
            </div>
            @endif
        </div>
    </section>

    {{-- Related Spa --}}
    @if ($relatedSpas->isNotEmpty())
    <section class="pt-10 md:pt-18">
        <div class="px-6 mb-10 text-center">
            <h2 class="text-xl sm:text-2xl md:text-3xl tracking-[0.18em] uppercase text-slate-800 font-medium">
                Other Spa & Wellness Journey
            </h2>
        </div>

        <x-sections.item-carousel :items="$relatedSpas" route-name="spa.show" />
    </section>
    @endif
</x-layouts.app>