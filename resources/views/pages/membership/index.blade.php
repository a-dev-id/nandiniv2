@push('meta')
@php
$metaTitle = $page->meta_title ?: $page->title;

$metaDescription = $page->meta_description
?: \Illuminate\Support\Str::limit(strip_tags($page->description ?: $page->excerpt ?: ''), 160, '');

$metaImage = $page->hero_image ?: $page->hero_mobile_image ?: null;
@endphp

<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">

<meta name="author" content="Nandini Jungle by Hanging Gardens">
<link rel="canonical" href="{{ url()->current() }}">

<meta property="og:type" content="website">
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:site_name" content="Nandini Jungle by Hanging Gardens">

@if ($metaImage)
<meta property="og:image" content="{{ asset('storage/' . $metaImage) }}">
@endif

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
@endpush

<x-layouts.app>
    <x-heroes.membership-hero :page="$page" primary-label="Join Now" primary-url="#" secondary-label="Sign In" secondary-url="#" />
    <x-sections.membership-how-it-works />
    <x-sections.membership-benefits button-label="Explore Benefits" button-url="#" />
    <x-sections.membership-earn-points />
    <x-sections.membership-use-points />
    <x-sections.membership-join-today image="images/membership/join-today.webp" mobile-image="images/membership/join-today-mobile.webp" primary-label="Join Now" primary-url="#" secondary-label="Sign In" secondary-url="#" />
    <x-sections.membership-faqs contact-label="Contact" contact-url="#" />
</x-layouts.app>