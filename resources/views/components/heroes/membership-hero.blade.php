@props([
'page' => null,
'imageSrc' => '',
'mobileImageSrcManual' => '',
'altText' => 'Membership Hero Image',

'title' => null,
'description' => null,
'subtitle' => null,

'showContent' => false,
'showOverlay' => false,

'primaryLabel' => 'Join Now',
'primaryUrl' => '#',

'secondaryLabel' => 'Sign In',
'secondaryUrl' => '#',
])

@php
$desktopImageSrc = '';
$mobileImageSrc = '';
$alt = $altText;

$showContent = filter_var($showContent, FILTER_VALIDATE_BOOLEAN);
$showOverlay = filter_var($showOverlay, FILTER_VALIDATE_BOOLEAN);

if ($page && ($page->hero_image || $page->hero_mobile_image)) {
$desktopImage = $page->hero_image ?: $page->hero_mobile_image;
$mobileImage = $page->hero_mobile_image ?: $page->hero_image;

$desktopImageSrc = asset('storage/' . $desktopImage);
$mobileImageSrc = asset('storage/' . $mobileImage);

$alt = $page->hero_image_alt
?: $page->hero_mobile_image_alt
?: $page->title
?: $altText;
} else {
$desktopImageSrc = $imageSrc;
$mobileImageSrc = $mobileImageSrcManual ?: $imageSrc;
}

$heroTitle = $title ?: ($page->title ?? 'Nandini Rewards');

$heroDescription = $description
?: ($page->description ?? null)
?: $subtitle
?: ($page->excerpt ?? 'Earn and redeem points that take you everywhere you want to go.');

$heroDescription = is_string($heroDescription) ? trim($heroDescription) : null;

$heroDescriptionHasHtml = $heroDescription
&& $heroDescription !== strip_tags($heroDescription);

$resolveButtonUrl = function ($manualUrl, string $routeName): string {
$manualUrl = trim((string) $manualUrl);

if ($manualUrl !== '' && $manualUrl !== '#') {
return $manualUrl;
}

return \Illuminate\Support\Facades\Route::has($routeName)
? route($routeName)
: '#';
};

$primaryHref = $resolveButtonUrl($primaryUrl, 'membership.register');
$secondaryHref = $resolveButtonUrl($secondaryUrl, 'membership.login');
@endphp

<header class="relative shadow-xl">
    <div class="relative w-full aspect-square lg:aspect-auto lg:h-[70vh] overflow-hidden bg-slate-100">
        @if ($desktopImageSrc || $mobileImageSrc)
        <picture class="block w-full h-full">
            @if ($mobileImageSrc)
            <source media="(max-width: 767px)" srcset="{{ $mobileImageSrc }}">
            @endif

            <img src="{{ $desktopImageSrc ?: $mobileImageSrc }}" alt="{{ $alt }}" class="absolute inset-0 w-full h-full object-cover object-center" width="1920" height="1080" loading="eager" fetchpriority="high" decoding="async" />
        </picture>
        @endif

        @if ($showOverlay)
        <div class="absolute inset-0 bg-black/25"></div>
        <div class="absolute inset-0 bg-linear-to-r from-black/60 via-black/25 to-transparent"></div>
        @endif

        @if ($showContent)
        <div class="absolute inset-0 flex items-center">
            <div class="w-full px-6 pt-20 pb-12 md:px-12 lg:px-[70px] lg:pt-24 lg:pb-16">
                <div class="max-w-3xl text-white">
                    @if ($heroDescription)
                    <div class="mb-4 max-w-xl text-base md:text-lg font-light leading-[1.65] text-white/95 [&_p]:mb-1.5 [&_p:last-child]:mb-0">
                        @if ($heroDescriptionHasHtml)
                        {!! $heroDescription !!}
                        @else
                        {!! nl2br(e($heroDescription)) !!}
                        @endif
                    </div>
                    @endif

                    <h1 class="text-2xl leading-snug uppercase text-white font-medium mb-3">
                        {{ $heroTitle }}
                    </h1>

                    <div class="mt-9 flex flex-wrap gap-5">
                        @if ($primaryLabel)
                        <x-buttons.link-button :href="$primaryHref" variant="solid">
                            {{ $primaryLabel }}
                        </x-buttons.link-button>
                        @endif

                        @if ($secondaryLabel)
                        <x-buttons.link-button :href="$secondaryHref" variant="white-outline">
                            {{ $secondaryLabel }}
                        </x-buttons.link-button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</header>
