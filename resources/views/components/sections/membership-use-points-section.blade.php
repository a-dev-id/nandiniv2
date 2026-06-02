{{-- resources/views/components/sections/membership-use-points-section.blade.php --}}

@props([
'section' => null,
'rewards' => collect(),
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
: 'text-gray-600';

$viewMoreLabel = trim((string) ($section?->button_label ?? 'View More'));
$viewMoreUrl = \Illuminate\Support\Facades\Route::has('membership.privilege-redemption')
? route('membership.privilege-redemption')
: url('/membership/privilege-redemption');
$memberIsLoggedIn = auth('member')->check();
$membershipLoginUrl = \Illuminate\Support\Facades\Route::has('membership.login')
? route('membership.login')
: url('/membership/sign-in');

$resolveMemberLink = function (string $url) use ($memberIsLoggedIn, $membershipLoginUrl): string {
return (! $memberIsLoggedIn && $url === '#') ? $membershipLoginUrl : $url;
};

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

$formatPoints = function ($reward): string {
$pointsLabel = trim((string) data_get($reward, 'points_label', ''));

if ($pointsLabel !== '') {
return $pointsLabel;
}

$pointsRequired = data_get($reward, 'points_required');

if ($pointsRequired === null || $pointsRequired === '') {
return '';
}

return number_format((int) $pointsRequired) . ' POINTS';
};

$rewardItems = collect($rewards ?? [])
->map(function ($reward) use ($formatPoints) {
$rewardTitle = trim((string) data_get($reward, 'title', ''));

$rewardDescription = trim((string) data_get($reward, 'excerpt', ''));

if ($rewardDescription === '') {
$rewardDescription = trim(strip_tags((string) data_get($reward, 'description', '')));
}

$categoryName = trim((string) (
data_get($reward, 'category.name')
?: data_get($reward, 'category.title')
?: data_get($reward, 'category.label')
?: ''
));

$buttonLabel = trim((string) data_get($reward, 'button_label', ''));

if ($buttonLabel === '') {
$buttonLabel = 'Redeem';
}

$buttonUrl = trim((string) data_get($reward, 'button_url', ''));

if ($buttonUrl === '') {
$buttonUrl = '#';
}

return [
'title' => $rewardTitle,
'description' => $rewardDescription,
'points_label' => $formatPoints($reward),
'button_label' => $buttonLabel,
'button_url' => $buttonUrl,
'image' => data_get($reward, 'image') ?: '',
'image_alt' => data_get($reward, 'image_alt') ?: $rewardTitle,
'category' => $categoryName,
];
})
->filter(fn ($item) => filled($item['title'] ?? null))
->values()
->all();

$defaultItems = [
[
'title' => 'Riverside Sanctuary Spa',
'description' => 'Indulge in the ultimate riverside retreat by the sacred Ayung River.',
'points_label' => '528 POINTS',
'button_label' => 'Redeem',
'button_url' => '#',
'image' => 'images/membership/use-points-spa.webp',
'image_alt' => 'Riverside Sanctuary Spa',
'category' => 'Spa',
],
[
'title' => 'Room Upgrade On Us',
'description' => 'Enjoy more space, stunning views, and the serene atmosphere of Nandini Jungle — thoughtfully extended as our gift to you.',
'points_label' => '428 POINTS / NIGHT',
'button_label' => 'Redeem',
'button_url' => '#',
'image' => 'images/membership/use-points-room.webp',
'image_alt' => 'Room Upgrade On Us',
'category' => 'Room',
],
[
'title' => 'Luxe High Tea',
'description' => 'Relish a refined afternoon with our Luxe High Tea amidst the verdant jungles of Ubud.',
'points_label' => '320 POINTS',
'button_label' => 'Redeem',
'button_url' => '#',
'image' => 'images/membership/use-points-tea.webp',
'image_alt' => 'Luxe High Tea',
'category' => 'Dining',
],
];

$items = count($rewardItems) > 0 ? $rewardItems : $defaultItems;
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
        <h2 class="text-center text-2xl sm:text-3xl md:text-3xl leading-snug tracking-[0.15em] md:tracking-[0.22em] uppercase font-medium {{ $titleColorClass }}">
            {!! str_ireplace(
            ['&lt;br&gt;', '&lt;br/&gt;', '&lt;br /&gt;'],
            '<br class="hidden md:block">',
            e($title)
            ) !!}
        </h2>
        @endif

        @if ($hasDescription)
        <div class="mt-8 text-[15px] sm:text-base leading-relaxed max-w-2xl sm:max-w-3xl md:max-w-5xl mx-auto {{ $descriptionColorClass }} {{ $descriptionAlignClass }}">
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
            $buttonUrl = $resolveMemberLink($buttonUrl);
            $categoryLabel = trim((string) ($item['category'] ?? ''));
            $imageAlt = trim((string) ($item['image_alt'] ?? $itemTitle));
            @endphp

            <article class="group flex h-full flex-col bg-white shadow-xl shadow-black/10 ring-1 ring-black/5">
                @if ($imageUrl)
                <div class="aspect-square overflow-hidden bg-slate-100 md:aspect-[4/3]">
                    <img src="{{ $imageUrl }}" alt="{{ $imageAlt ?: $itemTitle }}" class="h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" loading="lazy">
                </div>
                @endif

                <div class="flex flex-1 flex-col px-7 py-8">
                    @if ($categoryLabel)
                    <p class="mb-4 text-xs font-semibold uppercase tracking-[0.22em] text-[#b28a4a]">
                        {{ $categoryLabel }}
                    </p>
                    @endif

                    @if ($itemTitle)
                    <h3 class="text-xl sm:text-2xl md:text-2xl leading-snug tracking-[0.15em] uppercase font-semibold text-slate-800">
                        {{ $itemTitle }}
                    </h3>
                    @endif

                    @if ($itemDescription)
                    <div class="mt-5 grow text-[15px] sm:text-base leading-relaxed text-gray-600">
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
