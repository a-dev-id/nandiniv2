@props([
'section' => null,
'height' => 'h-[460px] sm:h-[560px] lg:h-[760px]',
'darkOverlay' => true,
'align' => 'center',
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

$resolvedTitle = $section?->title ?? '';
$resolvedSubtitle = $section?->subtitle ?? '';
$resolvedExcerpt = $section?->excerpt ?? '';
$resolvedDescription = $section?->description ?? '';

$resolvedTitleText = $cleanText($resolvedTitle);
$resolvedSubtitleText = $cleanText($resolvedSubtitle);
$resolvedExcerptText = $cleanText($resolvedExcerpt);
$resolvedDescriptionText = $cleanText($resolvedDescription);

$sectionImage = $section?->images?->first();

$desktopRawImage = $sectionImage?->image ?? '';

$mobileRawImage = $sectionImage?->mobile_image
?: $sectionImage?->image
?: '';

$resolvedDesktopImage = $resolveImage($desktopRawImage ?: $mobileRawImage);
$resolvedMobileImage = $resolveImage($mobileRawImage ?: $desktopRawImage);

$resolvedAlt = $sectionImage?->image_alt
?: $sectionImage?->mobile_image_alt
?: $resolvedTitleText
?: 'Section image';

$buttonLabel = $section?->button_label ?? null;
$buttonUrl = null;

if (($section?->button_link_type ?? 'manual') === 'route' && $section?->button_route) {
$buttonUrl = \Illuminate\Support\Facades\Route::has($section->button_route)
? route($section->button_route)
: null;
} else {
$buttonUrl = $section?->button_url ?? null;
}

$buttonLabel = \App\Support\DetailPageButtonLabel::resolve(
$buttonLabel,
$section?->button_route,
$buttonUrl,
);

/*
|--------------------------------------------------------------------------
| Text alignment from database
|--------------------------------------------------------------------------
*/
$sectionAlign = $section?->text_align ?: $align;

$safeAlign = in_array($sectionAlign, ['left', 'center', 'right'], true)
? $sectionAlign
: 'center';

$contentAlignClass = match ($safeAlign) {
'left' => 'items-center justify-start text-left',
'right' => 'items-center justify-end text-right',
default => 'items-center justify-center text-center',
};

$innerWidthClass = match ($safeAlign) {
'left' => 'w-full max-w-3xl px-6 sm:px-10 lg:pl-20 py-10 sm:py-12',
'right' => 'w-full max-w-3xl px-6 sm:px-10 lg:pr-20 py-10 sm:py-12',
default => 'w-full max-w-3xl px-6 sm:px-10 py-10 sm:py-12',
};

$textAlignClass = match ($safeAlign) {
'left' => '',
'right' => 'ml-auto',
default => 'mx-auto',
};

$buttonAlignClass = match ($safeAlign) {
'left' => 'justify-start',
'right' => 'justify-end',
default => 'justify-center',
};
@endphp

@if ($section)
<section class="w-full">
    <div class="relative w-full overflow-hidden bg-neutral-100">

        @if ($resolvedDesktopImage || $resolvedMobileImage)
        <picture class="absolute inset-0 w-full h-full overflow-hidden">
            @if ($resolvedMobileImage)
            <source media="(max-width: 767px)" srcset="{{ $resolvedMobileImage }}">
            @endif

            <img src="{{ $resolvedDesktopImage ?: $resolvedMobileImage }}" alt="{{ $resolvedAlt }}" class="absolute inset-0 w-full h-full object-cover object-center" loading="lazy">
        </picture>
        @endif

        <div class="relative {{ $height }}"></div>

        @if ($darkOverlay)
        <div class="absolute inset-0 bg-black/25"></div>
        @endif

        <div class="absolute inset-0 flex {{ $contentAlignClass }} px-2 lg:px-6">
            <div class="{{ $innerWidthClass }}">

                @if ($resolvedTitleText !== '')
                <h2 class="text-lg text-white uppercase leading-snug font-medium mb-3 sm:text-xl">
                    {{ $resolvedTitleText }}
                </h2>
                @endif

                @if ($resolvedSubtitleText !== '')
                <p class="mt-2 text-white/90 uppercase text-xs sm:text-sm">
                    {{ $resolvedSubtitleText }}
                </p>
                @endif

                @if ($resolvedExcerptText !== '')
                <p class="mt-2 text-white/90 text-xs leading-relaxed max-w-2xl {{ $textAlignClass }} sm:text-sm">
                    {{ $resolvedExcerptText }}
                </p>
                @endif

                @if ($resolvedDescriptionText !== '')
                <div class="mt-2 text-white/90 text-xs leading-relaxed max-w-2xl {{ $textAlignClass }} [&_p]:mb-2 [&_p:last-child]:mb-0 [&_strong]:font-semibold [&_ul]:list-none [&_ul]:pl-0 [&_ol]:list-none [&_ol]:pl-0 [&_li]:mb-1 sm:text-sm">
                    {!! $resolvedDescription !!}
                </div>
                @endif

                @if ($buttonLabel && $buttonUrl)
                <div class="mt-8 flex {{ $buttonAlignClass }}">
                    <a href="{{ $buttonUrl }}" class="inline-flex items-center justify-center bg-[#A88444] text-white px-5 py-2.5 uppercase text-xs font-medium hover:bg-[#B8945B] transition tracking-[0.08em] sm:text-sm">
                        {{ $buttonLabel }}
                    </a>
                </div>
                @endif

            </div>
        </div>

    </div>
</section>
@endif
