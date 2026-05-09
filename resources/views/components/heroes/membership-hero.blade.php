@props([
'page' => null,
'imageSrc' => '',
'mobileImageSrcManual' => '',
'altText' => 'Membership Hero Image',

'title' => null,
'description' => null,
'subtitle' => null,

'primaryLabel' => 'Join Now',
'primaryUrl' => '#',

'secondaryLabel' => 'Sign In',
'secondaryUrl' => '#',
])

@php
$desktopImageSrc = '';
$mobileImageSrc = '';
$alt = $altText;

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

$heroDescription = trim(strip_tags($heroDescription));
@endphp

<header class="relative shadow-xl">
    <div class="relative w-full aspect-square lg:aspect-auto lg:h-[70vh] overflow-hidden bg-slate-100">
        @if ($desktopImageSrc || $mobileImageSrc)
        <picture class="block w-full h-full">
            @if ($mobileImageSrc)
            <source media="(max-width: 767px)" srcset="{{ $mobileImageSrc }}">
            @endif

            <img src="{{ $desktopImageSrc ?: $mobileImageSrc }}" alt="{{ $alt }}" class="absolute inset-0 w-full h-full object-cover object-center" loading="lazy" />
        </picture>
        @endif

        {{-- Overlay --}}
        <div class="absolute inset-0 bg-black/25"></div>
        <div class="absolute inset-0 bg-linear-to-r from-black/55 via-black/20 to-transparent"></div>

        {{-- Content --}}
        <div class="absolute inset-0 flex items-center">
            <div class="w-full px-6 md:px-12 lg:px-[70px]">
                <div class="max-w-2xl text-white">
                    <h1 class="text-[32px] sm:text-[38px] md:text-[44px] uppercase tracking-[0.18em] leading-tight">
                        {{ $heroTitle }}
                    </h1>

                    @if ($heroDescription)
                    <p class="mt-2 max-w-xl text-sm md:text-base font-light leading-relaxed text-white/95">
                        {{ $heroDescription }}
                    </p>
                    @endif

                    <div class="mt-10 flex flex-wrap gap-5">
                        @if ($primaryLabel)
                        <x-buttons.link-button :href="$primaryUrl" variant="solid">
                            {{ $primaryLabel }}
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
    </div>
</header>