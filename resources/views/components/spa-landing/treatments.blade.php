@props([
    'treatments',
    'settings' => null,
])

@php
    $resolveImage = static function (?string $path): ?string {
        if (blank($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->url($path);
    };

    $formatDuration = static function (?string $excerpt): ?string {
        preg_match('/\b(\d+(?:\.\d+)?|one|two|three|four)[-\s](minute|hour)s?\b/i', (string) $excerpt, $matches);

        if (blank($matches[0] ?? null)) {
            return null;
        }

        $number = strtolower($matches[1]);
        $number = [
            'one' => '1',
            'two' => '2',
            'three' => '3',
            'four' => '4',
        ][$number] ?? $number;
        $unit = strtoupper($matches[2]);

        return $number.' '.$unit.($number === '1' ? '' : 'S');
    };

@endphp

<section id="treatments" class="scroll-mt-20 bg-white px-6 py-14 font-sans md:px-12 md:py-20 lg:px-6" aria-labelledby="spa-treatments-title" data-gtm-section="treatments">
    <div class="mx-auto max-w-7xl">
        <header class="mx-auto mb-9 max-w-4xl text-center md:mb-12">
                <p class="mb-3 text-[10px] font-medium uppercase tracking-[.18em] text-[#A88444] sm:text-xs">SPA TREATMENTS</p>
                <h2 id="spa-treatments-title" class="text-lg leading-snug font-medium text-slate-700 uppercase sm:text-xl">
                SPA TREATMENTS AT ESSENCE SPA
                </h2>
            <p class="mx-auto mt-5 max-w-3xl text-xs leading-relaxed text-slate-600 sm:text-sm">
                Discover restorative treatments inspired by Balinese healing traditions and Nandini's jungle setting. From traditional therapies to relaxing rituals for body and mind, each spa experience is designed to restore balance and encourage deep relaxation.
            </p>
        </header>

        @if ($treatments->isNotEmpty())
            <div class="grid grid-cols-1 gap-x-7 gap-y-12 md:grid-cols-2 lg:grid-cols-3 lg:gap-x-8 lg:gap-y-16">
                @foreach ($treatments as $treatment)
                    @php
                        $image = $resolveImage($treatment->preview_image);
                        $isSignature = str_contains(strtolower($treatment->title), 'signature');
                        $duration = $formatDuration($treatment->excerpt);
                    @endphp
                    <article class="group flex h-full flex-col" aria-labelledby="spa-treatment-{{ $treatment->id }}">
                        <div class="relative aspect-4/3 w-full overflow-hidden bg-[#ebe9e2]">
                            @if ($image)
                                <img src="{{ $image }}" alt="{{ $treatment->image_alt ?: $treatment->title }}" class="h-full w-full object-cover object-center transition duration-700 ease-out group-hover:scale-[1.025]" width="1200" height="900" loading="lazy" decoding="async">
                            @else
                                <div class="flex h-full items-center justify-center px-6 text-center text-xs font-medium uppercase tracking-[.18em] text-[#A88444]" aria-hidden="true">ESSENCE SPA</div>
                            @endif
                            @if ($isSignature)
                                <p class="absolute bottom-0 left-0 bg-[#26342e] px-4 py-2 text-[9px] font-medium uppercase tracking-[.18em] text-[#f5ead0]">Signature Experience</p>
                            @endif
                        </div>
                        <div class="flex flex-1 flex-col border border-t-0 border-slate-200 bg-white px-5 pt-5 pb-6 sm:px-6">
                            @if ($duration)<p class="mb-3 min-h-5 text-[10px] font-medium uppercase tracking-[.14em] text-[#A88444]">{{ $duration }}</p>@endif
                            <h3 id="spa-treatment-{{ $treatment->id }}" class="font-sans text-base leading-snug font-semibold text-slate-700 uppercase [--heading-letter-spacing:.06em] sm:text-lg">
                                {{ $treatment->title }}
                            </h3>
                            @if ($treatment->excerpt)
                                <p class="mt-3 flex-1 text-xs leading-relaxed text-slate-600 sm:text-sm">{{ $treatment->excerpt }}</p>
                            @endif
                            <div class="mt-6 flex flex-wrap items-center gap-3">
                                <x-buttons.link-button :href="route('voucher.show', $treatment)" variant="outline" class="min-h-10 flex-1 px-4 text-xs sm:flex-none">
                                    MORE DETAILS
                                </x-buttons.link-button>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
