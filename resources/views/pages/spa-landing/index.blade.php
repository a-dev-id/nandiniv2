@php
    $title = $spaSettings?->meta_title ?: 'Spa & Wellness | Nandini Jungle by Hanging Gardens';
    $description = $spaSettings?->meta_description ?: 'Restore body, mind and soul with deeply restorative spa rituals in the heart of the Ubud jungle.';
    $canonical = 'https://'.config('domains.spa').'/';
@endphp

@push('meta')
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    @if ($spaSettings?->meta_author)<meta name="author" content="{{ $spaSettings->meta_author }}">@endif
    <link rel="canonical" href="{{ $canonical }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $canonical }}">
    @if ($spaSettings?->meta_site_name)<meta property="og:site_name" content="{{ $spaSettings->meta_site_name }}">@endif
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
    <x-spa-landing.hero :settings="$spaSettings" :image="$heroImage" :mobile-image="$heroMobileImage" />
    <x-spa-landing.information-bar :settings="$spaSettings" />
    <x-spa-landing.wellness-philosophy :settings="$spaSettings" :image="$wellnessPhilosophyImage" />
    <div id="spa-content" aria-hidden="true"></div>
</x-layouts.app>
