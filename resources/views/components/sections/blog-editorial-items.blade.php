@props([
    'items' => [],
    'mutedTextColorClass' => 'text-slate-700',
])

@php
    $presentation = data_get($items, 'presentation');
    $entries = collect(data_get($items, 'entries', []))
        ->filter(fn ($entry) => is_array($entry));
    $sourceLabel = data_get($items, 'source.label');
    $sourceUrl = data_get($items, 'source.url');
@endphp

@if ($entries->isNotEmpty() && $presentation === 'facts')
    <dl class="mt-8 grid border-y border-[#D8C6A8] text-left sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($entries as $entry)
            <div class="border-b border-[#E9DFCF] px-1 py-6 last:border-b-0 sm:px-5 sm:[&:nth-child(odd)]:border-r lg:border-b-0 lg:border-r lg:first:pl-0 lg:last:border-r-0 lg:last:pr-0">
                <dt class="font-span text-xl font-normal leading-tight text-[#8F6B34] sm:text-2xl">
                    {{ data_get($entry, 'label') }}
                </dt>
                <dd class="mt-2 text-sm leading-relaxed {{ $mutedTextColorClass }}">
                    {{ data_get($entry, 'description') }}
                </dd>
            </div>
        @endforeach
    </dl>

    @if ($sourceLabel && $sourceUrl)
        <p class="mt-4 text-left text-xs leading-relaxed text-slate-500">
            Source:
            <a href="{{ $sourceUrl }}" target="_blank" rel="noopener noreferrer" class="underline decoration-slate-300 underline-offset-2 transition hover:text-slate-700">
                {{ $sourceLabel }}
            </a>
        </p>
    @endif
@elseif ($entries->isNotEmpty() && $presentation === 'blocks')
    <div class="mt-8 grid gap-4 text-left sm:grid-cols-2 {{ $entries->count() === 3 ? 'lg:grid-cols-3' : '' }}">
        @foreach ($entries as $entry)
            <article class="border border-[#E6DDCE] bg-white p-5 sm:p-6">
                <h3 class="text-sm font-medium uppercase leading-relaxed tracking-[0.08em] text-slate-700">
                    {{ data_get($entry, 'title') }}
                </h3>
                <p class="mt-3 text-sm leading-relaxed {{ $mutedTextColorClass }}">
                    {{ data_get($entry, 'description') }}
                </p>
            </article>
        @endforeach
    </div>
@elseif ($entries->isNotEmpty() && $presentation === 'links')
    <nav class="mt-8 grid gap-3 text-left sm:grid-cols-2" aria-label="Related Ayung River experiences">
        @foreach ($entries as $entry)
            <a href="{{ data_get($entry, 'url') }}" class="group flex items-center justify-between border border-[#D8C6A8] px-5 py-4 text-xs font-medium uppercase tracking-[0.08em] text-slate-700 transition hover:border-[#A88444] hover:bg-[#F8F4EC] hover:text-[#8F6B34] sm:text-sm">
                <span>{{ data_get($entry, 'label') }}</span>
                <span class="ml-4 text-[#A88444] transition group-hover:translate-x-1" aria-hidden="true">→</span>
            </a>
        @endforeach
    </nav>
@endif
