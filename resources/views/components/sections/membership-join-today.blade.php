@props([
'section' => null,
'title' => 'Not A Member Yet? Join Today',
'description' => 'Earn and redeem points that take you everywhere you want to go.',
'image' => '',
'mobileImage' => '',
'altText' => 'Nandini Rewards Membership',

'primaryLabel' => 'Join Now',
'primaryUrl' => '#',

'secondaryLabel' => 'Sign In',
'secondaryUrl' => '#',

'height' => 'h-[560px] md:h-[650px] lg:h-[70vh]',
'darkOverlay' => true,
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

return asset($raw);
};

$sectionImage = $section?->images?->first();

$desktopRawImage = $sectionImage?->image ?: $image;

$mobileRawImage = $sectionImage?->mobile_image
?: $sectionImage?->image
?: $mobileImage
?: $image;

$resolvedDesktopImage = $resolveImage($desktopRawImage ?: $mobileRawImage);
$resolvedMobileImage = $resolveImage($mobileRawImage ?: $desktopRawImage);

$resolvedTitle = $section?->title ?: $title;

$resolvedDescription = $section?->excerpt
?: $section?->description
?: $description;

$resolvedDescription = trim(strip_tags($resolvedDescription));

$resolvedAlt = $sectionImage?->image_alt
?: $sectionImage?->mobile_image_alt
?: $resolvedTitle
?: $altText;

$sectionButtonLabel = $section?->button_label ?? null;
$sectionButtonUrl = null;

if (($section?->button_link_type ?? 'manual') === 'route' && $section?->button_route) {
$sectionButtonUrl = \Illuminate\Support\Facades\Route::has($section->button_route)
? route($section->button_route)
: null;
} else {
$sectionButtonUrl = $section?->button_url ?? null;
}

$finalPrimaryLabel = $sectionButtonLabel ?: $primaryLabel;
$finalPrimaryUrl = $sectionButtonUrl ?: $primaryUrl;
@endphp

<section class="w-full bg-white px-0 py-0">
    <div class="relative w-full overflow-hidden bg-neutral-100">
        @if ($resolvedDesktopImage || $resolvedMobileImage)
        <picture class="absolute inset-0 h-full w-full overflow-hidden">
            @if ($resolvedMobileImage)
            <source media="(max-width: 767px)" srcset="{{ $resolvedMobileImage }}">
            @endif

            <img src="{{ $resolvedDesktopImage ?: $resolvedMobileImage }}" alt="{{ $resolvedAlt }}" class="absolute inset-0 h-full w-full object-cover object-center" loading="lazy">
        </picture>
        @endif

        <div class="relative {{ $height }}"></div>

        @if ($darkOverlay)
        <div class="absolute inset-0 bg-black/25"></div>
        <div class="absolute inset-0 bg-linear-to-r from-black/55 via-black/20 to-transparent"></div>
        @endif

        <div class="absolute inset-0 flex items-center justify-start px-6 md:px-12 lg:px-[90px]">
            <div class="max-w-3xl text-left text-white">
                @if ($resolvedTitle)
                <h2 class="text-2xl sm:text-3xl md:text-3xl font-medium uppercase leading-snug tracking-[0.15em] md:tracking-[0.22em] text-white">
                    {!! str_ireplace(
                    ['&lt;br&gt;', '&lt;br/&gt;', '&lt;br /&gt;', "\n"],
                    '<br>',
                    e($resolvedTitle)
                    ) !!}
                </h2>
                @endif

                @if ($resolvedDescription)
                <p class="mt-3 max-w-2xl text-[15px] leading-relaxed text-white/90 sm:text-base">
                    {{ $resolvedDescription }}
                </p>
                @endif

                <div class="mt-8 flex flex-wrap gap-4">
                    @if ($finalPrimaryLabel)
                    <x-buttons.link-button :href="$finalPrimaryUrl" variant="solid">
                        {{ $finalPrimaryLabel }}
                    </x-buttons.link-button>
                    @endif

                    @if ($secondaryLabel)
                    <x-buttons.link-button :href="$secondaryUrl" variant="white-outline">
                        {{ $secondaryLabel }}
                    </x-buttons.link-button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
