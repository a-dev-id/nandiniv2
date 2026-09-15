<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiningSetting extends Model
{
    public const DEFAULT_OCCASIONS = [
        'Honeymoon Dinner',
        'Anniversary',
        'Proposal',
        'Celebrations',
    ];

    protected $fillable = [
        'reservation_whatsapp', 'reservation_email', 'reservation_url',
        'food_menu_url', 'beverage_menu_url', 'opening_hours', 'location',
        'cuisine', 'dress_code', 'outside_guest_information',
        'meta_title', 'meta_description', 'meta_author', 'meta_site_name',
        'hero_video_id', 'hero_image', 'hero_image_alt', 'hero_eyebrow',
        'hero_heading', 'hero_subheading', 'hero_description',
        'hero_primary_cta_label', 'hero_primary_cta_url',
        'hero_secondary_cta_label', 'hero_secondary_cta_url',
        'philosophy_eyebrow', 'philosophy_heading', 'philosophy_description',
        'philosophy_image', 'philosophy_image_alt', 'philosophy_accent_text',
        'why_dine_eyebrow', 'why_dine_heading', 'why_dine_items',
        'information_bar_items',
        'private_dining_eyebrow', 'private_dining_heading', 'private_dining_description',
        'private_dining_image', 'private_dining_image_alt', 'private_dining_cta_label',
        'private_dining_cta_url', 'private_dining_tags',
        'visit_eyebrow', 'visit_heading', 'visit_information_items',
        'visit_food_menu_label', 'visit_food_menu_url',
        'visit_premium_menu_label', 'visit_premium_menu_url',
        'visit_beverage_menu_label', 'visit_beverage_menu_url',
        'faq_eyebrow', 'faq_heading', 'faq_items',
        'reservation_cta_background_image', 'reservation_cta_background_image_alt',
        'reservation_cta_heading', 'reservation_cta_description',
        'reservation_cta_label', 'reservation_cta_url',
        'experiences_eyebrow', 'experiences_heading',
        'signature_dishes', 'signature_menu_label', 'signature_menu_url',
        'guest_reviews_heading', 'guest_reviews_see_more_label', 'guest_reviews_see_more_url',
    ];

    protected $casts = [
        'why_dine_items' => 'array',
        'information_bar_items' => 'array',
        'private_dining_tags' => 'array',
        'visit_information_items' => 'array',
        'faq_items' => 'array',
        'signature_dishes' => 'array',
    ];

    /** @return array<int, string> */
    public function occasionOptions(): array
    {
        $labels = collect($this->private_dining_tags ?? [])
            ->pluck('label')
            ->map(fn (mixed $label): string => trim((string) $label))
            ->filter()
            ->unique()
            ->values()
            ->all();

        return $labels === [] ? self::DEFAULT_OCCASIONS : $labels;
    }
}
