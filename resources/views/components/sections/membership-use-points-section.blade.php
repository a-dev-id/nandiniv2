{{-- resources/views/components/sections/membership-use-points-section.blade.php --}}

@props([
'section' => null,
])

@php
$title = trim((string) ($section?->title ?? 'Use Your Points'));
$subtitle = trim((string) ($section?->subtitle ?? ''));
$description = trim((string) ($section?->description ?? 'Experience the perks! Join today and receive your Starter Pack, featuring exclusive discounts on stays, dining, entertainment, and more.'));

$hasTitle = $title !== '';
$hasSubtitle = $subtitle !== '';
$hasDescription = $description !== '';

$textAlign = $section?->text_align ?: 'center';

$descriptionAlignClass = match ($textAlign) {
'left' => 'text-left',
'right' => 'text-right',
default => 'text-center',
};

$backgroundColor = $section?->background_color ?: 'white';

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

$viewMoreLabel = trim((string) ($section?->button_label ?? 'View More'));
$viewMoreUrl = trim((string) ($section?->button_url ?? '#')) ?: '#';

$defaultItems = [
[
'title' => 'Riverside Sanctuary Spa',
'description' => 'Indulge in the ultimate riverside retreat by the sacred Ayung River.',
'points_label' => '528 Points',
'button_label' => 'Redeem',
'button_url' => '#',
'image' => 'images/membership/use-points-spa.webp',
],
[
'title' => 'Room Upgrade On Us',
'description' => 'Enjoy more space, stunning views, and the serene atmosphere of Nandini Jungle — thoughtfully extended as our gift to you.',
'points_label' => '428 Points / Night',
'button_label' => 'Redeem',
'button_url' => '#',
'image' => 'images/membership/use-points-room.webp',
],
[
'title' => 'Luxe High Tea',
'description' => 'Relish a refined afternoon with our Luxe High Tea amidst the verdant jungles of Ubud.',
'points_label' => '320 Points',
'button_label' => 'Redeem',
'button_url' => '#',
'image' => 'images/membership/use-points-tea.webp',
],
];

$sectionItems = collect($section?->items ?? [])
->filter(fn ($item) => filled($item['title'] ?? null) || filled($item['description'] ?? null))
->values()
->all();

$items = count($sectionItems) > 0 ? $sectionItems : $defaultItems;

$resolveImage = function (?string $image): ?string {
$image = trim((string) $image);

if ($image === '') {
return null;
}

if (\Illuminate\Support\Str::startsWith($image, ['http://', 'https://'])) {
return $image;
}

if (\Illuminate\Support\Str::startsWith($image, ['images/', '/images/'])) {
return asset(ltrim($image, '/'));
}

return asset('storage/' . ltrim($image, '/'));
};
@endphp

<section class="{{ $backgroundClass }} px-6 py-14 md:py-20">
    <div class="mx-auto max-w-[1500px]">

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
        <div class="mx-auto mt-8 max-w-[920px] text-[15px] leading-7 {{ $descriptionColorClass }} {{ $descriptionAlignClass }}">
            {!! $description !!}
        </div>
        @endif

        <div class="mt-12 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($items as $item)
            @php
            $imageUrl = $resolveImage($item['image'] ?? null);
            $itemTitle = trim((string) ($item['title'] ?? ''));
            $itemDescription = trim((string) ($item['description'] ?? ''));
            $pointsLabel = trim((string) ($item['points_label'] ?? ''));
            $buttonLabel = trim((string) ($item['button_label'] ?? 'Redeem'));
            $buttonUrl = trim((string) ($item['button_url'] ?? '#')) ?: '#';
            @endphp

            <article class="group flex h-full flex-col bg-white shadow-xl shadow-black/10 ring-1 ring-black/5">
                @if ($imageUrl)
                <div class="aspect-square overflow-hidden bg-slate-100 md:aspect-[4/3]">
                    <img src="{{ $imageUrl }}" alt="{{ $itemTitle }}" class="h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" loading="lazy">
                </div>
                @endif

                <div class="flex flex-1 flex-col px-7 py-8">
                    @if ($itemTitle)
                    <h3 class="text-xl leading-snug tracking-[0.18em] md:tracking-[0.22em] uppercase font-semibold text-slate-900">
                        {{ $itemTitle }}
                    </h3>
                    @endif

                    @if ($itemDescription)
                    <div class="mt-5 grow text-[15px] leading-7 text-slate-700">
                        {!! nl2br(e($itemDescription)) !!}
                    </div>
                    @endif

                    <div class="mt-auto flex items-end justify-between gap-5 pt-10">
                        @if ($pointsLabel)
                        <p class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-900">
                            {{ $pointsLabel }}
                        </p>
                        @endif

                        @if ($buttonLabel)
                        <a href="{{ $buttonUrl }}" class="inline-flex min-w-[145px] items-center justify-center border border-slate-900 px-6 py-4 text-xs font-semibold uppercase tracking-[0.32em] text-slate-900 transition duration-300 hover:border-[#b28a4a] hover:bg-[#b28a4a] hover:text-white">
                            {{ $buttonLabel }}
                        </a>
                        @endif
                    </div>
                </div>
            </article>
            @endforeach
        </div>

        @if ($viewMoreLabel)
        <div class="mt-14 flex justify-center">
            <a href="{{ $viewMoreUrl }}" class="inline-flex items-center justify-center border border-slate-900 px-8 py-4 text-xs font-semibold uppercase tracking-[0.32em] text-slate-900 transition duration-300 hover:border-[#b28a4a] hover:bg-[#b28a4a] hover:text-white">
                {{ $viewMoreLabel }}
            </a>
        </div>
        @endif
    </div>
</section>