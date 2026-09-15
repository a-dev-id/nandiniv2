@section('inquiry-modal', true)

@php
    $title = $diningSettings?->meta_title;
    $description = $diningSettings?->meta_description;
    $canonical = 'https://dining.nandinibali.com/';
@endphp

@push('meta')
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <meta name="author" content="{{ $diningSettings?->meta_author }}">
    <link rel="canonical" href="{{ $canonical }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:site_name" content="{{ $diningSettings?->meta_site_name }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $description }}">
    @if ($heroImage)
        <meta property="og:image" content="{{ $heroImage }}">
        <meta name="twitter:image" content="{{ $heroImage }}">
    @endif
@endpush

@push('css')
    <link rel="preload" href="{{ asset('fonts/Span-Regular.otf') }}" as="font" type="font/otf" crossorigin>
@endpush

<x-layouts.app>
    <x-dining-landing.hero :image="$heroImage" :settings="$diningSettings" />
    <x-dining-landing.philosophy :settings="$diningSettings" :image="$philosophyImage" />
    <x-dining-landing.benefits :settings="$diningSettings" />
    <x-dining-landing.experiences :settings="$diningSettings" />
    <x-dining-landing.signature-dishes :dish="$signatureDish" />
    <x-dining-landing.dish-of-the-month :settings="$diningSettings" />
    <x-dining-landing.special-occasions :settings="$diningSettings" />
    <x-sections.guest-reviews :reviews="$testimonials" :heading="$diningSettings?->guest_reviews_heading" />
    <x-dining-landing.visit-info-faq :settings="$diningSettings" />
    <x-dining-landing.reservation-cta :settings="$diningSettings" />
</x-layouts.app>
