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
    <x-heroes.membership-hero :page="$page" primary-label="Join Now" primary-url="#" secondary-label="Sign In" secondary-url="#" :show-content="true" :show-overlay="true" />

    @forelse ($sections as $section)
    @switch($section->section_key)

    @case('intro_text_section')
    <x-sections.intro-text-section :section="$section" />
    @break

    @case('how_it_works_section')
    <x-sections.how-it-works-section :section="$section" />
    @break

    @case('member_benefits_section')
    <x-sections.member-benefits-section :section="$section" />
    @break

    @case('membership_tier_section')
    <x-sections.membership-tier-section :section="$section" />
    @break

    @case('membership_use_points_section')
    <x-sections.membership-use-points-section :section="$section" :rewards="$rewards" />
    @break

    @case('membership_faq_section')
    <x-sections.membership-join-today image="images/membership/join-today.webp" mobile-image="images/membership/join-today-mobile.webp" primary-label="Join Now" primary-url="/membership/join" secondary-label="Sign In" secondary-url="/membership/sign-in" />

    <x-sections.membership-faq-section :section="$section" contact-label="Contact" contact-url="https://wa.me/6281236871170" />
    @break

    @case('image_overlay_section')
    <x-sections.image-overlay-section :section="$section" />
    @break

    @case('contained_image_section')
    <x-sections.contained-image-section :section="$section" />
    @break

    @default
    @if (app()->environment('local'))
    <div class="px-6 py-4 text-red-600">
        Unknown section key: {{ $section->section_key }}
    </div>
    @endif

    @endswitch
    @empty
    @if (app()->environment('local'))
    <div class="px-6 py-4 text-red-600">
        No active page sections found for this page.
    </div>
    @endif
    @endforelse
</x-layouts.app>