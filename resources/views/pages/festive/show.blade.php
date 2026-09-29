@php
    $title = $event->meta_title ?: $event->title.' | Nandini Jungle by Hanging Gardens';
    $description = $event->meta_description ?: $event->hero_description;
    $canonical = route('festive.show', $event);
@endphp

@push('meta')
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <link rel="canonical" href="{{ $canonical }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $canonical }}">
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
    <x-festive.detail.hero :event="$event" :image="$heroImage" />

    @if ($event->menu_visible)
        <x-festive.detail.menu :event="$event" :items="$menuItems" />
    @endif

    @if ($event->programme_visible)
        <x-festive.detail.programme :event="$event" :image="$programmeImage" />
    @endif

    @if ($event->reservation_visible)
        <x-festive.detail.reservation-cta :event="$event" :image="$heroImage" />
    @endif
</x-layouts.app>
