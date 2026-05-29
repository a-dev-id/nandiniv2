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
    <x-heroes.image-hero :page="$page" />

    <x-sections.page-description :page="$page" />

    @forelse ($sections as $section)
        @switch($section->section_key)
            @case('membership_faq_section')
                <x-sections.membership-faq-section :section="$section" contact-label="Contact" :contact-url="route('contact.index')" />
                @break

            @case('intro_text_section')
                <x-sections.intro-text-section :section="$section" />
                @break

            @case('image_overlay_section')
                <x-sections.image-overlay-section :section="$section" />
                @break

            @case('split_media_section')
                <x-sections.split-media-section :section="$section" :excerpt-only="false" image-span="8" text-span="4" />
                @break

            @case('split_media_reverse')
                <x-sections.split-media-section :section="$section" :reverse="true" :excerpt-only="false" image-span="8" text-span="4" />
                @break

            @case('three_images_section')
                <x-sections.three-images-section :section="$section" />
                @break

            @case('two_images_section')
                <x-sections.two-images-section :section="$section" />
                @break

            @case('two_images_reverse')
                <x-sections.two-images-section :section="$section" :reverse="true" />
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
