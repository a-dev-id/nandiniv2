@props([
    'image',
    'imageAlt',
    'eyebrow',
    'title',
    'reverse' => false,
    'background' => 'white',
    'sectionName' => null,
])

@php
$backgroundClass = $background === 'muted' ? 'bg-[#F7F7F7]' : 'bg-white';
@endphp

<section class="{{ $backgroundClass }} py-14 md:py-28" @if ($sectionName) data-gtm-section="{{ $sectionName }}" @endif>
    <div class="mx-auto grid w-full max-w-screen-2xl grid-cols-1 items-stretch gap-8 px-4 sm:px-6 lg:grid-cols-12 lg:gap-10 lg:px-8">
        <div class="relative min-h-[360px] overflow-hidden sm:min-h-[480px] lg:col-span-7 lg:min-h-[560px] {{ $reverse ? 'lg:order-2' : 'lg:order-1' }}">
            @if ($image)
                <img src="{{ $image }}" alt="{{ $imageAlt }}" class="absolute inset-0 h-full w-full object-cover" width="1200" height="900" loading="lazy" decoding="async">
            @endif
        </div>
        <div class="flex items-center lg:col-span-5 {{ $reverse ? 'lg:order-1' : 'lg:order-2' }}">
            <div class="w-full px-2 text-center sm:px-8 lg:px-12">
                <p class="mb-2 text-xs uppercase text-slate-500 sm:text-sm">{{ $eyebrow }}</p>
                <h2 class="mb-3 text-lg font-medium uppercase leading-snug text-slate-700 sm:text-xl">{{ $title }}</h2>
                <div class="text-xs leading-relaxed text-gray-600 sm:text-sm">
                    {{ $slot }}
                </div>
                @isset($actions)
                    <div class="mt-8 flex flex-wrap justify-center gap-3">
                        {{ $actions }}
                    </div>
                @endisset
            </div>
        </div>
    </div>
</section>
