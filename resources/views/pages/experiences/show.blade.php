@section('inquiry-modal', true)

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

    $inquiryImage = $experience->card_image
    ?? $experience->image
    ?? $experience->hero_image
    ?? $page->hero_image
    ?? null;

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
    <x-heroes.image-hero
        :image-src="asset('storage/' . $heroImage)"
        :mobile-image-src-manual="$heroMobileImage ? asset('storage/' . $heroMobileImage) : asset('storage/' . $heroImage)"
        :alt-text="$heroAlt"
    />
    @endif

    <section class="py-14 md:py-20 px-6">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-xl leading-snug uppercase text-slate-700 font-medium mb-3 sm:text-2xl">
                {{ $experience->title }}
            </h1>

            @if (! empty($experience->subtitle))
            <p class="text-base md:text-xl text-slate-700 mb-8 uppercase sm:text-lg">
                {{ $experience->subtitle }}
            </p>
            @endif

            @if (! empty($experience->description))
            <div class="prose prose-slate text-xs leading-relaxed text-gray-600 max-w-2xl sm:max-w-3xl md:max-w-5xl mx-auto sm:text-sm">
                {!! $experience->description !!}
            </div>
            @elseif (! empty($experience->excerpt))
            <p class="text-xs leading-relaxed text-gray-600 max-w-2xl sm:max-w-3xl md:max-w-5xl mx-auto sm:text-sm">
                {{ $experience->excerpt }}
            </p>
            @endif

            @if ($prices->isNotEmpty())
            <div class="my-10 text-center text-slate-700">
                <p class="text-xs leading-relaxed sm:text-sm">
                    Start from
                </p>

                <div class="mt-2 space-y-6">
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
                        <p class="text-xs leading-relaxed font-medium sm:text-sm">
                            {{ $price->label }}
                        </p>
                        @endif

                        <p class="mt-1 text-base md:text-xl font-bold sm:text-lg">
                            {{ $currency }} {{ number_format((float) $amount, 0, '.', ',') }}{{ $priceSuffix }}
                        </p>

                        @if ($unitLabel)
                        <p class="mt-1 text-xs leading-relaxed sm:text-sm">
                            {{ $unitLabel }}
                        </p>
                        @endif

                        @if (! empty($price->notes))
                        <p class="mt-2 text-[12px] leading-relaxed text-slate-600 sm:text-[14px]">
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
                <x-buttons.link-button
                    href="#"
                    variant="solid"
                    data-inquiry-button
                    data-inquiry-title="{{ $experience->title }}"
                    :data-inquiry-image="$inquiryImage ? asset('storage/' . $inquiryImage) : null"
                >
                    Inquire Now
                </x-buttons.link-button>
            </div>
        </div>
    </section>

    @if ($relatedExperiences->isNotEmpty())
    <section class="pt-14 md:pt-20">
        <div class="px-6 mb-10 text-center">
            <h2 class="text-lg leading-snug uppercase text-slate-700 font-medium mb-3 sm:text-xl">
                Other Experiences
            </h2>
        </div>

        <x-sections.item-carousel :items="$relatedExperiences" route-name="experiences.show" />
    </section>
    @endif
</x-layouts.app>
