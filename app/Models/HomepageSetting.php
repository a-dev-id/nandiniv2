<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageSetting extends Model
{
    public const SECTIONS = [
        'villas' => 'Jungle Villas & Royal Suites',
        'experiences' => 'Experiences',
        'offers' => 'Offers',
        'dining' => 'Dining',
        'spa' => 'Spa & Wellness',
        'adventures' => 'Adventure Journeys',
    ];

    protected $fillable = [
        'villas_heading', 'villas_description', 'villas_action_label',
        'experiences_heading', 'experiences_description', 'experiences_action_label', 'experiences_footer_label',
        'offers_heading', 'offers_description', 'offers_action_label', 'offers_footer_label',
        'dining_heading', 'dining_description', 'dining_action_label',
        'spa_heading', 'spa_description', 'spa_action_label',
        'adventures_heading', 'adventures_description', 'adventures_action_label', 'adventures_footer_label',
        'featured_suite_action_label', 'guest_reviews_heading', 'guest_reviews_action_label',
    ];
}
