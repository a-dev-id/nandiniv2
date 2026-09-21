@props(['settings' => null])

@php
    $items = $settings?->information_bar_items ?? [];

    if (blank($items)) {
        $items = [
            ['icon' => 'clock', 'label' => 'Opening Hours', 'value' => '08:00 AM – 10:00 PM'],
            ['icon' => 'calendar', 'label' => 'Booking', 'value' => 'Advance booking recommended'],
            ['icon' => 'location', 'label' => 'Location', 'value' => 'Nandini Jungle, Ubud, Bali'],
            [
                'icon' => 'phone',
                'label' => 'Reservations',
                'value' => $settings?->reservation_whatsapp ?: '+62 812 3687 1170',
                'link' => $settings?->reservation_url ?: 'https://wa.me/6281236871170',
            ],
        ];
    }
@endphp

<x-landing-information-bar :items="$items" label="Spa information" />
