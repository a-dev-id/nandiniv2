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

@php
$storySections = $sections->keyBy('section_key');
$hasStoryLayout = $storySections->has('about_story_hero');

$plainText = static function (?string $value): string {
    $text = html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = str_replace("\xc2\xa0", ' ', $text);

    return trim((string) preg_replace('/\s+/', ' ', $text));
};

$imageUrl = static function (?string $path): string {
    $path = trim((string) $path);

    if ($path === '') {
        return '';
    }

    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        return $path;
    }

    if (str_starts_with($path, '/storage/') || str_starts_with($path, '/')) {
        return $path;
    }

    if (str_starts_with($path, 'storage/')) {
        return '/' . $path;
    }

    return asset('storage/' . $path);
};

$sectionButtonUrl = static function ($section): ?string {
    if (($section?->button_link_type ?? 'manual') === 'route' && filled($section?->button_route)) {
        return \Illuminate\Support\Facades\Route::has($section->button_route)
            ? route($section->button_route)
            : null;
    }

    return filled($section?->button_url) ? $section->button_url : null;
};

$hero = $storySections->get('about_story_hero');
$origins = $storySections->get('about_story_origins');
$timeline = $storySections->get('about_story_timeline');
$firstChapter = $storySections->get('about_story_chapter');
$riverChapter = $storySections->get('about_story_chapter_reverse');
$comparison = $storySections->get('about_story_comparison');
$growth = $storySections->get('about_story_growth');
$mosaic = $storySections->get('about_story_mosaic');
$gallery = $storySections->get('about_story_gallery');
$values = $storySections->get('about_story_values');
$today = $storySections->get('about_story_today');
$final = $storySections->get('about_story_final');
@endphp

<x-layouts.app>
    @if (! $hasStoryLayout)
        <x-heroes.image-hero :page="$page" />
        <x-sections.page-description :page="$page" />

        @foreach ($sections as $section)
            @if ($section->section_key === 'dining_information_section')
                <x-sections.dining-information-section :section="$section" />
            @elseif ($section->section_key === 'image_overlay_section')
                <x-sections.image-overlay-section :section="$section" />
            @elseif ($section->section_key === 'contained_image_section')
                <x-sections.contained-image-section :section="$section" />
            @elseif ($section->section_key === 'split_media_section')
                <x-sections.split-media-section :section="$section" :excerpt-only="false" image-span="8" text-span="4" />
            @elseif ($section->section_key === 'split_media_reverse')
                <x-sections.split-media-section :section="$section" :reverse="true" :excerpt-only="false" image-span="8" text-span="4" />
            @elseif ($section->section_key === 'three_images_section')
                <x-sections.three-images-section :section="$section" />
            @elseif ($section->section_key === 'two_images_section')
                <x-sections.two-images-section :section="$section" />
            @elseif ($section->section_key === 'two_images_reverse')
                <x-sections.two-images-section :section="$section" :reverse="true" />
            @endif
        @endforeach
    @else
        <div class="overflow-x-hidden bg-white text-slate-700">
            @if ($hero)
                @php
                $heroImage = $hero->images->first();
                $heroDesktop = $imageUrl($heroImage?->image ?: $page->hero_image);
                $heroMobile = $imageUrl($heroImage?->mobile_image ?: $heroImage?->image ?: $page->hero_mobile_image ?: $page->hero_image);
                $heroAlt = $heroImage?->image_alt ?: $heroImage?->mobile_image_alt ?: $page->hero_image_alt ?: $plainText($hero->title);
                $heroButtonUrl = $sectionButtonUrl($hero);
                @endphp

                <header class="shadow-xl" data-gtm-section="hero">
                    <div class="relative aspect-[4/3] w-full overflow-hidden bg-slate-100 lg:aspect-auto lg:h-[70vh]">
                    @if ($heroDesktop || $heroMobile)
                        <picture class="absolute inset-0 block h-full w-full">
                            @if ($heroMobile)
                                <source media="(max-width: 767px)" srcset="{{ $heroMobile }}">
                            @endif
                            <img src="{{ $heroDesktop ?: $heroMobile }}" alt="{{ $heroAlt }}" class="h-full w-full object-cover" width="1920" height="1080" loading="eager" fetchpriority="high">
                        </picture>
                    @endif
                    </div>
                </header>
                <section id="our-story" class="px-6 py-14 text-center md:py-20" data-gtm-section="introduction">
                    <div class="mx-auto max-w-5xl">
                        @if (filled($hero->subtitle))
                            <p class="mb-2 text-xs uppercase text-slate-500 sm:text-sm">{{ $plainText($hero->subtitle) }}</p>
                        @endif
                        <h1 class="text-xl font-medium uppercase text-slate-700 sm:text-2xl">
                            {!! nl2br(e($plainText($hero->title))) !!}
                        </h1>
                        @if (filled($hero->description))
                            <div class="mx-auto mt-3 max-w-4xl text-xs leading-relaxed text-gray-600 [&_p]:mb-6 [&_p:last-child]:mb-0 sm:text-sm">
                                {!! $hero->description !!}
                            </div>
                        @endif
                        @if (filled($hero->excerpt))
                            <p class="mt-5 text-xs uppercase text-slate-500 sm:text-sm">{{ $plainText($hero->excerpt) }}</p>
                        @endif
                        @if ($heroButtonUrl && filled($hero->button_label))
                            <a href="{{ $heroButtonUrl }}" class="mt-8 inline-flex items-center justify-center bg-[#A88444] px-5 py-2.5 text-xs font-medium uppercase tracking-[0.08em] text-white transition hover:bg-[#B8945B] sm:text-sm">
                                {{ $hero->button_label }}
                            </a>
                        @endif
                    </div>
                </section>
            @endif

            @if ($origins)
                @php
                $originMain = $origins->images->get(0);
                $originSmall = $origins->images->get(1);
                $originMeta = collect($origins->items)->first() ?? [];
                @endphp
                <section class="bg-[#F7F7F7] py-14 md:py-28" data-gtm-section="origins">
                    <div class="mx-auto grid w-full max-w-screen-2xl items-center gap-8 px-4 sm:px-6 lg:grid-cols-12 lg:gap-10 lg:px-8">
                        <div class="relative min-h-[420px] sm:min-h-[560px] lg:col-span-7">
                            @if ($originMain && $imageUrl($originMain->image ?: $originMain->mobile_image))
                                <img src="{{ $imageUrl($originMain->image ?: $originMain->mobile_image) }}" alt="{{ $originMain->image_alt ?: $plainText($origins->title) }}" class="absolute inset-x-0 top-0 h-[88%] w-[91%] object-cover" loading="lazy">
                            @endif
                            @if ($originSmall && $imageUrl($originSmall->image ?: $originSmall->mobile_image))
                                <div class="absolute bottom-0 right-0 h-[46%] w-[52%] border-[10px] border-[#F7F7F7] sm:border-[12px]">
                                    <img src="{{ $imageUrl($originSmall->image ?: $originSmall->mobile_image) }}" alt="{{ $originSmall->image_alt ?: $plainText($origins->title) }}" class="h-full w-full object-cover" loading="lazy">
                                </div>
                            @endif
                        </div>
                        <div class="lg:col-span-5 lg:px-8">
                            @if (filled($origins->subtitle))
                                <p class="mb-2 text-xs uppercase text-slate-500 sm:text-sm">{{ $plainText($origins->subtitle) }}</p>
                            @endif
                            <h2 class="mb-3 text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">{!! nl2br(e($plainText($origins->title))) !!}</h2>
                            <div class="text-xs leading-relaxed text-gray-600 [&_p]:mb-6 [&_p:last-child]:mb-0 sm:text-sm">{!! $origins->description !!}</div>
                            @if (filled($originMeta['quote'] ?? null))
                                <blockquote class="mt-8 border-t border-slate-300 pt-6 font-span text-lg italic leading-relaxed text-slate-700 sm:text-xl">
                                    “{{ $originMeta['quote'] }}”
                                    @if (filled($originMeta['attribution'] ?? null))
                                        <cite class="mt-3 block font-sans text-xs not-italic text-gray-600">— {{ $originMeta['attribution'] }}</cite>
                                    @endif
                                </blockquote>
                            @endif
                        </div>
                    </div>
                </section>
            @endif

            @if ($timeline)
                <section class="bg-white py-14 md:py-28" data-gtm-section="history">
                    <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-8">
                        <div class="text-center">
                            <p class="mb-2 text-xs uppercase text-slate-500 sm:text-sm">{{ $plainText($timeline->subtitle) }}</p>
                            <h2 class="text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">{{ $plainText($timeline->title) }}</h2>
                        </div>
                        <ol class="relative mt-10 grid gap-8 border-l border-slate-300 pl-7 md:grid-cols-4 md:border-l-0 md:border-t md:pl-0 xl:grid-cols-7">
                            @foreach ($timeline->items ?? [] as $item)
                                <li class="relative md:pt-9">
                                    <span class="absolute -left-[2.1rem] top-2 h-3 w-3 rounded-full border-[3px] border-white bg-[#A88444] ring-1 ring-[#A88444] md:-top-1.5 md:left-0"></span>
                                    <p class="font-span text-2xl leading-none text-[#A88444] sm:text-3xl">{{ $item['year'] ?? '' }}</p>
                                    <h3 class="mt-3 text-sm font-medium uppercase text-slate-700">{{ $item['title'] ?? '' }}</h3>
                                    <p class="mt-2 text-xs leading-relaxed text-gray-600">{{ $item['description'] ?? '' }}</p>
                                </li>
                            @endforeach
                        </ol>
                    </div>
                </section>
            @endif

            @foreach ([$firstChapter, $riverChapter] as $chapter)
                @if ($chapter)
                    @php
                    $isReverse = $chapter->section_key === 'about_story_chapter_reverse';
                    $chapterImage = $chapter->images->first();
                    $chapterButtonUrl = $sectionButtonUrl($chapter);
                    @endphp
                    <section class="py-14 md:py-28 {{ $isReverse ? 'bg-white' : 'bg-[#F7F7F7]' }}" data-gtm-section="{{ $isReverse ? 'river_story' : 'resort_origins' }}">
                        <div class="mx-auto grid w-full max-w-screen-2xl grid-cols-1 items-stretch gap-8 px-4 sm:px-6 lg:grid-cols-12 lg:gap-10 lg:px-8">
                        <div class="relative min-h-[390px] lg:col-span-8 lg:min-h-[560px] {{ $isReverse ? 'lg:order-2' : 'lg:order-1' }}">
                            @if ($chapterImage && $imageUrl($chapterImage->image ?: $chapterImage->mobile_image))
                                <img src="{{ $imageUrl($chapterImage->image ?: $chapterImage->mobile_image) }}" alt="{{ $chapterImage->image_alt ?: $plainText($chapter->title) }}" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
                            @endif
                        </div>
                        <div class="flex items-center lg:col-span-4 {{ $isReverse ? 'lg:order-1' : 'lg:order-2' }}">
                            <div class="w-full px-4 text-center sm:px-8 md:px-10 lg:px-12">
                                @if (filled($chapter->excerpt))<p class="font-span text-3xl leading-none text-[#A88444] sm:text-4xl">{{ $plainText($chapter->excerpt) }}</p>@endif
                                <p class="mb-2 mt-4 text-xs uppercase text-slate-500 sm:text-sm">{{ $plainText($chapter->subtitle) }}</p>
                                <h2 class="mb-3 text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">{!! nl2br(e($plainText($chapter->title))) !!}</h2>
                                <div class="text-xs leading-relaxed text-gray-600 [&_p]:mb-6 [&_p:last-child]:mb-0 sm:text-sm">{!! $chapter->description !!}</div>
                                @foreach ($chapter->items ?? [] as $item)
                                    <div class="mt-7 inline-grid grid-cols-[auto_auto] items-end gap-x-3 gap-y-1">
                                        <strong class="font-span text-4xl font-normal leading-none text-slate-700">{{ $item['value'] ?? '' }}</strong>
                                        <span class="pb-1 text-xs uppercase text-slate-500">{{ $item['label'] ?? '' }}</span>
                                    </div>
                                @endforeach
                                @if ($chapterButtonUrl && filled($chapter->button_label))
                                    <div><a href="{{ $chapterButtonUrl }}" class="mt-8 inline-flex items-center justify-center bg-[#A88444] px-5 py-2.5 text-xs font-medium uppercase tracking-[0.08em] text-white transition hover:bg-[#B8945B] sm:text-sm">{{ $chapter->button_label }}</a></div>
                                @endif
                            </div>
                        </div>
                        </div>
                    </section>
                @endif
            @endforeach

            @if ($comparison)
                @php
                $comparisonItems = collect($comparison->items);
                $thenItem = $comparisonItems->get(0, []);
                $nowItem = $comparisonItems->get(1, []);
                $thenImage = $comparison->images->get(0);
                $nowImage = $comparison->images->get(1);
                @endphp
                <section class="bg-[#F7F7F7] py-14 md:py-28" data-gtm-section="evolution">
                    <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-8">
                        <div class="mx-auto max-w-4xl text-center">
                            <p class="mb-2 text-xs uppercase text-slate-500 sm:text-sm">{{ $plainText($comparison->subtitle) }}</p>
                            <h2 class="text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">{{ $plainText($comparison->title) }}</h2>
                            <div class="mx-auto mt-3 max-w-3xl text-xs leading-relaxed text-gray-600 sm:text-sm">{!! $comparison->description !!}</div>
                        </div>
                        <div class="mt-10 grid min-h-[480px] overflow-hidden md:grid-cols-2">
                            <div class="relative flex min-h-[340px] items-center justify-center bg-white p-8 text-center">
                                @if ($thenImage && $imageUrl($thenImage->image ?: $thenImage->mobile_image))
                                    <img src="{{ $imageUrl($thenImage->image ?: $thenImage->mobile_image) }}" alt="{{ $thenImage->image_alt ?: ($thenItem['title'] ?? '') }}" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
                                    <div class="absolute inset-0 bg-black/25"></div>
                                @endif
                                <div class="relative max-w-sm border border-slate-200 bg-white/95 p-8">
                                    <p class="text-xs uppercase text-slate-500 sm:text-sm">{{ $thenItem['label'] ?? '' }}</p>
                                    <h3 class="mt-3 text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">{{ $thenItem['title'] ?? '' }}</h3>
                                    <p class="mt-3 text-xs leading-relaxed text-gray-600 sm:text-sm">{{ $thenItem['description'] ?? '' }}</p>
                                </div>
                            </div>
                            <div class="relative min-h-[340px] bg-slate-100">
                                @if ($nowImage && $imageUrl($nowImage->image ?: $nowImage->mobile_image))
                                    <img src="{{ $imageUrl($nowImage->image ?: $nowImage->mobile_image) }}" alt="{{ $nowImage->image_alt ?: ($nowItem['title'] ?? '') }}" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
                                @endif
                                @if (filled($nowItem['label'] ?? null))<span class="absolute right-5 top-5 bg-white/95 px-3 py-2 text-xs font-medium uppercase text-slate-700">{{ $nowItem['label'] }}</span>@endif
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            @if ($growth)
                @php $growthImage = $growth->images->first(); @endphp
                <section class="bg-white py-14 md:py-28" data-gtm-section="growth">
                    <div class="mx-auto grid w-full max-w-screen-2xl items-center gap-8 px-4 sm:px-6 lg:grid-cols-12 lg:gap-10 lg:px-8">
                        <div class="lg:col-span-5 lg:px-8">
                            <p class="font-span text-3xl leading-none text-[#A88444] sm:text-4xl">{{ $plainText($growth->excerpt) }}</p>
                            <p class="mb-2 mt-4 text-xs uppercase text-slate-500 sm:text-sm">{{ $plainText($growth->subtitle) }}</p>
                            <h2 class="mb-3 text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">{!! nl2br(e($plainText($growth->title))) !!}</h2>
                            <div class="text-xs leading-relaxed text-gray-600 [&_p]:mb-6 [&_p:last-child]:mb-0 sm:text-sm">{!! $growth->description !!}</div>
                        </div>
                        <div class="relative min-h-[380px] overflow-hidden bg-slate-100 lg:col-span-7 lg:min-h-[520px]">
                            @if ($growthImage && $imageUrl($growthImage->image ?: $growthImage->mobile_image))
                                <img src="{{ $imageUrl($growthImage->image ?: $growthImage->mobile_image) }}" alt="{{ $growthImage->image_alt ?: $plainText($growth->title) }}" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
                            @endif
                        </div>
                    </div>
                </section>
            @endif

            @if ($mosaic)
                <section class="bg-[#F7F7F7] py-14 md:py-28" data-gtm-section="renovation">
                    <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-8">
                        <div class="mx-auto max-w-4xl text-center">
                            <p class="font-span text-3xl leading-none text-[#A88444] sm:text-4xl">{{ $plainText($mosaic->excerpt) }}</p>
                            <p class="mb-2 mt-4 text-xs uppercase text-slate-500 sm:text-sm">{{ $plainText($mosaic->subtitle) }}</p>
                            <h2 class="text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">{{ $plainText($mosaic->title) }}</h2>
                            <div class="mx-auto mt-3 max-w-3xl text-xs leading-relaxed text-gray-600 sm:text-sm">{!! $mosaic->description !!}</div>
                        </div>
                        <div class="mt-10 grid auto-rows-[240px] grid-cols-2 gap-3 md:auto-rows-[280px] md:grid-cols-[1.3fr_.7fr_.7fr]">
                            @foreach ($mosaic->images->take(5) as $image)
                                <figure class="group relative overflow-hidden {{ $loop->first ? 'col-span-2 row-span-1 md:col-span-1 md:row-span-2' : '' }}">
                                    <img src="{{ $imageUrl($image->image ?: $image->mobile_image) }}" alt="{{ $image->image_alt ?: $image->caption ?: $plainText($mosaic->title) }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-105" loading="lazy">
                                    @if (filled($image->caption))<figcaption class="absolute bottom-4 left-4 bg-black/50 px-3 py-2 text-[10px] font-medium uppercase tracking-[0.12em] text-white sm:text-[11px]">{{ $image->caption }}</figcaption>@endif
                                </figure>
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif

            @if ($gallery)
                <section class="bg-white py-14 md:py-28" data-gtm-section="gallery">
                    <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-8">
                        <div class="mx-auto max-w-4xl text-center">
                            <p class="font-span text-3xl leading-none text-[#A88444] sm:text-4xl">{{ $plainText($gallery->excerpt) }}</p>
                            <p class="mb-2 mt-4 text-xs uppercase text-slate-500 sm:text-sm">{{ $plainText($gallery->subtitle) }}</p>
                            <h2 class="text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">{{ $plainText($gallery->title) }}</h2>
                            <div class="mx-auto mt-3 max-w-3xl text-xs leading-relaxed text-gray-600 sm:text-sm">{!! $gallery->description !!}</div>
                        </div>
                        <div class="mt-10 grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
                            @foreach ($gallery->images->take(4) as $image)
                                @php $card = collect($gallery->items)->get($loop->index, []); @endphp
                                <figure class="group relative aspect-[4/5] overflow-hidden">
                                    <img src="{{ $imageUrl($image->image ?: $image->mobile_image) }}" alt="{{ $image->image_alt ?: ($card['title'] ?? $plainText($gallery->title)) }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-105" loading="lazy">
                                    <figcaption class="absolute inset-x-5 bottom-5 bg-white/95 p-4 text-slate-700">
                                        <span class="text-[10px] font-medium uppercase text-slate-500">{{ $card['eyebrow'] ?? '' }}</span>
                                        <strong class="mt-1 block font-span text-lg font-normal uppercase sm:text-xl">{{ $card['title'] ?? $image->caption }}</strong>
                                    </figcaption>
                                </figure>
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif

            @if ($values)
                <section class="bg-[#F7F7F7] py-14 md:py-28" data-gtm-section="values">
                    <div class="mx-auto w-full max-w-screen-2xl px-4 sm:px-6 lg:px-8">
                        <div class="mx-auto max-w-4xl text-center">
                            <p class="mb-2 text-xs uppercase text-slate-500 sm:text-sm">{{ $plainText($values->subtitle) }}</p>
                            <h2 class="text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">{{ $plainText($values->title) }}</h2>
                            <div class="mx-auto mt-3 max-w-3xl text-xs leading-relaxed text-gray-600 sm:text-sm">{!! $values->description !!}</div>
                        </div>
                        <div class="mt-10 grid gap-10 md:grid-cols-3 md:gap-6">
                            @foreach ($values->images->take(3) as $image)
                                @php $value = collect($values->items)->get($loop->index, []); @endphp
                                <article>
                                    <div class="aspect-[4/3] overflow-hidden"><img src="{{ $imageUrl($image->image ?: $image->mobile_image) }}" alt="{{ $image->image_alt ?: ($value['title'] ?? $plainText($values->title)) }}" class="h-full w-full object-cover transition duration-700 hover:scale-105" loading="lazy"></div>
                                    <p class="mt-5 font-span text-3xl text-[#A88444] sm:text-4xl">{{ $value['value'] ?? '' }}</p>
                                    <h3 class="mt-2 text-base font-medium uppercase leading-snug text-slate-700 sm:text-lg">{{ $value['title'] ?? '' }}</h3>
                                    <p class="mt-3 text-xs leading-relaxed text-gray-600 sm:text-sm">{{ $value['description'] ?? '' }}</p>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif

            @if ($today)
                @php
                $todayImage = $today->images->first();
                $todayItems = collect($today->items);
                $todayStats = $todayItems->where('kind', 'stat');
                $todayLinks = $todayItems->where('kind', 'link');
                @endphp
                <section class="bg-white py-14 md:py-28" data-gtm-section="nandini_today">
                    <div class="mx-auto grid w-full max-w-screen-2xl items-center gap-8 px-4 sm:px-6 lg:grid-cols-12 lg:gap-10 lg:px-8">
                        <div class="relative min-h-[400px] overflow-hidden bg-slate-100 lg:col-span-7 lg:min-h-[560px]">
                            @if ($todayImage && $imageUrl($todayImage->image ?: $todayImage->mobile_image))
                                <img src="{{ $imageUrl($todayImage->image ?: $todayImage->mobile_image) }}" alt="{{ $todayImage->image_alt ?: $plainText($today->title) }}" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
                            @endif
                        </div>
                        <div class="text-center lg:col-span-5 lg:px-8">
                            <p class="mb-2 text-xs uppercase text-slate-500 sm:text-sm">{{ $plainText($today->subtitle) }}</p>
                            <h2 class="text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">{{ $plainText($today->title) }}</h2>
                            @if ($todayStats->isNotEmpty())
                                <div class="mt-7 grid grid-cols-3 gap-4">
                                    @foreach ($todayStats as $stat)
                                        <div class="border-t border-slate-300 pt-4">
                                            <strong class="block font-span text-3xl font-normal leading-none text-[#A88444] sm:text-4xl">{{ $stat['value'] ?? '' }}</strong>
                                            <span class="mt-2 block text-[10px] uppercase text-slate-500 sm:text-xs">{{ $stat['label'] ?? '' }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            <div class="mt-7 text-xs leading-relaxed text-gray-600 [&_p]:mb-6 [&_p:last-child]:mb-0 sm:text-sm">{!! $today->description !!}</div>
                            <div class="mt-8 grid gap-3 sm:grid-cols-3">
                                @foreach ($todayLinks as $link)
                                    <a href="{{ $link['url'] ?? '#' }}" class="flex min-h-28 flex-col justify-end border border-slate-200 p-4 text-left transition hover:border-[#A88444] hover:bg-[#F7F7F7]">
                                        <small class="text-[10px] font-medium uppercase text-slate-500">{{ $link['eyebrow'] ?? '' }}</small>
                                        <strong class="mt-2 font-span text-base font-normal uppercase text-slate-700">{{ $link['title'] ?? '' }}</strong>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            @if ($final)
                @php
                $finalImage = $final->images->first();
                $finalButtons = collect($final->items)->reject(
                    fn ($button) => strcasecmp((string) ($button['label'] ?? ''), 'Explore Nandini') === 0
                );
                @endphp
                <section class="relative flex h-[460px] items-center overflow-hidden bg-neutral-100 px-6 text-center text-white sm:h-[560px] lg:h-[760px]" data-gtm-section="booking_cta">
                    @if ($finalImage && $imageUrl($finalImage->image ?: $finalImage->mobile_image))
                        <img src="{{ $imageUrl($finalImage->image ?: $finalImage->mobile_image) }}" alt="{{ $finalImage->image_alt ?: $plainText($final->title) }}" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
                    @endif
                    <div class="absolute inset-0 bg-black/50"></div>
                    <div class="relative mx-auto max-w-3xl">
                        <p class="mb-2 text-xs uppercase text-white/90 sm:text-sm">{{ $plainText($final->subtitle) }}</p>
                        <h2 class="text-lg font-medium uppercase leading-snug text-white sm:text-xl">{!! nl2br(e($plainText($final->title))) !!}</h2>
                        <div class="mx-auto mt-3 max-w-2xl text-xs leading-relaxed text-white/90 [&_p]:mb-6 [&_p:last-child]:mb-0 sm:text-sm">{!! $final->description !!}</div>
                        <div class="mt-8 flex flex-wrap justify-center gap-3">
                            @foreach ($finalButtons as $button)
                                <a href="{{ $button['url'] ?? '#' }}" class="inline-flex items-center justify-center px-5 py-2.5 text-xs font-medium uppercase tracking-[0.08em] text-white transition sm:text-sm {{ $loop->first ? 'bg-[#A88444] hover:bg-[#B8945B]' : 'border border-white/70 bg-transparent hover:bg-white/10' }}">
                                    {{ $button['label'] ?? '' }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif
        </div>
    @endif
</x-layouts.app>
