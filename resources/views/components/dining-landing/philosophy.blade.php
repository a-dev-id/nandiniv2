@props([
    'settings' => null,
    'image' => null,
])

<section class="bg-white px-6 py-14 font-sans md:py-20" aria-labelledby="dining-philosophy-title">
    <div class="mx-auto grid max-w-7xl items-center gap-8 lg:grid-cols-[minmax(0,45fr)_minmax(0,55fr)] lg:gap-10">
        <div class="min-w-0">
            <p class="mb-3 text-[10px] font-medium tracking-[.18em] text-[#A88444] uppercase sm:text-xs">{{ $settings?->philosophy_eyebrow }}</p>
            <h2 id="dining-philosophy-title" class="mb-3 text-lg leading-snug font-medium text-slate-700 uppercase sm:text-xl">
                {!! nl2br(e($settings?->philosophy_heading)) !!}
            </h2>
            <p class="max-w-2xl text-xs leading-relaxed text-slate-600 sm:text-sm">
                {!! nl2br(e($settings?->philosophy_description)) !!}
            </p>
        </div>

        <div class="grid min-w-0 gap-6 md:grid-cols-[minmax(0,1fr)_140px] md:items-center md:gap-4">
            <div class="aspect-[4/3] overflow-hidden bg-white">
                @if ($image)
                    <img src="{{ $image }}" alt="{{ $settings?->philosophy_image_alt }}" class="h-full w-full object-cover object-center" width="1200" height="900" loading="lazy" decoding="async">
                @endif
            </div>
            <p class="justify-self-end text-right font-['Segoe_Script','Snell_Roundhand','Brush_Script_MT',cursive] text-[28px] leading-[1.3] text-[#d1b77d] md:rotate-[-8deg] md:justify-self-center md:text-center md:text-[26px]" aria-hidden="true">
                {!! nl2br(e($settings?->philosophy_accent_text)) !!}
            </p>
        </div>
    </div>
</section>
