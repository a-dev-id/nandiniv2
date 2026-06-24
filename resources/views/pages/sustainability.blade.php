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

    <section class="bg-white px-6 pb-16 md:pb-24">
        <div class="mx-auto grid max-w-6xl grid-cols-1 gap-5 md:grid-cols-2">
            <div class="relative aspect-video overflow-hidden bg-black cursor-pointer" role="button" tabindex="0" aria-label="Play The Big Bloom" data-youtube-embed data-src="https://www.youtube-nocookie.com/embed/BdVcsMHRi5o?rel=0&modestbranding=1&playsinline=1" data-title="The Big Bloom">
                <img src="https://i.ytimg.com/vi/BdVcsMHRi5o/hqdefault.jpg" alt="The Big Bloom video preview" class="h-full w-full object-cover" width="480" height="360" loading="lazy" decoding="async">
                <div class="absolute inset-0 bg-black/20"></div>
                <span class="absolute left-1/2 top-1/2 flex h-14 w-14 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full bg-black/55 text-white ring-1 ring-white/70" aria-hidden="true">
                    <span class="ml-1 h-0 w-0 border-y-[10px] border-l-[16px] border-y-transparent border-l-white"></span>
                </span>
            </div>

            <div class="relative aspect-video overflow-hidden bg-black cursor-pointer" role="button" tabindex="0" aria-label="Play International Flower Competition" data-youtube-embed data-src="https://www.youtube-nocookie.com/embed/Znvc4anarMc?rel=0&modestbranding=1&playsinline=1" data-title="International Flower Competition">
                <img src="https://i.ytimg.com/vi/Znvc4anarMc/hqdefault.jpg" alt="International Flower Competition video preview" class="h-full w-full object-cover" width="480" height="360" loading="lazy" decoding="async">
                <div class="absolute inset-0 bg-black/20"></div>
                <span class="absolute left-1/2 top-1/2 flex h-14 w-14 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full bg-black/55 text-white ring-1 ring-white/70" aria-hidden="true">
                    <span class="ml-1 h-0 w-0 border-y-[10px] border-l-[16px] border-y-transparent border-l-white"></span>
                </span>
            </div>
        </div>
    </section>

</x-layouts.app>
