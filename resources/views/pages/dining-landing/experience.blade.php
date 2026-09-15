@php
    $title = $experience['title'].' | Nandini Jungle Dining';
    $description = $cmsExperience?->meta_description;
    $body = trim((string) $cmsExperience?->description);
@endphp

@push('meta')
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <meta name="author" content="Nandini Jungle by Hanging Gardens">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="Nandini Jungle by Hanging Gardens">
    @if ($image)
        <meta property="og:image" content="{{ $image }}">
        <meta name="twitter:image" content="{{ $image }}">
    @endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $description }}">
@endpush

@push('css')
    <link rel="preload" href="{{ asset('fonts/Span-Regular.otf') }}" as="font" type="font/otf" crossorigin>
@endpush

<x-layouts.app>
    <main>
        <section class="relative isolate min-h-[560px] overflow-hidden bg-[#1a3028] md:min-h-[680px]" aria-label="{{ $experience['title'] }}">
            @if ($image)
                <img src="{{ $image }}" alt="{{ $experience['alt'] }}" class="absolute inset-0 -z-20 h-full w-full object-cover object-center" width="1920" height="1080" fetchpriority="high" decoding="async">
            @endif
        </section>

        <section class="bg-white px-6 py-14 font-sans md:py-20" aria-labelledby="dining-experience-overview-title">
            <div class="mx-auto grid max-w-7xl gap-8 lg:grid-cols-[minmax(0,40fr)_minmax(0,60fr)] lg:gap-16">
                <div>
                    <p class="mb-3 text-[10px] font-medium tracking-[.18em] text-[#A88444] uppercase sm:text-xs">{{ $cmsExperience?->intro_eyebrow }}</p>
                    <h2 id="dining-experience-overview-title" class="text-lg leading-snug font-medium text-slate-700 uppercase sm:text-xl">{{ $cmsExperience?->page_heading }}</h2>
                </div>

                <div>
                    @if ($body !== '')
                        <div class="prose prose-slate max-w-none text-sm leading-relaxed text-slate-600 [&_p]:mb-5 [&_p:last-child]:mb-0">{!! $body !!}</div>
                    @endif

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        @if ($cmsExperience?->menu_url && $cmsExperience?->menu_cta_label)
                            <x-buttons.link-button :href="$cmsExperience->menu_url" variant="outline" target="_blank" rel="noopener" class="w-full sm:w-auto">{{ $cmsExperience->menu_cta_label }}</x-buttons.link-button>
                        @endif
                        @if ($cmsExperience?->reservation_url && $cmsExperience?->reservation_cta_label)
                            <x-buttons.link-button :href="$cmsExperience->reservation_url" variant="solid" class="w-full sm:w-auto">{{ $cmsExperience->reservation_cta_label }}</x-buttons.link-button>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        @if ($cmsExperience?->gallery?->isNotEmpty())
            <section class="bg-white px-6 pb-14 font-sans md:pb-20" aria-label="{{ $experience['title'] }} gallery">
                <div class="mx-auto grid max-w-7xl gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($cmsExperience->gallery as $galleryImage)
                        <figure>
                            <div class="aspect-[4/3] overflow-hidden bg-[#f3f4f5]">
                                <img src="{{ asset('storage/'.$galleryImage->image) }}" alt="{{ $galleryImage->image_alt }}" class="h-full w-full object-cover" loading="lazy" decoding="async">
                            </div>
                            @if ($galleryImage->caption)<figcaption class="mt-2 text-xs text-slate-600">{{ $galleryImage->caption }}</figcaption>@endif
                        </figure>
                    @endforeach
                </div>
            </section>
        @endif

        <x-dining-landing.experiences
            :exclude="$experience['slug']"
            :settings="$diningSettings"
            heading-id="related-dining-experiences-title"
            background-class="bg-[#f3f4f5]"
        />

        <x-dining-landing.reservation-cta :settings="$diningSettings" />
    </main>
</x-layouts.app>
