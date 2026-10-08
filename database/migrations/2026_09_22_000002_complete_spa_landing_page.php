<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spa_settings', function (Blueprint $table): void {
            $table->boolean('hero_visible')->default(true)->after('meta_site_name');
            $table->text('hero_subheading')->nullable()->after('hero_heading');
            $table->boolean('information_bar_visible')->default(true)->after('hero_secondary_cta_url');
            $table->boolean('wellness_philosophy_visible')->default(true)->after('information_bar_items');
            $table->string('wellness_philosophy_link_label')->nullable()->after('wellness_philosophy_image_alt');
            $table->text('wellness_philosophy_link_url')->nullable()->after('wellness_philosophy_link_label');
            $table->boolean('wellness_journeys_visible')->default(true)->after('wellness_philosophy_link_url');
            $table->boolean('signature_visible')->default(true)->after('wellness_journeys_items');
            $table->string('signature_eyebrow')->nullable()->after('signature_visible');
            $table->text('signature_heading')->nullable()->after('signature_eyebrow');
            $table->text('signature_description')->nullable()->after('signature_heading');
            $table->string('signature_image')->nullable()->after('signature_description');
            $table->string('signature_image_alt')->nullable()->after('signature_image');
            $table->string('signature_link_label')->nullable()->after('signature_image_alt');
            $table->text('signature_link_url')->nullable()->after('signature_link_label');
            $table->boolean('why_nandini_visible')->default(true)->after('signature_link_url');
            $table->boolean('guest_review_visible')->default(true)->after('why_nandini_items');
            $table->text('guest_review_quote')->nullable()->after('guest_review_visible');
            $table->string('guest_review_label')->nullable()->after('guest_review_quote');
            $table->string('guest_review_image')->nullable()->after('guest_review_label');
            $table->string('guest_review_image_alt')->nullable()->after('guest_review_image');
            $table->boolean('booking_cta_visible')->default(true)->after('guest_review_image_alt');
            $table->string('booking_cta_eyebrow')->nullable()->after('booking_cta_visible');
            $table->text('booking_cta_heading')->nullable()->after('booking_cta_eyebrow');
            $table->text('booking_cta_description')->nullable()->after('booking_cta_heading');
            $table->string('booking_cta_button_label')->nullable()->after('booking_cta_description');
            $table->text('booking_cta_button_url')->nullable()->after('booking_cta_button_label');
            $table->string('booking_cta_image')->nullable()->after('booking_cta_button_url');
            $table->string('booking_cta_image_alt')->nullable()->after('booking_cta_image');
        });

        $settings = DB::table('spa_settings')->where('id', 1)->first();
        $reservationUrl = $settings?->reservation_url ?: 'https://wa.me/6281236871170';
        $bookingUrl = $settings?->booking_cta_button_url
            ?: $reservationUrl.'?text='.rawurlencode('Hello, I would like to book a spa experience at Nandini Jungle.');
        $mainSpaUrl = 'https://'.config('domains.main').'/spa-wellness';
        $mediaBase = 'https://nandinibali.com/storage/';

        DB::table('spa_settings')->updateOrInsert(['id' => 1], [
            'reservation_whatsapp' => $settings?->reservation_whatsapp ?: '+62 812 3687 1170',
            'reservation_url' => $reservationUrl,
            'meta_title' => 'Nandini Jungle Spa | Wellness in the Heart of Nature',
            'meta_description' => 'Discover restorative Balinese wellness rituals at Nandini Jungle Spa in Ubud, surrounded by tropical nature and the river.',
            'meta_author' => $settings?->meta_author ?: 'Nandini Jungle by Hanging Gardens',
            'meta_site_name' => $settings?->meta_site_name ?: 'Nandini Jungle by Hanging Gardens',
            'hero_visible' => true,
            'hero_image' => $mediaBase.'pages/hero/fb4a52d4-35a1-4c29-804d-100e00dd6b89.webp',
            'hero_mobile_image' => $mediaBase.'pages/hero/fb4a52d4-35a1-4c29-804d-100e00dd6b89.webp',
            'hero_image_alt' => 'Nandini Jungle Spa surrounded by tropical nature in Ubud, Bali',
            'hero_mobile_image_alt' => 'Nandini Jungle Spa surrounded by tropical nature in Ubud, Bali',
            'hero_eyebrow' => 'Wellness at Nandini Jungle',
            'hero_heading' => 'Essence Spa',
            'hero_subheading' => 'Wellness in the Heart of Nature',
            'hero_description' => 'Rebalance your body, mind and soul with deeply restorative spa rituals inspired by Bali, nature and the surrounding jungle. Let the sound of the river and the healing touch of our therapists guide you into a deeper sense of well-being.',
            'hero_primary_cta_label' => 'Book a Spa Experience',
            'hero_primary_cta_url' => $reservationUrl,
            'hero_secondary_cta_label' => 'Explore Treatments',
            'hero_secondary_cta_url' => '#treatments',
            'information_bar_visible' => true,
            'information_bar_items' => json_encode([
                ['icon' => 'clock', 'label' => 'Opening Hours', 'value' => '09:00 AM – 10:00 PM', 'link' => null],
                ['icon' => 'location', 'label' => 'Location', 'value' => 'Nandini Jungle, Ubud – Bali', 'link' => null],
                ['icon' => 'calendar', 'label' => 'Advance Booking', 'value' => 'Recommended', 'link' => null],
                ['icon' => 'phone', 'label' => 'Reservations', 'value' => '+62 812 3687 1170', 'link' => $reservationUrl],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'wellness_philosophy_visible' => true,
            'wellness_philosophy_eyebrow' => 'Our Philosophy',
            'wellness_philosophy_heading' => "A Deeper Sense\nof Well-Being",
            'wellness_philosophy_description' => 'At Nandini Jungle Spa, wellness is a harmonious journey of body, mind and spirit. Inspired by Balinese traditions and the healing power of nature, our treatments invite you to slow down, reconnect and embrace a more meaningful sense of well-being.',
            'wellness_philosophy_image' => $mediaBase.'pages/sections/7bdab6e8-62b3-416a-85fb-3419a6a15ee8.webp',
            'wellness_philosophy_image_alt' => 'Nandini Spa jacuzzi surrounded by tropical jungle',
            'wellness_philosophy_link_label' => null,
            'wellness_philosophy_link_url' => null,
            'wellness_journeys_visible' => true,
            'wellness_journeys_eyebrow' => 'Signature Treatments',
            'wellness_journeys_heading' => 'Journeys of Renewal',
            'wellness_journeys_description' => 'From traditional Balinese rituals to nature-inspired therapies, discover restorative wellness experiences created to renew the body, calm the mind and reconnect you with the jungle.',
            'wellness_journeys_items' => json_encode([
                [
                    'title' => '2-Day Balinese Wellness Escape',
                    'description' => 'A two-day journey to restore balance through traditional Balinese massage, flower bath ritual, body scrub and natural facial treatments.',
                    'image' => $mediaBase.'spas/hero/68d37345-f6e6-4f1d-a962-725cf049fe62.webp',
                    'image_alt' => 'Balinese massage at Nandini Jungle Spa',
                    'details_label' => 'View Details',
                    'details_url' => '/spa-wellness/2-day-balinese-wellness-escape',
                    'book_label' => 'Book Now',
                    'book_url' => $reservationUrl,
                ],
                [
                    'title' => '3-Day Inner Harmony Retreat',
                    'description' => 'A three-day retreat designed to calm the body, cleanse the skin and restore balance through signature treatments and mindful rituals.',
                    'image' => $mediaBase.'spas/hero/b5490cd9-d622-4ce2-b483-992ef4ea0c3c.webp',
                    'image_alt' => 'Spa treatment beds overlooking the jungle',
                    'details_label' => 'View Details',
                    'details_url' => '/spa-wellness/3-day-inner-harmony-retreat',
                    'book_label' => 'Book Now',
                    'book_url' => $reservationUrl,
                ],
                [
                    'title' => '4-Day Deep Balinese Wellness Immersion',
                    'description' => 'A four-day immersive wellness journey created for deep relaxation, rejuvenation and a renewed sense of balance.',
                    'image' => $mediaBase.'spas/hero/19d9f7d8-6a93-422d-8a13-b333a2384ff8.webp',
                    'image_alt' => 'Flower bath ritual at Nandini Jungle Spa',
                    'details_label' => 'View Details',
                    'details_url' => '/spa-wellness/4-day-deep-balinese-wellness-immersion',
                    'book_label' => 'Book Now',
                    'book_url' => $reservationUrl,
                ],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'signature_visible' => true,
            'signature_eyebrow' => 'A Unique Setting',
            'signature_heading' => 'Spa on the River',
            'signature_description' => 'Our signature riverside spa brings wellness closer to the natural rhythm of the jungle. Surrounded by tropical greenery and the sound of flowing water, each treatment becomes a deeply immersive moment of calm, connection and renewal.',
            'signature_image' => $mediaBase.'pages/sections/ea97fbfe-7ca1-43fe-8e30-51b1e9dcea01.webp',
            'signature_image_alt' => 'Spa on the River at Nandini Jungle',
            'signature_link_label' => 'Discover the Spa',
            'signature_link_url' => $mainSpaUrl,
            'why_nandini_visible' => true,
            'why_nandini_eyebrow' => 'Why Nandini Jungle Spa',
            'why_nandini_heading' => 'Wellness Rooted in Nature',
            'why_nandini_items' => json_encode([
                ['icon' => 'jungle', 'title' => 'Natural Surroundings', 'description' => 'A serene jungle setting shaped by the healing presence of nature.'],
                ['icon' => 'ritual', 'title' => 'Authentic Balinese Rituals', 'description' => 'Wellness inspired by traditional Balinese practices and healing traditions.'],
                ['icon' => 'care', 'title' => 'Personalised Care', 'description' => 'Thoughtful treatments tailored to your individual wellness journey.'],
                ['icon' => 'river', 'title' => 'River-Side Tranquillity', 'description' => 'A one-of-a-kind setting immersed in the sounds and atmosphere of the river.'],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'guest_review_visible' => true,
            'guest_review_quote' => 'The most peaceful and healing spa experience. The sound of the river, the jungle, and the care from the therapists made it truly special.',
            'guest_review_label' => 'Guest Experience',
            'guest_review_image' => $mediaBase.'pages/sections/7bdab6e8-62b3-416a-85fb-3419a6a15ee8.webp',
            'guest_review_image_alt' => 'Spa experience at Nandini Jungle',
            'booking_cta_visible' => true,
            'booking_cta_eyebrow' => 'Your Wellness Journey Awaits',
            'booking_cta_heading' => 'Book Your Spa Experience',
            'booking_cta_description' => 'Step away from the everyday and reconnect with nature through a restorative Nandini Jungle Spa experience.',
            'booking_cta_button_label' => 'BOOK NOW',
            'booking_cta_button_url' => $bookingUrl,
            'booking_cta_image' => $mediaBase.'pages/sections/ea97fbfe-7ca1-43fe-8e30-51b1e9dcea01.webp',
            'booking_cta_image_alt' => '',
            'created_at' => $settings?->created_at ?: now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('spa_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'hero_visible',
                'hero_subheading',
                'information_bar_visible',
                'wellness_philosophy_visible',
                'wellness_philosophy_link_label',
                'wellness_philosophy_link_url',
                'wellness_journeys_visible',
                'signature_visible',
                'signature_eyebrow',
                'signature_heading',
                'signature_description',
                'signature_image',
                'signature_image_alt',
                'signature_link_label',
                'signature_link_url',
                'why_nandini_visible',
                'guest_review_visible',
                'guest_review_quote',
                'guest_review_label',
                'guest_review_image',
                'guest_review_image_alt',
                'booking_cta_visible',
                'booking_cta_eyebrow',
                'booking_cta_heading',
                'booking_cta_description',
                'booking_cta_button_label',
                'booking_cta_button_url',
                'booking_cta_image',
                'booking_cta_image_alt',
            ]);
        });
    }
};
