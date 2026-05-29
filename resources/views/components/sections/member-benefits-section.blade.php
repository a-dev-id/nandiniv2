@props([
'section' => null,
])

@if ($section)
@php
$items = collect($section->items ?? []);

$formatValue = function ($value) {
$value = trim((string) $value);

if ($value === '') {
return '-';
}

return $value;
};

$isCheck = fn ($value): bool => in_array(trim((string) $value), ['✓', 'check', 'yes', 'true', '1'], true);
@endphp

<section class="py-14 md:py-20 px-6 bg-white">
    <div class="max-w-[1500px] mx-auto">

        {{-- Header --}}
        <div class="mx-auto max-w-4xl text-center">
            @if ($section->subtitle)
            <p class="mb-4 text-sm md:text-base leading-relaxed tracking-[0.18em] uppercase text-[#b28a4a] font-medium">
                {{ $section->subtitle }}
            </p>
            @endif

            @if ($section->title)
            <h2 class="text-2xl sm:text-3xl md:text-3xl leading-snug tracking-[0.15em] md:tracking-[0.22em] uppercase text-slate-800 font-medium">
                {{ $section->title }}
            </h2>
            @endif

            @if ($section->description)
            <div class="mt-5 text-[15px] sm:text-base leading-relaxed text-gray-600 max-w-2xl sm:max-w-3xl md:max-w-5xl mx-auto">
                {!! $section->description !!}
            </div>
            @endif
        </div>

        {{-- Table --}}
        @if ($items->isNotEmpty())
        <div class="mt-14 overflow-x-auto">
            <table class="w-full min-w-[900px] border-collapse text-[15px] text-slate-950">
                <thead>
                    <tr class="border-b border-slate-800">
                        <th class="w-[38%] px-6 py-5 text-left text-xl tracking-[0.18em] uppercase font-semibold">
                            Member Benefits
                        </th>
                        <th class="px-6 py-5 text-center text-xl tracking-[0.18em] uppercase font-semibold">
                            Bronze
                        </th>
                        <th class="px-6 py-5 text-center text-xl tracking-[0.18em] uppercase font-semibold">
                            Silver
                        </th>
                        <th class="px-6 py-5 text-center text-xl tracking-[0.18em] uppercase font-semibold">
                            Gold
                        </th>
                        <th class="px-6 py-5 text-center text-xl tracking-[0.18em] uppercase font-semibold">
                            Platinum
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($items as $index => $item)
                    <tr class="{{ $index % 2 === 1 ? 'bg-slate-100' : 'bg-white' }}">
                        <td class="px-6 py-5 leading-6 text-left">
                            {{ $item['benefit'] ?? '' }}
                        </td>

                        @foreach (['bronze', 'silver', 'gold', 'platinum'] as $tier)
                        @php
                        $value = $formatValue($item[$tier] ?? '-');
                        @endphp

                        <td class="px-6 py-5 text-center leading-6">
                            @if ($isCheck($value))
                            <span class="text-lg leading-none">✓</span>
                            @else
                            {{ $value }}
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4 text-[15px] sm:text-base leading-relaxed italic text-gray-600">
                <i>{{ $section->excerpt }}</i>
            </div>
        </div>
        @endif
    </div>
</section>
@endif
