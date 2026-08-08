@props([
    'events',
    'scheduleMode' => 'default',
    'label' => 'events',
])

<div class="item-carousel-wrap relative mx-auto px-10 sm:px-14 lg:px-16">
    <div class="itemcarousel-slick" data-total="{{ $events->count() }}">
        @foreach ($events as $event)
            <div class="flex h-full px-2 sm:px-3">
                <x-events.card :event="$event" :schedule-mode="$scheduleMode" />
            </div>
        @endforeach
    </div>

    <button
        type="button"
        class="itemcarousel-prev fold-carousel-arrow absolute left-0 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center bg-[#A88444] text-white transition hover:bg-[#B8945B] disabled:cursor-default disabled:opacity-40 md:h-12 md:w-12"
        aria-label="Previous {{ $label }}"
    >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="h-4 w-4" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"></path>
        </svg>
    </button>

    <button
        type="button"
        class="itemcarousel-next fold-carousel-arrow absolute right-0 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center bg-[#A88444] text-white transition hover:bg-[#B8945B] disabled:cursor-default disabled:opacity-40 md:h-12 md:w-12"
        aria-label="Next {{ $label }}"
    >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="h-4 w-4" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"></path>
        </svg>
    </button>
</div>
