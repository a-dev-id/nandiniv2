@php
    $title = $settings?->meta_title ?: 'Festive Season | Nandini Jungle by Hanging Gardens';
    $description = $settings?->meta_description ?: 'Celebrate Christmas and New Year in the heart of Bali at Nandini Jungle.';
    $canonical = route('festive.index');
@endphp

@push('meta')
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    @if ($settings?->meta_author)<meta name="author" content="{{ $settings->meta_author }}">@endif
    <link rel="canonical" href="{{ $canonical }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $canonical }}">
    @if ($settings?->meta_site_name)<meta property="og:site_name" content="{{ $settings->meta_site_name }}">@endif
    @if ($heroImage)
        <meta property="og:image" content="{{ $heroImage }}">
        <meta name="twitter:image" content="{{ $heroImage }}">
    @endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $description }}">
@endpush

@push('css')
    <link rel="preload" href="{{ asset('fonts/Span-Regular.otf') }}" as="font" type="font/otf" crossorigin>
@endpush

<x-layouts.app>
    @if ($settings?->hero_visible ?? true)
        <x-festive.hero :settings="$settings" :image="$heroImage" />
    @endif

    @if ($settings?->introduction_visible ?? true)
        <x-festive.introduction :settings="$settings" />
    @endif

    @if ($settings?->celebrations_visible ?? true)
        <x-festive.celebrations :items="$celebrations" />
    @endif

    @if ($settings?->programme_visible ?? true)
        <x-festive.programme :settings="$settings" />
    @endif

    @if ($settings?->booking_cta_visible ?? true)
        <x-festive.booking-cta :settings="$settings" :image="$bookingCtaImage" />
    @endif
</x-layouts.app>
