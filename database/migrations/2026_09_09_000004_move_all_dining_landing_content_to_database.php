<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dining_settings', function (Blueprint $table): void {
            $table->string('hero_video_id')->nullable()->after('meta_description');
            $table->string('meta_author')->nullable()->after('meta_description');
            $table->string('meta_site_name')->nullable()->after('meta_author');
            $table->string('hero_image')->nullable()->after('hero_video_id');
            $table->string('hero_image_alt')->nullable()->after('hero_image');
            $table->string('hero_eyebrow')->nullable()->after('hero_image_alt');
            $table->text('hero_heading')->nullable()->after('hero_eyebrow');
            $table->text('hero_subheading')->nullable()->after('hero_heading');
            $table->text('hero_description')->nullable()->after('hero_subheading');
            $table->string('hero_primary_cta_label')->nullable()->after('hero_description');
            $table->text('hero_primary_cta_url')->nullable()->after('hero_primary_cta_label');
            $table->string('hero_secondary_cta_label')->nullable()->after('hero_primary_cta_url');
            $table->text('hero_secondary_cta_url')->nullable()->after('hero_secondary_cta_label');
            $table->string('experiences_eyebrow')->nullable()->after('why_dine_items');
            $table->text('experiences_heading')->nullable()->after('experiences_eyebrow');
            $table->json('signature_dishes')->nullable()->after('experiences_heading');
            $table->string('signature_menu_label')->nullable()->after('signature_dishes');
            $table->text('signature_menu_url')->nullable()->after('signature_menu_label');
            $table->string('guest_reviews_heading')->nullable()->after('signature_menu_url');
            $table->string('guest_reviews_see_more_label')->nullable()->after('guest_reviews_heading');
            $table->text('guest_reviews_see_more_url')->nullable()->after('guest_reviews_see_more_label');
        });

        $settings = DB::table('dining_settings')->where('id', 1)->first();

        if (! $settings) {
            DB::table('dining_settings')->insert(['id' => 1, 'created_at' => now(), 'updated_at' => now()]);
            $settings = DB::table('dining_settings')->where('id', 1)->first();
        }

        $heroVideoId = config('dining.hero_video_id') ?: 'GZav9hOJKts';
        $heroImage = config('dining.hero_image');
        if (blank($heroImage)) {
            $pageHero = DB::table('pages')->where('id', 4)->where('is_active', true)->value('hero_image');
            $heroImage = filled($pageHero) ? $pageHero : null;
        }

        if (blank($heroImage) && filled($heroVideoId)) {
            $heroImage = 'https://i.ytimg.com/vi/'.$heroVideoId.'/maxresdefault.jpg';
        }

        $heroMenuUrl = filled($settings->food_menu_url) ? $settings->food_menu_url : config('dining.menu_url');
        $reservationUrl = filled($settings->reservation_url) ? $settings->reservation_url : config('dining.reservation_url');
        $heroMenuUrl = $heroMenuUrl ?: 'https://wa.me/6281236871170?text='.rawurlencode('Hello, may I view the dining menu, please?');

        $menuItems = DB::table('page_sections')->where('page_id', 4)
            ->where('section_key', 'dining_information_section')->where('is_active', true)
            ->orderBy('sort_order')->value('items');
        $menuItems = is_string($menuItems) ? (json_decode($menuItems, true) ?: []) : ($menuItems ?: []);
        $signatureMenuItem = collect($menuItems)->first(fn ($item) => strtolower(trim($item['label'] ?? '')) === 'view menu');
        $signatureMenuUrl = $signatureMenuItem['url'] ?? null;
        $signatureMenuUrl = config('dining.menu_url') ?: $signatureMenuUrl ?: 'https://drive.google.com/file/d/1XwHnNgEreiCA4mexe65GOc44Q9WSRSsg/view?usp=sharing';

        $privateImage = config('dining.experience_images.romantic');
        if (blank($privateImage)) {
            $romantic = DB::table('experiences')->where('slug', 'romantic-dining-by-the-chapel')->where('is_active', true)->first();
            $privateImage = $romantic?->image ?: $romantic?->card_image;
        }
        $privateImage = $privateImage ?: 'https://placehold.co/1200x900/F3F4F5/8F6B34?text=Private+Dining';

        $defaults = [
            'meta_title' => 'Luxury Dining in Ubud | Nandini Jungle by Hanging Gardens',
            'meta_description' => 'Discover luxury jungle dining in Ubud at Nandini Jungle by Hanging Gardens, featuring Wild Ginger Restaurant, refined Balinese flavours, romantic dining and curated culinary experiences.',
            'meta_author' => 'Nandini Jungle by Hanging Gardens',
            'meta_site_name' => 'Nandini Jungle by Hanging Gardens',
            'hero_video_id' => $heroVideoId,
            'hero_image' => $heroImage,
            'hero_image_alt' => '',
            'hero_eyebrow' => 'Dining at Nandini Jungle',
            'hero_heading' => "A Culinary Journey\nin the Heart of the Jungle",
            'hero_subheading' => 'Exquisite flavours. Enchanting surroundings. Unforgettable moments.',
            'hero_description' => 'Discover a dining experience where authentic Balinese ingredients meet international finesse, set within the lush jungle of Ubud.',
            'hero_primary_cta_label' => 'Reserve a table',
            'hero_primary_cta_url' => $reservationUrl,
            'hero_secondary_cta_label' => 'View menu',
            'hero_secondary_cta_url' => $heroMenuUrl,
            'philosophy_eyebrow' => 'Our philosophy',
            'philosophy_heading' => "More Than a Meal,\nA Meaningful Experience",
            'philosophy_description' => 'At Nandini Jungle, dining is a celebration of nature, culture and connection. Our culinary philosophy is inspired by the richness of Indonesia, crafted with the finest ingredients, and served with heartfelt hospitality in an extraordinary jungle setting.',
            'philosophy_image' => config('dining.philosophy_image') ?: 'https://placehold.co/1200x900/F7F4EE/8F6B34?text=Chef+Plating',
            'philosophy_image_alt' => 'A chef adds the finishing touches to a plated dish at Nandini Jungle.',
            'philosophy_accent_text' => "Flavours\nfrom the Heart\nof Bali",
            'why_dine_eyebrow' => 'Why dine at Nandini',
            'why_dine_heading' => "An Extraordinary Setting\nfor Every Occasion",
            'why_dine_items' => json_encode([
                ['title' => "Breathtaking\nJungle Setting", 'description' => 'Dine surrounded by the serene beauty of Ubud’s lush rainforest.', 'icon' => 'leaves'],
                ['title' => "Authentic &\nRefined Cuisine", 'description' => 'A harmonious blend of Indonesian, Balinese and international flavours.', 'icon' => 'bowl'],
                ['title' => "Award-Winning\nWine Collection", 'description' => 'Curated wines from around the world.', 'icon' => 'wine'],
                ['title' => 'Romantic & Intimate', 'description' => 'Perfect for couples, honeymooners and special celebrations.', 'icon' => 'heart'],
            ], JSON_UNESCAPED_UNICODE),
            'experiences_eyebrow' => 'Dining experiences',
            'experiences_heading' => 'Distinctive Dining, Made for You',
            'signature_dishes' => json_encode([[
                'eyebrow' => 'Dish of the Month', 'heading' => 'Rahang Tuna',
                'introduction' => 'Celebrating the culinary traditions of the archipelago, Rahang Tuna showcases the prized tuna cheek — tender, flavourful, and delicately grilled over natural fire.',
                'label' => 'Ocean’s Finest Cut', 'title' => 'Rahang Tuna', 'price_display' => 'IDR 420,000++ per person',
                'panel_description' => 'Tender tuna cheek, delicately grilled over natural fire and served with crisp potato wedges, seasonal vegetables and house-made sauce.',
                'image' => '/images/dining/rahang-tuna.jpeg',
                'alt' => 'Rahang Tuna with potato wedges, seasonal vegetables and house-made sauce at Nandini Jungle',
            ]], JSON_UNESCAPED_UNICODE),
            'signature_menu_label' => 'View full menu',
            'signature_menu_url' => $signatureMenuUrl,
            'private_dining_image' => $privateImage,
            'private_dining_image_alt' => 'Romantic candlelit private dining setup at Nandini Jungle by Hanging Gardens',
            'guest_reviews_heading' => 'What Our Guests Say',
            'guest_reviews_see_more_label' => 'See More',
            'guest_reviews_see_more_url' => route('guest-reviews.index'),
            'reservation_cta_background_image' => '/images/dining/romantic-dining-by-the-chapel.webp',
            'reservation_cta_background_image_alt' => '',
        ];

        $updates = [];
        foreach ($defaults as $field => $value) {
            if ($settings->{$field} === null && $value !== null) {
                $updates[$field] = $value;
            }
        }

        if ($updates !== []) {
            $updates['updated_at'] = now();
            DB::table('dining_settings')->where('id', 1)->update($updates);
        }

        $cardDefaults = [
            'wild-ginger-restaurant' => ['Wild Ginger Restaurant', 'Dine surrounded by nature, offering a refined à la carte menu inspired by Indonesian and international cuisine.', 'Explore Restaurant', 'Signature dish at Wild Ginger Restaurant, Nandini Jungle Ubud'],
            'bar-and-lounge' => ['Bar & Lounge', 'Relax with handcrafted cocktails, fine wines and light bites in a stylish open-air lounge.', 'Explore Bar & Lounge', 'Craft cocktail and light bites at Nandini Jungle Bar & Lounge'],
            'afternoon-tea' => ['Afternoon Tea', 'A delightful selection of sweet and savoury creations, served in the heart of the jungle.', 'Discover Afternoon Tea', 'Afternoon tea selection at Nandini Jungle in Ubud'],
            'wine-cellar-experience' => ['Wine Cellar Experience', 'An exclusive collection of premium wines, perfect for a refined evening or a private tasting.', 'Explore Wine Cellar', 'Wine being poured during the Nandini Jungle wine cellar experience'],
            'romantic-dining' => ['Romantic Dining', 'Create unforgettable moments with a candlelit dinner in a magical jungle atmosphere.', 'Discover Romantic Dining', 'Candlelit romantic dining experience at Nandini Jungle'],
        ];

        foreach ($cardDefaults as $slug => [$title, $description, $cta, $alt]) {
            $record = DB::table('experiences')->where('dining_slug', $slug)->first();
            if (! $record) {
                continue;
            }
            $cardUpdates = array_filter([
                'dining_card_title' => $record->dining_card_title === null ? $title : null,
                'dining_short_description' => $record->dining_short_description === null ? $description : null,
                'dining_cta_label' => $record->dining_cta_label === null ? $cta : null,
                'card_image_alt' => $record->card_image_alt === null ? $alt : null,
            ], fn ($value) => $value !== null);
            if ($cardUpdates !== []) {
                DB::table('experiences')->where('id', $record->id)->update($cardUpdates + ['updated_at' => now()]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('dining_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'meta_author', 'meta_site_name', 'hero_video_id', 'hero_image', 'hero_image_alt', 'hero_eyebrow', 'hero_heading',
                'hero_subheading', 'hero_description', 'hero_primary_cta_label', 'hero_primary_cta_url',
                'hero_secondary_cta_label', 'hero_secondary_cta_url', 'experiences_eyebrow',
                'experiences_heading', 'signature_dishes', 'signature_menu_label', 'signature_menu_url',
                'guest_reviews_heading', 'guest_reviews_see_more_label', 'guest_reviews_see_more_url',
            ]);
        });
    }
};
