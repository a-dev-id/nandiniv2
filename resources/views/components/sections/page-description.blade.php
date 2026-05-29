@props([
'page' => null,
])

@if ($page)
<section class="py-14 md:py-20 px-6 text-center">
    <div class="max-w-3xl md:max-w-5xl mx-auto">

        {{-- Title --}}
        {{-- <h1 class="text-4xl leading-snug tracking-[0.15em] md:tracking-[0.25em] uppercase text-slate-800 mb-6 md:mb-8 font-medium">
            {{ $page->title }}
        </h1> --}}

        <h1 class="text-4xl leading-snug tracking-[0.15em] md:tracking-[0.25em] uppercase text-slate-800 mb-6 md:mb-8 font-medium">
            {!! str_ireplace(
            ['&lt;br&gt;', '&lt;br/&gt;', '&lt;br /&gt;'],
            '<br class="hidden md:block">',
            e($page->title)
            ) !!}
        </h1>

        {{-- Description --}}
        <div class="text-[15px] sm:text-base leading-relaxed text-gray-600 max-w-2xl sm:max-w-3xl md:max-w-5xl mx-auto
            [&_h2]:mt-12
            [&_h2]:mb-6
            [&_h2]:text-2xl
            sm:[&_h2]:text-3xl
            md:[&_h2]:text-3xl
            [&_h2]:leading-snug
            [&_h2]:tracking-[0.15em]
            md:[&_h2]:tracking-[0.22em]
            [&_h2]:uppercase
            [&_h2]:text-slate-800
            [&_h2]:font-medium
            [&_h3]:mt-10
            [&_h3]:mb-5
            [&_h3]:text-xl
            sm:[&_h3]:text-2xl
            md:[&_h3]:text-2xl
            [&_h3]:leading-snug
            [&_h3]:tracking-[0.15em]
            [&_h3]:uppercase
            [&_h3]:text-slate-800
            [&_h3]:font-medium
            [&_p]:mx-auto
            [&_p]:max-w-2xl
            sm:[&_p]:max-w-3xl
            md:[&_p]:max-w-5xl
            [&_p]:text-[15px]
            sm:[&_p]:text-base
            [&_p]:leading-relaxed
            [&_p]:text-gray-600
            [&_p]:mb-6
            [&_p:last-child]:mb-0">
            {!! $page->description !!}
        </div>

    </div>
</section>
@endif
