@props([
'items' => collect(),
'wrapperClass' => '',
'routeName' => null,
])

<section class="pb-16 md:pb-28 {{ $wrapperClass }}">
    <div class="mx-auto lg:px-16 relative">

        <div class="itemcarousel-slick">
            @foreach ($items as $item)
            <article class="px-3 h-full w-full flex">
                <div class="flex flex-col h-full w-full">

                    @php
                    $image = $item->card_image ?? $item->hero_image ?? null;
                    $alt = $item->card_image_alt ?? $item->hero_image_alt ?? $item->title;

                    $url = '#';

                    if (! empty($item->show_url)) {
                    $url = $item->show_url;
                    } elseif ($routeName && \Illuminate\Support\Facades\Route::has($routeName)) {
                    $url = route($routeName, $item->slug);
                    }
                    @endphp

                    <a href="{{ $url }}" class="block">
                        <div class="aspect-square md:aspect-3/2 overflow-hidden bg-slate-100 group">
                            @if ($image)
                            <img src="{{ asset('storage/' . $image) }}" alt="{{ $alt }}" class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" loading="lazy" />
                            @endif
                        </div>
                    </a>

                    <div class="pt-7 flex flex-col grow">
                        <h3 class="text-slate-800 uppercase tracking-[0.22em] text-lg sm:text-xl lg:text-2xl font-medium">
                            {{ $item->title }}
                        </h3>

                        @if (! empty($item->excerpt))
                        <p class="mt-3 text-slate-800 text-[15px] leading-relaxed grow">
                            {{ $item->excerpt }}
                        </p>
                        @endif

                        <a href="{{ $url }}" class="mt-6 uppercase tracking-[0.25em] text-[14px] text-slate-800 hover:underline font-bold">
                            DISCOVER
                        </a>
                    </div>

                </div>
            </article>
            @endforeach
        </div>

        <button type="button" class="itemcarousel-prev absolute left-2 lg:left-8 top-[30%] md:top-1/2 -translate-y-1/2 w-10 h-10 md:w-12 md:h-12 bg-black text-white flex items-center justify-center z-10" aria-label="Previous">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"></path>
            </svg>
        </button>

        <button type="button" class="itemcarousel-next absolute right-2 lg:right-8 top-[30%] md:top-1/2 -translate-y-1/2 w-10 h-10 md:w-12 md:h-12 bg-black text-white flex items-center justify-center z-10" aria-label="Next">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"></path>
            </svg>
        </button>

    </div>
</section>