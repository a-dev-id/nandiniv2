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
    <x-heroes.image-hero :page="$page" />

    <x-sections.page-description :page="$page" />

    @if ($awards->count() > 0)
    <section class="px-6 pb-16 md:pb-24">
        <div class="mx-auto max-w-7xl">
            @foreach ($awards as $award)
            @php
            $image = $award->card_image
            ?? $award->hero_image
            ?? $award->hero_mobile_image
            ?? null;

            $imageAlt = $award->card_image_alt
            ?? $award->hero_image_alt
            ?? $award->title
            ?? 'Award image';

            $url = '#';

            if (\Illuminate\Support\Facades\Route::has('awards.show')) {
            $url = route('awards.show', $award->slug);
            } elseif (! empty($award->button_url)) {
            $url = $award->button_url;
            }

            $descriptionRaw = $award->description ?: $award->excerpt ?: '';

            $descriptionText = html_entity_decode(
            strip_tags((string) $descriptionRaw),
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
            );

            $descriptionText = str_replace("\xc2\xa0", ' ', $descriptionText);
            $descriptionText = preg_replace('/\s+/', ' ', $descriptionText);
            $descriptionText = trim((string) $descriptionText);
            @endphp

            <article class="border-b border-slate-200 py-10 md:py-12">
                <h2 class="mb-8 text-2xl sm:text-3xl md:text-3xl leading-snug font-medium uppercase tracking-[0.12em] text-slate-800">
                    {{ $award->title }}
                </h2>

                <div class="flex flex-col gap-6 md:flex-row md:items-start md:gap-8">
                    <div class="shrink-0">
                        @if ($image)
                        <img src="{{ asset('storage/' . $image) }}" alt="{{ $imageAlt }}" class="block object-contain" style="width: 128px; height: 128px; object-fit: contain;" loading="lazy">
                        @endif
                    </div>

                    <div class="flex min-w-0 flex-1 flex-col">
                        @if ($descriptionText !== '')
                        <p class="text-[15px] leading-7 text-slate-700">
                            {{ $descriptionText }}
                        </p>
                        @endif

                    </div>
                </div>
            </article>
            @endforeach

            @if ($awards->hasPages())
            <div class="mt-14">
                {{ $awards->links() }}
            </div>
            @endif
        </div>
    </section>
    @endif
</x-layouts.app>
