@php
$heroPosterMobile = asset('images/hero/nandini-video-poster-mobile-960.webp');
$heroPosterMobileSrcset = asset('images/hero/nandini-video-poster-mobile-640.webp') . ' 640w, ' . $heroPosterMobile . ' 960w';
$heroPosterMobileAvifSrcset = asset('images/hero/nandini-video-poster-mobile-640.avif') . ' 640w, ' . asset('images/hero/nandini-video-poster-mobile-960.avif') . ' 960w';
$heroPosterDesktop = asset('images/hero/nandini-video-poster-desktop-1280.webp');
$heroPosterDesktopAvif = asset('images/hero/nandini-video-poster-desktop-1280.avif');
@endphp

@push('meta')
<title>{{ $page->meta_title ?: $page->title }}</title>
<meta name="description" content="{{ $page->meta_description ?? '' }}">

<link rel="preload" as="image" type="image/avif" href="{{ asset('images/hero/nandini-video-poster-mobile-960.avif') }}" imagesrcset="{{ $heroPosterMobileAvifSrcset }}" imagesizes="100vw" media="(max-width: 1023px)" fetchpriority="high">
<link rel="preload" as="image" type="image/avif" href="{{ $heroPosterDesktopAvif }}" media="(min-width: 1024px)" fetchpriority="high">

@if (! empty($page->meta_keywords))
<meta name="keywords" content="{{ $page->meta_keywords }}">
@endif

<meta name="author" content="Nandini Jungle by Hanging Gardens">
<link rel="canonical" href="{{ url()->current() }}">

<meta property="og:type" content="website">
<meta property="og:title" content="{{ $page->meta_title ?: $page->title }}">
<meta property="og:description" content="{{ $page->meta_description ?? '' }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:site_name" content="Nandini Jungle by Hanging Gardens">

@if (! empty($page->hero_image))
<meta property="og:image" content="{{ asset('storage/' . $page->hero_image) }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta name="twitter:image" content="{{ asset('storage/' . $page->hero_image) }}">
@endif

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $page->meta_title ?: $page->title }}">
<meta name="twitter:description" content="{{ $page->meta_description ?? '' }}">

<script type="application/ld+json">{!! json_encode(
    \App\Support\HomepageStructuredData::make($page),
    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR
) !!}</script>
@endpush

<x-layouts.app>
    @php
    $leadContainedImage = $sections->firstWhere('section_key', 'contained_image_section');
    $remainingSections = $sections->reject(fn ($section) => in_array($section->id, array_filter([
    $leadContainedImage?->id,
    ]), true));
    @endphp

    <x-heroes.video-hero
        video-id="8aZOOwSdxwE"
        :poster-mobile="$heroPosterMobile"
        :poster-mobile-srcset="$heroPosterMobileSrcset"
        :poster-mobile-avif-srcset="$heroPosterMobileAvifSrcset"
        :poster-desktop="$heroPosterDesktop"
        :poster-desktop-avif="$heroPosterDesktopAvif"
        data-gtm-section="hero"
    />

    <x-sections.page-description :page="$page" :show-awards="true" :show-subtitle="true" data-gtm-section="introduction" />

    @if ($leadContainedImage)
    <x-sections.contained-image-section :section="$leadContainedImage" />
    @endif

    <section class="bg-white px-6 pt-14 text-center md:pt-20" data-gtm-section="villas">
        <h2 class="text-lg font-medium uppercase text-slate-700 mb-3 sm:text-xl">
            {{ $homepageSettings->villas_heading }}
        </h2>
        <p class="mx-auto max-w-4xl text-xs leading-relaxed text-slate-600 mb-3 sm:text-sm">
            {{ $homepageSettings->villas_description }}
        </p>
    </section>

    <x-sections.item-carousel :items="$villas" wrapper-class="pt-8 md:pt-3" bottom-padding-class="pb-8 md:pb-12" button-align-class="justify-start" :action-label="$homepageSettings->villas_action_label" :mobile-arrows-on-image="true" />

    <x-sections.presidential-suite-feature :accommodation="$presidentialSuite" :action-label="$homepageSettings->featured_suite_action_label" data-gtm-section="featured_suite" />

    @if ($experienceCategories->isNotEmpty())
    <section class="bg-white px-6 pt-10 text-center md:pt-14" data-gtm-section="experiences">
        <h2 class="text-lg font-medium uppercase text-slate-700 mb-3 sm:text-xl">
            {{ $homepageSettings->experiences_heading }}
        </h2>
        <p class="mx-auto max-w-4xl text-xs leading-relaxed text-slate-600 mb-3 sm:text-sm">
            {{ $homepageSettings->experiences_description }}
        </p>
    </section>

    <x-sections.item-carousel :items="$experienceCategories" route-name="experiences.category" wrapper-class="pt-8 md:pt-3" bottom-padding-class="pb-8 md:pb-12" button-align-class="justify-start" :action-label="$homepageSettings->experiences_action_label" :footer-label="$homepageSettings->experiences_footer_label" :footer-href="route('experiences.index')" :show-reserve-button="false" :mobile-arrows-on-image="true" />
    @endif



    @if ($offers->isNotEmpty())
    <section class="bg-white px-6 pt-10 text-center md:pt-14" data-gtm-section="offers">
        <h2 class="text-lg font-medium uppercase text-slate-700 mb-3 sm:text-xl">
            {{ $homepageSettings->offers_heading }}
        </h2>
        <p class="mx-auto max-w-4xl text-xs leading-relaxed text-slate-600 mb-3 sm:text-sm">
            {{ $homepageSettings->offers_description }}
        </p>
    </section>

    <x-sections.item-carousel :items="$offers" route-name="offers.show" wrapper-class="pt-8 md:pt-3" bottom-padding-class="pb-8 md:pb-12" button-align-class="justify-start" :action-label="$homepageSettings->offers_action_label" :footer-label="$homepageSettings->offers_footer_label" :footer-href="route('offers.index')" :mobile-arrows-on-image="true" />
    @endif

    @if ($diningSections->isNotEmpty())
    <section class="bg-white px-6 pt-10 text-center md:pt-14" data-gtm-section="dining">
        <h2 class="text-lg font-medium uppercase text-slate-700 mb-3 sm:text-xl">
            {{ $homepageSettings->dining_heading }}
        </h2>
        <p class="mx-auto max-w-4xl text-xs leading-relaxed text-slate-600 mb-3 sm:text-sm">
            {{ $homepageSettings->dining_description }}
        </p>
    </section>

    <x-sections.item-carousel :items="$diningSections" wrapper-class="pt-8 md:pt-3" bottom-padding-class="pb-8 md:pb-12" button-align-class="justify-start" :action-label="$homepageSettings->dining_action_label" :show-reserve-button="false" :mobile-arrows-on-image="true" />
    @endif

    @if ($spaSections->isNotEmpty())
    <section class="bg-white px-6 pt-10 text-center md:pt-14" data-gtm-section="spa_wellness">
        <h2 class="text-lg font-medium uppercase text-slate-700 mb-3 sm:text-xl">
            {{ $homepageSettings->spa_heading }}
        </h2>
        <p class="mx-auto max-w-4xl text-xs leading-relaxed text-slate-600 mb-3 sm:text-sm">
            {{ $homepageSettings->spa_description }}
        </p>
    </section>

    <x-sections.item-carousel :items="$spaSections" wrapper-class="pt-8 md:pt-3" bottom-padding-class="pb-8 md:pb-12" button-align-class="justify-start" :action-label="$homepageSettings->spa_action_label" :show-reserve-button="false" :mobile-arrows-on-image="true" />
    @endif

    @if ($ubudJungleAdventures->isNotEmpty())
    <section class="bg-white px-6 pt-10 text-center md:pt-14" data-gtm-section="activities">
        <h2 class="text-lg font-medium uppercase text-slate-700 mb-3 sm:text-xl">
            {{ $homepageSettings->adventures_heading }}
        </h2>
        <p class="mx-auto max-w-4xl text-xs leading-relaxed text-slate-600 mb-3 sm:text-sm">
            {{ $homepageSettings->adventures_description }}
        </p>
    </section>

    <x-sections.item-carousel :items="$ubudJungleAdventures" route-name="experiences.show" wrapper-class="pt-8 md:pt-3" bottom-padding-class="pb-8 md:pb-12" button-align-class="justify-start" :action-label="$homepageSettings->adventures_action_label" :footer-label="$homepageSettings->adventures_footer_label" :footer-href="route('experiences.category', 'ubud-jungle-adventures')" :show-reserve-button="false" :mobile-arrows-on-image="true" />
    @endif

    @foreach ($remainingSections as $section)
    @if ($section->section_key === 'image_overlay_section')
    <x-sections.image-overlay-section :section="$section" data-gtm-section="riverside_wellness" />
    @endif

    @if ($section->section_key === 'split_media_section')
    <x-sections.split-media-section :section="$section" :excerpt-only="false" image-span="8" text-span="4" />
    @endif

    @if ($section->section_key === 'split_media_reverse')
    <x-sections.split-media-section :section="$section" :reverse="true" :excerpt-only="false" image-span="8" text-span="4" />
    @endif

    @if ($section->section_key === 'three_images_section')
    <x-sections.three-images-section :section="$section" />
    @endif

    @if ($section->section_key === 'two_images_section')
    <x-sections.two-images-section :section="$section" />
    @endif

    @if ($section->section_key === 'two_images_reverse')
    <x-sections.two-images-section :section="$section" :reverse="true" />
    @endif
    @endforeach

    <x-sections.guest-reviews :reviews="$guestReviews" :heading="$homepageSettings->guest_reviews_heading" :see-more-label="$homepageSettings->guest_reviews_action_label" :see-more-href="route('guest-reviews.index')" data-gtm-section="guest_reviews" />
</x-layouts.app>
