@push('meta')
@php
$metaTitle = $blog->meta_title ?: $blog->title;

$metaDescription = $blog->meta_description
?: \Illuminate\Support\Str::limit(strip_tags($blog->excerpt ?: $blog->description ?: ''), 160, '');

$metaImage = $blog->hero_image
?? $blog->card_image
?? $page->hero_image
?? null;
@endphp

<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">

<meta name="author" content="{{ $blog->author_name ?: 'Nandini Jungle by Hanging Gardens' }}">
<link rel="canonical" href="{{ url()->current() }}">

<meta property="og:type" content="article">
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
    $heroImage = $blog->hero_image
    ?? $blog->card_image
    ?? $page->hero_image
    ?? null;

    $heroMobileImage = $blog->hero_mobile_image
    ?? $blog->hero_image
    ?? $blog->card_image
    ?? $page->hero_mobile_image
    ?? $heroImage;

    $heroAlt = $blog->hero_image_alt
    ?? $blog->card_image_alt
    ?? $blog->title;

    $publishedDate = $blog->published_at;

    $buttonLabel = $blog->button_label ?: 'Read More';

    $buttonUrl = $blog->button_url
    ? html_entity_decode($blog->button_url, ENT_QUOTES | ENT_HTML5, 'UTF-8')
    : null;
    @endphp

    {{-- Hero Image --}}
    @if ($heroImage)
    <section class="relative w-full">
        <picture>
            @if ($heroMobileImage)
            <source media="(max-width: 767px)" srcset="{{ asset('storage/' . $heroMobileImage) }}">
            @endif

            <img src="{{ asset('storage/' . $heroImage) }}" alt="{{ $heroAlt }}" class="w-full h-[65vh] md:h-[85vh] object-cover object-center">
        </picture>
    </section>
    @endif

    {{-- Blog Content --}}
    <section class="py-14 md:py-20 px-6">
        <div class="max-w-4xl mx-auto text-center">
            <p class="mb-4 text-xs uppercase tracking-[0.22em] text-slate-500">
                {{ ucfirst($blog->type) }}

                @if ($publishedDate)
                · {{ $publishedDate->format('j F Y') }}
                @endif
            </p>

            <h1 class="text-4xl leading-snug tracking-[0.15em] md:tracking-[0.25em] uppercase text-slate-800 mb-6 md:mb-8 font-medium">
                {{ $blog->title }}
            </h1>

            @if (! empty($blog->subtitle))
            <p class="text-lg md:text-xl text-slate-700 mb-8 uppercase tracking-[0.15em]">
                {{ $blog->subtitle }}
            </p>
            @endif

            @if (! empty($blog->author_name))
            <p class="mb-10 text-sm text-slate-600">
                By {{ $blog->author_name }}
            </p>
            @endif

            @if (! empty($blog->description))
            <div class="prose prose-slate text-[15px] sm:text-base leading-relaxed text-gray-600 max-w-2xl sm:max-w-3xl md:max-w-5xl mx-auto
                [&_p]:mb-4
                [&_p]:min-h-6
                [&_p:last-child]:mb-0
                [&_strong]:font-semibold
                [&_strong]:text-slate-900
                [&_ul]:my-6
                [&_ol]:my-6
                [&_li]:mb-1
                [&_h2]:mt-10
                [&_h2]:mb-4
                [&_h2]:text-xl sm:[&_h2]:text-2xl md:[&_h2]:text-[28px]
                [&_h2]:font-semibold
                [&_h3]:mt-8
                [&_h3]:mb-3
                [&_h3]:text-lg sm:[&_h3]:text-xl
                [&_h3]:font-semibold">
                {!! $blog->description !!}
            </div>
            @elseif (! empty($blog->excerpt))
            <p class="text-[15px] sm:text-base leading-relaxed text-gray-600 max-w-2xl sm:max-w-3xl md:max-w-5xl mx-auto">
                {{ $blog->excerpt }}
            </p>
            @endif

            @if (! empty($buttonUrl))
            <div class="mt-10 mb-8">
                <x-buttons.link-button href="{{ $buttonUrl }}" variant="solid">
                    {{ $buttonLabel }}
                </x-buttons.link-button>
            </div>
            @endif
        </div>
    </section>

    {{-- Blog Sections --}}
    @foreach ($sections as $section)
    @if ($section->section_key === 'image_overlay_section')
    <x-sections.image-overlay-section :section="$section" />

    @elseif ($section->section_key === 'split_media_section')
    <x-sections.split-media-section :section="$section" />

    @elseif ($section->section_key === 'split_media_reverse')
    <x-sections.split-media-section :section="$section" reverse />

    @elseif ($section->section_key === 'three_images_section')
    <x-sections.three-images-section :section="$section" />

    @elseif ($section->section_key === 'two_images_section')
    <x-sections.two-images-section :section="$section" />

    @elseif ($section->section_key === 'two_images_reverse')
    <x-sections.two-images-section :section="$section" reverse />

    @elseif ($section->section_key === 'video_text_section')
    <x-sections.video-text-section :section="$section" />
    @endif
    @endforeach

    {{-- Related Blog --}}
    @if ($relatedBlogs->isNotEmpty())
    <section class="pt-14 md:pt-20">
        <div class="px-6 mb-10 text-center">
            <h2 class="text-2xl sm:text-3xl md:text-3xl leading-snug tracking-[0.15em] md:tracking-[0.22em] uppercase text-slate-800 font-medium">
                Related Articles
            </h2>
        </div>

        <x-sections.item-carousel :items="$relatedBlogs" route-name="blog.show" />
    </section>
    @endif
</x-layouts.app>
