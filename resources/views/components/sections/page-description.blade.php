@props([
'page' => null,
])

@if ($page)
<section class="py-14 md:py-20 px-6 text-center">
    <div class="max-w-3xl md:max-w-5xl mx-auto">

        {{-- Title --}}
        {{-- <h1 class="text-2xl sm:text-3xl md:text-4xl leading-snug tracking-[0.15em] md:tracking-[0.25em] uppercase text-slate-800 mb-6 md:mb-8 font-medium">
            {{ $page->title }}
        </h1> --}}

        <h1 class="text-2xl sm:text-3xl md:text-4xl leading-snug tracking-[0.15em] md:tracking-[0.25em] uppercase text-slate-800 mb-6 md:mb-8 font-medium">
            {!! str_ireplace(
            ['&lt;br&gt;', '&lt;br/&gt;', '&lt;br /&gt;'],
            '<br class="hidden md:block">',
            e($page->title)
            ) !!}
        </h1>

        {{-- Description --}}
        <div class="text-[15px] sm:text-base leading-relaxed text-gray-600 max-w-2xl sm:max-w-3xl md:max-w-5xl mx-auto">
            {!! $page->description !!}
        </div>

    </div>
</section>
@endif