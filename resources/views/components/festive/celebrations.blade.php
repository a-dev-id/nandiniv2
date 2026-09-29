@props(['items' => []])

<section aria-label="Festive dining experiences" data-gtm-section="celebrations">
    @foreach ($items as $item)
        @php
            $reverse = $loop->even;
            $anchor = trim((string) ($item['anchor'] ?? ''));
        @endphp
        <article @if ($anchor !== '') id="{{ $anchor }}" @endif @class([
            'grid scroll-mt-20 lg:min-h-[540px] lg:grid-cols-2',
            'bg-[#F7F7F7]' => $anchor === 'christmas',
            'bg-white' => $anchor !== 'christmas',
        ])>
            <div @class([
                'min-h-[340px] overflow-hidden md:min-h-[440px] lg:min-h-[540px]',
                'lg:order-2' => $reverse,
            ])>
                @if ($item['image_url'] ?? null)
                    <img src="{{ $item['image_url'] }}" alt="{{ $item['image_alt'] ?? '' }}" class="h-full w-full object-cover object-center" width="1440" height="1080" loading="lazy" decoding="async">
                @endif
            </div>
            <div @class([
                'flex items-center px-6 py-12 md:px-12 md:py-16 lg:px-[clamp(64px,7vw,120px)] lg:py-20',
                'lg:order-1' => $reverse,
            ])>
                <div class="max-w-xl">
                    @if ($item['date'] ?? null)
                        <p class="mb-4 text-[10px] font-medium uppercase tracking-[.18em] text-[#A88444] sm:text-xs">{{ $item['date'] }}</p>
                    @endif
                    @if ($item['heading'] ?? null)
                        <h2 class="text-lg font-medium uppercase leading-snug text-slate-700 [--heading-letter-spacing:.15em] sm:text-xl">{!! nl2br(e($item['heading'])) !!}</h2>
                    @endif
                    @if ($item['description'] ?? null)
                        <p class="mt-3 text-xs leading-relaxed text-gray-600 sm:text-sm">{!! nl2br(e($item['description'])) !!}</p>
                    @endif
                    @if ($item['price'] ?? null)
                        <p class="my-5 text-xs font-semibold tracking-[.08em] text-[#A88444] sm:text-sm">{{ $item['price'] }}</p>
                    @endif
                    @if (($item['button_label'] ?? null) && ($item['button_url'] ?? null))
                        <x-buttons.link-button :href="$item['button_url']" variant="solid">{{ $item['button_label'] }}</x-buttons.link-button>
                    @endif
                </div>
            </div>
        </article>
    @endforeach
</section>
