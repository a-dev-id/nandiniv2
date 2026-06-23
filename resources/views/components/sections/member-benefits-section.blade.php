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

$tiers = [
'bronze' => 'Dana',
'silver' => 'Upaya',
'gold' => 'Dhyana',
'platinum' => 'Jnana',
];
@endphp

<section class="bg-white px-0 py-12 md:px-6 md:py-20">
    <div class="mx-auto max-w-[1500px]">

        {{-- Header --}}
        <div class="mx-auto max-w-4xl px-5 text-center md:px-0">
            @if ($section->subtitle)
            <p class="mb-4 text-sm font-medium uppercase leading-relaxed text-[#b28a4a] md:text-base">
                {{ $section->subtitle }}
            </p>
            @endif

            @if ($section->title)
            <h2 class="text-xl font-medium uppercase leading-snug mb-3">
                {{ $section->title }}
            </h2>
            @endif

            @if ($section->description)
            <div class="mx-auto mt-2 max-w-2xl text-sm leading-relaxed text-[#4b5563] sm:max-w-3xl md:max-w-5xl">
                {!! $section->description !!}
            </div>
            @endif
        </div>

        @if ($items->isNotEmpty())

        {{-- Mobile Google-style Comparison Table --}}
        <div class="mt-10 px-5 md:hidden">
            <div class="border border-[#e7dfcf] bg-white shadow-[0_10px_30px_rgba(15,23,42,0.08)]">
                <table class="w-full table-fixed border-collapse text-[#10233f]">
                    <thead>
                        <tr class="border-b border-[#d8c49a] bg-[#fbfaf7]">
                            <th class="w-[45%] border-r border-[#e7dfcf] px-3 py-4 text-left text-[11px] font-semibold uppercase text-[#10233f]">
                                Benefits
                            </th>

                            @foreach ($tiers as $tierLabel)
                            <th class="w-[13.75%] border-r border-[#e7dfcf] px-1 py-3 last:border-r-0">
                                <div class="mx-auto flex h-24 items-center justify-center">
                                    <span class="[writing-mode:vertical-rl] rotate-180 text-[11px] font-semibold uppercase text-[#10233f]">
                                        {{ $tierLabel }}
                                    </span>
                                </div>
                            </th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($items as $index => $item)
                        <tr class="{{ $index % 2 === 1 ? 'bg-[#fbfaf7]' : 'bg-white' }}">
                            <td class="border-r border-t border-[#e7dfcf] px-3 py-4 text-[13px] font-medium leading-relaxed text-[#10233f]">
                                {{ $item['benefit'] ?? '' }}
                            </td>

                            @foreach ($tiers as $tierKey => $tierLabel)
                            @php
                            $value = $formatValue($item[$tierKey] ?? '-');
                            @endphp

                            <td class="border-r border-t border-[#e7dfcf] px-1 py-4 text-center last:border-r-0">
                                @if ($isCheck($value))
                                <span class="inline-flex h-6 w-6 items-center justify-center text-base font-semibold leading-none text-[#b28a4a]">
                                    ✓
                                </span>
                                @elseif ($value === '-')
                                <span class="text-sm text-[#d8c49a]">
                                    —
                                </span>
                                @else
                                <span class="block break-words text-[11px] font-semibold leading-snug text-[#10233f]">
                                    {{ $value }}
                                </span>
                                @endif
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Desktop Table --}}
        <div class="mt-14 hidden px-6 md:block">
            <table class="w-full table-fixed border-collapse text-sm text-[#10233f] text-sm">
                <thead>
                    <tr class="border-b border-[#b28a4a]">
                        <th class="w-[40%] px-4 py-5 text-left text-base font-semibold uppercase text-[#10233f] lg:px-6 lg:text-xl">
                            Member Benefits
                        </th>

                        @foreach ($tiers as $tierLabel)
                        <th class="w-[15%] px-3 py-5 text-center text-base font-semibold uppercase text-[#10233f] lg:px-6 lg:text-xl">
                            {{ $tierLabel }}
                        </th>
                        @endforeach
                    </tr>
                </thead>

                <tbody>
                    @foreach ($items as $index => $item)
                    <tr class="{{ $index % 2 === 1 ? 'bg-[#fbfaf7]' : 'bg-white' }}">
                        <td class="break-words px-4 py-5 text-left leading-6 text-[#10233f] lg:px-6">
                            {{ $item['benefit'] ?? '' }}
                        </td>

                        @foreach ($tiers as $tierKey => $tierLabel)
                        @php
                        $value = $formatValue($item[$tierKey] ?? '-');
                        @endphp

                        <td class="break-words px-3 py-5 text-center leading-6 lg:px-6">
                            @if ($isCheck($value))
                            <span class="text-lg leading-none text-[#b28a4a]">
                                ✓
                            </span>
                            @elseif ($value === '-')
                            <span class="text-[#d8c49a]">
                                —
                            </span>
                            @else
                            <span class="text-[#10233f]">
                                {{ $value }}
                            </span>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($section->excerpt)
        <div class="mt-2 px-5 text-sm italic leading-relaxed text-[#4b5563] md:px-6">
            <i>{!! $section->excerpt !!}</i>
        </div>
        @endif

        @endif
    </div>
</section>
@endif