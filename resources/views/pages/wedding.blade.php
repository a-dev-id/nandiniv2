@section('inquiry-modal', true)

@php
$metaTitle = $page->meta_title ?: $page->title;
$metaDescription = $page->meta_description ?? '';
$canonicalUrl = 'https://nandinibali.com/weddings';
$content = $sections->keyBy('section_key');
$chapel = $content->get('wedding_chapel');
$river = $content->get('wedding_river');
$ceremonies = $content->get('wedding_ceremony_options');
$dining = $content->get('wedding_dining');
$accommodation = $content->get('wedding_accommodation');
$planning = $content->get('wedding_planning');
$finalCta = $content->get('wedding_final_cta');

$imageUrl = function (?string $path): ?string {
    if (blank($path)) {
        return null;
    }

    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        return $path;
    }

    if (str_starts_with($path, '/')) {
        return asset(ltrim($path, '/'));
    }

    if (! Storage::disk('public')->exists($path)) {
        return 'https://nandinibali.com/storage/'.ltrim($path, '/');
    }

    return Storage::disk('public')->url($path);
};

$sectionImage = function ($section) use ($imageUrl): array {
    $image = $section?->images?->first();

    return [
        'desktop' => $imageUrl($image?->image ?: $image?->mobile_image),
        'mobile' => $imageUrl($image?->mobile_image ?: $image?->image),
        'alt' => $image?->image_alt ?: $image?->mobile_image_alt ?: $section?->title ?: '',
    ];
};

$schema = [
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    '@id' => 'https://nandinibali.com/weddings#webpage',
    'url' => $canonicalUrl,
    'name' => $metaTitle,
    'description' => $metaDescription,
    'isPartOf' => ['@id' => 'https://nandinibali.com/#website'],
    'about' => ['@id' => 'https://nandinibali.com/#hotel'],
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
@if (! empty($page->hero_image))
<meta property="og:image" content="{{ asset('storage/' . $page->hero_image) }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta name="twitter:image" content="{{ asset('storage/' . $page->hero_image) }}">
@endif
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
<script type="application/ld+json">
{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}
</script>
@endpush

<x-layouts.app>
    <x-heroes.image-hero :page="$page" :use-production-fallback="true" />

    <section class="bg-white px-6 py-14 text-center md:py-20" data-gtm-section="wedding_intro">
        <div class="mx-auto max-w-5xl">
            @if (filled($page->subtitle))
                <p class="mb-2 text-xs uppercase text-[#A88444] sm:text-sm">{{ $page->subtitle }}</p>
            @endif
            <h1 class="text-xl font-medium uppercase leading-snug text-slate-700 sm:text-2xl">{{ $page->title }}</h1>
            <div class="mx-auto mt-4 max-w-4xl text-xs leading-relaxed text-gray-600 [&_p]:mb-5 [&_p:last-child]:mb-0 sm:text-sm">
                {!! $page->description !!}
            </div>
        </div>
    </section>

    @foreach ([$chapel, $river] as $venue)
        @if ($venue)
            @php
            $venueImage = $sectionImage($venue);
            $isRiver = $venue->section_key === 'wedding_river';
            @endphp
            <section class="{{ $isRiver ? 'bg-[#F7F7F7]' : 'bg-white' }} py-14 md:py-24" data-gtm-section="{{ $isRiver ? 'wedding_river' : 'wedding_chapel' }}">
                <div class="mx-auto grid w-full max-w-screen-2xl items-stretch gap-8 px-4 sm:px-6 lg:grid-cols-12 lg:gap-10 lg:px-8">
                    <div class="relative min-h-[360px] overflow-hidden bg-slate-100 sm:min-h-[480px] lg:col-span-8 lg:min-h-[560px] {{ $isRiver ? 'lg:order-2' : 'lg:order-1' }}">
                        @if ($venueImage['desktop'] || $venueImage['mobile'])
                            <picture class="absolute inset-0 block h-full w-full">
                                @if ($venueImage['mobile'])
                                    <source media="(max-width: 767px)" srcset="{{ $venueImage['mobile'] }}">
                                @endif
                                <img src="{{ $venueImage['desktop'] ?: $venueImage['mobile'] }}" alt="{{ $venueImage['alt'] }}" class="h-full w-full object-cover transition-transform duration-700 hover:scale-[1.02]" width="1400" height="1050" loading="lazy" decoding="async">
                            </picture>
                        @endif
                    </div>
                    <div class="flex items-center lg:col-span-4 {{ $isRiver ? 'lg:order-1' : 'lg:order-2' }}">
                        <div class="w-full px-4 text-center sm:px-8 lg:px-10">
                            @if (filled($venue->subtitle))
                                <p class="mb-2 text-xs uppercase text-[#A88444] sm:text-sm">{{ $venue->subtitle }}</p>
                            @endif
                            <h2 class="text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">{{ $venue->title }}</h2>
                            <div class="mx-auto mt-3 max-w-xl text-xs leading-relaxed text-gray-600 [&_p]:mb-5 [&_p:last-child]:mb-0 sm:text-sm">
                                {!! $venue->description !!}
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif
    @endforeach

    @if ($ceremonies)
        <section class="bg-white py-14 md:py-24" data-gtm-section="ceremony_options">
            <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-4xl text-center">
                    <p class="mb-2 text-xs uppercase text-[#A88444] sm:text-sm">{{ $ceremonies->subtitle }}</p>
                    <h2 class="text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">{{ $ceremonies->title }}</h2>
                    <div class="mx-auto mt-3 max-w-3xl text-xs leading-relaxed text-gray-600 sm:text-sm">{!! $ceremonies->description !!}</div>
                </div>
                <div class="mt-10 grid border-y border-slate-200 md:grid-cols-3">
                    @foreach ($ceremonies->items ?? [] as $index => $item)
                        <article class="px-6 py-10 text-center md:px-9 md:py-12 {{ $index > 0 ? 'border-t border-slate-200 md:border-l md:border-t-0' : '' }}">
                            <span class="font-span text-3xl text-[#A88444]" aria-hidden="true">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                            <h3 class="mt-4 text-sm font-medium uppercase leading-snug text-slate-700 sm:text-base">{{ $item['title'] ?? '' }}</h3>
                            <p class="mx-auto mt-3 max-w-sm text-xs leading-relaxed text-gray-600 sm:text-sm">{{ $item['description'] ?? '' }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($dining)
        @php
        $diningImage = $sectionImage($dining);
        @endphp
        <section class="bg-[#F7F7F7] py-14 md:py-20" data-gtm-section="wedding_dining">
            <div class="mx-auto grid w-full max-w-screen-xl items-center gap-8 px-4 sm:px-6 lg:grid-cols-12 lg:gap-10 lg:px-8">
                <div class="relative min-h-[320px] overflow-hidden bg-slate-100 sm:min-h-[420px] lg:col-span-7">
                    @if ($diningImage['desktop'] || $diningImage['mobile'])
                        <picture class="absolute inset-0 block h-full w-full">
                            @if ($diningImage['mobile'])
                                <source media="(max-width: 767px)" srcset="{{ $diningImage['mobile'] }}">
                            @endif
                            <img src="{{ $diningImage['desktop'] ?: $diningImage['mobile'] }}" alt="{{ $diningImage['alt'] }}" class="h-full w-full object-cover" width="1200" height="900" loading="lazy" decoding="async">
                        </picture>
                    @endif
                </div>
                <div class="text-center lg:col-span-5 lg:px-8">
                    <p class="mb-2 text-xs uppercase text-[#A88444] sm:text-sm">{{ $dining->subtitle }}</p>
                    <h2 class="text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">{{ $dining->title }}</h2>
                    <div class="mx-auto mt-3 max-w-xl text-xs leading-relaxed text-gray-600 [&_p]:mb-5 [&_p:last-child]:mb-0 sm:text-sm">{!! $dining->description !!}</div>
                    @if (filled($dining->button_label) && filled($dining->button_url))
                        <x-buttons.link-button :href="$dining->button_url" variant="solid" class="mt-7">{{ $dining->button_label }}</x-buttons.link-button>
                    @endif
                </div>
            </div>
        </section>
    @endif

    @if ($accommodation)
        <section class="bg-white py-14 md:py-24" data-gtm-section="wedding_accommodation">
            <div class="mx-auto w-full max-w-screen-xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-4xl text-center">
                    <p class="mb-2 text-xs uppercase text-[#A88444] sm:text-sm">{{ $accommodation->subtitle }}</p>
                    <h2 class="text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">{{ $accommodation->title }}</h2>
                    <div class="mx-auto mt-3 max-w-3xl text-xs leading-relaxed text-gray-600 [&_a]:font-medium [&_a]:text-[#A88444] [&_a]:underline-offset-4 hover:[&_a]:underline [&_p]:mb-5 [&_p:last-child]:mb-0 sm:text-sm">{!! $accommodation->description !!}</div>
                </div>
                <div class="mx-auto mt-10 grid max-w-5xl gap-8 md:grid-cols-2 md:gap-12">
                    @foreach ($accommodation->items ?? [] as $item)
                        <article class="border-t border-slate-300 pt-7 text-center">
                            <h3 class="text-sm font-medium uppercase leading-snug text-slate-700 sm:text-base">{{ $item['title'] ?? '' }}</h3>
                            <p class="mx-auto mt-3 max-w-md text-xs leading-relaxed text-gray-600 sm:text-sm">{{ $item['description'] ?? '' }}</p>
                            @if (filled($item['url'] ?? null))
                                <x-buttons.link-button :href="$item['url']" variant="outline" class="mt-6">{{ $item['link_label'] ?? 'EXPLORE' }}</x-buttons.link-button>
                            @endif
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($planning)
        <section class="bg-[#F7F7F7] py-14 md:py-24" data-gtm-section="wedding_planning">
            <div class="mx-auto w-full max-w-screen-xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-4xl text-center">
                    <p class="mb-2 text-xs uppercase text-[#A88444] sm:text-sm">{{ $planning->subtitle }}</p>
                    <h2 class="text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">{{ $planning->title }}</h2>
                    <div class="mx-auto mt-3 max-w-3xl text-xs leading-relaxed text-gray-600 sm:text-sm">{!! $planning->description !!}</div>
                </div>
                <div class="mx-auto mt-10 grid max-w-5xl gap-x-12 gap-y-8 sm:grid-cols-2">
                    @foreach ($planning->items ?? [] as $index => $item)
                        <article class="grid grid-cols-[2.5rem_1fr] gap-4 border-t border-slate-300 pt-6">
                            <span class="font-span text-2xl leading-none text-[#A88444]" aria-hidden="true">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                            <div>
                                <h3 class="text-sm font-medium uppercase leading-snug text-slate-700 sm:text-base">{{ $item['title'] ?? '' }}</h3>
                                <p class="mt-2 text-xs leading-relaxed text-gray-600 sm:text-sm">{{ $item['description'] ?? '' }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($finalCta)
        @php
        $finalCtaImage = $sectionImage($finalCta);
        $finalCtaActions = $finalCta->items ?? [];
        @endphp
        <section class="relative min-h-[500px] overflow-hidden bg-slate-900" data-gtm-section="booking_cta">
            @if ($finalCtaImage['desktop'] || $finalCtaImage['mobile'])
                <picture class="absolute inset-0 block h-full w-full">
                    @if ($finalCtaImage['mobile'])
                        <source media="(max-width: 767px)" srcset="{{ $finalCtaImage['mobile'] }}">
                    @endif
                    <img src="{{ $finalCtaImage['desktop'] ?: $finalCtaImage['mobile'] }}" alt="{{ $finalCtaImage['alt'] }}" class="h-full w-full object-cover" width="1920" height="1080" loading="lazy" decoding="async">
                </picture>
            @endif
            <div class="absolute inset-0 bg-black/65"></div>
            <div class="relative z-10 mx-auto flex min-h-[500px] max-w-screen-2xl items-center justify-center px-6 py-16 text-center text-white">
                <div class="max-w-3xl">
                    <p class="mb-2 text-xs uppercase text-[#D7B778] sm:text-sm">{{ $finalCta->subtitle }}</p>
                    <h2 class="text-lg font-medium uppercase leading-snug text-white sm:text-xl">{{ $finalCta->title }}</h2>
                    <div class="mx-auto mt-3 max-w-2xl text-xs leading-relaxed text-white/85 sm:text-sm">{!! $finalCta->description !!}</div>
                    <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                        @foreach ($finalCtaActions as $action)
                            @if (($action['url'] ?? '') === '#wedding-inquiry')
                                <x-buttons.link-button href="#wedding-inquiry" :variant="$action['style'] ?? 'solid'" data-inquiry-button data-inquiry-title="Wedding Planning at Nandini Jungle" class="w-full sm:w-auto">{{ $action['label'] ?? 'PLAN YOUR WEDDING' }}</x-buttons.link-button>
                            @else
                                <x-buttons.link-button :href="$action['url'] ?? '#'" :variant="$action['style'] ?? 'white-outline'" class="w-full sm:w-auto">{{ $action['label'] ?? '' }}</x-buttons.link-button>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif
</x-layouts.app>
