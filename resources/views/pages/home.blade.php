@push('meta')
<title>{{ $page->meta_title ?: $page->title }}</title>
<meta name="description" content="{{ $page->meta_description ?? '' }}">

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
@endpush

<x-layouts.app>
    @php
    $leadContainedImage = $sections->firstWhere('section_key', 'contained_image_section');
    $remainingSections = $sections->reject(fn ($section) => in_array($section->id, array_filter([
    $leadContainedImage?->id,
    ]), true));
    @endphp

    <x-heroes.video-hero video-id="8aZOOwSdxwE" />

    <x-sections.page-description :page="$page" />

    @if ($leadContainedImage)
    <x-sections.contained-image-section :section="$leadContainedImage" />
    @endif

    <section class="bg-white px-6 pt-14 text-center md:pt-20">
        <h2 class="text-lg font-medium uppercase text-slate-700 mb-3 sm:text-xl">
            Luxury Jungle Villas & Royal Suites in Ubud, Bali
        </h2>
    </section>

    <x-sections.item-carousel :items="$villas" wrapper-class="pt-8 md:pt-3" bottom-padding-class="pb-8 md:pb-12" button-align-class="justify-start" />

    <x-sections.presidential-suite-feature :accommodation="$presidentialSuite" />

    @if ($experienceCategories->isNotEmpty())
    <section class="bg-white px-6 pt-10 text-center md:pt-14">
        <h2 class="text-lg font-medium uppercase text-slate-700 mb-3 sm:text-xl">
            Experiences Beyond the Stay
        </h2>
    </section>

    <x-sections.item-carousel :items="$experienceCategories" route-name="experiences.category" wrapper-class="pt-8 md:pt-3" bottom-padding-class="pb-8 md:pb-12" button-align-class="justify-start" :show-reserve-button="false" />
    @endif



    @if ($offers->isNotEmpty())
    <section class="bg-white px-6 pt-10 text-center md:pt-14">
        <h2 class="text-lg font-medium uppercase text-slate-700 mb-3 sm:text-xl">
            Exclusive Jungle Escapes
        </h2>
        <p class="mx-auto max-w-4xl text-xs leading-relaxed text-slate-600 mb-3 sm:text-sm">
            Discover exclusive Ubud offers at Nandini Jungle by Hanging Gardens. Book direct for the Best Rate Guarantee and find the perfect package for your Bali escape.
        </p>
    </section>

    <x-sections.item-carousel :items="$offers" route-name="offers.show" wrapper-class="pt-8 md:pt-3" bottom-padding-class="pb-8 md:pb-12" button-align-class="justify-start" :show-reserve-button="false" />
    @endif

    @if ($diningSections->isNotEmpty())
    <section class="bg-white px-6 pt-10 text-center md:pt-14">
        <h2 class="text-lg font-medium uppercase text-slate-700 mb-3 sm:text-xl">
            Culinary Journeys
        </h2>
    </section>

    <x-sections.item-carousel :items="$diningSections" wrapper-class="pt-8 md:pt-3" bottom-padding-class="pb-8 md:pb-12" button-align-class="justify-start" :show-reserve-button="false" />
    @endif

    @if ($spaSections->isNotEmpty())
    <section class="bg-white px-6 pt-10 text-center md:pt-14">
        <h2 class="text-lg font-medium uppercase text-slate-700 mb-3 sm:text-xl">
            Essence Spa
        </h2>
    </section>

    <x-sections.item-carousel :items="$spaSections" wrapper-class="pt-8 md:pt-3" bottom-padding-class="pb-8 md:pb-12" button-align-class="justify-start" :show-reserve-button="false" />
    @endif

    @if ($ubudJungleAdventures->isNotEmpty())
    <section class="bg-white px-6 pt-10 text-center md:pt-14">
        <h2 class="text-lg font-medium uppercase text-slate-700 mb-3 sm:text-xl">
            Bali Adventure Journeys
        </h2>
    </section>

    <x-sections.item-carousel :items="$ubudJungleAdventures" route-name="experiences.show" wrapper-class="pt-8 md:pt-3" bottom-padding-class="pb-8 md:pb-12" button-align-class="justify-start" :show-reserve-button="false" />
    @endif

    @foreach ($remainingSections as $section)
    @if ($section->section_key === 'image_overlay_section')
    <x-sections.image-overlay-section :section="$section" />
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
</x-layouts.app>
