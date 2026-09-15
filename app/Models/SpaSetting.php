<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpaSetting extends Model
{
    protected $fillable = [
        'reservation_whatsapp',
        'reservation_url',
        'meta_title',
        'meta_description',
        'meta_author',
        'meta_site_name',
        'hero_image',
        'hero_mobile_image',
        'hero_image_alt',
        'hero_mobile_image_alt',
        'hero_eyebrow',
        'hero_heading',
        'hero_description',
        'hero_primary_cta_label',
        'hero_primary_cta_url',
        'hero_secondary_cta_label',
        'hero_secondary_cta_url',
        'information_bar_items',
        'wellness_philosophy_eyebrow',
        'wellness_philosophy_heading',
        'wellness_philosophy_description',
        'wellness_philosophy_image',
        'wellness_philosophy_image_alt',
    ];

    protected $casts = [
        'information_bar_items' => 'array',
    ];
}
