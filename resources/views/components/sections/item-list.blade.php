@props([
'model' => 'offer', // offer | experience
'items' => null,
'withFilter' => false,
'limit' => 99,
'routeName' => null,
'hiddenCategories' => [],
])

@php
use App\Models\Offer;
use App\Models\Experience;
use Illuminate\Support\Facades\Route;

$active = request('category', 'all');
$hiddenCategories = collect($hiddenCategories)->filter()->values();

if ($hiddenCategories->contains($active)) {
$active = 'all';
}

$modelClass = match ($model) {
'experience' => Experience::class,
default => Offer::class,
};

/*
|--------------------------------------------------------------------------
| Use Passed Items If Available
|--------------------------------------------------------------------------
*/
$providedItems = $items !== null ? collect($items) : null;

/*
|--------------------------------------------------------------------------
| Dynamic Experience Filters
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
->take($limit)
->values();
} else {
$query = $modelClass::query();

/*
|--------------------------------------------------------------------------
| Status / Published Logic
|--------------------------------------------------------------------------
*/
if ($modelClass === Offer::class) {
$query->published();
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

/*
|--------------------------------------------------------------------------
| Experience Category Filter
|--------------------------------------------------------------------------
*/
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
        <div class="flex flex-wrap justify-center gap-3 mb-12">
            @foreach ($filters as $key => $label)
            <a href="{{ $key === 'all' ? request()->url() : request()->fullUrlWithQuery(['category' => $key]) }}" class="px-5 py-2 text-xs sm:text-sm uppercase tracking-[0.20em] border transition
                            {{ $active === $key
                                ? 'bg-[#A67C3D] text-white border-[#A67C3D]'
                                : 'bg-white text-slate-800 border-black/15 hover:border-black/40' }}">
                {{ $label }}
            </a>
            @endforeach
        </div>
        @endif

        {{-- GRID --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-x-10 gap-y-14">
            @forelse ($items as $item)
            @php
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

            $url = '#';

            if ($routeName && Route::has($routeName)) {
            $url = route($routeName, $item->slug);
            } elseif ($model === 'offer' && Route::has('offers.show')) {
            $url = route('offers.show', $item->slug);
            } elseif ($model === 'experience' && Route::has('experiences.show')) {
            $url = route('experiences.show', $item->slug);
            }
            @endphp

            <article class="flex">
                <div class="flex flex-col h-full w-full">

                    <div class="aspect-square md:aspect-3/2 overflow-hidden bg-slate-100 group">
                        @if ($desktopImage || $mobileImage)
                        <picture class="block w-full h-full">
                            @if ($mobileImage)
                            <source media="(max-width: 767px)" srcset="{{ asset('storage/' . $mobileImage) }}">
                            @endif

                            <img src="{{ asset('storage/' . ($desktopImage ?: $mobileImage)) }}" alt="{{ $desktopAlt }}" class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" loading="lazy" />
                        </picture>
                        @endif
                    </div>

                    <div class="pt-7 flex flex-col grow">
                        <h3 class="text-slate-800 uppercase tracking-[0.22em] text-lg sm:text-xl lg:text-2xl font-medium">
                            {{ $item->title }}
                        </h3>

                        @if (! empty($item->excerpt))
                        <p class="mt-3 text-slate-700 text-[15px] leading-relaxed grow">
                            {{ $item->excerpt }}
                        </p>
                        @endif

                        <a href="{{ $url }}" class="mt-7 uppercase tracking-[0.25em] text-[14px] text-slate-800 hover:underline font-bold">
                            DISCOVER
                        </a>
                    </div>

                </div>
            </article>
            @empty
            <div class="col-span-full text-center py-16">
                <p class="text-slate-600">
                    No items available.
                </p>
            </div>
            @endforelse
        </div>

    </div>
</section>