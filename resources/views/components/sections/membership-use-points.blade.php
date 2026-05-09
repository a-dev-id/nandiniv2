@props([
'title' => 'Use Your Points',
'description' => 'Experience the perks! Join today and receive your Starter Pack, featuring exclusive discounts on stays, dining, entertainment, and more.',
'items' => [],
])

@php
$defaultItems = [
[
'title' => 'Riverside Sanctuary Spa',
'description' => 'Indulge in the ultimate riverside retreat by the sacred Ayung River.',
'points' => '528 Points',
'image' => 'images/membership/riverside-sanctuary-spa.webp',
'url' => '#',
],
[
'title' => 'Room Upgrade On Us',
'description' => 'Enjoy more space, stunning views, and the serene atmosphere of Nandini Jungle — thoughtfully extended as our gift to you.',
'points' => '428 Points / Night',
'image' => 'images/membership/room-upgrade.webp',
'url' => '#',
],
[
'title' => 'Luxe High Tea',
'description' => 'Relish a refined afternoon with our Luxe High Tea amidst the verdant jungles of Ubud.',
'points' => '320 Points',
'image' => 'images/membership/luxe-high-tea.webp',
'url' => '#',
],
[
'title' => 'Chakra Meditation At Sacred River',
'description' => 'Awaken your spirit by the sacred river, where the gentle flow of water and the heartbeat of the jungle create a sanctuary of renewal.',
'points' => '648 Points',
'image' => 'images/membership/chakra-meditation.webp',
'url' => '#',
],
[
'title' => 'Balinese Blessing Purification',
'description' => "Surrender to the embrace of Bali's healing waters with a sacred purification ritual led by a Balinese priest.",
'points' => '567 Points',
'image' => 'images/membership/balinese-blessing.webp',
'url' => '#',
],
[
'title' => 'Cooking Class',
'description' => 'Join Nandini Cooking Class and learn how to cook like a Balinese, bringing a culinary journey you experienced to your home.',
'points' => '328 Points',
'image' => 'images/membership/cooking-class.webp',
'url' => '#',
],
];

$redeemItems = count($items) ? $items : $defaultItems;

$imageUrl = function ($image) {
if (! $image) {
return '';
}

return \Illuminate\Support\Str::startsWith($image, ['http://', 'https://'])
? $image
: asset($image);
};
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

        <div class="mt-12 grid grid-cols-1 gap-8 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($redeemItems as $item)
            <article class="flex h-full flex-col bg-white shadow-[0_6px_18px_rgba(0,0,0,0.22)]">
                <div class="relative aspect-square md:aspect-3/2 w-full overflow-hidden bg-slate-100">
                    @if (! empty($item['image']))
                    <img src="{{ $imageUrl($item['image']) }}" alt="{{ $item['title'] ?? 'Membership reward' }}" class="absolute inset-0 h-full w-full object-cover transition duration-500 hover:scale-105" loading="lazy">
                    @endif
                </div>

                <div class="flex flex-1 flex-col p-7">
                    <h3 class="font-serif text-xl uppercase leading-snug tracking-[0.16em] text-slate-900 md:text-2xl">
                        {{ $item['title'] }}
                    </h3>

                    <p class="mt-5 text-[15px] leading-relaxed text-gray-600">
                        {{ $item['description'] }}
                    </p>

                    <div class="mt-auto flex flex-col gap-4 pt-8 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-900">
                            {{ $item['points'] }}
                        </p>

                        <x-buttons.link-button :href="$item['url'] ?? '#'" variant="outline" class="min-w-[140px] px-8 py-3">
                            Redeem
                        </x-buttons.link-button>
                    </div>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>