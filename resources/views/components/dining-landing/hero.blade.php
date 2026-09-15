@props(['image' => null, 'settings' => null])

<section class="font-sans text-white [&_a:focus-visible]:outline-2 [&_a:focus-visible]:outline-offset-[5px] [&_a:focus-visible]:outline-[#d1b77d]" aria-labelledby="dining-title">
    <div class="relative aspect-[4/3] overflow-hidden bg-[#1a3028] md:aspect-auto md:min-h-[max(700px,100svh)] lg:min-h-[max(720px,100vh)]">
        @if ($settings?->hero_video_id)
            <div class="absolute inset-0">
                <x-heroes.video-hero :video-id="$settings->hero_video_id" :poster="$image" :hide-mobile-overlay="true" />
            </div>
        @elseif ($image)
            <img class="absolute inset-0 h-full w-full object-cover object-[65%_center] md:object-[center_60%]" src="{{ $image }}" alt="" fetchpriority="high" decoding="async">
        @endif
        <div class="absolute inset-0 hidden h-full w-full bg-[linear-gradient(90deg,rgba(0,0,0,.68)_0%,rgba(0,0,0,.48)_35%,rgba(0,0,0,.18)_65%,rgba(0,0,0,.12)_100%),linear-gradient(0deg,rgba(0,0,0,.35),transparent_50%)] md:block" aria-hidden="true"></div>
        <div class="relative flex h-full w-full items-center px-6 pt-20 pb-6 md:min-h-[inherit] md:px-12 md:pt-36 md:pb-12 lg:px-[clamp(64px,5vw,100px)]">
            <div class="hidden max-w-xl md:block">
                <p class="mb-4 text-[10px] font-medium tracking-[.18em] text-[#A88444] uppercase sm:text-xs">{{ $settings?->hero_eyebrow }}</p>
                <h1 id="dining-title" class="mb-5 font-span text-4xl leading-[1.05] [--heading-font-weight:400] [--heading-letter-spacing:-.025em] sm:text-5xl">{!! nl2br(e($settings?->hero_heading)) !!}</h1>
                <p class="mb-3 hidden text-xs leading-relaxed font-medium sm:block sm:text-sm">{!! nl2br(e($settings?->hero_subheading)) !!}</p>
                <p class="mb-6 hidden max-w-lg text-xs leading-relaxed text-white/85 sm:block sm:text-sm">{!! nl2br(e($settings?->hero_description)) !!}</p>
                <div class="flex flex-wrap gap-3">
                    @if (filled($settings?->hero_primary_cta_label) && filled($settings?->hero_primary_cta_url))
                        <x-buttons.link-button :href="$settings->hero_primary_cta_url" variant="solid">{{ $settings->hero_primary_cta_label }}</x-buttons.link-button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @php
        $informationItems = $settings?->information_bar_items ?? [];
    @endphp
    <div class="bg-[#f3f4f5] text-[#20271f]">
        <dl class="mx-auto grid max-w-7xl grid-cols-2 px-6 py-2 md:py-6 lg:grid-cols-4">
            @foreach ($informationItems as $item)
                <div class="flex flex-col items-start gap-3 border-r border-[#d1b77d]/25 py-[22px] pl-4 max-lg:odd:pl-0 max-lg:even:border-r-0 max-lg:nth-[-n+2]:border-b md:flex-row md:items-center md:gap-3.5 md:p-5 lg:px-5 lg:py-1.5 lg:first:pl-0 lg:last:border-r-0 lg:last:pr-0">
                    @if (in_array($item['icon'] ?? '', ['clock', 'dining', 'location', 'whatsapp', 'email', 'guests'], true))
                    <svg class="size-[26px] shrink-0 text-[#d1b77d]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
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
                    <div>
                        <dt class="mb-[7px] text-[10px] font-medium tracking-[.08em] uppercase md:tracking-[.12em]">{{ $item['label'] ?? '' }}</dt>
                        <dd class="text-[13px] leading-[1.5] md:text-sm">
                            @if (filled($item['link'] ?? null))
                                <a class="-my-[13px] inline-flex min-h-12 items-center" href="{{ $item['link'] }}">{!! nl2br(e($item['value'] ?? '')) !!}</a>
                            @else
                                {!! nl2br(e($item['value'] ?? '')) !!}
                            @endif
                        </dd>
                    </div>
                </div>
            @endforeach
        </dl>
    </div>
</section>
