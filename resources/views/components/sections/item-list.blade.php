@props([
'model' => 'offer', // offer | experience | blog | news | gallery
'items' => null,
'withFilter' => false,
'limit' => 99,
'routeName' => null,
'activeCategory' => null,
'hiddenCategories' => [],
])

@php
use App\Models\Offer;
use App\Models\Experience;
use App\Models\BlogNews;
use App\Models\Gallery;
use Illuminate\Support\Facades\Route;

$active = $activeCategory ?: request()->route('categorySlug') ?: request('category', 'all');
$hiddenCategories = collect($hiddenCategories)->filter()->values();

if ($hiddenCategories->contains($active)) {
$active = 'all';
}

$modelClass = match ($model) {
'experience' => Experience::class,
'blog', 'news' => BlogNews::class,
'gallery' => Gallery::class,
default => Offer::class,
};

$buttonText = 'MORE DETAILS';

$isPaginated = $items instanceof \Illuminate\Contracts\Pagination\Paginator;
$paginator = $isPaginated ? $items : null;
$showMoreStep = 9;

/*
|--------------------------------------------------------------------------
| Use Passed Items If Available
|--------------------------------------------------------------------------
*/
$providedItems = $items !== null
? collect($isPaginated ? $items->items() : $items)
: null;

/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/
$filters = [
'all' => 'All',
];

if ($model === 'experience' && $withFilter) {
$experienceCategoryLabels = [
'jungle-romance' => 'Romance',
'signature-dining-experiences' => 'Dining',
'jungle-wellness-spa-rituals' => 'Wellness',
'ubud-jungle-adventures' => 'Adventure',
'curated-experience-packages' => 'Activity Package',
];

if ($providedItems) {
$experienceCategories = $providedItems
->pluck('category')
->filter()
->reject(function ($category) use ($hiddenCategories) {
return $hiddenCategories->contains($category->slug ?? null);
})
->unique('slug')
->sortBy('sort_order');
} else {
$experienceCategories = Experience::query()
->where('is_active', true)
->whereHas('category', function ($query) use ($hiddenCategories) {
if ($hiddenCategories->isNotEmpty()) {
$query->whereNotIn('slug', $hiddenCategories->all());
}
})
->with('category')
->get()
->pluck('category')
->filter()
->unique('slug')
->sortBy('sort_order');
}

foreach ($experienceCategories as $category) {
$filters[$category->slug] = $experienceCategoryLabels[$category->slug]
?? $category->name
?? $category->title
?? str($category->slug)->replace('-', ' ')->title()->toString();
}
}

if ($model === 'gallery' && $withFilter) {
$galleryCategoryLabels = [
'resort' => 'Resort',
'pool' => 'Pool',
'jungle-villas' => 'Jungle Villas',
'the-villa' => 'Jungle Villas',
'the-royal-suites' => 'The Royal Suites',
'the-suites' => 'The Royal Suites',
];

$galleryCategoryOrder = [
'resort',
'pool',
'jungle-villas',
'the-villa',
'the-royal-suites',
'the-suites',
];

if ($providedItems) {
$galleryCategories = $providedItems
->pluck('category')
->filter()
->unique()
->values();
} else {
$galleryCategories = Gallery::query()
->published()
->whereNotNull('category')
->where('category', '!=', '')
->pluck('category')
->unique()
->values();
}

$galleryCategories = $galleryCategories
->sortBy(function ($category) use ($galleryCategoryOrder) {
$index = array_search($category, $galleryCategoryOrder, true);

return $index === false ? 999 : $index;
})
->values();

foreach ($galleryCategories as $category) {
$filters[$category] = $galleryCategoryLabels[$category]
?? str($category)->replace('-', ' ')->title()->toString();
}
}

/*
|--------------------------------------------------------------------------
| Main Items
|--------------------------------------------------------------------------
*/
if ($providedItems) {
$items = $providedItems
->when($model === 'experience' && $hiddenCategories->isNotEmpty(), function ($collection) use ($hiddenCategories) {
return $collection->reject(function ($item) use ($hiddenCategories) {
return $item->category && $hiddenCategories->contains($item->category->slug);
});
})
->when($model === 'experience' && $withFilter && $active !== 'all', function ($collection) use ($active) {
return $collection->filter(function ($item) use ($active) {
return $item->category && $item->category->slug === $active;
});
})
->when($model === 'gallery' && $withFilter && $active !== 'all', function ($collection) use ($active) {
return $collection->filter(function ($item) use ($active) {
return $item->category === $active;
});
})
->take($limit)
->values();
} else {
$query = $modelClass::query();

if ($modelClass === Offer::class) {
$query->published();
} elseif ($modelClass === BlogNews::class) {
$query->published();

if ($model === 'blog') {
$query->blog();
}

if ($model === 'news') {
$query->news();
}
} elseif ($modelClass === Gallery::class) {
$query->published();

if ($withFilter && $active !== 'all') {
$query->where('category', $active);
}
} else {
$query
->where('is_active', true)
->with('category');

if ($hiddenCategories->isNotEmpty()) {
$query->whereDoesntHave('category', function ($query) use ($hiddenCategories) {
$query->whereIn('slug', $hiddenCategories->all());
});
}
}

if ($model === 'experience' && $withFilter && $active !== 'all') {
$query->whereHas('category', function ($query) use ($active) {
$query->where('slug', $active);
});
}

$items = $query
->orderByRaw('sort_order IS NULL, sort_order ASC')
->orderBy('sort_order')
->orderByDesc('id')
->limit($limit)
->get();
}

$useShowMore = $model === 'experience'
&& $withFilter
&& $active === 'all'
&& $items->count() > $showMoreStep;
@endphp

<section class="pb-16 md:pb-28">
    <div class="mx-auto px-3 lg:px-16" @if ($useShowMore) x-data="{ visibleCount: {{ $showMoreStep }} }" @endif>

        {{-- FILTER --}}
        @if ($withFilter && count($filters) > 1)
        <div class="mb-10 flex flex-wrap justify-center gap-2.5">
            @foreach ($filters as $key => $label)
            @php
            $filterUrl = $key === 'all'
            ? request()->url()
            : request()->fullUrlWithQuery(['category' => $key]);

            if ($model === 'experience') {
            $filterUrl = $key === 'all'
            ? route('experiences.index')
            : route('experiences.category', ['categorySlug' => $key]);
            }
            @endphp

            <a href="{{ $filterUrl }}" class="border px-3 py-1.5 text-[11px] uppercase transition sm:text-sm {{ $active === $key ? 'border-[#A88444] bg-[#A88444] text-white' : 'border-black/15 bg-white text-slate-700 hover:border-black/40' }} tracking-[0.08em] font-medium">
                {{ $label }}
            </a>
            @endforeach
        </div>
        @endif

        {{-- GRID --}}
        <div
            @if ($model === 'gallery')
            x-data="{
                activeIndex: null,
                images: [],
                open(index) {
                    this.images = Array.from($el.querySelectorAll('[data-gallery-lightbox-item]')).map((item) => ({
                        src: item.dataset.gallerySrc,
                        alt: item.dataset.galleryAlt || 'Gallery image',
                    }));
                    this.activeIndex = index;
                    document.body.classList.add('overflow-hidden');
                },
                close() {
                    this.activeIndex = null;
                    document.body.classList.remove('overflow-hidden');
                },
                previous() {
                    if (! this.images.length) return;
                    this.activeIndex = (this.activeIndex - 1 + this.images.length) % this.images.length;
                },
                next() {
                    if (! this.images.length) return;
                    this.activeIndex = (this.activeIndex + 1) % this.images.length;
                },
            }"
            x-on:keydown.escape.window="close()"
            x-on:keydown.arrow-left.window="activeIndex !== null && previous()"
            x-on:keydown.arrow-right.window="activeIndex !== null && next()"
            @endif
        >
        <div class="{{ $model === 'gallery' ? 'columns-1 gap-4 md:columns-2 xl:columns-3' : 'grid grid-cols-1 gap-x-10 gap-y-14 md:grid-cols-2 xl:grid-cols-3' }}">
            @forelse ($items as $item)
            @php
            if ($model === 'gallery') {
            $desktopImage = $item->image ?? $item->mobile_image ?? null;
            $mobileImage = $item->mobile_image ?? $item->image ?? null;

            $desktopAlt = $item->image_alt
            ?? $item->mobile_image_alt
            ?? $item->title
            ?? 'Gallery image';
            } else {
            $desktopImage = $item->card_image
            ?? $item->hero_image
            ?? $item->hero_mobile_image
            ?? null;

            $mobileImage = $item->hero_mobile_image
            ?? $item->hero_image
            ?? $item->card_image
            ?? null;

            $desktopAlt = $item->card_image_alt
            ?? $item->hero_image_alt
            ?? $item->hero_mobile_image_alt
            ?? $item->title
            ?? 'Image';
            }

            $url = '#';

            if ($routeName && Route::has($routeName)) {
            $url = route($routeName, $item->slug);
            } elseif ($model === 'offer' && Route::has('offers.show')) {
            $url = route('offers.show', $item->slug);
            } elseif ($model === 'experience' && Route::has('experiences.show')) {
            $url = route('experiences.show', $item->slug);
            } elseif ($model === 'blog' && Route::has('blog.show')) {
            $url = route('blog.show', $item->slug);
            } elseif ($model === 'news' && Route::has('news.show')) {
            $url = route('news.show', $item->slug);
            }

            $reserveUrl = $model === 'offer'
            ? $item->resolved_button_url
            : null;

            $reserveLabel = $model === 'offer'
            ? ($item->button_label ?: 'Reserve')
            : null;

            $usesOfferCardLayout = in_array($model, ['offer', 'experience'], true);

            @endphp

            <article class="{{ $model === 'gallery' ? 'mb-4 break-inside-avoid' : 'flex' }}" @if ($useShowMore) x-show="{{ $loop->index }} < visibleCount" @endif>
                <div class="{{ $model === 'gallery' ? 'w-full' : 'flex h-full w-full flex-col' }} {{ $usesOfferCardLayout ? 'border border-slate-200 bg-white' : '' }}">

                    @if ($model === 'gallery')
                    <button
                        type="button"
                        data-gallery-lightbox-item
                        data-gallery-src="{{ asset('storage/' . ($desktopImage ?: $mobileImage)) }}"
                        data-gallery-alt="{{ $desktopAlt }}"
                        class="group block w-full overflow-hidden bg-slate-100 text-left"
                        x-on:click="open({{ $loop->index }})"
                        aria-label="Open {{ $desktopAlt }}"
                    >
                        @if ($desktopImage || $mobileImage)
                        <picture class="block w-full">
                            @if ($mobileImage)
                            <source media="(max-width: 767px)" srcset="{{ asset('storage/' . $mobileImage) }}">
                            @endif

                            <img src="{{ asset('storage/' . ($desktopImage ?: $mobileImage)) }}" alt="{{ $desktopAlt }}" class="h-auto w-full transition-transform duration-700 ease-out group-hover:scale-105" loading="lazy" />
                        </picture>
                        @endif
                    </button>
                    @else
                    <a href="{{ $url }}" class="block">
                        <div class="group aspect-[4/3] overflow-hidden bg-slate-100 md:aspect-3/2">
                            @if ($desktopImage || $mobileImage)
                            <picture class="block h-full w-full">
                                @if ($mobileImage)
                                <source media="(max-width: 767px)" srcset="{{ asset('storage/' . $mobileImage) }}">
                                @endif

                                <img src="{{ asset('storage/' . ($desktopImage ?: $mobileImage)) }}" alt="{{ $desktopAlt }}" class="h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" loading="lazy" />
                            </picture>
                            @endif
                        </div>
                    </a>

                    @if ($usesOfferCardLayout)
                    <div class="flex grow flex-col px-6 py-6 sm:px-7">
                        <h3 class="text-base font-semibold leading-snug text-slate-700 mb-3 sm:text-lg">
                            <a href="{{ $url }}" class="transition hover:text-[#A88444] uppercase">
                                {{ $item->title }}
                            </a>
                        </h3>

                        @if (! empty($item->excerpt))
                        <p class="mt-2 grow text-xs leading-relaxed text-slate-600 sm:text-sm">
                            {{ $item->excerpt }}
                        </p>
                        @endif

                        <div class="mt-9 flex flex-wrap items-center justify-start gap-4">
                            <a href="{{ $url }}" class="inline-flex min-w-[120px] items-center justify-center border border-slate-700 px-4 py-2.5 text-xs font-medium uppercase text-slate-700 transition hover:border-[#B8945B] hover:bg-[#B8945B] hover:text-white tracking-[0.08em] sm:text-sm">
                                More Details
                            </a>

                            @if ($model === 'offer' && $reserveUrl)
                            <a href="{{ $reserveUrl }}" class="inline-flex min-w-[120px] items-center justify-center border border-[#A88444] bg-[#A88444] px-4 py-2.5 text-xs font-medium uppercase text-white transition hover:border-[#B8945B] hover:bg-[#B8945B] tracking-[0.08em] sm:text-sm">
                                {{ $reserveLabel }}
                            </a>
                            @endif
                        </div>
                    </div>
                    @else
                    <div class="flex grow flex-col pt-7">
                        <h3 class="text-base leading-snug font-medium uppercase text-slate-700 mb-3 sm:text-lg">
                            {{ $item->title }}
                        </h3>

                        @if (! empty($item->excerpt))
                        <p class="mt-2 grow text-xs leading-relaxed text-gray-600 sm:text-sm">
                            {{ $item->excerpt }}
                        </p>
                        @endif

                        <div class="mt-7 flex {{ in_array($model, ['offer', 'experience'], true) ? 'justify-start' : 'justify-end' }}">
                            <a href="{{ $url }}" class="text-[12px] font-medium uppercase text-slate-700 hover:underline tracking-[0.08em] sm:text-[14px]">
                                {{ $buttonText }}
                            </a>
                        </div>
                    </div>
                    @endif
                    @endif

                </div>
            </article>
            @empty
            <div class="col-span-full py-16 text-center">
                <p class="text-slate-600">
                    No items available.
                </p>
            </div>
            @endforelse
        </div>

        @if ($model === 'gallery')
        <div
            x-cloak
            x-show="activeIndex !== null"
            x-transition.opacity
            class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 px-4 py-10"
            role="dialog"
            aria-modal="true"
            aria-label="Gallery image preview"
        >
            <button type="button" class="absolute right-5 top-5 z-20 flex h-10 w-10 items-center justify-center text-white transition hover:text-[#B8945B]" x-on:click="close()" aria-label="Close gallery preview">
                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" d="M6 6l12 12M18 6L6 18" />
                </svg>
            </button>

            <button type="button" class="absolute left-4 top-1/2 z-20 flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full bg-black/55 text-white transition hover:bg-[#B8945B]" x-on:click="previous()" aria-label="Previous image">
                <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 5l-7 7 7 7" />
                </svg>
            </button>

            <img
                x-bind:src="images[activeIndex]?.src"
                x-bind:alt="images[activeIndex]?.alt"
                class="max-h-[86vh] max-w-[86vw] object-contain shadow-2xl"
            >

            <button type="button" class="absolute right-4 top-1/2 z-20 flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full bg-black/55 text-white transition hover:bg-[#B8945B]" x-on:click="next()" aria-label="Next image">
                <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
        @endif

        </div>

        @if ($useShowMore)
        <div class="mt-14 flex justify-center">
            <button type="button" x-show="visibleCount < {{ $items->count() }}" @click="visibleCount += {{ $showMoreStep }}" class="inline-flex items-center justify-center border border-slate-900 px-7 py-3 text-xs font-semibold uppercase text-slate-700 transition hover:border-[#B8945B] hover:bg-[#B8945B] hover:text-white sm:text-sm">
                Show More
            </button>
        </div>
        @endif

        @if ($paginator && $paginator->hasPages())
        <div class="mt-14">
            @if ($model === 'blog')
            @php
            $currentPage = $paginator->currentPage();
            $lastPage = $paginator->lastPage();
            $visibleStart = max(1, min($currentPage - 1, $lastPage - 3));
            $visibleEnd = min($lastPage, max($currentPage + 1, 4));
            $blogPageUrl = fn (int $pageNumber) => $pageNumber <= 1
            ? route('blog.index')
            : route('blog.page', ['page' => $pageNumber]);
            @endphp

            <nav class="flex justify-center" aria-label="Blog pagination">
                <div class="inline-flex overflow-hidden rounded shadow-md ring-1 ring-slate-200">
                    @if ($paginator->onFirstPage())
                    <span class="flex h-10 w-10 items-center justify-center border-r border-slate-200 bg-slate-100 text-slate-400" aria-disabled="true" aria-label="Previous page">
                        &lsaquo;
                    </span>
                    @else
                    <a href="{{ $blogPageUrl($currentPage - 1) }}" rel="prev" class="flex h-10 w-10 items-center justify-center border-r border-slate-200 bg-[#A88444] text-white transition hover:bg-[#B8945B]" aria-label="Previous page">
                        &lsaquo;
                    </a>
                    @endif

                    @if ($visibleStart > 1)
                    <a href="{{ $blogPageUrl(1) }}" class="flex h-10 min-w-10 items-center justify-center border-r border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 transition hover:bg-[#B8945B] hover:text-white">
                        1
                    </a>

                    @if ($visibleStart > 2)
                    <span class="flex h-10 min-w-10 items-center justify-center border-r border-slate-200 bg-white px-4 text-sm font-medium text-slate-500">
                        ...
                    </span>
                    @endif
                    @endif

                    @for ($pageNumber = $visibleStart; $pageNumber <= $visibleEnd; $pageNumber++)
                    @if ($pageNumber === $currentPage)
                    <span class="flex h-10 min-w-10 items-center justify-center border-r border-slate-200 bg-[#A88444] px-4 text-sm font-medium text-white" aria-current="page">
                        {{ $pageNumber }}
                    </span>
                    @else
                    <a href="{{ $blogPageUrl($pageNumber) }}" class="flex h-10 min-w-10 items-center justify-center border-r border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 transition hover:bg-[#B8945B] hover:text-white">
                        {{ $pageNumber }}
                    </a>
                    @endif
                    @endfor

                    @if ($visibleEnd < $lastPage)
                    @if ($visibleEnd < $lastPage - 1)
                    <span class="flex h-10 min-w-10 items-center justify-center border-r border-slate-200 bg-white px-4 text-sm font-medium text-slate-500">
                        ...
                    </span>
                    @endif

                    <a href="{{ $blogPageUrl($lastPage) }}" class="flex h-10 min-w-10 items-center justify-center border-r border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 transition hover:bg-[#B8945B] hover:text-white">
                        {{ $lastPage }}
                    </a>
                    @endif

                    @if ($paginator->hasMorePages())
                    <a href="{{ $blogPageUrl($currentPage + 1) }}" rel="next" class="flex h-10 w-10 items-center justify-center bg-[#A88444] text-white transition hover:bg-[#B8945B]" aria-label="Next page">
                        &rsaquo;
                    </a>
                    @else
                    <span class="flex h-10 w-10 items-center justify-center bg-slate-100 text-slate-400" aria-disabled="true" aria-label="Next page">
                        &rsaquo;
                    </span>
                    @endif
                </div>
            </nav>
            @else
            {{ $paginator->links() }}
            @endif
        </div>
        @endif

    </div>
</section>
