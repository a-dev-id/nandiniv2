@props([
'model' => 'offer', // offer | experience | blog | news | gallery
'items' => null,
'withFilter' => false,
'limit' => 99,
'routeName' => null,
'hiddenCategories' => [],
])

@php
use App\Models\Offer;
use App\Models\Experience;
use App\Models\BlogNews;
use App\Models\Gallery;
use Illuminate\Support\Facades\Route;

$active = request('category', 'all');
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

$buttonText = in_array($model, ['blog', 'news'], true) ? 'READ MORE' : 'DISCOVER';

$isPaginated = $items instanceof \Illuminate\Contracts\Pagination\Paginator;
$paginator = $isPaginated ? $items : null;

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
$filters[$category->slug] = $category->name
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
@endphp

<section class="pb-16 md:pb-28">
    <div class="mx-auto px-3 lg:px-16">

        {{-- FILTER --}}
        @if ($withFilter && count($filters) > 1)
        <div class="mb-12 flex flex-wrap justify-center gap-3">
            @foreach ($filters as $key => $label)
            <a href="{{ $key === 'all' ? request()->url() : request()->fullUrlWithQuery(['category' => $key]) }}" class="border px-5 py-2 text-xs uppercase tracking-[0.20em] transition sm:text-sm
                {{ $active === $key
                    ? 'border-[#A67C3D] bg-[#A67C3D] text-white'
                    : 'border-black/15 bg-white text-slate-800 hover:border-black/40' }}">
                {{ $label }}
            </a>
            @endforeach
        </div>
        @endif

        {{-- GRID --}}
        <div class="grid grid-cols-1 gap-x-10 gap-y-14 md:grid-cols-2 xl:grid-cols-3">
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
            @endphp

            <article class="flex">
                <div class="flex h-full w-full flex-col">

                    @if ($model === 'gallery')
                    <div class="aspect-square overflow-hidden bg-slate-100 md:aspect-3/2">
                        @if ($desktopImage || $mobileImage)
                        <picture class="block h-full w-full">
                            @if ($mobileImage)
                            <source media="(max-width: 767px)" srcset="{{ asset('storage/' . $mobileImage) }}">
                            @endif

                            <img src="{{ asset('storage/' . ($desktopImage ?: $mobileImage)) }}" alt="{{ $desktopAlt }}" class="h-full w-full object-cover transition-transform duration-700 ease-out hover:scale-105" loading="lazy" />
                        </picture>
                        @endif
                    </div>
                    @else
                    <a href="{{ $url }}" class="block">
                        <div class="group aspect-square overflow-hidden bg-slate-100 md:aspect-3/2">
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

                    <div class="flex grow flex-col pt-7">
                        <h3 class="text-lg font-medium uppercase tracking-[0.22em] text-slate-800 sm:text-xl lg:text-2xl">
                            {{ $item->title }}
                        </h3>

                        @if (! empty($item->excerpt))
                        <p class="mt-3 grow text-[15px] leading-relaxed text-slate-700">
                            {{ $item->excerpt }}
                        </p>
                        @endif

                        <div class="mt-7 flex justify-end">
                            <a href="{{ $url }}" class="text-[14px] font-bold uppercase tracking-[0.25em] text-slate-800 hover:underline">
                                {{ $buttonText }}
                            </a>
                        </div>
                    </div>
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

        @if ($paginator && $paginator->hasPages())
        <div class="mt-14">
            {{ $paginator->links() }}
        </div>
        @endif

    </div>
</section>