@props(['event', 'image' => null])

<section class="grid bg-white font-sans text-slate-700 lg:min-h-[620px] lg:grid-cols-2" aria-labelledby="festive-programme-title" data-gtm-section="programme">
    <div class="min-h-[360px] overflow-hidden lg:min-h-[620px]">
        @if ($image)
            <img src="{{ $image }}" alt="{{ $event->programme_image_alt }}" class="h-full w-full object-cover object-center" width="1400" height="1050" loading="lazy" decoding="async">
        @endif
    </div>
    <div class="flex items-center px-6 py-14 md:px-12 md:py-16 lg:px-[clamp(64px,7vw,120px)] lg:py-20">
        <div class="w-full max-w-xl">
            @if ($event->programme_eyebrow)
                <p class="mb-4 text-[10px] font-medium uppercase tracking-[.18em] text-[#A88444] sm:text-xs">{{ $event->programme_eyebrow }}</p>
            @endif
            @if ($event->programme_heading)
                <h2 id="festive-programme-title" class="text-xl font-medium uppercase leading-snug text-slate-700 [--heading-letter-spacing:.15em] sm:text-2xl">{!! nl2br(e($event->programme_heading)) !!}</h2>
            @endif
            <div class="mt-8 divide-y divide-slate-200 border-y border-slate-200">
                @foreach ($event->programme_items ?? [] as $item)
                    <div class="grid gap-2 py-5 sm:grid-cols-[150px_1fr] sm:gap-6">
                        <p class="text-[10px] font-semibold uppercase tracking-[.1em] text-[#A88444] sm:text-xs">{{ $item['time'] ?? '' }}</p>
                        <p class="text-xs leading-relaxed text-gray-600 sm:text-sm">{{ $item['activity'] ?? '' }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
