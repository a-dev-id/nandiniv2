@push('meta')
<title>{{ $offer->meta_title ?: $offer->title }}</title>
<meta name="description" content="{{ $offer->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($offer->excerpt ?: $offer->description), 160, '') }}">

<meta name="author" content="Nandini Jungle by Hanging Gardens">
<link rel="canonical" href="{{ url()->current() }}">

<meta property="og:type" content="website">
<meta property="og:title" content="{{ $offer->meta_title ?: $offer->title }}">
<meta property="og:description" content="{{ $offer->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($offer->excerpt ?: $offer->description), 160, '') }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:site_name" content="Nandini Jungle by Hanging Gardens">

@if ($offer->hero_image)
<meta property="og:image" content="{{ asset('storage/' . $offer->hero_image) }}">
<meta name="twitter:image" content="{{ asset('storage/' . $offer->hero_image) }}">
@elseif ($offer->card_image)
<meta property="og:image" content="{{ asset('storage/' . $offer->card_image) }}">
<meta name="twitter:image" content="{{ asset('storage/' . $offer->card_image) }}">
@endif

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $offer->meta_title ?: $offer->title }}">
<meta name="twitter:description" content="{{ $offer->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($offer->excerpt ?: $offer->description), 160, '') }}">
@endpush

<x-layouts.app>
    @php
    $heroImage = $offer->hero_image ?: $offer->card_image;
    $heroMobileImage = $offer->hero_mobile_image ?: $offer->hero_image ?: $offer->card_image;
    $heroAlt = $offer->hero_image_alt ?: $offer->card_image_alt ?: $offer->title;
    $bookingUrl = $offer->resolved_booking_url;
    @endphp

    @if ($heroImage)
    <x-heroes.image-hero
        :image-src="asset('storage/' . $heroImage)"
        :mobile-image-src-manual="$heroMobileImage ? asset('storage/' . $heroMobileImage) : asset('storage/' . $heroImage)"
        :alt-text="$heroAlt"
    />
    @endif

    <section class="bg-white px-6 py-14 md:py-20 md:px-12 lg:px-[70px]">
        <div class="mx-auto max-w-5xl text-center">
            <h1 class="text-xl leading-snug uppercase text-slate-700 font-medium mb-3 sm:text-2xl">
                {{ $offer->title }}
            </h1>

            @if ($offer->excerpt)
            <p class="mt-2 text-sm md:text-lg leading-relaxed text-gray-600 sm:text-base">
                {{ $offer->excerpt }}
            </p>
            @endif

            @if ($offer->description)
            <div class="mt-8 text-xs leading-relaxed text-gray-600 max-w-4xl mx-auto [&_p]:mb-4 [&_ul]:pl-6 [&_li]:mb-1 sm:text-sm">
                {!! $offer->description !!}
            </div>
            @endif

            @if ($bookingUrl)
            <div class="mt-10">
                <a href="{{ $bookingUrl }}" class="inline-flex min-w-[190px] items-center justify-center border border-[#A88444] bg-[#A88444] px-7 py-4 text-xs uppercase text-white transition hover:border-[#B8945B] hover:bg-[#B8945B] sm:text-sm">
                    {{ $offer->button_label ?: 'Book Now' }}
                </a>
            </div>
            @endif
        </div>
    </section>

    @if ($relatedOffers->isNotEmpty())
    <section class="bg-[#F7F7F7] px-6 py-14 md:py-20 md:px-12 lg:px-[70px]">
        <div class="mx-auto w-full">
            <div class="mb-10 text-center">
                <h2 class="text-lg leading-snug uppercase text-slate-700 font-medium mb-3 sm:text-xl">
                    Related Honeymoon Offers
                </h2>
            </div>

            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($relatedOffers as $relatedOffer)
                @php
                $relatedImage = $relatedOffer->card_image ?: $relatedOffer->hero_image;
                $relatedAlt = $relatedOffer->card_image_alt ?: $relatedOffer->hero_image_alt ?: $relatedOffer->title;
                @endphp

                <article class="bg-white shadow-xl">
                    <a href="{{ route('honeymoon.show', $relatedOffer) }}" class="block">
                        <div class="aspect-[4/3] overflow-hidden bg-slate-100">
                            @if ($relatedImage)
                            <img src="{{ asset('storage/' . $relatedImage) }}" alt="{{ $relatedAlt }}" class="h-full w-full object-cover transition duration-700 hover:scale-105" loading="lazy">
                            @endif
                        </div>

                        <div class="p-7">
                            <h3 class="text-base leading-snug uppercase text-slate-950 font-medium mb-3 sm:text-lg">
                                {{ $relatedOffer->title }}
                            </h3>

                            @if ($relatedOffer->excerpt)
                            <p class="mt-2 text-xs leading-relaxed text-slate-700 sm:text-sm">
                                {{ \Illuminate\Support\Str::limit(strip_tags($relatedOffer->excerpt), 130) }}
                            </p>
                            @endif

                            <p class="mt-2 text-xs uppercase text-[#916B2C] sm:text-sm">
                                View Offer
                            </p>
                        </div>
                    </a>
                </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif
</x-layouts.app>
