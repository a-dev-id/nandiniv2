@props(['event', 'items' => []])

<section class="font-sans" aria-labelledby="festive-menu-title" data-gtm-section="menu">
    <div class="bg-white px-6 py-14 md:px-12 md:py-20 lg:px-[clamp(64px,5vw,100px)]">
        <div class="mx-auto max-w-7xl">
        <div class="mx-auto max-w-3xl pb-14 text-center md:pb-16">
            @if ($event->hero_eyebrow)
                <p class="mb-4 text-[10px] font-medium uppercase tracking-[.18em] text-[#A88444] sm:text-xs">{{ $event->hero_eyebrow }}</p>
            @endif
            @if ($event->hero_heading)
                <h1 class="text-xl font-medium uppercase leading-snug text-slate-700 [--heading-letter-spacing:.15em] sm:text-2xl">{!! nl2br(e($event->hero_heading)) !!}</h1>
            @endif
            @if ($event->hero_subheading)
                <p class="mt-3 text-xs font-medium uppercase tracking-[.08em] text-slate-500 sm:text-sm">{{ $event->hero_subheading }}</p>
            @endif
            @if ($event->hero_description)
                <p class="mx-auto mt-3 max-w-3xl text-xs leading-relaxed text-gray-600 sm:text-sm">{!! nl2br(e($event->hero_description)) !!}</p>
            @endif
            @if ($event->hero_price)
                <p class="mt-4 text-xs font-semibold tracking-[.08em] text-[#A88444] sm:text-sm">{{ $event->hero_price }}</p>
            @endif
            @if ($event->hero_button_label && $event->hero_button_url)
                <div class="mt-7">
                    <x-buttons.link-button :href="$event->hero_button_url" variant="solid">{{ $event->hero_button_label }}</x-buttons.link-button>
                </div>
            @endif
        </div>
        </div>
    </div>

    <div class="bg-[#F7F7F7] px-6 py-14 md:px-12 md:py-20 lg:px-[clamp(64px,5vw,100px)]">
        <div class="w-full">
            <div class="mx-auto mb-12 max-w-3xl text-center md:mb-16">
                @if ($event->menu_eyebrow)
                    <p class="mb-4 text-[10px] font-medium uppercase tracking-[.18em] text-[#A88444] sm:text-xs">{{ $event->menu_eyebrow }}</p>
                @endif
                @if ($event->menu_heading)
                    <h2 id="festive-menu-title" class="text-xl font-medium uppercase leading-snug text-slate-700 [--heading-letter-spacing:.15em] sm:text-2xl">{!! nl2br(e($event->menu_heading)) !!}</h2>
                @endif
                @if ($event->menu_description)
                    <p class="mx-auto mt-3 max-w-3xl text-xs leading-relaxed text-gray-600 sm:text-sm">{!! nl2br(e($event->menu_description)) !!}</p>
                @endif
            </div>

            <div class="flex w-full flex-wrap items-start justify-center gap-x-8 gap-y-12 lg:gap-x-10 lg:gap-y-16">
                @foreach ($items as $item)
                    @if (($item['type'] ?? 'dish') === 'intermezzo')
                        <article class="w-full border-y border-slate-200 bg-white px-6 py-10 text-center md:py-12">
                            @if ($item['label'] ?? null)
                                <p class="text-[10px] font-semibold uppercase tracking-[.18em] text-[#A88444]">{{ $item['label'] }}</p>
                            @endif
                            @if ($item['title'] ?? null)
                                <h3 class="mt-3 text-base font-semibold uppercase leading-snug text-slate-700 [--heading-letter-spacing:.1em] sm:text-lg">{{ $item['title'] }}</h3>
                            @endif
                            @if ($item['description'] ?? null)
                                <p class="mx-auto mt-3 max-w-xl text-xs leading-relaxed text-gray-600 sm:text-sm">{{ $item['description'] }}</p>
                            @endif
                        </article>
                    @else
                        <article class="w-full md:w-[calc(50%_-_1rem)] lg:w-[calc(25%_-_1.875rem)]">
                            @if ($item['image_url'] ?? null)
                                <div class="aspect-[4/3] overflow-hidden bg-slate-100">
                                    <img src="{{ $item['image_url'] }}" alt="{{ $item['image_alt'] ?? '' }}" class="h-full w-full object-cover transition duration-700 hover:scale-[1.02]" width="1200" height="900" loading="lazy" decoding="async">
                                </div>
                            @endif
                            <div class="pt-6">
                                @if ($item['label'] ?? null)
                                    <p class="mb-2 text-[10px] font-semibold uppercase tracking-[.18em] text-[#A88444]">{{ $item['label'] }}</p>
                                @endif
                                @if ($item['title'] ?? null)
                                    <h3 class="text-base font-semibold uppercase leading-snug text-slate-700 [--heading-letter-spacing:.1em] sm:text-lg">{{ $item['title'] }}</h3>
                                @endif
                                @if ($item['description'] ?? null)
                                    <p class="mt-3 text-xs leading-relaxed text-gray-600 sm:text-sm">{{ $item['description'] }}</p>
                                @endif
                            </div>
                        </article>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</section>
