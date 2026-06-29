@props([
'section' => null,
'page' => null,
'reverse' => false,
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

$title = $cleanText($section?->title ?? '');
$subtitle = $cleanText($section?->subtitle ?? '');
$description = (string) ($section?->description ?? '');
$excerpt = (string) ($section?->excerpt ?? '');
$body = trim($description) !== '' ? $description : $excerpt;

$sectionImage = $section?->images?->first();
$desktopRawImage = $sectionImage?->image ?? '';
$mobileRawImage = $sectionImage?->mobile_image ?: $sectionImage?->image ?: '';

if ($desktopRawImage === '' && $mobileRawImage === '') {
    $desktopRawImage = $section?->image ?: '';
    $mobileRawImage = $section?->mobile_image ?: $section?->image ?: '';
}

if ($desktopRawImage === '' && $mobileRawImage === '') {
    $fallbackPage = $page ?: $section?->page;
    $desktopRawImage = $fallbackPage?->hero_image ?: '';
    $mobileRawImage = $fallbackPage?->hero_mobile_image ?: $fallbackPage?->hero_image ?: '';
}

$desktopImageUrl = $resolveImage($desktopRawImage ?: $mobileRawImage);
$mobileImageUrl = $resolveImage($mobileRawImage ?: $desktopRawImage);
$imageAlt = $sectionImage?->image_alt
    ?: $sectionImage?->mobile_image_alt
    ?: ($page ?: $section?->page)?->hero_image_alt
    ?: ($page ?: $section?->page)?->hero_mobile_image_alt
    ?: $title
    ?: 'Section image';

$textAlign = $section?->text_align ?: 'left';
$textAlignClass = match ($textAlign) {
'center' => 'text-center',
'right' => 'text-right',
default => 'text-left',
};

$listAlignClass = match ($textAlign) {
'center' => '[&_ul]:inline-block [&_ol]:inline-block [&_ul]:text-left [&_ol]:text-left',
'right' => '[&_ul]:inline-block [&_ol]:inline-block [&_ul]:text-left [&_ol]:text-left',
default => '',
};

$backgroundColor = $section?->background_color ?: 'soft_gray';
$backgroundClass = match ($backgroundColor) {
'warm_ivory' => 'bg-[#fbf8f1]',
'light_gold' => 'bg-[#f6efe2]',
'dark_navy' => 'bg-[#071a33]',
'white' => 'bg-white',
default => 'bg-slate-50',
};

$titleColorClass = $backgroundColor === 'dark_navy' ? 'text-white' : 'text-slate-700';
$bodyColorClass = $backgroundColor === 'dark_navy' ? 'text-white/85' : 'text-slate-700';
$buttonClass = $backgroundColor === 'dark_navy'
? 'border-white text-white hover:bg-white hover:text-slate-900'
: 'border-slate-700 text-slate-700 hover:bg-[#A88444] hover:border-[#A88444] hover:text-white';

$imageOrderClass = $reverse ? 'lg:order-2' : 'lg:order-1';
$textOrderClass = $reverse ? 'lg:order-1' : 'lg:order-2';

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
<section class="{{ $backgroundClass }} px-6 py-14 md:py-20">
    <div class="mx-auto grid max-w-7xl grid-cols-1 items-stretch gap-8 lg:grid-cols-12 lg:gap-10">
        @if ($desktopImageUrl || $mobileImageUrl)
        <div class="{{ $imageOrderClass }} relative self-stretch overflow-hidden bg-slate-100 lg:col-span-7 xl:col-span-7">
            <picture class="block lg:hidden">
                @if ($mobileImageUrl)
                <source media="(max-width: 767px)" srcset="{{ $mobileImageUrl }}">
                @endif

                <img src="{{ $mobileImageUrl ?: $desktopImageUrl }}" alt="{{ $imageAlt }}" class="block aspect-[4/3] h-auto w-full object-cover" loading="lazy">
            </picture>

            <picture class="absolute inset-0 hidden h-full w-full lg:block">
                <img src="{{ $desktopImageUrl ?: $mobileImageUrl }}" alt="{{ $imageAlt }}" class="h-full w-full object-cover" loading="lazy">
            </picture>
        </div>
        @endif

        <div class="{{ $textOrderClass }} {{ ($desktopImageUrl || $mobileImageUrl) ? 'lg:col-span-5 xl:col-span-5' : 'lg:col-span-8 lg:col-start-3' }}">
            <div class="flex h-full flex-col justify-center {{ $textAlignClass }} {{ $bodyColorClass }}">
                @if ($title !== '')
                <h2 class="text-xl font-normal uppercase leading-snug {{ $titleColorClass }}">
                    {{ $title }}
                </h2>
                @endif

                @if ($subtitle !== '')
                <p class="mt-5 text-xs font-semibold leading-relaxed uppercase text-[#A88444] sm:text-sm">
                    {{ $subtitle }}
                </p>
                @endif

                @if ($body !== '')
                <div class="mt-8 text-xs leading-relaxed sm:text-sm [&_a]:underline [&_h2]:mb-3 [&_h2]:text-md [&_h2]:font-medium [&_h2]:leading-snug [&_h2]:text-slate-700 [&_p]:text-slate-700 [&_h3]:mb-1 [&_h3]:uppercase [&_h3]:mt-6 [&_h3]:text-base [&_h3]:font-semibold [&_li]:leading-6 [&_ol]:mb-5 [&_ol]:list-decimal [&_ol]:pl-5 [&_strong]:font-semibold [&_ul]:mb-5 [&_ul]:list-disc [&_ul]:pl-5 sm:[&_h3]:text-lg {{ $listAlignClass }}">
                    {!! $body !!}
                </div>
                @endif

                @if ($buttonLabel && $buttonUrl)
                <div class="mt-8">
                    <a href="{{ $buttonUrl }}" class="inline-flex items-center justify-center border px-6 py-3 text-xs font-semibold uppercase transition {{ $buttonClass }} sm:text-sm">
                        {{ $buttonLabel }}
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endif
