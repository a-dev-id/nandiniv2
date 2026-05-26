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
    <x-heroes.membership-hero :page="$page" :show-content="false" :show-overlay="false" />

    @forelse ($sections as $section)
    @switch($section->section_key)

    @case('intro_text_section')
    <x-sections.intro-text-section :section="$section" />
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