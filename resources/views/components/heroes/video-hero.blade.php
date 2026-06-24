@props([
'videoId',
])

@php
$embedUrl = "https://www.youtube-nocookie.com/embed/{$videoId}?autoplay=1&mute=1&controls=0&loop=1&rel=0&playlist={$videoId}";
$thumbnailUrl = "https://i.ytimg.com/vi/{$videoId}/maxresdefault.jpg";
@endphp

<header class="shadow-xl">

    <!-- Mobile / Tablet (1:1 ratio + stronger zoom) -->
    <div class="relative block lg:hidden w-full aspect-[4/3] overflow-hidden bg-black cursor-pointer" role="button" tabindex="0" aria-label="Play Nandini Jungle video" data-youtube-embed data-autoload="true" data-autoload-delay="600" data-src="{{ $embedUrl }}" data-title="Nandini Jungle video hero" data-frame-class="absolute inset-1/2 w-[180%] h-[180%] -translate-x-1/2 -translate-y-1/2 pointer-events-none">
        <img src="{{ $thumbnailUrl }}" alt="Nandini Jungle video preview" class="absolute inset-0 h-full w-full object-cover" width="1280" height="720" loading="eager" fetchpriority="high" decoding="async">
        <div class="absolute inset-0 bg-black/15"></div>
        <span class="absolute left-1/2 top-1/2 flex h-14 w-14 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full bg-black/55 text-white ring-1 ring-white/70" aria-hidden="true">
            <span class="ml-1 h-0 w-0 border-y-[10px] border-l-[16px] border-y-transparent border-l-white"></span>
        </span>
    </div>

    <!-- Desktop (Full Screen) -->
    <div class="relative hidden lg:block h-screen overflow-hidden bg-black cursor-pointer" role="button" tabindex="0" aria-label="Play Nandini Jungle video" data-youtube-embed data-autoload="true" data-autoload-delay="600" data-src="{{ $embedUrl }}" data-title="Nandini Jungle video hero" data-frame-class="absolute inset-0 w-[102%] h-[120%] top-[-10%] left-[-1%] pointer-events-none">
        <img src="{{ $thumbnailUrl }}" alt="Nandini Jungle video preview" class="absolute inset-0 h-full w-full object-cover" width="1280" height="720" loading="eager" fetchpriority="high" decoding="async">
        <div class="absolute inset-0 bg-black/15"></div>
        <span class="absolute left-1/2 top-1/2 flex h-16 w-16 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full bg-black/55 text-white ring-1 ring-white/70" aria-hidden="true">
            <span class="ml-1 h-0 w-0 border-y-[12px] border-l-[18px] border-y-transparent border-l-white"></span>
        </span>
    </div>

</header>
