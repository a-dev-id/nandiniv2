@props([
    'page',
    'section' => null,
])

@php
$sectionDesktopImage = $section?->images?->first()?->image;
$sectionMobileImage = $section?->images?->first()?->mobile_image;
$desktopImage = $sectionDesktopImage ?: $page->hero_image ?: $page->hero_mobile_image;
$mobileImage = $sectionMobileImage ?: $sectionDesktopImage ?: $page->hero_mobile_image ?: $page->hero_image;
$desktopImageUrl = $desktopImage ? Storage::disk('public')->url($desktopImage) : null;
$mobileImageUrl = $mobileImage ? Storage::disk('public')->url($mobileImage) : null;
$alt = $section?->images?->first()?->image_alt ?: $page->hero_image_alt ?: $page->hero_mobile_image_alt ?: $page->title;
@endphp

<header class="relative aspect-[4/3] w-full overflow-hidden bg-slate-800 shadow-xl lg:aspect-auto lg:h-[70vh]" data-gtm-section="hero">
    @if ($desktopImageUrl || $mobileImageUrl)
        <picture class="absolute inset-0 block h-full w-full">
            @if ($mobileImageUrl)
                <source media="(max-width: 767px)" srcset="{{ $mobileImageUrl }}">
            @endif
            <img src="{{ $desktopImageUrl ?: $mobileImageUrl }}" alt="{{ $alt }}" class="h-full w-full object-cover" width="1920" height="1080" loading="eager" fetchpriority="high" decoding="async">
        </picture>
    @endif
</header>
