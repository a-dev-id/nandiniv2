@props([
'items' => [],
'label' => 'Practical information',
])

<div class="bg-[#f3f4f5] font-sans text-[#20271f]" aria-label="{{ $label }}">
    <dl class="mx-auto grid max-w-7xl grid-cols-2 px-6 py-2 md:py-6 lg:grid-cols-4">
        @foreach ($items as $item)
        <div class="flex flex-col items-start gap-3 border-r border-[#d1b77d]/25 py-[22px] pl-4 max-lg:odd:pl-0 max-lg:even:border-r-0 max-lg:nth-[-n+2]:border-b md:flex-row md:items-center md:gap-3.5 md:p-5 lg:px-5 lg:py-1.5 lg:first:pl-0 lg:last:border-r-0 lg:last:pr-0">
            @if (in_array($item['icon'] ?? '', ['clock', 'calendar', 'dining', 'location', 'phone', 'whatsapp', 'email', 'guests'], true))
            <svg class="size-[26px] shrink-0 text-[#d1b77d]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                @switch($item['icon'] ?? '')
                @case('clock')
                <circle cx="12" cy="12" r="9" />
                <path d="M12 7v5l3 2" /> @break
                @case('calendar')
                <rect x="3" y="5" width="18" height="16" rx="2" />
                <path d="M8 3v4M16 3v4M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 17.5h.01M12 17.5h.01" /> @break
                @case('dining')
                <path d="M4 3v6a3 3 0 0 0 6 0V3M7 3v18M20 21V3c-4 2-5 7-5 11h5" /> @break
                @case('location')
                <path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z" />
                <circle cx="12" cy="10" r="2.5" /> @break
                @case('phone')
                <path d="M7.2 3.5 10 8 8.2 9.8a15.5 15.5 0 0 0 6 6L16 14l4.5 2.8c.5.3.7.9.5 1.4-.6 1.7-2.2 2.8-4 2.8C9.3 21 3 14.7 3 7c0-1.8 1.1-3.4 2.8-4 .5-.2 1.1 0 1.4.5Z" /> @break
                @case('whatsapp')
                <path d="M21 11.5a9 9 0 0 1-13.4 7.9L3 21l1.5-4.6A9 9 0 1 1 21 11.5Z" />
                <path d="m8 7 2 3-1 1c1 2 2 3 4 4l1-1 3 1c-1 4-5 2-8-1S5 8 8 7Z" /> @break
                @case('email')
                <rect x="3" y="5" width="18" height="14" rx="1" />
                <path d="m4 7 8 6 8-6" /> @break
                @case('guests')
                <circle cx="9" cy="8" r="3" />
                <circle cx="17" cy="9" r="2" />
                <path d="M3 20c0-4 2-7 6-7s6 3 6 7M15 14c4 0 6 2 6 6" /> @break
                @endswitch
            </svg>
            @endif

            <div>
                <dt class="mb-[7px] text-[10px] font-medium uppercase tracking-[.08em] md:tracking-[.12em]">{{ $item['label'] ?? '' }}</dt>
                <dd class="text-[13px] leading-[1.5] md:text-sm">
                    @if (filled($item['link'] ?? null))
                    <a class="-my-[13px] inline-flex min-h-12 items-center" href="{{ $item['link'] }}" @if (str_starts_with($item['link'], 'http' )) target="_blank" rel="noopener noreferrer" @endif>{!! nl2br(e($item['value'] ?? '')) !!}</a>
                    @else
                    {!! nl2br(e($item['value'] ?? '')) !!}
                    @endif
                </dd>
            </div>
        </div>
        @endforeach
    </dl>
</div>