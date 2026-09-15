@props(['settings' => null])

@php
    $items = $settings?->information_bar_items ?? [];

    if (blank($items)) {
        $items = [
            ['icon' => 'clock', 'label' => 'Opening Hours', 'value' => '08:00 AM – 10:00 PM'],
            ['icon' => 'calendar', 'label' => 'Booking', 'value' => 'Advance booking recommended'],
            ['icon' => 'location', 'label' => 'Location', 'value' => 'Nandini Jungle, Ubud, Bali'],
            [
                'icon' => 'phone',
                'label' => 'Reservations',
                'value' => ($settings?->reservation_whatsapp ?: '+62 812 3687 1170')."\n(WhatsApp)",
                'link' => $settings?->reservation_url ?: 'https://wa.me/6281236871170',
            ],
        ];
    }
@endphp

<section class="bg-[#f3f4f5] font-sans text-[#20271f]" aria-label="Spa information">
    <dl class="mx-auto grid max-w-7xl grid-cols-2 px-6 py-2 md:py-6 lg:grid-cols-4">
        @foreach ($items as $item)
            <div class="flex flex-col items-start gap-3 border-r border-[#d1b77d]/25 py-[22px] pl-4 max-lg:odd:pl-0 max-lg:even:border-r-0 max-lg:nth-[-n+2]:border-b md:flex-row md:items-center md:gap-3.5 md:p-5 lg:px-5 lg:py-1.5 lg:first:pl-0 lg:last:border-r-0 lg:last:pr-0">
                @if (in_array($item['icon'] ?? '', ['clock', 'calendar', 'location', 'phone'], true))
                    <svg class="size-[26px] shrink-0 text-[#d1b77d]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        @switch($item['icon'])
                            @case('clock') <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/> @break
                            @case('calendar') <rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 17.5h.01M12 17.5h.01"/> @break
                            @case('location') <path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/> @break
                            @case('phone') <path d="M7.2 3.5 10 8 8.2 9.8a15.5 15.5 0 0 0 6 6L16 14l4.5 2.8c.5.3.7.9.5 1.4-.6 1.7-2.2 2.8-4 2.8C9.3 21 3 14.7 3 7c0-1.8 1.1-3.4 2.8-4 .5-.2 1.1 0 1.4.5Z"/> @break
                        @endswitch
                    </svg>
                @endif

                <div>
                    <dt class="mb-[7px] text-[10px] font-medium uppercase tracking-[.08em] md:tracking-[.12em]">{{ $item['label'] ?? '' }}</dt>
                    <dd class="text-[13px] leading-[1.5] md:text-sm">
                        @if (filled($item['link'] ?? null))
                            <a class="-my-[13px] inline-flex min-h-12 items-center" href="{{ $item['link'] }}" @if (str_starts_with($item['link'], 'http')) target="_blank" rel="noopener noreferrer" @endif>{!! nl2br(e($item['value'] ?? '')) !!}</a>
                        @else
                            {!! nl2br(e($item['value'] ?? '')) !!}
                        @endif
                    </dd>
                </div>
            </div>
        @endforeach
    </dl>
</section>
