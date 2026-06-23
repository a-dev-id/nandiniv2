{{-- resources/views/components/sections/membership-tier-section.blade.php --}}

@props([
'section' => null,
])

@php
$title = trim((string) ($section?->title ?? 'Circle Level'));
$subtitle = trim((string) ($section?->subtitle ?? ''));
$description = trim((string) ($section?->description ?? 'Each circle represents a deeper relationship with Nandini Jungle, combining meaningful recognition, thoughtful service, and experiences designed around every guest.'));

$hasTitle = $title !== '';
$hasSubtitle = $subtitle !== '';
$hasDescription = $description !== '';

$textAlign = $section?->text_align ?: 'center';

$descriptionAlignClass = match ($textAlign) {
'left' => 'text-left',
'right' => 'text-right',
default => 'text-center',
};

$descriptionWidthClass = match ($textAlign) {
'left' => 'max-w-2xl sm:max-w-3xl md:max-w-5xl',
default => 'max-w-2xl sm:max-w-3xl md:max-w-5xl',
};

$backgroundColor = $section?->background_color ?: 'soft_gray';

$backgroundClass = match ($backgroundColor) {
'soft_gray' => 'bg-slate-50',
'warm_ivory' => 'bg-[#fbf8f1]',
'light_gold' => 'bg-[#f6efe2]',
'dark_navy' => 'bg-[#071a33]',
default => 'bg-white',
};

$titleColorClass = $backgroundColor === 'dark_navy'
? 'text-white'
: 'text-slate-700';

$descriptionColorClass = $backgroundColor === 'dark_navy'
? 'text-white/85'
: 'text-gray-600';

$cardMap = [
'bronze' => asset('images/membership/dana-blank2.jpg'),
'silver' => asset('images/membership/upaya-blank2.jpg'),
'gold' => asset('images/membership/dhyana-blank2.jpg'),
'platinum' => asset('images/membership/jnana-blank2.jpg'),
];

$pointsRangeMap = [
'bronze' => '0 - 400 Points',
'silver' => '401 - 800 Points',
'gold' => '801 - 1,200 Points',
'platinum' => '1,201+ Points',
];

$defaultTiers = [
[
'tier_name' => 'Dana',
'circle_name' => 'Dana',
'circle_meaning' => 'Generosity',
'card_design' => 'bronze',
'description' => 'Dana is reflected in how we give—through thoughtful perks, personalized touches, and genuine care. It is about creating moments where guests feel valued beyond the stay.',
],
[
'tier_name' => 'Upaya',
'circle_name' => 'Upaya',
'circle_meaning' => 'Thoughtful Service',
'card_design' => 'silver',
'description' => 'Upaya translates into how we serve. Every detail is handled with care and intention, ensuring each guest experience feels smooth, responsive, and well-considered.',
],
[
'tier_name' => 'Dhyana',
'circle_name' => 'Dhyana',
'circle_meaning' => 'Calm & Presence',
'card_design' => 'gold',
'description' => 'Dhyana comes to life through the peaceful jungle setting and curated experiences. It is about helping guests slow down, reconnect, and fully enjoy the present moment.',
],
[
'tier_name' => 'Jnana',
'circle_name' => 'Jnana',
'circle_meaning' => 'Meaningful Insight',
'card_design' => 'platinum',
'description' => 'Jnana is about understanding our guests better over time. Through the loyalty program, we learn preferences and create more personalized, relevant experiences with each return visit.',
],
];

$sectionItems = collect($section?->items ?? [])
->filter(fn ($item) => filled($item['tier_name'] ?? null) || filled($item['circle_name'] ?? null) || filled($item['description'] ?? null))
->values()
->all();

$tiers = count($sectionItems) > 0 ? $sectionItems : $defaultTiers;
@endphp

<section class="px-6 py-14 md:py-20 {{ $backgroundClass }}">
    <div class="mx-auto">

        @if ($hasSubtitle)
        <p class="mb-4 text-center text-sm md:text-base leading-relaxed uppercase text-[#b28a4a] font-medium">
            {!! str_ireplace(
            ['&lt;br&gt;', '&lt;br/&gt;', '&lt;br /&gt;'],
            '<br class="hidden md:block">',
            e($subtitle)
            ) !!}
        </p>
        @endif

        @if ($hasTitle)
        <h2 class="text-xl text-center leading-snug uppercase font-medium {{ $titleColorClass }} mb-3">
            {!! str_ireplace(
            ['&lt;br&gt;', '&lt;br/&gt;', '&lt;br /&gt;'],
            '<br class="hidden md:block">',
            e($title)
            ) !!}
        </h2>
        @endif

        @if ($hasDescription)
        <div class="{{ ($hasTitle || $hasSubtitle) ? 'mt-8' : '' }} mx-auto {{ $descriptionWidthClass }} text-sm leading-relaxed {{ $descriptionColorClass }} {{ $descriptionAlignClass }}">
            {!! $description !!}
        </div>
        @endif

        <div class="mt-14 grid gap-8 md:grid-cols-4">
            @foreach ($tiers as $tier)
            @php
            $cardDesign = $tier['card_design'] ?? 'bronze';
            $cardImage = $cardMap[$cardDesign] ?? $cardMap['bronze'];
            $pointsRange = $pointsRangeMap[$cardDesign] ?? $pointsRangeMap['bronze'];
            $tierName = $tier['tier_name'] ?? '';
            $circleName = $tier['circle_name'] ?? '';
            $circleMeaning = $tier['circle_meaning'] ?? '';
            $tierDescription = $tier['description'] ?? '';
            @endphp

            <article class="group bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl rounded-2xl">
                <div class="relative overflow-hidden bg-slate-100">
                    <img src="{{ $cardImage }}" alt="{{ $tierName }} member card" class="aspect-[16/9] w-full object-cover rounded-t-2xl" loading="lazy">

                    <div class="absolute bottom-[7%] right-[4%] rounded-[3px] border border-white/70 bg-black/35 px-3 py-2 text-right shadow-sm backdrop-blur-[1px]">
                        <p class="text-[9px] font-bold uppercase leading-none text-white drop-shadow">
                            {{ $pointsRange }}
                        </p>
                    </div>
                </div>

                <div class="px-6 py-7 md:px-8 md:py-8">
                    <div class="mb-5 flex items-start gap-4">
                        <span class="mt-1 flex h-9 w-9 shrink-0 items-center justify-center border border-[#b28a4a]/40 text-sm font-medium text-[#b28a4a]">
                            {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                        </span>

                        <div>
                            @if ($tierName)
                            <p class="mb-2 text-sm font-medium uppercase text-[#b28a4a]">
                                {{ $tierName }}
                            </p>
                            @endif

                            <h3 class="text-lg leading-snug text-slate-700 mb-3">
                                @if ($circleName)
                                <span class="font-semibold uppercase">{{ $circleName }}</span>
                                @endif

                                @if ($circleMeaning)
                                <span class="font-normal text-gray-600"><br>
                                    ({{ $circleMeaning }})
                                </span>
                                @endif
                            </h3>
                        </div>
                    </div>

                    @if ($tierDescription)
                    <div class="text-sm leading-relaxed text-gray-600">
                        {!! nl2br(e($tierDescription)) !!}
                    </div>
                    @endif
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>
