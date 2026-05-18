@props([
'section' => null,
])

@if ($section)
@php
$items = collect($section->items ?? []);
$itemCount = $items->count();

$backgroundColor = $section->background_color ?: 'white';

$backgroundClass = match ($backgroundColor) {
'soft_gray' => 'bg-slate-50',
'warm_ivory' => 'bg-[#fbf8f1]',
'light_gold' => 'bg-[#f6efe2]',
'dark_navy' => 'bg-[#071a33]',
default => 'bg-white',
};

$titleColorClass = $backgroundColor === 'dark_navy'
? 'text-white'
: 'text-slate-900';

$descriptionColorClass = $backgroundColor === 'dark_navy'
? 'text-white/85'
: 'text-slate-700';

$gridClass = match (true) {
$itemCount >= 4 => 'max-w-7xl lg:grid-cols-4',
$itemCount === 3 => 'max-w-5xl lg:grid-cols-3',
$itemCount === 2 => 'max-w-3xl lg:grid-cols-2',
default => 'max-w-sm lg:grid-cols-1',
};

$icons = [
'home' => '
<path d="M3 11.5 12 4l9 7.5" />
<path d="M5 10.5V20h14v-9.5" />
<path d="M10 20v-6h4v6" />',

'cup' => '
<path d="M6 3h10v11a5 5 0 0 1-10 0V3z" />
<path d="M16 7h2a3 3 0 0 1 0 6h-2" />
<path d="M4 21h16" />',

'heart' => '
<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78z" />',

'user' => '
<path d="M20 21a8 8 0 0 0-16 0" />
<circle cx="12" cy="7" r="4" />',

'book' => '
<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
<path d="M4 4.5A2.5 2.5 0 0 1 6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5z" />',

'arrow-up' => '
<path d="m5 12 7-7 7 7" />
<path d="M12 19V5" />',

'gift' => '
<rect x="3" y="8" width="18" height="4" rx="1" />
<path d="M12 8v13" />
<path d="M19 12v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7" />
<path d="M7.5 8a2.5 2.5 0 1 1 5 0" />
<path d="M12 8a2.5 2.5 0 1 1 5 0" />',

'star' => '
<path d="m12 2 3.09 6.26L22 9.27l-5 4.87L18.18 21 12 17.77 5.82 21 7 14.14l-5-4.87 6.91-1.01L12 2z" />',

'sparkles' => '
<path d="m12 3-1.9 5.8L4 11l6.1 2.2L12 19l1.9-5.8L20 11l-6.1-2.2L12 3z" />',
];
@endphp

<section class="py-16 md:py-24 px-6 {{ $backgroundClass }}">
    <div class="mx-auto max-w-7xl text-center">

        @if ($section->subtitle)
        <p class="mb-4 text-sm md:text-base leading-relaxed tracking-[0.18em] uppercase text-[#b28a4a] font-medium">
            {{ $section->subtitle }}
        </p>
        @endif

        @if ($section->title)
        <h2 class="text-2xl sm:text-3xl md:text-3xl leading-snug tracking-[0.18em] md:tracking-[0.25em] uppercase font-medium {{ $titleColorClass }}">
            {{ $section->title }}
        </h2>
        @endif

        @if ($section->description)
        <div class="mt-5 max-w-3xl mx-auto text-[15px] md:text-base leading-8 {{ $descriptionColorClass }}">
            {!! $section->description !!}
        </div>
        @endif

        @if ($items->isNotEmpty())
        <div class="mx-auto mt-16 grid grid-cols-1 gap-x-12 gap-y-14 sm:grid-cols-2 {{ $gridClass }}">
            @foreach ($items as $item)
            @php
            $iconKey = $item['icon'] ?? 'user';
            $iconSvg = $icons[$iconKey] ?? $icons['user'];
            @endphp

            <div class="flex flex-col items-center text-center">
                <div class="mb-7 text-[#b28a4a]">
                    <svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        {!! $iconSvg !!}
                    </svg>
                </div>

                @if (! empty($item['title']))
                <h3 class="text-xl md:text-2xl tracking-[0.18em] uppercase font-medium leading-snug {{ $titleColorClass }}">
                    {{ $item['title'] }}
                </h3>
                @endif

                @if (! empty($item['description']))
                <p class="mt-4 max-w-xs text-[15px] leading-7 {{ $descriptionColorClass }}">
                    {{ $item['description'] }}
                </p>
                @endif
            </div>
            @endforeach
        </div>
        @endif

    </div>
</section>
@endif