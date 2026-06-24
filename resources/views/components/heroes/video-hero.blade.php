@props([
'videoId',
])

@php
$embedUrl = "https://www.youtube.com/embed/{$videoId}?autoplay=1&mute=1&controls=0&loop=1&rel=0&playlist={$videoId}";
$thumbnailUrl = "https://i.ytimg.com/vi/{$videoId}/maxresdefault.jpg";
@endphp

<header class="shadow-xl">

    <!-- Mobile / Tablet (1:1 ratio + stronger zoom) -->
    <div class="relative block lg:hidden w-full aspect-[4/3] overflow-hidden bg-black" data-youtube-embed data-src="{{ $embedUrl }}" data-title="Nandini Jungle video hero" data-frame-class="absolute inset-1/2 w-[180%] h-[180%] -translate-x-1/2 -translate-y-1/2 pointer-events-none">
        <img src="{{ $thumbnailUrl }}" alt="Nandini Jungle video preview" class="absolute inset-0 h-full w-full object-cover" width="1280" height="720" loading="eager" fetchpriority="high" decoding="async">
        <div class="absolute inset-0 bg-black/15"></div>
    </div>

    <!-- Desktop (Full Screen) -->
    <div class="relative hidden lg:block h-screen overflow-hidden bg-black" data-youtube-embed data-src="{{ $embedUrl }}" data-title="Nandini Jungle video hero" data-frame-class="absolute inset-0 w-[102%] h-[120%] top-[-10%] left-[-1%] pointer-events-none">
        <img src="{{ $thumbnailUrl }}" alt="Nandini Jungle video preview" class="absolute inset-0 h-full w-full object-cover" width="1280" height="720" loading="eager" fetchpriority="high" decoding="async">
        <div class="absolute inset-0 bg-black/15"></div>
    </div>

</header>