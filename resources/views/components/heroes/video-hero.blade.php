@props([
'videoId',
])

@php
$embedUrl = "https://www.youtube.com/embed/{$videoId}?autoplay=1&mute=1&controls=0&loop=1&rel=0&playlist={$videoId}";
@endphp

<header class="shadow-xl">

    <!-- Mobile / Tablet (1:1 ratio + stronger zoom) -->
    <div class="relative block lg:hidden w-full aspect-square overflow-hidden">
        <iframe class="absolute inset-1/2 w-[180%] h-[180%] -translate-x-1/2 -translate-y-1/2 pointer-events-none" src="{{ $embedUrl }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen>
        </iframe>
    </div>

    <!-- Desktop (Full Screen) -->
    <div class="relative hidden lg:block h-screen overflow-hidden">
        <iframe class="absolute inset-0 w-[102%] h-[120%] top-[-10%] left-[-1%] pointer-events-none" src="{{ $embedUrl }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen>
        </iframe>
    </div>

</header>