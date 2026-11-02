@props([
'videoId',
'background' => false,
'poster' => null,
'posterMobile' => null,
'posterMobileSrcset' => null,
'posterMobileAvifSrcset' => null,
'posterDesktop' => null,
'posterDesktopAvif' => null,
'hideMobileOverlay' => false,
'title' => 'Nandini Jungle video',
'posterAlt' => '',
])

@php
$embedParameters = http_build_query([
    'autoplay' => 1,
    'mute' => 1,
    'controls' => 0,
    'disablekb' => 1,
    'enablejsapi' => 1,
    'fs' => 0,
    'loop' => 1,
    'origin' => request()->getSchemeAndHttpHost(),
    'playlist' => $videoId,
    'playsinline' => 1,
    'rel' => 0,
], '', '&', PHP_QUERY_RFC3986);
$embedUrl = "https://www.youtube-nocookie.com/embed/{$videoId}?{$embedParameters}";
$thumbnailUrl = $poster ?: "https://i.ytimg.com/vi/{$videoId}/maxresdefault.jpg";
$mobilePosterUrl = $posterMobile ?: $thumbnailUrl;
$desktopPosterUrl = $posterDesktop ?: $thumbnailUrl;
@endphp

@if ($background)
    <div class="pointer-events-none absolute inset-0 overflow-hidden bg-black [container-type:size]" aria-hidden="true" data-youtube-embed data-autoload="true" data-src="{{ $embedUrl }}" data-title="{{ $title }}" data-frame-class="absolute top-1/2 left-1/2 aspect-video h-auto w-[max(100cqw,177.78cqh)] -translate-x-1/2 -translate-y-1/2 pointer-events-none" {{ $attributes->only('data-gtm-section') }}>
        <picture class="absolute inset-0 z-10 block transition-opacity duration-700" data-youtube-poster>
            @if ($posterDesktopAvif)
                <source type="image/avif" media="(min-width: 1024px)" srcset="{{ $posterDesktopAvif }}">
            @endif
            @if ($posterMobileAvifSrcset)
                <source type="image/avif" srcset="{{ $posterMobileAvifSrcset }}" sizes="100vw">
            @endif
            @if ($posterDesktop)
                <source media="(min-width: 1024px)" srcset="{{ $desktopPosterUrl }}">
            @endif
            <img src="{{ $mobilePosterUrl }}" @if ($posterMobileSrcset) srcset="{{ $posterMobileSrcset }}" sizes="100vw" @endif alt="" class="h-full w-full object-cover" width="960" height="720" loading="eager" fetchpriority="high" decoding="async">
        </picture>
    </div>
@else
<header class="shadow-xl" {{ $attributes->only('data-gtm-section') }}>
    <div class="relative aspect-[4/3] w-full cursor-pointer overflow-hidden bg-black lg:aspect-auto lg:h-screen" role="button" tabindex="0" aria-label="Play {{ $title }}" data-youtube-embed data-autoload="true" data-src="{{ $embedUrl }}" data-title="{{ $title }}" data-frame-class="pointer-events-none absolute inset-1/2 h-[180%] w-[180%] -translate-x-1/2 -translate-y-1/2 lg:inset-0 lg:left-[-1%] lg:top-[-10%] lg:h-[120%] lg:w-[102%] lg:translate-x-0 lg:translate-y-0">
        <div class="absolute inset-0 z-10 transition-opacity duration-700" data-youtube-poster>
            <picture class="absolute inset-0 block">
                @if ($posterDesktopAvif)
                    <source type="image/avif" media="(min-width: 1024px)" srcset="{{ $posterDesktopAvif }}">
                @endif
                @if ($posterMobileAvifSrcset)
                    <source type="image/avif" srcset="{{ $posterMobileAvifSrcset }}" sizes="100vw">
                @endif
                @if ($posterDesktop)
                    <source media="(min-width: 1024px)" srcset="{{ $desktopPosterUrl }}">
                @endif
                <img src="{{ $mobilePosterUrl }}" @if ($posterMobileSrcset) srcset="{{ $posterMobileSrcset }}" sizes="100vw" @endif alt="{{ $posterAlt ?: 'Nandini Jungle video preview' }}" class="h-full w-full object-cover" width="960" height="720" loading="eager" fetchpriority="high" decoding="async">
            </picture>
            <div class="absolute inset-0 bg-black/15 {{ $hideMobileOverlay ? 'hidden md:block' : '' }} lg:block"></div>
            <span class="absolute left-1/2 top-1/2 flex h-14 w-14 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full bg-black/55 text-white ring-1 ring-white/70 lg:h-16 lg:w-16" aria-hidden="true">
                <span class="ml-1 h-0 w-0 border-y-[10px] border-l-[16px] border-y-transparent border-l-white lg:border-y-[12px] lg:border-l-[18px]"></span>
            </span>
        </div>
    </div>
</header>
@endif
