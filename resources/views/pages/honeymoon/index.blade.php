@php
$metaTitle = $page->meta_title ?: $page->title;
$metaDescription = $page->meta_description ?? '';
$metaImage = $page->hero_image ?? $page->hero_mobile_image ?? null;
$canonicalUrl = 'https://nandinibali.com/honeymoon';
$content = $sections->keyBy('section_key');
$hero = $content->get('honeymoon_hero');
$intro = $content->get('honeymoon_intro');
$featuresSection = $content->get('honeymoon_features');
$accommodationsSection = $content->get('honeymoon_accommodations');
$packageSection = $content->get('honeymoon_package');
$diningSection = $content->get('honeymoon_dining');
$spaSection = $content->get('honeymoon_spa');
$itinerarySection = $content->get('honeymoon_itinerary');
$celebrationsSection = $content->get('honeymoon_celebrations');
$faqSection = $content->get('honeymoon_faq');
$finalCtaSection = $content->get('honeymoon_final_cta');

$imageUrl = function (?string $path): ?string {
    if (blank($path)) {
        return null;
    }

    return str_starts_with($path, 'http://') || str_starts_with($path, 'https://')
        ? $path
        : Storage::disk('public')->url($path);
};
$sectionImage = fn ($section, ?string $fallback = null, string $fallbackAlt = ''): array => [
    'url' => $imageUrl($section?->images?->first()?->image ?: $fallback),
    'alt' => $section?->images?->first()?->image_alt ?: $fallbackAlt ?: $section?->title ?: '',
];
$sectionLink = function ($section, string $fallback = '#'): string {
    if ($section?->button_link_type === 'route' && filled($section?->button_route) && Route::has($section->button_route)) {
        return route($section->button_route);
    }

    return $section?->button_url ?: $fallback;
};
$bookingUrl = 'https://nandinijunglebyhanginggardens.reserve-online.net/?nights=3&bkcode=romance';
$packageUrl = $featuredHoneymoon
    ? route('honeymoon.show', ['slug' => $featuredHoneymoon->slug])
    : route('honeymoon.index');

$features = $featuresSection ? ($featuresSection->items ?? []) : [
    ['icon' => 'home', 'title' => 'Rainforest Setting', 'description' => 'Overlooking the Ayung River valley.'],
    ['icon' => 'diamond', 'title' => 'Private Villas & Suites', 'description' => 'Designed for privacy, comfort and special moments.'],
    ['icon' => 'sparkles', 'title' => 'Couples Spa & Wellness', 'description' => 'Relaxing treatments in the jungle.'],
    ['icon' => 'heart', 'title' => 'Romantic Dining', 'description' => 'Intimate dining experiences for two.'],
    ['icon' => 'leaf', 'title' => 'Unique Experiences', 'description' => 'Holy River, culture and nature activities.'],
    ['icon' => 'star', 'title' => 'Perfect for Occasions', 'description' => 'Honeymoons, anniversaries and proposals.'],
];
$accommodationItems = $accommodationsSection ? ($accommodationsSection->items ?? []) : $accommodations->map(fn ($accommodation) => [
    'title' => $accommodation->title,
    'description' => $accommodation->slug === 'panoramic-jungle-view-villa'
        ? 'The Panoramic Jungle View Villa is ideal for couples who want a deeper sense of privacy and connection with nature. Its elevated setting and wide jungle views create a peaceful atmosphere for honeymoon mornings, quiet afternoons and relaxed evenings together.'
        : $accommodation->excerpt,
    'image' => $accommodation->card_image,
    'image_alt' => $accommodation->card_image_alt,
    'url' => $accommodation->show_url,
    'link_label' => 'View Details',
])->all();
$itinerary = $itinerarySection ? ($itinerarySection->items ?? []) : [
    ['label' => 'Day 1', 'title' => 'Arrive & Slow Down', 'description' => 'Check in, settle into your villa or suite and enjoy a relaxed evening together.', 'image' => 'accommodations/cards/panoramic-jungle-view-villa-private-balcony-ubud-bali.webp', 'image_alt' => 'Jungle villa in Ubud for honeymoon couples'],
    ['label' => 'Day 2', 'title' => 'Spa & Romantic Dining', 'description' => 'Enjoy a couples wellness experience, followed by an intimate dinner.', 'image' => 'experience-categories/0f670856-8d39-49a1-97ff-96a88f13e2e2.webp', 'image_alt' => 'Spa and wellness experience at Nandini Jungle'],
    ['label' => 'Day 3', 'title' => 'Experience Bali Together', 'description' => 'Explore a Holy River experience, village activity or another curated experience.', 'image' => 'pages/sections/16cc904d-d6b3-4050-959d-82884d7d4268.webp', 'image_alt' => 'Riverside wellness experience at Nandini Jungle'],
    ['label' => 'Day 4', 'title' => 'A Slow Morning', 'description' => 'Enjoy breakfast and your final morning surrounded by the rainforest before departure.', 'image' => 'offers/cards/jungle-hideaway-dining-nandini-bali-2.webp', 'image_alt' => 'Romantic jungle dining at Nandini Jungle'],
];
$faqs = $usesHoneymoonSections
    ? ($faqSection?->items ?? [])
    : [
    ['question' => 'Is Nandini Jungle by Hanging Gardens suitable for a honeymoon in Bali?', 'answer' => 'Yes. Nandini offers a secluded rainforest setting, private villas and Royal Suites, spa and wellness experiences, romantic dining and curated activities for couples.'],
    ['question' => 'Where is Nandini located in relation to Ubud?', 'answer' => 'Nandini Jungle by Hanging Gardens is in Banjar Susut, Desa Buahan, Payangan, within the greater Ubud area of Bali.'],
    ['question' => 'Which villa or suite is best for honeymoon couples?', 'answer' => 'Couples can consider the Panoramic Jungle View Villa, Private Garden Royal Suite or Panoramic Corner Jacuzzi Royal Suite depending on their preferred level of space, privacy and atmosphere.'],
    ['question' => 'Does Nandini offer a honeymoon package?', 'answer' => 'Yes. The honeymoon page features a 4 Days / 3 Nights Honeymoon Package. Visit the package detail page for the latest inclusions and booking information.'],
    ['question' => 'Can couples arrange romantic dining?', 'answer' => 'Yes. Nandini offers romantic and private dining experiences for couples, including experiences suited to honeymoons, anniversaries and proposals.'],
    ['question' => 'Does Nandini offer couples spa experiences?', 'answer' => 'Nandini offers spa and wellness experiences in its rainforest setting, including experiences suitable for couples seeking time to relax together.'],
    ['question' => 'Can Nandini help with proposals or anniversary celebrations?', 'answer' => 'Romantic and private dining experiences are available by arrangement. Contact the Nandini team to discuss the preferred occasion and date.'],
    ['question' => 'What activities can honeymoon couples experience in Ubud?', 'answer' => "Couples can explore Nandini's curated experiences, including romantic dining, wellness, Holy River experiences and other nature and cultural activities."],
    ['question' => 'How many nights should couples stay for a honeymoon at Nandini?', 'answer' => "The ideal stay depends on your plans. Nandini's featured honeymoon package is designed around a 4 Days / 3 Nights stay."],
    ['question' => 'How can we reserve our honeymoon stay?', 'answer' => "You can reserve directly through Nandini's official booking engine or contact the reservations team for assistance."],
    ];
$introImage = $sectionImage($intro, 'pages/sections/422be0cf-6a86-4d31-b124-a13e1c02880a.webp', 'Honeymoon experience at Nandini Jungle by Hanging Gardens in Ubud, Bali');
$packageImage = $sectionImage($packageSection, 'pages/sections/422be0cf-6a86-4d31-b124-a13e1c02880a.webp', '4 Days 3 Nights honeymoon package at Nandini Jungle by Hanging Gardens');
$diningImage = $sectionImage($diningSection, 'experience-categories/c5e0deb3-cd14-4488-ba06-31efb046d0fd.webp', 'Romantic jungle dining experience for couples at Nandini Jungle by Hanging Gardens');
$spaImage = $sectionImage($spaSection, 'experience-categories/0f670856-8d39-49a1-97ff-96a88f13e2e2.webp', 'Couples spa and wellness experience at Nandini Jungle by Hanging Gardens');
$celebrationsImage = $sectionImage($celebrationsSection, 'offers/cards/jungle-hideaway-dining-nandini-bali-2.webp', 'Romantic proposal and anniversary celebration at Nandini Jungle by Hanging Gardens');
$finalCtaImage = $sectionImage($finalCtaSection, $page->hero_image ?: $page->hero_mobile_image, 'Romantic honeymoon escape at Nandini Jungle by Hanging Gardens in Ubud');
$packageActions = $packageSection ? ($packageSection->items ?? []) : [
    ['label' => 'View Honeymoon Package', 'url' => $packageUrl, 'style' => 'solid'],
    ['label' => 'Reserve Your Honeymoon', 'url' => $bookingUrl, 'style' => 'outline'],
];
$diningActions = $diningSection ? ($diningSection->items ?? []) : [
    ['label' => 'Explore Dining', 'url' => 'https://dining.nandinibali.com/', 'style' => 'solid'],
    ['label' => 'Romantic Experiences', 'url' => route('experiences.category', ['categorySlug' => 'jungle-romance']), 'style' => 'outline'],
];
$spaActions = $spaSection ? ($spaSection->items ?? []) : [
    ['label' => 'Explore Spa & Wellness', 'url' => route('spa-landing.index'), 'style' => 'solid'],
    ['label' => 'Jungle Spa Ubud', 'url' => url('/jungle-spa-ubud'), 'style' => 'outline'],
    ['label' => 'Holy River', 'url' => route('holy-river.index'), 'style' => 'outline'],
];
$finalCtaActions = $finalCtaSection ? ($finalCtaSection->items ?? []) : [
    ['label' => 'Explore Honeymoon Package', 'url' => $packageUrl, 'style' => 'solid'],
    ['label' => 'Reserve Your Stay', 'url' => $bookingUrl, 'style' => 'white-outline'],
];
$introButtonLabel = $intro ? $intro->button_label : 'Explore Our Resort';
$accommodationsButtonLabel = $accommodationsSection ? $accommodationsSection->button_label : 'Explore Villas & Royal Suites';
$celebrationsButtonLabel = $celebrationsSection ? $celebrationsSection->button_label : 'Plan a Romantic Celebration';
$schemaKey = fn (string $key): string => chr(64).$key;
$faqSchema = [
    $schemaKey('context') => 'https://schema.org',
    $schemaKey('type') => 'FAQPage',
    'mainEntity' => collect($faqs)->map(fn (array $faq) => [
        $schemaKey('type') => 'Question',
        'name' => $faq['question'],
        'acceptedAnswer' => [
            $schemaKey('type') => 'Answer',
            'text' => $faq['answer'],
        ],
    ])->values()->all(),
];
@endphp

@push('meta')
<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
<meta name="author" content="Nandini Jungle by Hanging Gardens">
<link rel="canonical" href="{{ $canonicalUrl }}">
<meta property="og:type" content="website">
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:site_name" content="Nandini Jungle by Hanging Gardens">
@if ($metaImage)
<meta property="og:image" content="{{ asset('storage/' . $metaImage) }}">
<meta name="twitter:image" content="{{ asset('storage/' . $metaImage) }}">
@endif
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
@if ($faqs)
<script type="application/ld+json">
{!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}
</script>
@endif
@endpush

<x-layouts.app>
    @if (! $usesHoneymoonSections || $hero)
        <x-honeymoon.hero :page="$page" :section="$hero" />
    @endif

    @if (! $usesHoneymoonSections || $intro)
    <section class="bg-white py-14 md:py-28" data-gtm-section="introduction">
        <div class="mx-auto grid w-full max-w-screen-2xl items-center gap-8 px-4 sm:px-6 lg:grid-cols-12 lg:gap-10 lg:px-8">
            <div class="text-center lg:col-span-5 lg:px-8 lg:text-left">
                <p class="mb-2 text-xs uppercase text-slate-500 sm:text-sm">{{ $hero?->subtitle ?: 'Honeymoon' }}</p>
                <h1 class="text-xl font-medium uppercase leading-snug text-slate-700 sm:text-2xl">{{ $hero?->title ?: 'Honeymoon Resort in Ubud, Bali' }}</h1>
                <p class="mt-3 text-xs leading-relaxed text-gray-600 sm:text-sm">{{ filled($hero?->description) ? strip_tags($hero->description) : 'A Romantic Jungle Honeymoon at Nandini Jungle by Hanging Gardens' }}</p>
                <p class="mb-2 mt-8 text-xs uppercase text-slate-500 sm:text-sm">{{ $intro?->subtitle ?: 'A Honeymoon in Nature' }}</p>
                <h2 class="mb-3 text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">{{ $intro?->title ?: 'Celebrate Your Honeymoon Surrounded by the Rainforest' }}</h2>
                <div class="space-y-5 text-xs leading-relaxed text-gray-600 [&_p]:mb-5 [&_p:last-child]:mb-0 sm:text-sm">
                    {!! $intro?->description ?: '<p>Celebrate your honeymoon at Nandini Jungle by Hanging Gardens, a romantic jungle resort in Payangan, within the greater Ubud area of Bali. Set above the Ayung River valley, Nandini offers private jungle villas and Royal Suites, couples spa experiences, romantic dining and memorable moments designed for two.</p><p>Whether you are planning a Bali honeymoon, anniversary or romantic escape, the resort offers a peaceful setting where you can slow down, reconnect and experience Ubud together.</p>' !!}
                </div>
                @if (filled($introButtonLabel))
                    <x-buttons.link-button :href="$sectionLink($intro, '#why')" variant="solid" class="mt-8">{{ $introButtonLabel }}</x-buttons.link-button>
                @endif
            </div>
            <div class="relative min-h-[360px] overflow-hidden sm:min-h-[480px] lg:col-span-7 lg:min-h-[560px]">
                @if ($introImage['url'])
                    <img src="{{ $introImage['url'] }}" alt="{{ $introImage['alt'] }}" class="absolute inset-0 h-full w-full object-cover" width="1200" height="900" loading="lazy" decoding="async">
                @endif
            </div>
        </div>
    </section>
    @endif

    @if (! $usesHoneymoonSections || $featuresSection)
    <section id="why" class="scroll-mt-24 bg-[#F7F7F7] py-14 md:py-28" data-gtm-section="why_nandini">
        <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-4xl text-center">
                <p class="mb-2 text-xs uppercase text-slate-500 sm:text-sm">{{ $featuresSection?->subtitle ?: 'Why Choose Nandini' }}</p>
                <h2 class="text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">{{ $featuresSection?->title ?: 'Why Choose Nandini for Your Honeymoon in Ubud?' }}</h2>
                <div class="mx-auto mt-3 max-w-3xl text-xs leading-relaxed text-gray-600 sm:text-sm">{!! $featuresSection?->description ?: '<p>A honeymoon at Nandini is shaped by privacy, nature and time together. The resort sits along a tropical hillside overlooking the Ayung River valley, away from Bali\'s busier coastal areas while remaining within the greater Ubud region. Couples can stay in private jungle accommodation, unwind with spa and wellness experiences, enjoy romantic dining surrounded by nature and discover cultural and riverside experiences together.</p>' !!}</div>
            </div>
            <div class="mt-10 grid gap-px bg-slate-200 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($features as $feature)
                    <article class="bg-white px-6 py-8 text-center sm:px-8">
                        <div class="mx-auto flex h-11 w-11 items-center justify-center text-[#A88444]" aria-hidden="true">
                            @switch($feature['icon'])
                                @case('home')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955a1.126 1.126 0 0 1 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875C9.75 15.504 10.254 15 10.875 15h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75"/></svg>
                                    @break
                                @case('diamond')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75 20.25 12 12 20.25 3.75 12 12 3.75Z"/></svg>
                                    @break
                                @case('sparkles')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.456-2.456L14.25 6l1.035-.259a3.375 3.375 0 0 0 2.456-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z"/></svg>
                                    @break
                                @case('heart')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0 5.25-9 11.25-9 11.25S3 13.5 3 8.25a4.5 4.5 0 0 1 8.25-2.49L12 6.75l.75-.99A4.5 4.5 0 0 1 21 8.25Z"/></svg>
                                    @break
                                @case('leaf')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 21C9 15.75 12.75 12 18 9.75M5.25 18.75c-2.25-7.5 2.25-13.5 15-15-.75 10.5-6.75 15-15 15Z"/></svg>
                                    @break
                                @default
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="m12 2.25 2.214 6.814h7.164l-5.796 4.211 2.214 6.815L12 15.879 6.204 20.09l2.214-6.815-5.796-4.211h7.164L12 2.25Z"/></svg>
                            @endswitch
                        </div>
                        <h3 class="mt-5 text-sm font-medium uppercase leading-snug text-slate-700 sm:text-base">{{ $feature['title'] }}</h3>
                        <p class="mt-2 text-xs leading-relaxed text-gray-600 sm:text-sm">{{ $feature['description'] ?? '' }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    @if (! $usesHoneymoonSections || $accommodationsSection)
    <section class="bg-white py-14 md:py-28" data-gtm-section="accommodations">
        <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-4xl text-center">
                <p class="mb-2 text-xs uppercase text-slate-500 sm:text-sm">{{ $accommodationsSection?->subtitle ?: 'Accommodation for Couples' }}</p>
                <h2 class="text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">{{ $accommodationsSection?->title ?: 'Jungle Villas & Royal Suites for Your Honeymoon' }}</h2>
                <div class="mx-auto mt-3 max-w-3xl text-xs leading-relaxed text-gray-600 sm:text-sm">{!! $accommodationsSection?->description ?: '<p>Discover private jungle villas and spacious Royal Suites created for romantic stays in Ubud.</p>' !!}</div>
                @if (filled($accommodationsButtonLabel))
                    <x-buttons.link-button :href="$sectionLink($accommodationsSection, route('accommodations.index'))" variant="solid" class="mt-8">{{ $accommodationsButtonLabel }}</x-buttons.link-button>
                @endif
            </div>
            <div class="mt-10 grid gap-8 md:grid-cols-3">
                @foreach ($accommodationItems as $accommodation)
                    <article class="flex h-full flex-col border border-slate-200 bg-white">
                        <a href="{{ $accommodation['url'] ?? '#' }}" class="block aspect-[4/3] overflow-hidden bg-slate-100">
                            @if ($imageUrl($accommodation['image'] ?? null))
                                <img src="{{ $imageUrl($accommodation['image']) }}" alt="{{ $accommodation['image_alt'] ?? $accommodation['title'] ?? '' }}" class="h-full w-full object-cover transition duration-500 hover:scale-[1.02]" width="800" height="600" loading="lazy" decoding="async">
                            @endif
                        </a>
                        <div class="flex flex-1 flex-col p-6 text-center">
                            <h3 class="text-sm font-medium uppercase leading-snug text-slate-700 sm:text-base">{{ $accommodation['title'] ?? '' }}</h3>
                            <p class="mt-3 flex-1 text-xs leading-relaxed text-gray-600 sm:text-sm">{{ $accommodation['description'] ?? '' }}</p>
                            @if (filled($accommodation['url'] ?? null))
                                <x-buttons.link-button :href="$accommodation['url']" variant="outline" class="mt-5 self-center">{{ $accommodation['link_label'] ?? 'View Details' }}</x-buttons.link-button>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    @if (! $usesHoneymoonSections || $packageSection)
    <x-honeymoon.showcase :image="$packageImage['url']" :image-alt="$packageImage['alt']" :eyebrow="$packageSection?->subtitle ?: 'Special Offer'" :title="$packageSection?->title ?: '4 Days / 3 Nights Honeymoon Package'" background="muted" section-name="honeymoon_package">
        {!! $packageSection?->description ?: '<p>Created for couples celebrating a honeymoon or romantic escape, our honeymoon experience brings together time to relax and reconnect.</p>' !!}
        <x-slot:actions>
            @foreach ($packageActions as $action)
                <x-buttons.link-button :href="$action['url'] ?? '#'" :variant="$action['style'] ?? 'outline'">{{ $action['label'] ?? '' }}</x-buttons.link-button>
            @endforeach
        </x-slot:actions>
    </x-honeymoon.showcase>
    @endif

    @if (! $usesHoneymoonSections || $diningSection)
    <x-honeymoon.showcase :image="$diningImage['url']" :image-alt="$diningImage['alt']" :eyebrow="$diningSection?->subtitle ?: 'Romantic Dining'" :title="$diningSection?->title ?: 'Romantic Dining in the Jungle'" :reverse="true" section-name="romantic_dining">
        {!! $diningSection?->description ?: '<p>Celebrate an evening together with romantic dining surrounded by Nandini\'s tropical landscape.</p>' !!}
        <x-slot:actions>
            @foreach ($diningActions as $action)
                <x-buttons.link-button :href="$action['url'] ?? '#'" :variant="$action['style'] ?? 'outline'">{{ $action['label'] ?? '' }}</x-buttons.link-button>
            @endforeach
        </x-slot:actions>
    </x-honeymoon.showcase>
    @endif

    @if (! $usesHoneymoonSections || $spaSection)
    <x-honeymoon.showcase :image="$spaImage['url']" :image-alt="$spaImage['alt']" :eyebrow="$spaSection?->subtitle ?: 'Spa & Wellness'" :title="$spaSection?->title ?: 'Spa & Wellness for Two'" background="muted" section-name="spa_wellness">
        {!! $spaSection?->description ?: '<p>Slow down together with spa and wellness experiences inspired by Nandini\'s rainforest setting.</p>' !!}
        <x-slot:actions>
            @foreach ($spaActions as $action)
                <x-buttons.link-button :href="$action['url'] ?? '#'" :variant="$action['style'] ?? 'outline'">{{ $action['label'] ?? '' }}</x-buttons.link-button>
            @endforeach
        </x-slot:actions>
    </x-honeymoon.showcase>
    @endif

    @if (! $usesHoneymoonSections || $itinerarySection)
    <section class="bg-white py-14 md:py-28" data-gtm-section="itinerary">
        <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-4xl text-center">
                <p class="mb-2 text-xs uppercase text-slate-500 sm:text-sm">{{ $itinerarySection?->subtitle ?: 'Example Itinerary' }}</p>
                <h2 class="text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">{{ $itinerarySection?->title ?: 'A Suggested 4-Day Honeymoon in Ubud' }}</h2>
                <div class="mx-auto mt-3 max-w-3xl text-xs leading-relaxed text-gray-600 sm:text-sm">{!! $itinerarySection?->description ?: '<p>An example itinerary to inspire your stay.</p>' !!}</div>
            </div>
            <ol class="mt-10 grid gap-8 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($itinerary as $day)
                    <li class="border border-slate-200 bg-white">
                        <div class="aspect-[4/3] overflow-hidden bg-slate-100">
                            @if ($imageUrl($day['image'] ?? null))
                                <img src="{{ $imageUrl($day['image']) }}" alt="{{ $day['image_alt'] ?? $day['title'] ?? '' }}" class="h-full w-full object-cover" width="700" height="525" loading="lazy" decoding="async">
                            @endif
                        </div>
                        <div class="p-6 text-center">
                            <p class="text-xs font-medium uppercase tracking-[0.12em] text-[#A88444] sm:text-sm">{{ $day['label'] ?? '' }}</p>
                            <h3 class="mt-2 text-sm font-medium uppercase leading-snug text-slate-700 sm:text-base">{{ $day['title'] ?? '' }}</h3>
                            <p class="mt-3 text-xs leading-relaxed text-gray-600 sm:text-sm">{{ $day['description'] ?? '' }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>
    @endif

    @if (! $usesHoneymoonSections || $celebrationsSection)
    <section class="relative min-h-[520px] overflow-hidden bg-slate-900" data-gtm-section="celebrations">
        @if ($celebrationsImage['url'])
            <img src="{{ $celebrationsImage['url'] }}" alt="{{ $celebrationsImage['alt'] }}" class="absolute inset-0 h-full w-full object-cover" width="1920" height="1080" loading="lazy" decoding="async">
        @endif
        <div class="absolute inset-0 bg-black/65"></div>
        <div class="relative z-10 mx-auto flex min-h-[520px] max-w-screen-2xl items-center justify-center px-6 py-16 text-center text-white">
            <div class="max-w-3xl">
                <p class="mb-2 text-xs uppercase text-white/80 sm:text-sm">{{ $celebrationsSection?->subtitle ?: 'Special Celebrations' }}</p>
                <h2 class="text-lg font-medium uppercase leading-snug text-white sm:text-xl">{{ $celebrationsSection?->title ?: 'Proposals, Anniversaries & Romantic Celebrations' }}</h2>
                <div class="mx-auto mt-3 max-w-2xl text-xs leading-relaxed text-white/85 sm:text-sm">{!! $celebrationsSection?->description ?: '<p>Celebrate meaningful occasions with romantic dining and personalized moments in the jungle.</p>' !!}</div>
                @if (filled($celebrationsButtonLabel))
                    <x-buttons.link-button :href="$sectionLink($celebrationsSection, 'https://dining.nandinibali.com/')" variant="solid" class="mt-8">{{ $celebrationsButtonLabel }}</x-buttons.link-button>
                @endif
            </div>
        </div>
    </section>
    @endif

    @if (! $usesHoneymoonSections || $faqSection)
    <section class="bg-[#F7F7F7] py-14 md:py-28" data-gtm-section="faq">
        <div class="mx-auto w-full max-w-screen-xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-4xl text-center">
                <p class="mb-2 text-xs uppercase text-slate-500 sm:text-sm">{{ $faqSection?->subtitle ?: 'Frequently Asked Questions' }}</p>
                <h2 class="text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">{{ $faqSection?->title ?: 'Honeymoon in Ubud — Frequently Asked Questions' }}</h2>
            </div>
            <div class="mt-10 grid items-start gap-x-10 lg:grid-cols-2">
                @foreach (array_chunk($faqs, 5) as $column)
                    <div>
                        @foreach ($column as $faq)
                            <details class="group border-b border-slate-300 py-5">
                                <summary class="flex cursor-pointer list-none items-start justify-between gap-6 text-left text-sm font-medium leading-relaxed text-slate-700 marker:content-none sm:text-base">
                                    <span>{{ $faq['question'] }}</span>
                                    <span class="mt-0.5 text-xl font-normal leading-none text-[#A88444] transition group-open:rotate-45" aria-hidden="true">+</span>
                                </summary>
                                <p class="pr-10 pt-3 text-xs leading-relaxed text-gray-600 sm:text-sm">{{ $faq['answer'] }}</p>
                            </details>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    @if (! $usesHoneymoonSections || $finalCtaSection)
    <section class="relative min-h-[560px] overflow-hidden bg-slate-900" data-gtm-section="final_cta">
        @if ($finalCtaImage['url'])
            <img src="{{ $finalCtaImage['url'] }}" alt="{{ $finalCtaImage['alt'] }}" class="absolute inset-0 h-full w-full object-cover" width="1920" height="1080" loading="lazy" decoding="async">
        @endif
        <div class="absolute inset-0 bg-black/65"></div>
        <div class="relative z-10 mx-auto flex min-h-[560px] max-w-screen-2xl items-center justify-center px-6 py-16 text-center text-white">
            <div class="max-w-3xl">
                <p class="mb-2 text-xs uppercase text-white/80 sm:text-sm">{{ $finalCtaSection?->subtitle ?: 'Your Honeymoon Awaits' }}</p>
                <h2 class="text-lg font-medium uppercase leading-snug text-white sm:text-xl">{{ $finalCtaSection?->title ?: 'Plan Your Honeymoon in the Ubud Jungle' }}</h2>
                <div class="mx-auto mt-3 max-w-2xl text-xs leading-relaxed text-white/85 sm:text-sm">{!! $finalCtaSection?->description ?: '<p>Celebrate your honeymoon surrounded by rainforest and river valley views.</p>' !!}</div>
                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    @foreach ($finalCtaActions as $action)
                        <x-buttons.link-button :href="$action['url'] ?? '#'" :variant="$action['style'] ?? 'white-outline'">{{ $action['label'] ?? '' }}</x-buttons.link-button>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif
</x-layouts.app>
