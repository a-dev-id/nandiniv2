@props(['dish' => null])

@if ($dish)
<section class="bg-[#f3f4f5] px-6 py-14 font-sans md:px-10 md:py-20 2xl:px-14" aria-labelledby="dining-signature-title">
    <div class="mx-auto grid max-w-7xl items-center gap-10 md:grid-cols-[minmax(0,45fr)_minmax(0,55fr)] md:gap-12 lg:gap-16">
        <div class="order-1 aspect-[16/10] overflow-hidden bg-[#f3f4f5] md:order-2">
            @if ($dish->image_url)
                <img src="{{ $dish->image_url }}" alt="{{ $dish->image_alt }}" class="h-full w-full object-cover" width="1200" height="900" loading="lazy" decoding="async">
            @endif
        </div>

        <div class="order-2 flex flex-col justify-center md:order-1">
            @if (filled($dish->eyebrow))
                <p class="mb-3 text-[10px] font-medium uppercase tracking-[.18em] text-[#A88444] sm:text-xs">{{ $dish->eyebrow }}</p>
            @endif
            <div class="flex flex-wrap items-baseline gap-x-5 gap-y-2">
                <h2 id="dining-signature-title" class="font-serif text-3xl uppercase leading-tight text-slate-800 sm:text-4xl">{{ $dish->name }}</h2>
                @if (filled($dish->price))
                    <span class="border-l border-slate-300 pl-5 text-xl font-semibold text-slate-800 sm:text-2xl">{{ $dish->price }}</span>
                @endif
            </div>
            @if (filled($dish->subtitle))
                <p class="mt-4 text-xs font-medium uppercase tracking-[.14em] text-slate-700 sm:text-sm">{{ $dish->subtitle }}</p>
            @endif
            @if (filled($dish->short_description))
                <p class="mt-5 max-w-xl text-sm leading-relaxed text-slate-600">{{ $dish->short_description }}</p>
            @endif
            @if (filled($dish->cta_label) && filled($dish->cta_url))
                <div class="mt-7">
                    <x-buttons.link-button :href="$dish->cta_url" variant="solid">{{ $dish->cta_label }}</x-buttons.link-button>
                </div>
            @endif
        </div>
    </div>
</section>
@endif
