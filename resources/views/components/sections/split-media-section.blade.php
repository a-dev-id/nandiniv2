@props([
'section' => null,
'reverse' => false,
'boxed' => true,
'imageSpan' => 8,
'textSpan' => 4,
'noButton' => false,
'excerptOnly' => true,
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

$imageSpan = max(1, min(12, (int) $imageSpan));
$textSpan = max(1, min(12, (int) $textSpan));

if (($imageSpan + $textSpan) > 12) {
$imageSpan = 8;
$textSpan = 4;
}

$imageSpanClass = match ($imageSpan) {
1 => 'lg:col-span-1',
2 => 'lg:col-span-2',
3 => 'lg:col-span-3',
4 => 'lg:col-span-4',
5 => 'lg:col-span-5',
6 => 'lg:col-span-6',
7 => 'lg:col-span-7',
8 => 'lg:col-span-8',
9 => 'lg:col-span-9',
10 => 'lg:col-span-10',
11 => 'lg:col-span-11',
default => 'lg:col-span-12',
};

$textSpanClass = match ($textSpan) {
1 => 'lg:col-span-1',
2 => 'lg:col-span-2',
3 => 'lg:col-span-3',
4 => 'lg:col-span-4',
5 => 'lg:col-span-5',
6 => 'lg:col-span-6',
7 => 'lg:col-span-7',
8 => 'lg:col-span-8',
9 => 'lg:col-span-9',
10 => 'lg:col-span-10',
11 => 'lg:col-span-11',
default => 'lg:col-span-12',
};

$wrapper = $boxed
? 'w-full max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8'
: 'w-full';

$gridOrderImage = $reverse ? 'lg:order-2' : 'lg:order-1';
$gridOrderText = $reverse ? 'lg:order-1' : 'lg:order-2';

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

$mobileRawImage = $sectionImage?->mobile_image
?: $sectionImage?->image
?: '';

$desktopImageUrl = $resolveImage($desktopRawImage ?: $mobileRawImage);
$mobileImageUrl = $resolveImage($mobileRawImage ?: $desktopRawImage);

$imageAlt = $sectionImage?->image_alt
?: $sectionImage?->mobile_image_alt
?: $titleText
?: 'Section image';

$buttonLabel = $section?->button_label ?: 'DISCOVER';
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
<section class="py-14 md:py-28 w-full {{ $reverse ? '' : 'bg-[#F7F7F7]' }}">
    <div class="{{ $wrapper }}">
        <div class="grid grid-cols-1 lg:grid-cols-12 items-stretch gap-8 lg:gap-10">

            <div class="{{ $imageSpanClass }} {{ $gridOrderImage }}">
                <div class="group relative aspect-square md:aspect-3/2 overflow-hidden bg-slate-100">
                    @if ($desktopImageUrl || $mobileImageUrl)
                    <picture class="block h-full w-full">
                        @if ($mobileImageUrl)
                        <source media="(max-width: 767px)" srcset="{{ $mobileImageUrl }}">
                        @endif

                        <img src="{{ $desktopImageUrl ?: $mobileImageUrl }}" alt="{{ $imageAlt }}" class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" loading="lazy">
                    </picture>
                    @endif
                </div>
            </div>

            <div class="{{ $textSpanClass }} {{ $gridOrderText }}">
                <div class="h-full flex flex-col justify-center px-4 sm:px-8 md:px-10 lg:px-12 md:py-14">
                    <div class="text-center">

                        @if ($titleText !== '')
                        <h2 class="text-2xl sm:text-3xl md:text-3xl leading-snug tracking-[0.15em] md:tracking-[0.20em] uppercase text-slate-800 font-medium">
                            {{ $titleText }}
                        </h2>
                        @endif

                        @if ($subtitleText !== '')
                        <p class="mt-3 text-xs sm:text-sm uppercase tracking-[0.18em] text-slate-500">
                            {{ $subtitleText }}
                        </p>
                        @endif

                        @if ($excerptText !== '')
                        <p class="mt-6 text-[15px] sm:text-base leading-relaxed text-gray-600 max-w-2xl sm:max-w-3xl md:max-w-5xl mx-auto">
                            {{ $excerptText }}
                        </p>
                        @endif

                        @if (! $excerptOnly && $descriptionText !== '')
                        <div class="mt-6 text-[15px] sm:text-base leading-relaxed text-gray-600 max-w-2xl sm:max-w-3xl md:max-w-5xl mx-auto prose prose-slate prose-p:my-0 prose-ul:my-2 prose-ol:my-2">
                            {!! $description !!}
                        </div>
                        @endif

                        @if (! $noButton && $buttonUrl)
                        <div class="mt-8">
                            <a href="{{ $buttonUrl }}" class="inline-flex items-center justify-center bg-[#A67C3D] text-white px-7 py-3 uppercase tracking-[0.22em] text-[12px] font-bold hover:bg-[#8F6B34] transition">
                                {{ $buttonLabel }}
                            </a>
                        </div>
                        @endif

                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
@endif
