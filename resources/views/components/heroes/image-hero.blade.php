@props([
'page' => null,
'imageSrc' => '',
'mobileImageSrcManual' => '',
'altText' => 'Header Image',
])

@php
$desktopImageSrc = '';
$mobileImageSrc = '';
$alt = $altText;

if ($page && ($page->hero_image || $page->hero_mobile_image)) {
$desktopImage = $page->hero_image ?: $page->hero_mobile_image;
$mobileImage = $page->hero_mobile_image ?: $page->hero_image;

$desktopImageSrc = Storage::disk('public')->url($desktopImage);
$mobileImageSrc = Storage::disk('public')->url($mobileImage);

$alt = $page->hero_image_alt
?: $page->hero_mobile_image_alt
?: $page->title
?: $altText;
} else {
$desktopImageSrc = $imageSrc;
$mobileImageSrc = $mobileImageSrcManual ?: $imageSrc;
}
@endphp

<header class="shadow-xl">
    <div class="relative w-full aspect-[4/3] lg:aspect-auto lg:h-[70vh] overflow-hidden bg-slate-100">
        @if ($desktopImageSrc || $mobileImageSrc)
        <picture class="block w-full h-full">
            @if ($mobileImageSrc)
            <source media="(max-width: 767px)" srcset="{{ $mobileImageSrc }}">
            @endif

            <img src="{{ $desktopImageSrc ?: $mobileImageSrc }}" alt="{{ $alt }}" class="absolute inset-0 w-full h-full object-cover" width="1920" height="1080" loading="eager" fetchpriority="high" decoding="async" />
        </picture>
        @endif
    </div>
</header>
