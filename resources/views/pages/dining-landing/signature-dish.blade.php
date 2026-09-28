@php
    $title = $dish->meta_title ?: $dish->name.' | Nandini Jungle Dining';
    $description = $dish->meta_description ?: $dish->short_description;
    $body = trim((string) $dish->content);
    $detail = $dish->detail;
    $hasStructuredDetail = $detail !== [];
    $heroImage = $hasStructuredDetail
        ? $dish->resolveImageUrl($detail['hero_image'] ?? null)
        : $dish->image_url;
@endphp

@push('meta')
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @if ($heroImage)<meta property="og:image" content="{{ $heroImage }}">@endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $description }}">
    @if ($heroImage)<meta name="twitter:image" content="{{ $heroImage }}">@endif
@endpush

@push('css')
    <link rel="preload" href="{{ asset('fonts/Span-Regular.otf') }}" as="font" type="font/otf" crossorigin>
@endpush

<x-layouts.app>
    @if ($hasStructuredDetail)
        <header class="shadow-xl" data-gtm-section="hero">
            <div class="relative aspect-[4/3] w-full overflow-hidden bg-slate-100 lg:aspect-auto lg:h-[70vh]">
            @if ($heroImage)
                    <img src="{{ $heroImage }}" alt="{{ $detail['hero_image_alt'] ?? '' }}" class="absolute inset-0 h-full w-full object-cover object-center" width="1920" height="1080" loading="eager" fetchpriority="high" decoding="async">
            @endif
            </div>
        </header>

        <section class="bg-white px-6 py-14 text-center font-sans md:px-10 md:py-20 2xl:px-14" aria-labelledby="signature-dish-title" data-gtm-section="overview">
            <div class="mx-auto max-w-5xl">
                <h1 id="signature-dish-title" class="text-xl font-medium uppercase text-slate-700 sm:text-2xl">{{ $detail['hero_title'] ?? $dish->name }}</h1>
                @if (filled($detail['hero_description'] ?? null))
                    <p class="mx-auto mt-3 max-w-4xl text-xs leading-relaxed text-gray-600 sm:text-sm">{{ $detail['hero_description'] }}</p>
                @endif
            </div>
        </section>

        @if ($detail['story_visible'] ?? true)
            @php($storyImage = $dish->resolveImageUrl($detail['story_image'] ?? null))
            <section id="story" class="scroll-mt-20 bg-white px-6 py-14 font-sans md:px-10 md:py-20 2xl:px-14" aria-labelledby="signature-dish-story-title" data-gtm-section="story">
                <div class="mx-auto grid max-w-7xl items-center gap-10 lg:grid-cols-2 lg:gap-16">
                    <div class="aspect-[4/3] overflow-hidden bg-[#f3f4f5]">
                        @if ($storyImage)
                            <img src="{{ $storyImage }}" alt="{{ $detail['story_image_alt'] ?? '' }}" class="h-full w-full object-cover" width="1200" height="900" loading="lazy" decoding="async">
                        @endif
                    </div>
                    <div>
                        @if (filled($detail['story_eyebrow'] ?? null))
                            <p class="mb-3 text-[10px] font-medium uppercase tracking-[.18em] text-[#A88444] sm:text-xs">{{ $detail['story_eyebrow'] }}</p>
                        @endif
                        <h2 id="signature-dish-story-title" class="text-2xl leading-tight text-slate-800 [--heading-letter-spacing:.08em] sm:text-3xl">{{ $detail['story_title'] ?? '' }}</h2>
                        @if (filled($detail['story_description'] ?? null))
                            <div class="blog-detail-content mt-5 text-slate-600">{!! $detail['story_description'] !!}</div>
                        @endif
                        @if (filled($detail['story_quote'] ?? null))
                            <blockquote class="mt-7 border-l-2 border-[#A88444] pl-5 font-span text-xl leading-relaxed text-[#8f6b34]">“{{ $detail['story_quote'] }}”</blockquote>
                        @endif
                    </div>
                </div>
            </section>
        @endif

        @if (($detail['highlights_visible'] ?? true) && ! empty($detail['highlights']))
            <section id="experience" class="scroll-mt-20 bg-[#f3f4f5] px-6 py-14 font-sans md:px-10 md:py-20 2xl:px-14" aria-labelledby="signature-dish-highlights-title" data-gtm-section="signature_experiences">
                <div class="mx-auto max-w-7xl text-center">
                    @if (filled($detail['highlights_eyebrow'] ?? null))
                        <p class="mb-3 text-[10px] font-medium uppercase tracking-[.18em] text-[#A88444] sm:text-xs">{{ $detail['highlights_eyebrow'] }}</p>
                    @endif
                    <h2 id="signature-dish-highlights-title" class="mx-auto max-w-3xl text-2xl leading-tight text-slate-800 [--heading-letter-spacing:.08em] sm:text-3xl">{{ $detail['highlights_title'] ?? '' }}</h2>
                    <ol class="mt-10 grid gap-6 text-left sm:grid-cols-2 lg:mt-12 lg:grid-cols-5">
                        @foreach ($detail['highlights'] as $highlight)
                            <li class="border-t border-[#A88444]/40 pt-5">
                                <span class="text-xs font-medium tracking-[.12em] text-[#A88444]">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                <h3 class="mt-3 font-sans text-xs uppercase leading-snug text-slate-800 [--heading-letter-spacing:.08em]">{{ $highlight['title'] ?? '' }}</h3>
                                <p class="mt-3 text-xs leading-relaxed text-slate-600 sm:text-sm">{{ $highlight['description'] ?? '' }}</p>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </section>
        @endif

        @if (($detail['components_visible'] ?? true) && ! empty($detail['components']))
            <section id="components" class="scroll-mt-20 bg-white px-6 py-14 font-sans md:px-10 md:py-20 2xl:px-14" aria-labelledby="signature-dish-components-title">
                <div class="mx-auto max-w-7xl">
                    <div class="mx-auto max-w-3xl text-center">
                        @if (filled($detail['components_eyebrow'] ?? null))
                            <p class="mb-3 text-[10px] font-medium uppercase tracking-[.18em] text-[#A88444] sm:text-xs">{{ $detail['components_eyebrow'] }}</p>
                        @endif
                        <h2 id="signature-dish-components-title" class="text-2xl leading-tight text-slate-800 [--heading-letter-spacing:.08em] sm:text-3xl">{{ $detail['components_title'] ?? '' }}</h2>
                        @if (filled($detail['components_description'] ?? null))
                            <p class="mt-4 text-sm leading-relaxed text-slate-600">{{ $detail['components_description'] }}</p>
                        @endif
                    </div>

                    <div class="mt-10 grid gap-x-6 gap-y-10 sm:grid-cols-2 lg:mt-12 lg:grid-cols-4">
                        @foreach ($detail['components'] as $component)
                            @php($componentImage = $dish->resolveImageUrl($component['image'] ?? null))
                            <article>
                                <div class="aspect-square overflow-hidden bg-[#f3f4f5]">
                                    @if ($componentImage)
                                        <img src="{{ $componentImage }}" alt="{{ $component['image_alt'] ?? '' }}" class="h-full w-full object-cover transition duration-500 hover:scale-[1.03]" width="1000" height="1000" loading="lazy" decoding="async">
                                    @endif
                                </div>
                                <h3 class="mt-5 font-sans text-sm uppercase leading-snug text-slate-800 [--heading-letter-spacing:.08em]">{{ $component['title'] ?? '' }}</h3>
                                <p class="mt-2 text-xs leading-relaxed text-slate-600 sm:text-sm">{{ $component['description'] ?? '' }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        @if ($detail['premium_visible'] ?? true)
            @php($premiumImage = $dish->resolveImageUrl($detail['premium_image'] ?? null))
            <section class="bg-[#f3f4f5] px-6 py-14 font-sans md:px-10 md:py-20 2xl:px-14" aria-labelledby="signature-dish-premium-title" data-gtm-section="premium_ingredients">
                <div class="mx-auto grid max-w-7xl items-center gap-10 lg:grid-cols-[minmax(0,45fr)_minmax(0,55fr)] lg:gap-16">
                    <div>
                        @if (filled($detail['premium_eyebrow'] ?? null))
                            <p class="mb-3 text-[10px] font-medium uppercase tracking-[.18em] text-[#A88444] sm:text-xs">{{ $detail['premium_eyebrow'] }}</p>
                        @endif
                        <h2 id="signature-dish-premium-title" class="text-2xl leading-tight text-slate-800 [--heading-letter-spacing:.08em] sm:text-3xl">{{ $detail['premium_title'] ?? '' }}</h2>
                        @if (filled($detail['premium_description'] ?? null))
                            <div class="blog-detail-content mt-5 text-slate-600">{!! $detail['premium_description'] !!}</div>
                        @endif
                    </div>
                    <div class="aspect-[4/3] overflow-hidden bg-white">
                        @if ($premiumImage)
                            <img src="{{ $premiumImage }}" alt="{{ $detail['premium_image_alt'] ?? '' }}" class="h-full w-full object-cover" width="1200" height="900" loading="lazy" decoding="async">
                        @endif
                    </div>
                </div>
            </section>
        @endif

        @if ($sections->isNotEmpty())
            <section class="bg-white px-6 py-14 font-sans md:px-10 md:py-20" aria-label="Additional signature dish content" data-gtm-section="related_content">
                <div class="mx-auto max-w-5xl">
                    @foreach ($sections as $section)
                        @if ($section->section_key === 'intro_text_section')
                            <x-sections.blog-content-section :section="$section" layout="text" />
                        @elseif ($section->section_key === 'contained_image_section')
                            <x-sections.blog-content-section :section="$section" layout="stacked" />
                        @elseif ($section->section_key === 'split_media_section')
                            <x-sections.blog-content-section :section="$section" layout="split" />
                        @elseif ($section->section_key === 'split_media_reverse')
                            <x-sections.blog-content-section :section="$section" layout="split" reverse />
                        @endif
                    @endforeach
                </div>
            </section>
        @endif

        @if ($detail['reservation_visible'] ?? true)
            @php($reservationImage = $dish->resolveImageUrl($detail['reservation_image'] ?? null))
            <section id="reserve" class="relative isolate flex min-h-[460px] scroll-mt-20 items-center overflow-hidden bg-[#142c24] px-6 py-16 font-sans text-white" aria-labelledby="signature-dish-reservation-title" data-gtm-section="booking_cta">
                @if ($reservationImage)
                    <img src="{{ $reservationImage }}" alt="{{ $detail['reservation_image_alt'] ?? '' }}" class="absolute inset-0 -z-20 h-full w-full object-cover" width="1920" height="900" loading="lazy" decoding="async">
                @endif
                <div class="absolute inset-0 -z-10 bg-black/55" aria-hidden="true"></div>
                <div class="mx-auto w-full max-w-[760px] text-center">
                    @if (filled($detail['reservation_eyebrow'] ?? null))
                        <p class="mb-3 text-[10px] font-medium uppercase tracking-[.18em] text-[#C7A263] sm:text-xs">{{ $detail['reservation_eyebrow'] }}</p>
                    @endif
                    <h2 id="signature-dish-reservation-title" class="text-2xl leading-tight text-white [--heading-letter-spacing:.08em] sm:text-3xl">{{ $detail['reservation_title'] ?? '' }}</h2>
                    @if (filled($detail['reservation_description'] ?? null))
                        <p class="mx-auto mt-5 max-w-2xl text-sm leading-relaxed text-white/85">{{ $detail['reservation_description'] }}</p>
                    @endif
                    @if (filled($detail['reservation_button_label'] ?? null) && filled($detail['reservation_button_url'] ?? null))
                        <div class="mt-8">
                            <x-buttons.link-button :href="$detail['reservation_button_url']" variant="solid" class="w-full sm:w-auto">{{ $detail['reservation_button_label'] }}</x-buttons.link-button>
                        </div>
                    @endif
                </div>
            </section>
        @endif
    @else
        <section class="bg-[#faf9f6] px-6 pb-14 pt-32 font-sans md:px-10 md:pb-20 md:pt-40 2xl:px-14" aria-labelledby="signature-dish-title" data-gtm-section="hero">
            <div class="mx-auto grid max-w-7xl items-center gap-10 md:grid-cols-2 md:gap-14">
                <div class="aspect-[4/3] overflow-hidden bg-[#f3f4f5] md:order-2">
                    @if ($dish->image_url)
                        <img src="{{ $dish->image_url }}" alt="{{ $dish->image_alt }}" class="h-full w-full object-cover" width="1200" height="900" fetchpriority="high" decoding="async">
                    @endif
                </div>
                <div class="md:order-1">
                    @if (filled($dish->eyebrow))<p class="mb-3 text-[10px] font-medium uppercase tracking-[.18em] text-[#A88444] sm:text-xs">{{ $dish->eyebrow }}</p>@endif
                    <h1 id="signature-dish-title" class="font-serif text-4xl leading-tight text-slate-800 sm:text-5xl">{{ $dish->name }}</h1>
                    @if (filled($dish->price))<p class="mt-3 text-xl font-semibold text-slate-800">{{ $dish->price }}</p>@endif
                    @if (filled($dish->short_description))<p class="mt-5 max-w-xl text-sm leading-relaxed text-slate-600 sm:text-base">{{ $dish->short_description }}</p>@endif
                    @if (filled($diningSettings?->reservation_cta_label) && filled($diningSettings?->reservation_cta_url))
                        <div class="mt-8"><x-buttons.link-button :href="$diningSettings->reservation_cta_url" variant="solid">{{ $diningSettings->reservation_cta_label }}</x-buttons.link-button></div>
                    @endif
                </div>
            </div>
        </section>

        @if ($body !== '')
            <section class="bg-white px-6 py-14 font-sans md:py-20" aria-labelledby="signature-dish-content-title" data-gtm-section="overview">
                <div class="mx-auto max-w-3xl">
                    <p class="mb-3 text-[10px] font-medium uppercase tracking-[.18em] text-[#A88444] sm:text-xs">About the Dish</p>
                    <h2 id="signature-dish-content-title" class="sr-only">About {{ $dish->name }}</h2>
                    <div class="prose prose-slate max-w-none text-sm leading-relaxed text-slate-600 [&_p]:mb-5 [&_p:last-child]:mb-0">{!! $body !!}</div>
                </div>
            </section>
        @endif

        @if ($sections->isNotEmpty())
            <section class="bg-white px-6 py-14 font-sans md:px-10 md:py-20" aria-label="Signature dish content" data-gtm-section="related_content">
                <div class="mx-auto max-w-5xl">
                    @foreach ($sections as $section)
                        @if ($section->section_key === 'intro_text_section')
                            <x-sections.blog-content-section :section="$section" layout="text" />
                        @elseif ($section->section_key === 'contained_image_section')
                            <x-sections.blog-content-section :section="$section" layout="stacked" />
                        @elseif ($section->section_key === 'split_media_section')
                            <x-sections.blog-content-section :section="$section" layout="split" />
                        @elseif ($section->section_key === 'split_media_reverse')
                            <x-sections.blog-content-section :section="$section" layout="split" reverse />
                        @endif
                    @endforeach
                </div>
            </section>
        @endif

        <x-dining-landing.reservation-cta :settings="$diningSettings" />
    @endif
</x-layouts.app>
