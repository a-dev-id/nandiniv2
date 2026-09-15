@props(['settings' => null])

@php
    $informationItems = $settings?->visit_information_items ?? [];
    $splitAt = (int) ceil(count($informationItems) / 2);
    $informationColumns = [array_slice($informationItems, 0, $splitAt), array_slice($informationItems, $splitAt)];
    $faqs = $settings?->faq_items ?? [];
@endphp

<section class="bg-[#f3f4f5] px-6 py-14 font-sans md:py-20" aria-label="Dining practical information and frequently asked questions">
    <div class="mx-auto grid max-w-7xl gap-12 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.04fr)] lg:gap-0">
        <div class="min-w-0 lg:pr-12 xl:pr-14">
            <p class="mb-3 text-[10px] font-medium tracking-[.18em] text-[#A88444] uppercase sm:text-xs">{{ $settings?->visit_eyebrow }}</p>
            <h2 class="text-lg leading-snug font-medium text-slate-700 uppercase sm:text-xl">{!! nl2br(e($settings?->visit_heading)) !!}</h2>

            <div class="mt-8 grid gap-x-8 gap-y-6 sm:grid-cols-2">
                @foreach ($informationColumns as $column)
                    <dl class="space-y-6">
                        @foreach ($column as $item)
                            <div class="flex min-w-0 items-start gap-4">
                                @if (in_array($item['icon'] ?? '', ['clock', 'dining', 'location', 'whatsapp', 'email', 'guests'], true))
                                <svg class="mt-0.5 size-7 shrink-0 text-[#d1b77d]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    @switch($item['icon'] ?? '')
                                        @case('clock') <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/> @break
                                        @case('dining') <path d="M4 3v6a3 3 0 0 0 6 0V3M7 3v18M20 21V3c-4 2-5 7-5 11h5"/> @break
                                        @case('location') <path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/> @break
                                        @case('whatsapp') <path d="M21 11.5a9 9 0 0 1-13.4 7.9L3 21l1.5-4.6A9 9 0 1 1 21 11.5Z"/><path d="m8 7 2 3-1 1c1 2 2 3 4 4l1-1 3 1c-1 4-5 2-8-1S5 8 8 7Z"/> @break
                                        @case('email') <rect x="3" y="5" width="18" height="14" rx="1"/><path d="m4 7 8 6 8-6"/> @break
                                        @case('guests') <circle cx="9" cy="8" r="3"/><circle cx="17" cy="9" r="2"/><path d="M3 20c0-4 2-7 6-7s6 3 6 7M15 14c4 0 6 2 6 6"/> @break
                                    @endswitch
                                </svg>
                                @endif
                                <div class="min-w-0">
                                    <dt class="mb-1.5 text-[11px] leading-[1.4] font-semibold tracking-[.1em] text-[#20271f] uppercase">{{ $item['label'] ?? '' }}</dt>
                                    <dd class="text-sm leading-[1.5] text-neutral-600 md:text-[15px]">
                                        @if (filled($item['link'] ?? $item['href'] ?? null))
                                            <a href="{{ $item['link'] ?? $item['href'] }}" class="break-words transition-colors hover:text-[#8f6b34] hover:underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#8f6b34]">{!! nl2br(e($item['value'] ?? '')) !!}</a>
                                        @else
                                            {!! nl2br(e(str_replace('|', "\n", $item['value'] ?? ''))) !!}
                                        @endif
                                    </dd>
                                </div>
                            </div>
                        @endforeach
                    </dl>
                @endforeach
            </div>

            <div class="mt-9 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                @if (filled($settings?->visit_food_menu_label) && filled($settings?->visit_food_menu_url))
                    <x-buttons.link-button :href="$settings->visit_food_menu_url" variant="solid" target="_blank" rel="noopener" class="w-full whitespace-nowrap sm:w-auto">{{ $settings->visit_food_menu_label }}</x-buttons.link-button>
                @endif
                @if (filled($settings?->visit_premium_menu_label) && filled($settings?->visit_premium_menu_url))
                    <x-buttons.link-button :href="$settings->visit_premium_menu_url" variant="outline" target="_blank" rel="noopener" class="w-full whitespace-nowrap sm:w-auto">{{ $settings->visit_premium_menu_label }}</x-buttons.link-button>
                @endif
                @if (filled($settings?->visit_beverage_menu_label) && filled($settings?->visit_beverage_menu_url))
                    <x-buttons.link-button :href="$settings->visit_beverage_menu_url" variant="outline" target="_blank" rel="noopener" class="w-full whitespace-nowrap sm:w-auto">{{ $settings->visit_beverage_menu_label }}</x-buttons.link-button>
                @endif
            </div>
        </div>

        <div class="min-w-0 border-t border-[#8f6b34]/20 pt-12 lg:border-t-0 lg:border-l lg:pt-0 lg:pl-12 xl:pl-14" x-data="{ openItem: 0 }">
            <p class="mb-3 text-[10px] font-medium tracking-[.18em] text-[#A88444] uppercase sm:text-xs">{{ $settings?->faq_eyebrow }}</p>
            <h2 class="text-lg leading-snug font-medium text-slate-700 uppercase sm:text-xl">{!! nl2br(e($settings?->faq_heading)) !!}</h2>

            <div class="mt-8 space-y-3">
                @foreach ($faqs as $faq)
                    @php $answerId = 'dining-faq-answer-'.$loop->index.'-'.substr(sha1(($faq['question'] ?? '').$loop->index), 0, 8); @endphp
                    <div class="border border-[#8f6b34]/15 bg-white">
                        <button type="button" class="flex min-h-14 w-full items-center justify-between gap-5 px-5 py-3 text-left text-sm leading-[1.45] font-medium text-[#20271f] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#8f6b34] md:text-[15px]" @click="openItem = openItem === {{ $loop->iteration }} ? 0 : {{ $loop->iteration }}" :aria-expanded="(openItem === {{ $loop->iteration }}).toString()" aria-controls="{{ $answerId }}">
                            <span>{{ $faq['question'] ?? '' }}</span>
                            <svg class="size-4 shrink-0 text-[#8f6b34] transition-transform duration-200" :class="{ 'rotate-180': openItem === {{ $loop->iteration }} }" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="m3 6 5 5 5-5"/></svg>
                        </button>
                        <div id="{{ $answerId }}" x-show="openItem === {{ $loop->iteration }}" x-collapse>
                            <p class="px-5 pb-5 text-sm leading-[1.6] text-neutral-600 md:text-[15px]">{!! nl2br(e($faq['answer'] ?? '')) !!}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
