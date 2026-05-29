@props([
'section' => null,

'cuisine' => 'Western and Indonesian',

'breakfastTime' => '07:00 am to 10:30 am',
'lunchTime' => '12:00 pm to 03:00 pm',
'dinnerTime' => '06:30 pm to 10:30 pm',

'reserveUrl' => '#',
'menuUrl' => '#',
'premiumMenuUrl' => '#',
'beverageListUrl' => '#',

'phone' => '+62 812-3687-1170',
'email' => 'reservation@nandinibali.com',
])

@php
$defaultRestaurantInformation = '
<p><strong>Cuisine:</strong><br>' . e($cuisine) . '</p>
<p><strong>Opening times:</strong><br>
    Breakfast: ' . e($breakfastTime) . '<br>
    Lunch: ' . e($lunchTime) . '<br>
    Dinner: ' . e($dinnerTime) . '</p>
';

$defaultReservationsInformation = '
<p><strong>Contact Us:</strong><br>' . e($phone) . '<br>(Whatsapp Enabled)</p>
<p><strong>Email Us:</strong><br>' . e($email) . '</p>
';

$restaurantInformation = $section?->description ?: $defaultRestaurantInformation;
$reservationsInformation = $section?->excerpt ?: $defaultReservationsInformation;

$buttons = $section?->items ?? [];

if (is_string($buttons)) {
$buttons = json_decode($buttons, true) ?: [];
}

if (blank($buttons)) {
$buttons = [
[
'label' => 'Reserve Now',
'url' => $reserveUrl,
],
[
'label' => 'View Menu',
'url' => $menuUrl,
],
[
'label' => 'Premium Menu',
'url' => $premiumMenuUrl,
],
[
'label' => 'Beverage List',
'url' => $beverageListUrl,
],
];
}
@endphp

<section class="bg-[#F3F4F5] px-6 py-14 md:px-12 md:py-16 lg:px-[70px]">
    <div class="mx-auto grid max-w-7xl gap-12 md:grid-cols-3 md:gap-16">
        {{-- Restaurant Information --}}
        <div>
            <h2 class="mb-6 text-md font-medium uppercase tracking-[0.12em] text-slate-900">
                Restaurant Information
            </h2>

            <div class="text-sm leading-relaxed text-slate-700 [&_p]:mb-2 [&_strong]:font-semibold [&_strong]:text-slate-800 [&_ul]:list-none [&_ul]:pl-0 [&_ol]:list-none [&_ol]:pl-0 [&_li]:mb-1">
                {!! $restaurantInformation !!}
            </div>
        </div>

        {{-- Additional Information --}}
        <div>
            <h2 class="mb-6 text-md font-medium uppercase tracking-[0.12em] text-slate-900">
                Additional Information
            </h2>

            <div class="space-y-3">
                @foreach ($buttons as $index => $button)
                @php
                $label = $button['label'] ?? null;
                $url = $button['url'] ?? '#';
                @endphp

                @if ($label)
                <a href="{{ $url }}" @if (str_starts_with($url, 'http' )) target="_blank" rel="noopener" @endif class="flex h-[50px] w-full items-center justify-center border border-black px-6 text-[11px] font-bold uppercase tracking-[0.14em] transition {{ $index === 0 ? 'bg-black text-white hover:bg-transparent hover:text-black' : 'bg-transparent text-black hover:bg-black hover:text-white' }}">
                    {{ $label }}
                </a>
                @endif
                @endforeach
            </div>
        </div>

        {{-- Reservations --}}
        <div>
            <h2 class="mb-6 text-md font-medium uppercase tracking-[0.12em] text-slate-900">
                Reservations
            </h2>

            <div class="text-sm leading-relaxed text-slate-700 [&_p]:mb-2 [&_strong]:font-semibold [&_strong]:text-slate-800 [&_a]:hover:text-[#916B2C] [&_ul]:list-none [&_ul]:pl-0 [&_ol]:list-none [&_ol]:pl-0 [&_li]:mb-1">
                {!! $reservationsInformation !!}
            </div>
        </div>
    </div>
</section>