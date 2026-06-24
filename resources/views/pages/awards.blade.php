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
                <h2 class="mb-5 text-lg leading-snug font-medium uppercase text-slate-700 sm:text-xl">
                    {{ $award->title }}
                </h2>

                <div class="flex flex-col gap-6 md:flex-row md:items-start md:gap-8">
                    <div class="flex h-36 w-40 shrink-0 items-center justify-center">
                        @if ($image)
                        <img src="{{ asset('storage/' . $image) }}" alt="{{ $imageAlt }}" class="block max-h-36 max-w-40 object-contain" loading="lazy">
                        @endif
                    </div>

                    <div class="min-w-0 flex-1">
                        @if ($descriptionText !== '')
                        <p class="text-xs leading-7 text-slate-700 sm:text-sm">
                            {{ $descriptionText }}
                        </p>
                        @endif

                    </div>
                </div>
            </article>
            @endforeach

            @if ($awards->hasPages())
            <div class="mt-14">
                <nav role="navigation" aria-label="Pagination Navigation">
                    <div class="hidden sm:flex-1 sm:flex sm:gap-2 sm:items-center sm:justify-between">
                        <div>
                            <p class="text-xs text-gray-700 leading-5 sm:text-sm">
                                Showing
                                <span class="font-medium">{{ $awards->firstItem() }}</span>
                                to
                                <span class="font-medium">{{ $awards->lastItem() }}</span>
                                of
                                <span class="font-medium">{{ $awards->total() }}</span>
                                results
                            </p>
                        </div>

                        <div>
                            <span class="inline-flex rtl:flex-row-reverse shadow-sm rounded-md">
                                @php
                                $awardPageUrl = fn(int $page): string => $page === 1
                                ? route('awards.index')
                                : route('awards.page', ['page' => $page]);
                                @endphp

                                @if ($awards->onFirstPage())
                                <span aria-disabled="true" aria-label="Previous">
                                    <span class="inline-flex items-center px-2 py-2 text-xs font-medium text-gray-500 bg-white border border-gray-300 cursor-not-allowed rounded-l-md leading-5 sm:text-sm" aria-hidden="true">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                </span>
                                @else
                                <a href="{{ $awardPageUrl($awards->currentPage() - 1) }}" rel="prev" class="inline-flex items-center px-2 py-2 text-xs font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md leading-5 transition duration-150 sm:text-sm" aria-label="Previous">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                                @endif

                                @for ($pageNumber = 1; $pageNumber <= $awards->lastPage(); $pageNumber++)
                                    @if ($pageNumber === $awards->currentPage())
                                    <span aria-current="page">
                                        <span class="inline-flex items-center px-4 py-2 -ml-px text-xs font-medium bg-[#A67C3D] border border-[#A67C3D] text-white cursor-default leading-5 sm:text-sm">{{ $pageNumber }}</span>
                                    </span>
                                    @else
                                    <a href="{{ $awardPageUrl($pageNumber) }}" class="inline-flex items-center px-4 py-2 -ml-px text-xs font-medium text-gray-700 bg-white border border-gray-300 leading-5 transition duration-150 sm:text-sm" aria-label="Go to page {{ $pageNumber }}">
                                        {{ $pageNumber }}
                                    </a>
                                    @endif
                                    @endfor

                                    @if ($awards->hasMorePages())
                                    <a href="{{ $awardPageUrl($awards->currentPage() + 1) }}" rel="next" class="inline-flex items-center px-2 py-2 -ml-px text-xs font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md leading-5 transition duration-150 sm:text-sm" aria-label="Next">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                    @else
                                    <span aria-disabled="true" aria-label="Next">
                                        <span class="inline-flex items-center px-2 py-2 -ml-px text-xs font-medium text-gray-500 bg-white border border-gray-300 cursor-not-allowed rounded-r-md leading-5 sm:text-sm" aria-hidden="true">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </span>
                                    @endif
                            </span>
                        </div>
                    </div>
                </nav>
            </div>
            @endif
        </div>
    </section>
    @endif
</x-layouts.app>
