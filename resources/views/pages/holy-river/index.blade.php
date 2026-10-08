@push('meta')
@php
    $metaTitle = $page->meta_title ?: $page->title;
    $metaDescription = $page->meta_description ?? '';
    $canonical = 'https://nandinibali.com/holy-river';
    $metaImage = $page->hero_image ?? $page->hero_mobile_image ?? null;
    $metaImageUrl = $metaImage ? asset('storage/'.$metaImage) : null;
    $structuredData = [
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        '@id' => $canonical.'#webpage',
        'url' => $canonical,
        'name' => $metaTitle,
        'description' => $metaDescription,
        'isPartOf' => ['@id' => 'https://nandinibali.com/#website'],
        'about' => ['@id' => 'https://nandinibali.com/#hotel'],
    ];
@endphp

<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
<meta name="author" content="Nandini Jungle by Hanging Gardens">
<link rel="canonical" href="{{ $canonical }}">

<meta property="og:type" content="website">
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:site_name" content="Nandini Jungle by Hanging Gardens">

@if ($metaImageUrl)
<meta property="og:image" content="{{ $metaImageUrl }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta name="twitter:image" content="{{ $metaImageUrl }}">
@endif

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
<script type="application/ld+json">@json($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)</script>
@endpush

<x-layouts.app>
    @php
        $sectionTrackingNames = [
            8 => 'melukat',
            9 => 'ayung_river',
            10 => 'blessing_purification',
            11 => 'holy_river_experiences_intro',
            12 => 'spa_on_river',
        ];
        $bookingUrl = 'https://wa.me/6281236871170?text='.rawurlencode('Hello, I would like to enquire about the Holy River and Balinese purification experiences at Nandini Jungle.');
    @endphp

    <x-heroes.video-hero
        video-id="DQGm1PB0828"
        title="Holy River and Balinese blessing at Nandini Jungle"
        poster-alt="Balinese priest preparing a blessing ritual at Nandini Jungle"
        data-gtm-section="hero"
    />

    <x-sections.video-text-section
        :page="$page"
        video-id="eh5h5P6_3LQ"
        video-alt="Guest speaking about the Holy River experience at Nandini Jungle"
        :show-subtitle="true"
        data-gtm-section="holy_river_intro"
    />

    @foreach ($sections as $section)
        @if ($section->section_key === 'image_overlay_section')
            <x-sections.image-overlay-section :section="$section" :data-gtm-section="$sectionTrackingNames[$section->id] ?? null" />
        @endif

        @if ($section->section_key === 'contained_image_section')
            <x-sections.contained-image-section :section="$section" :data-gtm-section="$sectionTrackingNames[$section->id] ?? null" />
        @endif

        @if ($section->section_key === 'split_media_section')
            <x-sections.split-media-section :section="$section" :excerpt-only="false" image-span="8" text-span="4" :data-gtm-section="$sectionTrackingNames[$section->id] ?? null" />
        @endif

        @if ($section->section_key === 'split_media_reverse')
            <x-sections.split-media-section :section="$section" :reverse="true" :excerpt-only="false" image-span="8" text-span="4" :data-gtm-section="$sectionTrackingNames[$section->id] ?? null" />
        @endif

        @if ($section->section_key === 'intro_text_section')
            <x-sections.intro-text-section
                :section="$section"
                id="holy-river-experiences"
                :data-gtm-section="$sectionTrackingNames[$section->id] ?? null"
            />

            @if ($experiences->isNotEmpty())
                <x-sections.item-carousel
                    :items="$experiences"
                    route-name="holy-river.show"
                    :show-reserve-button="false"
                    action-label="More Details"
                    data-gtm-section="holy_river_experiences"
                />
            @endif
        @endif
    @endforeach

    <section class="relative isolate flex min-h-[420px] items-center overflow-hidden bg-[#142c24] px-6 py-16 text-center font-sans text-white md:min-h-[460px] md:px-12 md:py-20" aria-labelledby="holy-river-booking-title" data-gtm-section="booking_cta">
        <div class="mx-auto w-full max-w-3xl border-y border-white/25 py-10 md:py-12">
            <p class="mb-3 text-[10px] font-medium uppercase tracking-[.18em] text-[#e2cca0] sm:text-xs">HOLY RIVER AT NANDINI</p>
            <h2 id="holy-river-booking-title" class="text-lg leading-snug font-medium text-white uppercase sm:text-xl">PLAN YOUR HOLY RIVER EXPERIENCE</h2>
            <p class="mx-auto mt-5 max-w-2xl text-xs leading-relaxed text-white/85 sm:text-sm">
                Experience Balinese purification, traditional blessings and the peaceful setting of the Ayung River at Nandini Jungle.
            </p>
            <div class="mt-8 flex flex-col items-center justify-center gap-4 sm:flex-row">
                <x-buttons.link-button href="#holy-river-experiences" variant="white-outline">EXPLORE HOLY RIVER EXPERIENCES</x-buttons.link-button>
                <x-buttons.link-button :href="$bookingUrl" variant="solid">ENQUIRE / RESERVE</x-buttons.link-button>
            </div>
        </div>
    </section>
</x-layouts.app>
