@props([
'page' => null,
'showAwards' => false,
])

@if ($page)
<section class="py-14 md:py-20 px-6 text-center">
    <div class="max-w-3xl md:max-w-5xl mx-auto">

        @if ($showAwards)
        <div class="mb-10 flex items-center justify-center gap-5 sm:gap-7 md:mb-12 md:gap-8" aria-label="Awards and recognition">
            <img src="{{ asset('images/awards-best-luxury-jungle-retreat.png') }}" class="h-20 w-auto sm:h-24" width="576" height="725" loading="lazy" alt="Best Luxury Jungle Retreat 2024">
            <img src="{{ asset('images/awards-best-luxury-jungle-retreat-2025.png') }}" class="h-20 w-auto sm:h-24" width="378" height="472" loading="lazy" alt="Best Luxury Jungle Retreat and Spa 2025">
            <img src="{{ asset('https://static.tacdn.com/img2/travelers_choice/widgets/tchotel_2026_L.png') }}" class="h-20 w-auto sm:h-24" width="160" height="160" loading="lazy" alt="Tripadvisor Travelers' Choice 2026">
        </div>
        @endif

        {{-- Title --}}
        {{-- <h1 class="text-xl leading-snug uppercase text-slate-700 font-medium mb-3 sm:text-2xl">
            {{ $page->title }}
        </h1> --}}

        <h1 class="text-xl uppercase text-slate-700 font-medium mb-3 sm:text-2xl">
            {!! str_ireplace(
            ['&lt;br&gt;', '&lt;br/&gt;', '&lt;br /&gt;'],
            '<br class="hidden md:block">',
            e($page->title)
            ) !!}
        </h1>

        {{-- Description --}}
        <div class="text-xs leading-relaxed text-gray-600 max-w-2xl sm:max-w-3xl md:max-w-5xl mx-auto [&_h2]:mt-12 [&_h1]:mb-3 [&_h2]:mb-3 [&_h3]:mb-3 [&_h2]:text-lg [&_h2]:leading-snug [&_h2]:uppercase [&_h2]:text-slate-700 [&_h2]:font-medium [&_h3]:mt-10 [&_h3]:text-base [&_h3]:leading-snug [&_h3]:uppercase [&_h3]:text-slate-700 [&_h3]:font-medium [&_p]:mx-auto [&_p]:max-w-2xl sm:[&_p]:max-w-3xl md:[&_p]:max-w-4xl [&_p]:text-xs [&_p]:leading-relaxed [&_p]:text-gray-600 [&_p]:mb-6 [&_p:last-child]:mb-0 sm:text-sm sm:[&_h2]:text-xl sm:[&_h3]:text-lg sm:[&_p]:text-sm">
            {!! $page->description !!}
        </div>

    </div>
</section>
@endif