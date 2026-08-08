@props([
    'event',
    'scheduleMode' => 'default',
])

@php
$scheduleLabel = $scheduleMode === 'upcoming'
    ? $event->upcoming_schedule_label
    : $event->schedule_label;
@endphp

<article class="flex h-full flex-col overflow-hidden border border-slate-200 bg-white">
    <div class="group aspect-[12/17] overflow-hidden bg-slate-100">
        <img src="{{ Storage::disk('public')->url($event->image) }}" alt="{{ $event->alt_text }}" class="h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" width="600" height="850" loading="lazy" decoding="async">
    </div>

    <div class="flex flex-1 flex-col px-5 py-6 sm:px-7">
        <p class="text-xs font-medium uppercase tracking-[0.08em] text-[#A88444] sm:text-sm">
            {{ $event->event_type->label() }}
        </p>
        @if (filled($scheduleLabel))
        <p class="mt-2 text-xs tracking-[0.06em] text-slate-500 sm:text-sm">{{ $scheduleLabel }}</p>
        @endif

        <h3 class="mt-4 text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">{{ $event->title }}</h3>

        <div class="mt-auto pt-6">
            <a href="https://cho.pe/wildginger.web" target="_blank" rel="noopener noreferrer" class="inline-flex w-fit items-center justify-center border border-[#A88444] bg-[#A88444] px-5 py-3 text-xs font-medium uppercase tracking-[0.08em] text-white transition duration-300 hover:border-[#B8945B] hover:bg-[#B8945B] hover:text-white sm:text-sm">
                Reserve a table
            </a>
        </div>
    </div>
</article>
