@push('meta')
@php
$metaTitle = $page->meta_title ?: $page->title;
$metaDescription = $page->meta_description ?? '';
$metaImage = $page->hero_image ?? $page->hero_mobile_image ?? null;
@endphp

<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">

@if (! empty($page->meta_keywords))
<meta name="keywords" content="{{ $page->meta_keywords }}">
@endif

<meta name="author" content="Nandini Jungle by Hanging Gardens">
<link rel="canonical" href="{{ url()->current() }}">

<meta property="og:type" content="website">
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
    $sectionTrackingNames = [
        8 => 'wellness_sanctuary',
        9 => 'healing',
        10 => 'accommodation',
        11 => 'holy_water',
        12 => 'booking_cta',
    ];
    @endphp

    <x-heroes.video-hero video-id="DQGm1PB0828" data-gtm-section="hero" />

    <x-sections.video-text-section :page="$page" video-id="eh5h5P6_3LQ" data-gtm-section="introduction" />

    @foreach ($sections as $section)
    @if ($section->section_key === 'image_overlay_section')
    <x-sections.image-overlay-section :section="$section" :data-gtm-section="$sectionTrackingNames[$section->id] ?? null" />
    @endif

    @if ($section->section_key === 'contained_image_section')
    <x-sections.contained-image-section :section="$section" :data-gtm-section="$sectionTrackingNames[$section->id] ?? null" />
    @endif

    @if ($section->section_key === 'split_media_section')
    <x-sections.split-media-section :section="$section" :excerpt-only="false" image-span="8" text-span="4" :data-gtm-section="$sectionTrackingNames[$section->id] ?? null" />
    @endif

    @if ($section->section_key === 'split_media_reverse')
    <x-sections.split-media-section :section="$section" :reverse="true" :excerpt-only="false" image-span="8" text-span="4" :data-gtm-section="$sectionTrackingNames[$section->id] ?? null" />
    @endif

    @if ($section->section_key === 'intro_text_section')
    <x-sections.intro-text-section :section="$section" :data-gtm-section="$sectionTrackingNames[$section->id] ?? null" />

    <x-sections.item-carousel :items="$experiences" route-name="holy-river.show" data-gtm-section="activities" />
    @endif
    @endforeach
</x-layouts.app>
