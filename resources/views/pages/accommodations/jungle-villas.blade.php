@push('meta')
<x-seo.json-ld :data="\App\Support\SeoStructuredData::page('https://nandinibali.com/jungle-villas', $page->title, $page->meta_description)" />
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
    <x-heroes.image-hero :page="$page" />

    <x-sections.page-description :page="$page" />

    @if ($page->slug === 'jungle-villas' && isset($accommodations) && $accommodations->isNotEmpty())
    @php
    $villaDetails = [
        ['icon' => 'square-meter.png', 'label' => '48 sqm'],
        ['icon' => 'double-bed.png', 'label' => 'King-size bed'],
        ['icon' => 'family.png', 'label' => '2 Adults + 1 Child'],
        ['icon' => 'balcony.png', 'label' => 'Private Balcony'],
        ['icon' => 'bathtub-outline.png', 'label' => 'Outdoor Bathtub & Rain Shower'],
    ];
    $villaCardContent = [
        'jungle-view-villa' => [
            'description' => 'Surrounded by tropical forest, the Jungle View Villa combines Balinese-inspired design with understated luxury. This 48-square-metre retreat features a king-size bed, a private balcony and an outdoor bathtub with rain shower, inviting guests to unwind in the tranquillity of the jungle.',
            'details' => $villaDetails,
        ],
        'sunrise-view-villa' => [
            'description' => "Wake to the soft morning light overlooking Ubud's rainforest. The 48-square-metre Sunrise View Villa offers a peaceful atmosphere with a king-size bed, private balcony and outdoor bathtub with rain shower, creating an intimate setting for a relaxing Bali escape.",
            'details' => $villaDetails,
        ],
        'panoramic-jungle-view-villa' => [
            'description' => "Discover sweeping views of the forested river valley from the Panoramic Jungle View Villa. Featuring 48 square metres of refined accommodation, a canopied king-size bed, private balcony and outdoor bathtub with rain shower, this villa offers an immersive connection to Ubud's tropical landscape.",
            'details' => $villaDetails,
        ],
    ];
    @endphp
    <x-sections.item-carousel :items="$accommodations" :card-content="$villaCardContent" data-gtm-section="villas" />
    <div class="mx-auto max-w-4xl px-6 pb-14 text-center text-xs leading-relaxed text-gray-600 sm:text-sm md:pb-20">
        <p>For more spacious accommodation, explore our <a href="{{ route('accommodations.suites') }}" class="text-slate-700 underline">Royal Suites</a>. Discover the setting and experiences of our <a href="/ubud-jungle-resort-in-bali" class="text-slate-700 underline">Bali jungle resort in Ubud</a> to plan your stay.</p>
    </div>
    @endif
</x-layouts.app>
