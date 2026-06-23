@push('meta')
<title>{{ $page->meta_title ?: $page->title }}</title>
<meta name="description" content="{{ $page->meta_description ?? '' }}">

@if (! empty($page->meta_keywords))
<meta name="keywords" content="{{ $page->meta_keywords }}">
@endif

<meta name="author" content="Nandini Jungle by Hanging Gardens">
<link rel="canonical" href="{{ url()->current() }}">

<meta property="og:type" content="website">
<meta property="og:title" content="{{ $page->meta_title ?: $page->title }}">
<meta property="og:description" content="{{ $page->meta_description ?? '' }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:site_name" content="Nandini Jungle by Hanging Gardens">

@if (! empty($page->hero_image))
<meta property="og:image" content="{{ asset('storage/' . $page->hero_image) }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta name="twitter:image" content="{{ asset('storage/' . $page->hero_image) }}">
@endif

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $page->meta_title ?: $page->title }}">
<meta name="twitter:description" content="{{ $page->meta_description ?? '' }}">
@endpush

<x-layouts.app>
    <x-heroes.image-hero :page="$page" />

    <x-sections.page-description :page="$page" />

    @foreach ($sections as $section)
    @if ($section->section_key === 'spa_information_section')
    <x-sections.spa-information-section :section="$section" />

    @if ($spas->isNotEmpty())
    <h2 class="text-xl text-center leading-snug uppercase font-medium mt-20 mb-3">
        Sacred Jungle Wellness Journey
    </h2>

    {{-- Description --}}
    <div class="pt-8 md:pt-8 mx-auto max-w-[950px] text-center text-gray-600">
        <div class="[&_h1]:text-2xl [&_h1]:leading-snug [&_h1]:uppercase text-slate-700 [&_h1]:mb-3 [&_h2]:mb-3 [&_h3]:mb-3 [&_h1]:font-medium [&_h2]:text-xl [&_h2]:font-semibold [&_h2]:leading-tight [&_h2]:normal-case [&_h3]:text-lg [&_h3]:font-semibold [&_h3]:leading-snug [&_p]:mb-2 [&_p]:text-base [&_p]:leading-7 [&_ul]:mb-5 [&_ul]:list-disc [&_ul]:pl-6 [&_ol]:mb-5 [&_ol]:list-decimal [&_ol]:pl-6 [&_li]:mb-2 [&_li]:text-base [&_li]:leading-7 [&_ul_ul]:mt-2 [&_ul_ul]:list-disc [&_ul_ul]:pl-6 [&_strong]:font-semibold">
            Reconnect with your inner rhythm where nature meets ancient wisdom.The Sacred Jungle Wellness Journey is a transformative spa experience rooted in traditional Balinese healing. Surrounded by pristine rainforest, this holistic retreat combines restorative massages, purifying botanical treatments, and sacred water rituals to melt away stress, detoxify the body, and restore deep mental clarity.
        </div>
    </div>

    <x-sections.item-carousel :items="$spas" route-name="spa.show" wrapper-class="pt-10 md:pt-16" />
    @endif
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