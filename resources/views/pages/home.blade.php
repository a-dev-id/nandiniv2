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

    <x-sections.page-description :page="$page" :show-awards="true" />

    @if ($leadContainedImage)
    <x-sections.contained-image-section :section="$leadContainedImage" />
    @endif

    <section class="bg-white px-6 pt-14 text-center md:pt-20">
        <h2 class="text-lg font-medium uppercase text-slate-700 mb-3 sm:text-xl">
            Private Jungle Villas & Royal Suites
        </h2>
        <p class="mx-auto max-w-4xl text-xs leading-relaxed text-slate-600 mb-3 sm:text-sm">
            Stay in private jungle villas in Ubud and spacious royal suites surrounded by rainforest, tropical greenery, and the calm atmosphere of Bali. Each accommodation at Nandini Jungle by Hanging Gardens is designed for comfort, privacy, and warm Balinese character, creating a refined jungle retreat for couples, families, and guests seeking space to reconnect with nature.
        </p>
    </section>

    <x-sections.item-carousel :items="$villas" wrapper-class="pt-8 md:pt-3" bottom-padding-class="pb-8 md:pb-12" button-align-class="justify-start" action-label="More Details" :mobile-arrows-on-image="true" />

    <x-sections.presidential-suite-feature :accommodation="$presidentialSuite" />

    @if ($experienceCategories->isNotEmpty())
    <section class="bg-white px-6 pt-10 text-center md:pt-14">
        <h2 class="text-lg font-medium uppercase text-slate-700 mb-3 sm:text-xl">
            Experiences Beyond the Stay
        </h2>
        <p class="mx-auto max-w-4xl text-xs leading-relaxed text-slate-600 mb-3 sm:text-sm">
            Enhance your stay at Nandini Jungle by Hanging Gardens with curated experiences in the heart of Bali's rainforest. From romantic dining and jungle celebrations to floating breakfast and calming spa rituals, each experience is designed to enrich your Ubud retreat with nature, romance, and authentic Balinese hospitality.
        </p>
    </section>

    <x-sections.item-carousel :items="$experienceCategories" route-name="experiences.category" wrapper-class="pt-8 md:pt-3" bottom-padding-class="pb-8 md:pb-12" button-align-class="justify-start" action-label="Explore More" :footer-href="route('experiences.index')" :show-reserve-button="false" :mobile-arrows-on-image="true" />
    @endif



    @if ($offers->isNotEmpty())
    <section class="bg-white px-6 pt-10 text-center md:pt-14">
        <h2 class="text-lg font-medium uppercase text-slate-700 mb-3 sm:text-xl">
            Curated Escapes at Nandini Jungle
        </h2>
        <p class="mx-auto max-w-4xl text-xs leading-relaxed text-slate-600 mb-3 sm:text-sm">
            Discover exclusive offers at Nandini Jungle by Hanging Gardens, created for guests who want more than a short getaway. From extended stays to twin-island escapes, each package combines nature, comfort, and meaningful experiences for a memorable Bali jungle resort stay.
        </p>
    </section>

    <x-sections.item-carousel :items="$offers" route-name="offers.show" wrapper-class="pt-8 md:pt-3" bottom-padding-class="pb-8 md:pb-12" button-align-class="justify-start" action-label="More Details" :footer-href="route('offers.index')" :mobile-arrows-on-image="true" />
    @endif

    @if ($diningSections->isNotEmpty())
    <section class="bg-white px-6 pt-10 text-center md:pt-14">
        <h2 class="text-lg font-medium uppercase text-slate-700 mb-3 sm:text-xl">
            Culinary Journeys
        </h2>
        <p class="mx-auto max-w-4xl text-xs leading-relaxed text-slate-600 mb-3 sm:text-sm">
            Dine surrounded by rainforest views and warm Balinese hospitality at Nandini Jungle by Hanging Gardens. From authentic flavors to afternoon tea and relaxed drinks, every culinary experience is crafted to complement your stay at a peaceful jungle resort in Ubud.
        </p>
    </section>

    <x-sections.item-carousel :items="$diningSections" wrapper-class="pt-8 md:pt-3" bottom-padding-class="pb-8 md:pb-12" button-align-class="justify-start" action-label="Explore More" :show-reserve-button="false" :mobile-arrows-on-image="true" />
    @endif

    @if ($spaSections->isNotEmpty())
    <section class="bg-white px-6 pt-10 text-center md:pt-14">
        <h2 class="text-lg font-medium uppercase text-slate-700 mb-3 sm:text-xl">
            Jungle Spa & Wellness
        </h2>
        <p class="mx-auto max-w-4xl text-xs leading-relaxed text-slate-600 mb-3 sm:text-sm">
            Restore body, mind, and spirit at Essence Spa, where each treatment is inspired by the natural surroundings of Nandini Jungle by Hanging Gardens. Set within a quiet rainforest environment, our spa experiences combine nature, relaxation, and Balinese wellness traditions for a calming wellness retreat in Ubud.
        </p>
    </section>

    <x-sections.item-carousel :items="$spaSections" wrapper-class="pt-8 md:pt-3" bottom-padding-class="pb-8 md:pb-12" button-align-class="justify-start" action-label="Explore More" :show-reserve-button="false" :mobile-arrows-on-image="true" />
    @endif

    @if ($ubudJungleAdventures->isNotEmpty())
    <section class="bg-white px-6 pt-10 text-center md:pt-14">
        <h2 class="text-lg font-medium uppercase text-slate-700 mb-3 sm:text-xl">
            Bali Adventure Journeys
        </h2>
        <p class="mx-auto max-w-4xl text-xs leading-relaxed text-slate-600 mb-3 sm:text-sm">
            Discover Bali's natural landscapes beyond Nandini Jungle by Hanging Gardens. From Ayung River rafting and ATV adventures to Mount Batur sunrise tours, each journey is designed for guests who want to explore more of the island while returning to the calm of their jungle retreat in Ubud.
        </p>
    </section>

    <x-sections.item-carousel :items="$ubudJungleAdventures" route-name="experiences.show" wrapper-class="pt-8 md:pt-3" bottom-padding-class="pb-8 md:pb-12" button-align-class="justify-start" action-label="More Details" :footer-href="route('experiences.category', 'ubud-jungle-adventures')" :show-reserve-button="false" :mobile-arrows-on-image="true" />
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

    <x-sections.guest-reviews :reviews="$guestReviews" :see-more-href="route('guest-reviews.index')" />
</x-layouts.app>
