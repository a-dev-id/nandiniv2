@props([
'section' => null,
'layout' => 'split',
'reverse' => false,
'noButton' => false,
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

$title = $section?->title ?? '';
$subtitle = $section?->subtitle ?? '';
$excerpt = $section?->excerpt ?? '';
$description = $section?->description ?? '';

$titleText = $cleanText($title);
$subtitleText = $cleanText($subtitle);
$excerptText = $cleanText($excerpt);
$descriptionText = $cleanText($description);

$sectionImage = $section?->images?->first();
$desktopRawImage = $sectionImage?->image ?? '';
$mobileRawImage = $sectionImage?->mobile_image ?: $sectionImage?->image ?: '';
$desktopImageUrl = $resolveImage($desktopRawImage ?: $mobileRawImage);
$mobileImageUrl = $resolveImage($mobileRawImage ?: $desktopRawImage);
$hasImage = $desktopImageUrl !== '' || $mobileImageUrl !== '';

$imageAlt = $sectionImage?->image_alt
?: $sectionImage?->mobile_image_alt
?: $titleText
?: 'Blog section image';

$textAlign = $section?->text_align ?: 'left';

$textAlignClass = match ($textAlign) {
'center' => 'text-center',
'right' => 'text-right',
default => 'text-left',
};

$buttonAlignClass = match ($textAlign) {
'center' => 'justify-center',
'right' => 'justify-end',
default => 'justify-start',
};

$backgroundColor = $section?->background_color ?: 'white';

$backgroundClass = match ($backgroundColor) {
'soft_gray' => 'bg-[#F7F7F7]',
'warm_ivory' => 'bg-[#F8F4EC]',
'light_gold' => 'bg-[#F4E8D0]',
'dark_navy' => 'bg-slate-900',
default => 'bg-white',
};

$textColorClass = $backgroundColor === 'dark_navy' ? 'text-slate-100' : 'text-slate-800';
$mutedTextColorClass = $backgroundColor === 'dark_navy' ? 'text-slate-200' : 'text-slate-700';
$sectionPaddingClass = $backgroundColor === 'white' ? '' : 'p-5 sm:p-6';
$contentLayout = $hasImage ? $layout : 'text';

$imageOrderClass = $reverse ? 'md:order-2' : 'md:order-1';
$textOrderClass = $reverse ? 'md:order-1' : 'md:order-2';

$buttonLabel = $section?->button_label ?: null;
$buttonUrl = null;

if (($section?->button_link_type ?? 'manual') === 'route' && $section?->button_route) {
$buttonUrl = \Illuminate\Support\Facades\Route::has($section->button_route)
? route($section->button_route)
: null;
} else {
$buttonUrl = $section?->button_url;
}
@endphp

@if ($section)
<section class="my-12 md:my-14 {{ $backgroundClass }} {{ $sectionPaddingClass }}">
    @if ($contentLayout === 'split')
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 md:items-start md:gap-8">
        <div class="{{ $imageOrderClass }}">
            <div class="relative aspect-[16/9] w-full overflow-hidden bg-slate-100">
                <picture class="block h-full w-full">
                    @if ($mobileImageUrl)
                    <source media="(max-width: 767px)" srcset="{{ $mobileImageUrl }}">
                    @endif

                    <img src="{{ $desktopImageUrl ?: $mobileImageUrl }}" alt="{{ $imageAlt }}" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
                </picture>
            </div>
        </div>

        <div class="{{ $textOrderClass }}">
            <x-sections.blog-content-section-text
                :title="$titleText"
                :subtitle="$subtitleText"
                :excerpt="$excerptText"
                :description="$description"
                :description-text="$descriptionText"
                :text-align-class="$textAlignClass"
                :text-color-class="$textColorClass"
                :muted-text-color-class="$mutedTextColorClass"
            />

            @if (! $noButton && $buttonLabel && $buttonUrl)
            <div class="mt-8 flex {{ $buttonAlignClass }}">
                <x-buttons.link-button href="{{ $buttonUrl }}" variant="solid">
                    {{ $buttonLabel }}
                </x-buttons.link-button>
            </div>
            @endif
        </div>
    </div>
    @else
    <div class="space-y-5">
        @if ($contentLayout === 'stacked' && $hasImage)
        <div class="relative aspect-[16/9] w-full overflow-hidden bg-slate-100">
            <picture class="block h-full w-full">
                @if ($mobileImageUrl)
                <source media="(max-width: 767px)" srcset="{{ $mobileImageUrl }}">
                @endif

                <img src="{{ $desktopImageUrl ?: $mobileImageUrl }}" alt="{{ $imageAlt }}" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
            </picture>
        </div>
        @endif

        <x-sections.blog-content-section-text
            :title="$titleText"
            :subtitle="$subtitleText"
            :excerpt="$excerptText"
            :description="$description"
            :description-text="$descriptionText"
            :text-align-class="$textAlignClass"
            :text-color-class="$textColorClass"
            :muted-text-color-class="$mutedTextColorClass"
        />

        @if (! $noButton && $buttonLabel && $buttonUrl)
        <div class="pt-3 flex {{ $buttonAlignClass }}">
            <x-buttons.link-button href="{{ $buttonUrl }}" variant="solid">
                {{ $buttonLabel }}
            </x-buttons.link-button>
        </div>
        @endif
    </div>
    @endif
</section>
@endif
