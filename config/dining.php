<?php

return [
    'public_url' => env('DINING_PUBLIC_URL', 'https://dining.nandinibali.com/'),
    // Public URL or root-relative asset path. Otherwise reuse the main Dining CMS hero.
    'hero_image' => env('DINING_HERO_IMAGE'),
    'reservation_url' => env('DINING_RESERVATION_URL'),
    'menu_url' => env('DINING_MENU_URL'),
    'hero_video_id' => env('DINING_HERO_VIDEO_ID'),
    // Public URL or root-relative path to the approved chef-plating photograph.
    'philosophy_image' => env('DINING_PHILOSOPHY_IMAGE'),
];
