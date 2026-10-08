@props(['settings' => null])

@php
    $eyebrow = $settings?->why_nandini_eyebrow ?: 'WHY NANDINI';
    $heading = $settings?->why_nandini_heading ?: 'WELLNESS ROOTED IN NATURE';
    $items = $settings?->why_nandini_items ?? [];

    if (blank($items)) {
        $items = [
            ['icon' => 'jungle', 'title' => 'JUNGLE SANCTUARY', 'description' => 'Treatments surrounded by tropical nature.'],
            ['icon' => 'ritual', 'title' => 'BALINESE RITUALS', 'description' => 'Wellness inspired by traditional Balinese practices.'],
            ['icon' => 'care', 'title' => 'PERSONALISED CARE', 'description' => 'Experiences tailored to individual wellbeing.'],
            ['icon' => 'river', 'title' => 'RIVER-SIDE SERENITY', 'description' => 'A unique spa environment shaped by the jungle landscape.'],
        ];
    }
@endphp

<section class="bg-[#f3f4f5] px-6 py-14 text-center font-sans md:px-12 md:py-20 lg:px-6" aria-labelledby="spa-why-nandini-title" data-gtm-section="spa_benefits">
    <div class="mx-auto max-w-7xl">
        <p class="mb-3 text-[10px] font-medium uppercase tracking-[.18em] text-[#A88444] sm:text-xs">{{ $eyebrow }}</p>
        <h2 id="spa-why-nandini-title" class="mx-auto text-lg leading-snug font-medium text-slate-700 uppercase sm:text-xl">
            {!! nl2br(e($heading)) !!}
        </h2>

        <ul class="mt-10 grid grid-cols-1 gap-y-8 border-y border-[#A88444]/20 py-8 min-[480px]:grid-cols-2 min-[480px]:gap-x-8 md:mt-14 md:py-10 lg:grid-cols-4 lg:gap-x-10">
            @foreach ($items as $item)
                <li class="mx-auto w-full max-w-[280px] py-3">
                    @if (in_array($item['icon'] ?? '', ['jungle', 'ritual', 'care', 'river'], true))
                        <svg class="mx-auto mb-5 size-8 text-[#A88444]" viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="1.45" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            @switch($item['icon'])
                                @case('jungle')
                                    <path d="M20 36V15M20 20C10 18 8 10 12 4c8 3 11 9 8 16Zm0 10C9 30 4 24 5 17c9 0 15 5 15 13Zm0-6c0-10 5-16 13-17 3 9-2 15-13 17Zm0 10c1-8 7-11 15-10-1 8-7 12-15 10Z"/><path d="m20 20-6-10m6 20L9 22m11 2 9-11m-9 21 10-6"/>
                                    @break
                                @case('ritual')
                                    <circle cx="20" cy="7" r="3"/><path d="M20 10v10m0-7-7 7m7-7 7 7M14 35c0-6 2-11 6-15 4 4 6 9 6 15M8 35c2-6 6-9 12-9s10 3 12 9H8Z"/>
                                    @break
                                @case('care')
                                    <path d="M20 29 9 18C1 10 12 1 20 11c8-10 19-1 11 7L20 29Z"/><path d="M5 28c4-2 8-1 12 3l3 3 3-3c4-4 8-5 12-3M10 36h20"/>
                                    @break
                                @case('river')
                                    <path d="M20 3c5 4 10 5 15 6v9c0 9-5 15-15 19C10 33 5 27 5 18V9c5-1 10-2 15-6Z"/><path d="M12 18c3-3 6-3 9 0s6 3 9 0M11 24c3-3 6-3 9 0s6 3 9 0"/>
                                    @break
                            @endswitch
                        </svg>
                    @endif

                    <h3 class="mb-3 font-sans text-xs leading-[1.3] text-[#26342e] uppercase [--heading-font-weight:600] [--heading-letter-spacing:.1em] md:text-[13px]">
                        {!! nl2br(e($item['title'] ?? '')) !!}
                    </h3>
                    <p class="mx-auto max-w-[240px] text-xs leading-relaxed text-slate-600 sm:text-sm">
                        {!! nl2br(e($item['description'] ?? '')) !!}
                    </p>
                </li>
            @endforeach
        </ul>
    </div>
</section>
