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
    <x-heroes.image-hero :page="$page" />

    <x-sections.page-description :page="$page" />

    <x-sections.item-list model="gallery" :items="$galleries" :with-filter="true" :limit="99" />
</x-layouts.app>