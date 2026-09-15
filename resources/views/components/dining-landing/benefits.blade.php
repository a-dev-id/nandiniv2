@props(['settings' => null])

<section class="bg-[#f3f4f5] px-6 py-14 font-sans text-center md:py-20" aria-labelledby="dining-benefits-title">
    <div class="mx-auto max-w-7xl">
        <p class="mb-3 text-[10px] font-medium tracking-[.18em] text-[#A88444] uppercase sm:text-xs">{{ $settings?->why_dine_eyebrow }}</p>
        <h2 id="dining-benefits-title" class="mx-auto text-lg leading-snug font-medium text-slate-700 uppercase sm:text-xl">
            {!! nl2br(e($settings?->why_dine_heading)) !!}
        </h2>

        <ul class="mt-10 grid grid-cols-1 min-[420px]:grid-cols-2 min-[420px]:gap-y-10 lg:mt-12 lg:grid-cols-4 lg:gap-y-0">
            @foreach (($settings?->why_dine_items ?? []) as $benefit)
                <li class="relative mx-auto w-full max-w-[320px] px-6 py-7 max-[420px]:border-b max-[420px]:border-[#8f6b34]/15 max-[420px]:last:border-b-0 min-[420px]:max-w-none min-[420px]:px-3 min-[420px]:py-0 min-[420px]:after:absolute min-[420px]:after:top-0 min-[420px]:after:right-0 min-[420px]:after:h-[140px] min-[420px]:after:w-px min-[420px]:after:bg-[#8f6b34]/20 min-[420px]:max-lg:even:after:hidden md:px-6 lg:nth-[4n]:after:hidden lg:last:after:hidden">
                    @if (in_array($benefit['icon'] ?? '', ['leaves', 'bowl', 'wine', 'heart', 'star', 'sparkles', 'flame', 'coffee', 'clock', 'dining', 'location', 'whatsapp', 'email', 'guests'], true))
                    <svg class="mx-auto mb-5 size-9 text-[#d1b77d] lg:size-10" viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        @switch($benefit['icon'])
                            @case('leaves')
                                <path d="M20 36V15M20 20C10 18 8 10 12 4c8 3 11 9 8 16Zm0 10C9 30 4 24 5 17c9 0 15 5 15 13Zm0-6c0-10 5-16 13-17 3 9-2 15-13 17Zm0 10c1-8 7-11 15-10-1 8-7 12-15 10Z"/>
                                <path d="m20 20-6-10m6 20L9 22m11 2 9-11m-9 21 10-6"/>
                                @break
                            @case('bowl')
                                <path d="M4 20h32c-1 9-7 14-16 14S5 29 4 20ZM12 37h16M16 34l-1 3m9-3 1 3M11 14c-4-4 4-5 0-9m9 9c-4-4 4-6 0-10m9 10c-4-4 4-5 0-9"/>
                                @break
                            @case('wine')
                                <path d="M14 3h12l2 15a8 8 0 0 1-16 0l2-15ZM13 14h14M20 26v10m-7 1h14"/>
                                @break
                            @case('heart')
                                <path d="M20 34 6 20C-4 9 11-2 20 10 29-2 44 9 34 20L20 34Z"/>
                                @break
                            @case('star')
                                <path d="m20 3 5.2 10.5 11.6 1.7-8.4 8.2 2 11.6L20 29.5 9.6 35l2-11.6-8.4-8.2 11.6-1.7L20 3Z"/>
                                @break
                            @case('sparkles')
                                <path d="M20 3c1.2 8.4 4.6 11.8 13 13-8.4 1.2-11.8 4.6-13 13-1.2-8.4-4.6-11.8-13-13 8.4-1.2 11.8-4.6 13-13ZM32 27c.5 3.2 1.8 4.5 5 5-3.2.5-4.5 1.8-5 5-.5-3.2-1.8-4.5-5-5 3.2-.5 4.5-1.8 5-5Z"/>
                                @break
                            @case('flame')
                                <path d="M20 37c-7 0-12-5-12-12 0-6 4-10 9-16 0 5 3 7 5 9 1-6 4-10 4-14 5 6 7 12 6 19-1 8-5 14-12 14Z"/><path d="M20 37c-4 0-7-3-7-7 0-3 2-6 5-9 0 3 2 4 3 6 1-3 2-5 3-7 2 3 3 6 3 9 0 5-3 8-7 8Z"/>
                                @break
                            @case('coffee')
                                <path d="M7 14h22v8c0 8-4 13-11 13S7 30 7 22v-8Zm22 3h3a5 5 0 0 1 0 10h-4M11 38h18M13 9c-3-3 3-4 0-7m8 7c-3-3 3-4 0-7"/>
                                @break
                            @case('clock') <circle cx="20" cy="20" r="15"/><path d="M20 11v9l6 4"/> @break
                            @case('dining') <path d="M8 4v11a5 5 0 0 0 10 0V4M13 4v32M34 36V4c-7 4-9 13-9 20h9"/> @break
                            @case('location') <path d="M34 17c0 10-14 20-14 20S6 27 6 17a14 14 0 1 1 28 0Z"/><circle cx="20" cy="17" r="4"/> @break
                            @case('whatsapp') <path d="M36 19a16 16 0 0 1-24 14L4 36l3-8A16 16 0 1 1 36 19Z"/><path d="m13 11 3 5-2 2c2 4 4 6 8 8l2-2 5 2c-2 7-9 4-14-1s-9-12-2-14Z"/> @break
                            @case('email') <rect x="5" y="9" width="30" height="23" rx="2"/><path d="m7 12 13 10 13-10"/> @break
                            @case('guests') <circle cx="15" cy="14" r="5"/><circle cx="29" cy="15" r="4"/><path d="M5 35c0-7 4-12 10-12s10 5 10 12m0-10c7 0 10 4 10 10"/> @break
                        @endswitch
                    </svg>
                    @endif
                    <h3 class="mb-3 font-sans text-xs leading-[1.25] text-[#20271f] uppercase [--heading-letter-spacing:.08em] md:text-[13px]">
                        {!! nl2br(e($benefit['title'] ?? '')) !!}
                    </h3>
                    <p class="mx-auto max-w-[240px] text-xs leading-relaxed text-slate-600 sm:text-sm">{!! nl2br(e($benefit['description'] ?? '')) !!}</p>
                </li>
            @endforeach
        </ul>
    </div>
</section>
