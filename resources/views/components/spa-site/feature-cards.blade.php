@props(['section'])

@php
$items = collect($section->items ?? []);
$images = $section->images;
@endphp

<section class="bg-[#fbf8f1] px-6 py-16 md:py-24">
    <div class="mx-auto max-w-7xl">
        <div class="mx-auto max-w-3xl text-center">
            @if ($section->subtitle)
                <p class="text-xs font-medium uppercase tracking-[0.16em] text-[#791841] sm:text-sm">{{ $section->subtitle }}</p>
            @endif
            <h2 class="mt-3 font-serif text-3xl leading-tight text-slate-700 sm:text-4xl">{{ $section->title }}</h2>
            <span class="mx-auto mt-5 block h-px w-12 bg-[#791841]"></span>
            @if ($section->description)
                <div class="mt-6 text-sm leading-7 text-slate-600">{!! $section->description !!}</div>
            @endif
        </div>

        <div class="mt-12 grid gap-7 md:grid-cols-3 lg:gap-9">
            @foreach ($items as $index => $item)
                @php $image = $images->get($index); @endphp
                <article class="group flex h-full flex-col border border-black/10 bg-white">
                    <div class="aspect-[4/3] overflow-hidden bg-slate-100">
                        @if ($image?->image)
                            <picture>
                                @if ($image->mobile_image)<source media="(max-width: 767px)" srcset="{{ asset('storage/'.$image->mobile_image) }}">@endif
                                <img src="{{ asset('storage/'.$image->image) }}" alt="{{ $image->image_alt ?: ($item['title'] ?? $section->title) }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-105" loading="lazy" decoding="async">
                            </picture>
                        @endif
                    </div>
                    <div class="flex grow flex-col px-6 py-7 text-center sm:px-7">
                        <h3 class="font-serif text-2xl text-slate-700">{{ $item['title'] ?? '' }}</h3>
                        <span class="mx-auto my-4 block h-px w-9 bg-[#791841]"></span>
                        <p class="grow text-sm leading-7 text-slate-600">{{ $item['description'] ?? '' }}</p>
                        @if ($section->button_url)
                            <a href="{{ $section->button_url }}" class="mt-6 text-xs font-semibold uppercase tracking-[0.14em] text-[#791841] underline-offset-4 hover:underline focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#791841]">{{ $section->button_label ?: 'Enquire Now' }}</a>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
