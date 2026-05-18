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
'left' => 'max-w-[1040px]',
default => 'max-w-[760px]',
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
: 'text-slate-800';

$descriptionColorClass = $backgroundColor === 'dark_navy'
? 'text-white/85'
: 'text-slate-700';

$cardMap = [
'bronze' => asset('images/membership/dana.jpeg'),
'silver' => asset('images/membership/upaya.jpeg'),
'gold' => asset('images/membership/dhyana.jpeg'),
'platinum' => asset('images/membership/jnana.jpeg'),
];

$defaultTiers = [
[
'tier_name' => 'Bronze',
'circle_name' => 'Dana',
'circle_meaning' => 'Generosity',
'card_design' => 'bronze',
'description' => 'Dana is reflected in how we give—through thoughtful perks, personalized touches, and genuine care. It is about creating moments where guests feel valued beyond the stay.',
],
[
'tier_name' => 'Silver',
'circle_name' => 'Upaya',
'circle_meaning' => 'Thoughtful Service',
'card_design' => 'silver',
'description' => 'Upaya translates into how we serve. Every detail is handled with care and intention, ensuring each guest experience feels smooth, responsive, and well-considered.',
],
[
'tier_name' => 'Gold',
'circle_name' => 'Dhyana',
'circle_meaning' => 'Calm & Presence',
'card_design' => 'gold',
'description' => 'Dhyana comes to life through the peaceful jungle setting and curated experiences. It is about helping guests slow down, reconnect, and fully enjoy the present moment.',
],
[
'tier_name' => 'Platinum',
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
        <p class="mb-4 text-center text-sm md:text-base leading-relaxed tracking-[0.18em] uppercase text-[#b28a4a] font-medium">
            {!! str_ireplace(
            ['&lt;br&gt;', '&lt;br/&gt;', '&lt;br /&gt;'],
            '<br class="hidden md:block">',
            e($subtitle)
            ) !!}
        </p>
        @endif

        @if ($hasTitle)
        <h2 class="text-center text-2xl sm:text-3xl md:text-3xl leading-snug tracking-[0.15em] md:tracking-[0.25em] uppercase font-medium {{ $titleColorClass }}">
            {!! str_ireplace(
            ['&lt;br&gt;', '&lt;br/&gt;', '&lt;br /&gt;'],
            '<br class="hidden md:block">',
            e($title)
            ) !!}
        </h2>
        @endif

        @if ($hasDescription)
        <div class="{{ ($hasTitle || $hasSubtitle) ? 'mt-8' : '' }} mx-auto {{ $descriptionWidthClass }} text-[15px] leading-7 {{ $descriptionColorClass }} {{ $descriptionAlignClass }}">
            {!! $description !!}
        </div>
        @endif

        <div class="mt-14 grid gap-8 md:grid-cols-4">
            @foreach ($tiers as $tier)
            @php
            $cardDesign = $tier['card_design'] ?? 'bronze';
            $cardImage = $cardMap[$cardDesign] ?? $cardMap['bronze'];
            $tierName = $tier['tier_name'] ?? '';
            $circleName = $tier['circle_name'] ?? '';
            $circleMeaning = $tier['circle_meaning'] ?? '';
            $tierDescription = $tier['description'] ?? '';
            @endphp

            <article class="group bg-white shadow-sm  transition duration-300 hover:-translate-y-1 hover:shadow-xl rounded-2xl">
                <div class="overflow-hidden bg-slate-100">
                    <img src="{{ $cardImage }}" alt="{{ $tierName }} member card" class="aspect-[16/9] w-full object-cover rounded-t-2xl" loading="lazy">
                </div>

                <div class="px-6 py-7 md:px-8 md:py-8">
                    <div class="mb-5 flex items-start gap-4">
                        <span class="mt-1 flex h-9 w-9 shrink-0 items-center justify-center border border-[#b28a4a]/40 text-xs font-medium text-[#b28a4a]">
                            {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                        </span>

                        <div>
                            @if ($tierName)
                            <p class="mb-2 text-xs font-medium uppercase tracking-[0.28em] text-[#b28a4a]">
                                {{ $tierName }}
                            </p>
                            @endif

                            <h3 class="text-lg text-slate-800">
                                @if ($circleName)
                                <span class="font-semibold uppercase">{{ $circleName }}</span>
                                @endif

                                @if ($circleMeaning)
                                <span class="font-normal text-slate-700">
                                    ({{ $circleMeaning }})
                                </span>
                                @endif
                            </h3>
                        </div>
                    </div>

                    @if ($tierDescription)
                    <div class="text-[15px] leading-7 text-slate-700">
                        {!! nl2br(e($tierDescription)) !!}
                    </div>
                    @endif
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>