@props([
'title' => 'Member Benefits',
'description' => 'Nandini Jungle Rewards offers effortless luxury with exclusive member rates, room upgrades, and complimentary stays. The program features four membership levels, each unlocking a more elevated jungle experience.',
'buttonLabel' => 'Explore Benefits',
'buttonUrl' => '#',
])

@php
$tiers = ['Bronze', 'Silver', 'Gold', 'Platinum'];

$benefits = [
[
'label' => 'Extra savings on rooms',
'bronze' => '-',
'silver' => '5%',
'gold' => '10%',
'platinum' => '15%',
],
[
'label' => '10% off Food & Beverage (excluding alcoholic drinks)',
'bronze' => 'check',
'silver' => 'check',
'gold' => 'check',
'platinum' => 'check',
],
[
'label' => '10% off Spa treatments',
'bronze' => 'check',
'silver' => 'check',
'gold' => 'check',
'platinum' => 'check',
],
[
'label' => '15-minute foot massage (one-time)',
'bronze' => '-',
'silver' => 'check',
'gold' => 'check',
'platinum' => 'check',
],
[
'label' => 'Late check-out (up to 2.00 pm, subject to availability)',
'bronze' => '-',
'silver' => 'check',
'gold' => 'check',
'platinum' => 'check',
],
[
'label' => 'Early check-in (from 10.00 am, subject to availability)',
'bronze' => '-',
'silver' => 'check',
'gold' => 'check',
'platinum' => 'check',
],
[
'label' => 'Villa category upgrade (subject to availability)',
'bronze' => '-',
'silver' => '-',
'gold' => 'check',
'platinum' => 'check',
],
[
'label' => 'One-time canapés during stay',
'bronze' => '-',
'silver' => '-',
'gold' => 'check',
'platinum' => 'check',
],
[
'label' => 'One-time 60-minute spa treatment',
'bronze' => '-',
'silver' => '-',
'gold' => '-',
'platinum' => 'check',
],
[
'label' => '24-hour stay concept<br>(check-in anytime, subject to availability)',
'bronze' => '-',
'silver' => '-',
'gold' => '-',
'platinum' => 'check',
],
];

$tierKeys = ['bronze', 'silver', 'gold', 'platinum'];
@endphp

<section class="bg-white px-6 py-14 md:py-20">
    <div class="mx-auto max-w-[1500px]">
        <div class="mx-auto max-w-3xl text-center md:max-w-5xl">
            <h2 class="mb-6 font-serif text-2xl font-medium uppercase leading-snug tracking-[0.15em] text-slate-800 sm:text-3xl md:mb-8 md:text-4xl md:tracking-[0.25em]">
                {{ $title }}
            </h2>

            @if ($description)
            <p class="mx-auto max-w-2xl text-[15px] leading-relaxed text-gray-600 sm:max-w-3xl sm:text-base md:max-w-5xl">
                {{ $description }}
            </p>
            @endif
        </div>

        <div class="mt-16 overflow-x-auto">
            <table class="w-full min-w-[1200px] border-collapse text-[15px] text-slate-950">
                <thead>
                    <tr>
                        <th class="w-[36%] border-b border-slate-500 py-5 pl-8 pr-6 text-left text-[15px] font-bold uppercase tracking-[0.15em]">
                            Member Benefits
                        </th>

                        @foreach ($tiers as $tier)
                        <th class="border-b border-slate-500 px-8 py-5 text-center font-serif text-xl font-normal uppercase tracking-[0.18em] sm:text-2xl md:text-3xl">
                            {{ $tier }}
                        </th>
                        @endforeach
                    </tr>
                </thead>

                <tbody>
                    @foreach ($benefits as $benefit)
                    <tr class="{{ $loop->even ? 'bg-slate-100' : 'bg-white' }}">
                        <td class="py-5 pl-8 pr-6 text-left text-[15px] leading-relaxed text-slate-800">
                            {!! $benefit['label'] !!}
                        </td>

                        @foreach ($tierKeys as $tierKey)
                        <td class="px-8 py-5 text-center text-[15px]">
                            @if ($benefit[$tierKey] === 'check')
                            <span class="text-lg leading-none">✓</span>
                            @else
                            <span>{{ $benefit[$tierKey] }}</span>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mx-auto mt-10 max-w-4xl text-center">
            <h3 class="text-lg font-bold uppercase tracking-[0.18em] text-slate-950">
                Tier Recognition
            </h3>

            <p class="mt-2 text-[15px] leading-relaxed text-gray-600 sm:text-base">
                · Personalized welcome note for Silver and above · Priority handling of villa preferences,
                special requests, and spa reservations for Gold and Platinum
            </p>

            <div class="mt-8">
                <x-buttons.link-button :href="$buttonUrl" variant="outline" class="w-full max-w-[520px]">
                    {{ $buttonLabel }}
                </x-buttons.link-button>
            </div>
        </div>
    </div>
</section>