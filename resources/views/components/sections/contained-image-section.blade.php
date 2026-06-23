@props([
'section' => null,
])

@php
$resolveImage = function (?string $raw): string {
$raw = trim((string) $raw);

if ($raw === '') {
return '';
}

if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) {
return $raw;
}

if (str_starts_with($raw, '/storage/')) {
return $raw;
}

if (str_starts_with($raw, 'storage/')) {
return '/' . $raw;
}

if (str_starts_with($raw, '/')) {
return $raw;
}

return asset('storage/' . $raw);
};

$cleanText = function (?string $value): string {
$text = html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
$text = str_replace("\xc2\xa0", ' ', $text);
$text = preg_replace('/\s+/', ' ', $text);

return trim((string) $text);
};

$sectionImage = $section?->images?->first();

$desktopRawImage = $sectionImage?->image ?? '';
$mobileRawImage = $sectionImage?->mobile_image ?: $desktopRawImage;

$desktopImage = $resolveImage($desktopRawImage ?: $mobileRawImage);
$mobileImage = $resolveImage($mobileRawImage ?: $desktopRawImage);

$alt = $sectionImage?->image_alt
?: $sectionImage?->mobile_image_alt
?: $cleanText($section?->title)
?: 'Section image';
@endphp

@if ($section && ($desktopImage || $mobileImage))
<section class="bg-white px-3 md:px-6">
    <div class="mx-auto max-w-[1200px]">
        <picture class="group block w-full overflow-hidden bg-neutral-100">
            @if ($mobileImage)
            <source media="(max-width: 767px)" srcset="{{ $mobileImage }}">
            @endif

            <img src="{{ $desktopImage ?: $mobileImage }}" alt="{{ $alt }}" class="aspect-square w-full object-cover object-center transition-transform duration-700 ease-out group-hover:scale-105 md:aspect-[3/1]" loading="lazy">
        </picture>
    </div>
</section>
@endif
