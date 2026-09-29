@props(['items' => []])

<section class="border-b border-slate-200 bg-[#F7F7F7] px-6 py-9 md:px-12 lg:px-[clamp(64px,5vw,100px)]" aria-label="Event details" data-gtm-section="information">
    <div class="mx-auto grid max-w-7xl gap-y-7 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($items as $item)
            <div class="px-3 text-center lg:border-l lg:border-[#d9cfbd] lg:first:border-l-0">
                @if ($item['label'] ?? null)
                    <p class="text-[10px] font-semibold uppercase tracking-[.18em] text-[#A88444]">{{ $item['label'] }}</p>
                @endif
                @if ($item['value'] ?? null)
                    <p class="mt-2 text-xs leading-relaxed text-gray-600 sm:text-sm">{{ $item['value'] }}</p>
                @endif
            </div>
        @endforeach
    </div>
</section>
