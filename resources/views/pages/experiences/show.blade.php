@push('meta')
@php
$metaTitle = $experience->meta_title ?: $experience->title;

$metaDescription = $experience->meta_description
?: \Illuminate\Support\Str::limit(strip_tags($experience->excerpt ?: $experience->description ?: ''), 160, '');

$metaImage = $experience->card_image
?? $experience->image
?? $experience->hero_image
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
    $heroImage = $experience->card_image
    ?? $experience->image
    ?? $experience->hero_image
    ?? $page->hero_image
    ?? null;

    $heroMobileImage = $experience->card_image
    ?? $experience->mobile_image
    ?? $experience->hero_mobile_image
    ?? $page->hero_mobile_image
    ?? $heroImage;

    $heroAlt = $experience->card_image_alt
    ?? $experience->image_alt
    ?? $experience->hero_image_alt
    ?? $experience->title;

    $prices = $experience->prices
    ? $experience->prices->filter(fn ($price) => (bool) ($price->is_active ?? true))->values()
    : collect();

    $formatPriceType = function (?string $value): string {
    if (empty($value)) {
    return '';
    }

    return match ($value) {
    '++', 'plus_plus' => '++',
    'net', 'nett' => ' Nett',
    'inclusive' => ' Inclusive',
    default => '',
    };
    };

    $formatUnitType = function (?string $value): ?string {
    if (empty($value)) {
    return null;
    }

    return match ($value) {
    'per_person' => 'Per Person',
    'per_couple' => 'Per Couple',
    'per_booking' => 'Per Booking',
    default => str($value)->replace('_', ' ')->title()->toString(),
    };
    };
    @endphp

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

    <section class="py-14 md:py-20 px-6">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-4xl leading-snug tracking-[0.15em] md:tracking-[0.25em] uppercase text-slate-800 mb-6 md:mb-8 font-medium">
                {{ $experience->title }}
            </h1>

            @if (! empty($experience->subtitle))
            <p class="text-lg md:text-xl text-slate-700 mb-8 uppercase tracking-[0.15em]">
                {{ $experience->subtitle }}
            </p>
            @endif

            @if (! empty($experience->description))
            <div class="prose prose-slate text-[15px] sm:text-base leading-relaxed text-gray-600 max-w-2xl sm:max-w-3xl md:max-w-5xl mx-auto">
                {!! $experience->description !!}
            </div>
            @elseif (! empty($experience->excerpt))
            <p class="text-[15px] sm:text-base leading-relaxed text-gray-600 max-w-2xl sm:max-w-3xl md:max-w-5xl mx-auto">
                {{ $experience->excerpt }}
            </p>
            @endif

            @if ($prices->isNotEmpty())
            <div class="my-10 text-center text-slate-800">
                <p class="text-[15px] leading-relaxed">
                    Start from
                </p>

                <div class="mt-3 space-y-6">
                    @foreach ($prices as $price)
                    @php
                    $amount = $price->price ?? $price->amount ?? $price->unit_price ?? null;
                    $currency = $price->currency ?? 'IDR';
                    $priceSuffix = $formatPriceType($price->price_type ?? null);
                    $unitLabel = $formatUnitType($price->unit_type ?? $price->pricing_type ?? null);
                    @endphp

                    @if ($amount)
                    <div>
                        @if (! empty($price->label))
                        <p class="text-[15px] leading-relaxed font-medium">
                            {{ $price->label }}
                        </p>
                        @endif

                        <p class="mt-1 text-lg md:text-xl font-bold tracking-wide">
                            {{ $currency }} {{ number_format((float) $amount, 0, '.', ',') }}{{ $priceSuffix }}
                        </p>

                        @if ($unitLabel)
                        <p class="mt-1 text-[15px] leading-relaxed">
                            {{ $unitLabel }}
                        </p>
                        @endif

                        @if (! empty($price->notes))
                        <p class="mt-2 text-[14px] leading-relaxed text-slate-600">
                            {{ $price->notes }}
                        </p>
                        @endif
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            @endif

            <div class="mb-8">
                <x-buttons.link-button href="#" variant="solid">
                    Inquire Now
                </x-buttons.link-button>
            </div>
        </div>
    </section>

    @if ($relatedExperiences->isNotEmpty())
    <section class="pt-14 md:pt-20">
        <div class="px-6 mb-10 text-center">
            <h2 class="text-2xl sm:text-3xl md:text-3xl leading-snug tracking-[0.15em] md:tracking-[0.22em] uppercase text-slate-800 font-medium">
                Other Experiences
            </h2>
        </div>

        <x-sections.item-carousel :items="$relatedExperiences" route-name="experiences.show" />
    </section>
    @endif
</x-layouts.app>