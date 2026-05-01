@props([
'section' => null,
])

@if ($section)
<section class="py-14 md:py-20 px-6 text-center">
    <div class="max-w-3xl md:max-w-5xl mx-auto">

        {{-- Title --}}
        @if ($section->title)
        <h1 class="text-lg sm:text-xl lg:text-2xl tracking-[0.25em] uppercase text-slate-800 font-medium">
            {!! str_ireplace(
            ['&lt;br&gt;', '&lt;br/&gt;', '&lt;br /&gt;'],
            '<br class="hidden md:block">',
            e($section->title)
            ) !!}
        </h1>
        @endif

        {{-- Description --}}
        @if ($section->description)
        <div class="text-[15px] sm:text-base leading-relaxed text-gray-600 max-w-2xl sm:max-w-3xl md:max-w-5xl mx-auto">
            {!! $section->description !!}
        </div>
        @endif

    </div>
</section>
@endif