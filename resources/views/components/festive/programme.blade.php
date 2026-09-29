@props(['settings' => null])

@php($days = $settings?->programme_days ?? [])

<section id="programme" class="scroll-mt-20 bg-[#F7F7F7] px-6 py-14 md:px-12 md:py-20 lg:px-[clamp(64px,5vw,100px)]" aria-labelledby="festive-programme-title" data-gtm-section="programme">
    <div class="w-full max-w-none">
        <div class="mb-10 text-center md:mb-12">
            @if ($settings?->programme_eyebrow)
                <p class="mb-4 text-[10px] font-medium uppercase tracking-[.18em] text-[#A88444] sm:text-xs">{{ $settings->programme_eyebrow }}</p>
            @endif
            @if ($settings?->programme_heading)
                <h2 id="festive-programme-title" class="text-xl font-medium uppercase leading-snug text-slate-700 [--heading-letter-spacing:.15em] sm:text-2xl">{!! nl2br(e($settings->programme_heading)) !!}</h2>
            @endif
        </div>

        <div class="grid gap-10 lg:grid-cols-3 lg:gap-8 xl:gap-10">
            @foreach ($days as $day)
                <article>
                    @if ($day['date'] ?? null)
                        <h3 class="mb-4 text-base font-semibold uppercase leading-snug text-slate-700 [--heading-letter-spacing:.1em] sm:text-lg">{{ $day['date'] }}</h3>
                    @endif
                    <div class="border-t border-[#d9d2c4]">
                        @foreach (($day['items'] ?? []) as $item)
                            <div class="grid gap-1 border-b border-[#d9d2c4] py-4 md:grid-cols-[170px_1fr] md:gap-6 lg:grid-cols-1 lg:gap-2 xl:grid-cols-[130px_1fr] xl:gap-4">
                                <time class="text-[10px] font-semibold text-[#A88444] sm:text-xs">{{ $item['time'] ?? '' }}</time>
                                <span class="text-xs leading-relaxed text-gray-600 sm:text-sm">{{ $item['activity'] ?? '' }}</span>
                            </div>
                        @endforeach
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
