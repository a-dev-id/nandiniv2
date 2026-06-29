@push('meta')
@php
$metaTitle = $page->meta_title ?: $page->title;

$metaDescription = $page->meta_description
?: \Illuminate\Support\Str::limit(strip_tags($page->description ?: $page->excerpt ?: ''), 160, '');

$metaImage = $page->hero_image ?: $page->hero_mobile_image ?: null;
$sections = $page->sections ?? collect();
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

    <section class="px-6 py-14 text-center md:py-20">
        <div class="mx-auto max-w-5xl">
            <h1 class="text-xl font-normal uppercase leading-snug text-slate-700 sm:text-2xl">
                {!! str_ireplace(
                ['&lt;br&gt;', '&lt;br/&gt;', '&lt;br /&gt;'],
                '<br class="hidden md:block">',
                e($page->title)
                ) !!}
            </h1>

            @if (! empty($page->subtitle))
            <h2 class="mx-auto mt-5 italic font-sans text-slate-700 sm:text-xl">
                {!! str_ireplace(
                ['&lt;br&gt;', '&lt;br/&gt;', '&lt;br /&gt;'],
                '<br class="hidden md:block">',
                e($page->subtitle)
                ) !!}
            </h2>
            @endif

            @if (! empty($page->description))
            <div class="mx-auto {{ ! empty($page->subtitle) ? 'mt-6' : 'mt-10' }} text-xs leading-relaxed text-gray-600 sm:text-sm [&_h2]:m-0 [&_h2]:mb-5 [&_h2]:font-sans [&_h2]:text-lg [&_h2]:font-normal [&_h2]:italic [&_h2]:normal-case [&_h2]:tracking-normal [&_h2]:leading-relaxed [&_h2]:text-slate-700 sm:[&_h2]:text-xl [&_h3]:mb-4 [&_h3]:mt-8 [&_h3]:font-normal [&_h3]:italic [&_h3]:uppercase [&_h3]:leading-snug [&_h3]:text-slate-700 [&_h3]:text-base sm:[&_h3]:text-lg [&_p]:mx-auto [&_p]:mb-6 [&_p]:text-xs [&_p]:leading-relaxed [&_p]:text-gray-600 sm:[&_p]:text-sm [&_p:last-child]:mb-0">
                {!! $page->description !!}
            </div>
            @endif
        </div>
    </section>

    @foreach ($sections as $section)
    @if ($section->section_key === 'intro_text_section')
    <x-sections.intro-text-section :section="$section" />
    @endif

    @if ($section->section_key === 'image_overlay_section')
    <x-sections.image-overlay-section :section="$section" />
    @endif

    @if ($section->section_key === 'contained_image_section')
    <x-sections.contained-image-section :section="$section" />
    @endif

    @if ($section->section_key === 'split_media_section')
    <x-sections.split-media-section :section="$section" :excerpt-only="false" image-span="8" text-span="4" />
    @endif

    @if ($section->section_key === 'split_media_reverse')
    <x-sections.split-media-section :section="$section" :reverse="true" :excerpt-only="false" image-span="8" text-span="4" />
    @endif

    @if ($section->section_key === 'seo_split_media_section')
    <x-sections.seo-split-media-section :section="$section" :page="$page" />
    @endif

    @if ($section->section_key === 'seo_split_media_reverse')
    <x-sections.seo-split-media-section :section="$section" :page="$page" :reverse="true" />
    @endif

    @if ($section->section_key === 'three_images_section')
    <x-sections.three-images-section :section="$section" />
    @endif

    @if ($section->section_key === 'two_images_section')
    <x-sections.two-images-section :section="$section" />
    @endif

    @if ($section->section_key === 'two_images_reverse')
    <x-sections.two-images-section :section="$section" :reverse="true" />
    @endif

    @endforeach
</x-layouts.app>
