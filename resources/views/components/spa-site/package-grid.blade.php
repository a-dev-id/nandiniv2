@props(['items'])

<section class="bg-white px-6 pb-16 pt-2 md:pb-24 md:pt-4">
    <div class="mx-auto grid max-w-6xl gap-8 md:grid-cols-2 lg:gap-10">
        @foreach ($items as $item)
            @php
            $image = $item->card_image ?: $item->hero_image;
            $alt = $item->card_image_alt ?: $item->hero_image_alt ?: $item->title;
            $summary = trim((string) preg_replace('/\s+/', ' ', html_entity_decode(strip_tags((string) ($item->excerpt ?: $item->description)), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
            @endphp
            <article class="group flex h-full flex-col border border-black/10 bg-[#fbf8f1]">
                <a href="{{ $item->show_url }}" class="block aspect-[16/10] overflow-hidden bg-slate-100">
                    @if ($image)<img src="{{ asset('storage/'.$image) }}" alt="{{ $alt }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-105" loading="lazy" decoding="async">@endif
                </a>
                <div class="flex grow flex-col p-7 sm:p-9">
                    <span class="mb-4 block h-px w-10 bg-[#791841]"></span>
                    <h3 class="font-serif text-2xl leading-snug text-slate-700 sm:text-3xl"><a href="{{ $item->show_url }}" class="hover:text-[#791841]">{{ $item->title }}</a></h3>
                    @if ($summary)<p class="mt-4 grow text-sm leading-7 text-slate-600">{{ $summary }}</p>@endif
                    <div class="mt-7"><a href="{{ $item->show_url }}" class="inline-flex min-h-11 items-center justify-center bg-[#791841] px-6 text-xs font-semibold uppercase tracking-[0.12em] text-white transition hover:bg-[#5f1233] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#791841]">More Details</a></div>
                </div>
            </article>
        @endforeach
    </div>
</section>
