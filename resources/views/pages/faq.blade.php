@push('meta')
<x-seo.json-ld :data="\App\Support\SeoStructuredData::page('https://nandinibali.com/faq', $page->title, $page->meta_description)" />
@php
$metaTitle = $page->meta_title ?: $page->title;

$metaDescription = $page->meta_description
    ?: \Illuminate\Support\Str::limit(strip_tags($page->description ?: $page->excerpt ?: ''), 160, '');

$metaImage = $page->hero_image ?: $page->hero_mobile_image ?: null;
@endphp

<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">

<meta name="author" content="Nandini Jungle by Hanging Gardens">
<link rel="canonical" href="{{ url()->current() }}">

<meta property="og:type" content="website">
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:site_name" content="Nandini Jungle by Hanging Gardens">

@if ($metaImage)
<meta property="og:image" content="{{ asset('storage/' . $metaImage) }}">
@endif

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
@endpush

<x-layouts.app>
    <x-heroes.image-hero :page="$page" />

    <section class="px-6 py-16 text-center md:py-20" data-gtm-section="introduction">
        <div class="mx-auto max-w-[760px]">
            <h1 class="text-xl font-medium uppercase leading-snug text-slate-700 sm:text-2xl">{{ $page->title }}</h1>
            <div class="mt-6 font-sans text-[15px] leading-[1.8] text-gray-600 sm:text-base [&_p+p]:mt-4">
                {!! $page->description !!}
            </div>
        </div>
        @if ($faqGroups)
        <nav class="mx-auto mt-9 flex max-w-[760px] flex-wrap justify-center gap-x-7 gap-y-3 font-sans text-sm text-slate-700" aria-label="FAQ topics">
            @foreach ($faqGroups as $group)
            <a href="#{{ $group['id'] }}" class="border-b border-[#A88444]/40 pb-1 transition-colors hover:border-[#A88444] hover:text-[#A88444] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#A88444]">{{ explode(' & ', $group['title'])[0] === 'Dining' ? 'Dining & Spa' : explode(' & ', $group['title'])[0] }}</a>
            @endforeach
        </nav>
        @endif
    </section>

    @if ($faqGroups)
    <div class="mx-auto max-w-[908px] space-y-16 px-6 pb-20 md:pb-24" data-gtm-section="faq">
        @foreach ($faqGroups as $group)
        <section id="{{ $group['id'] }}" class="scroll-mt-32" aria-labelledby="{{ $group['id'] }}-title">
            <h2 id="{{ $group['id'] }}-title" class="mb-7 text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">{{ $group['title'] }}</h2>
            <div class="space-y-3">
                @foreach ($group['items'] as $item)
                <details class="group rounded-sm border border-slate-200 bg-white transition-colors hover:border-[#A88444]/60 open:border-[#A88444]/60">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-5 p-5 font-sans text-base font-medium leading-relaxed text-slate-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#A88444] sm:p-6 sm:text-lg [&::-webkit-details-marker]:hidden">
                        <span>{{ $item['question'] }}</span>
                        <svg class="h-4 w-4 shrink-0 text-[#A88444]" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <path d="M2 8h12" stroke="currentColor" stroke-width="1.5" />
                            <path class="group-open:hidden" d="M8 2v12" stroke="currentColor" stroke-width="1.5" />
                        </svg>
                    </summary>
                    <div class="max-w-[760px] px-5 pb-6 font-sans text-[15px] leading-[1.8] text-gray-600 sm:px-6 sm:text-base [&_a]:text-slate-700 [&_a]:underline [&_a]:underline-offset-4 [&_a:hover]:text-[#A88444] [&_p+p]:mt-4">
                        {!! $item['answer'] !!}
                    </div>
                </details>
                @endforeach
            </div>
        </section>
        @endforeach
    </div>
    @endif

    @forelse ($sections as $section)
        @switch($section->section_key)
            @case('membership_faq_section')
                <x-sections.membership-faq-section :section="$section" contact-label="Contact" :contact-url="route('contact.index')" data-gtm-section="faq" />
                @break

            @case('intro_text_section')
                @if (! collect($faqGroups)->contains('section_id', $section->id))
                <x-sections.intro-text-section :section="$section" data-gtm-section="faq" />
                @endif
                @break

            @case('image_overlay_section')
                <x-sections.image-overlay-section :section="$section" />
                @break

            @case('contained_image_section')
                <x-sections.contained-image-section :section="$section" />
                @break

            @case('split_media_section')
                <x-sections.split-media-section :section="$section" :excerpt-only="false" image-span="8" text-span="4" />
                @break

            @case('split_media_reverse')
                <x-sections.split-media-section :section="$section" :reverse="true" :excerpt-only="false" image-span="8" text-span="4" />
                @break

            @case('three_images_section')
                <x-sections.three-images-section :section="$section" />
                @break

            @case('two_images_section')
                <x-sections.two-images-section :section="$section" />
                @break

            @case('two_images_reverse')
                <x-sections.two-images-section :section="$section" :reverse="true" />
                @break

            @default
                @if (app()->environment('local'))
                    <div class="px-6 py-4 text-red-600">
                        Unknown section key: {{ $section->section_key }}
                    </div>
                @endif
        @endswitch
    @empty
        @if (app()->environment('local'))
            <div class="px-6 py-4 text-red-600">
                No active page sections found for this page.
            </div>
        @endif
    @endforelse
</x-layouts.app>
