<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FestiveSetting extends Model
{
    protected $fillable = [
        'meta_title', 'meta_description', 'meta_author', 'meta_site_name',
        'hero_visible', 'hero_image', 'hero_image_alt', 'hero_eyebrow', 'hero_heading',
        'hero_subheading', 'hero_description', 'hero_primary_cta_label', 'hero_primary_cta_url',
        'hero_secondary_cta_label', 'hero_secondary_cta_url',
        'introduction_visible', 'introduction_eyebrow', 'introduction_heading', 'introduction_description',
        'celebrations_visible', 'celebrations',
        'programme_visible', 'programme_eyebrow', 'programme_heading', 'programme_days',
        'booking_cta_visible', 'booking_cta_image', 'booking_cta_image_alt', 'booking_cta_eyebrow',
        'booking_cta_heading', 'booking_cta_description', 'booking_cta_button_label', 'booking_cta_button_url',
    ];

    protected $casts = [
        'hero_visible' => 'boolean',
        'introduction_visible' => 'boolean',
        'celebrations_visible' => 'boolean',
        'programme_visible' => 'boolean',
        'booking_cta_visible' => 'boolean',
        'celebrations' => 'array',
        'programme_days' => 'array',
    ];
}
