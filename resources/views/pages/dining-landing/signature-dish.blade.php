@php
    $title = $dish->meta_title;
    $description = $dish->meta_description;
    $body = trim((string) $dish->content);
@endphp

@push('meta')
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @if ($dish->image_url)<meta property="og:image" content="{{ $dish->image_url }}">@endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $description }}">
@endpush

@push('css')
    <link rel="preload" href="{{ asset('fonts/Span-Regular.otf') }}" as="font" type="font/otf" crossorigin>
@endpush

<x-layouts.app>
    <main>
        <section class="bg-[#faf9f6] px-6 pb-14 pt-32 font-sans md:px-10 md:pb-20 md:pt-40 2xl:px-14" aria-labelledby="signature-dish-title">
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
            <section class="bg-white px-6 py-14 font-sans md:py-20" aria-labelledby="signature-dish-content-title">
                <div class="mx-auto max-w-3xl">
                    <p class="mb-3 text-[10px] font-medium uppercase tracking-[.18em] text-[#A88444] sm:text-xs">About the Dish</p>
                    <h2 id="signature-dish-content-title" class="sr-only">About {{ $dish->name }}</h2>
                    <div class="prose prose-slate max-w-none text-sm leading-relaxed text-slate-600 [&_p]:mb-5 [&_p:last-child]:mb-0">{!! $body !!}</div>
                </div>
            </section>
        @endif

        @if ($sections->isNotEmpty())
            <section class="bg-white px-6 py-14 font-sans md:px-10 md:py-20" aria-label="Signature dish content">
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
    </main>
</x-layouts.app>
