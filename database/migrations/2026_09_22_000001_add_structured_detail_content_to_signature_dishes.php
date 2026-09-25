<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('signature_dishes', function (Blueprint $table): void {
            $table->json('detail_content')->nullable()->after('content');
        });

        $sourceDirectory = public_path('images/nasi-jinggo');
        $destinationDirectory = 'dining/signature-dishes/nasi-jinggo';
        $images = [
            'nasi-jinggo.jpg' => 'hero.jpg',
            'Pork Rib Bakar.jpg' => 'pork-rib-bakar.jpg',
            'Nasi Putih.jpg' => 'nasi-putih.jpg',
            'Saté.jpg' => 'sate.jpg',
            'Lobster Sambal.jpg' => 'lobster-sambal.jpg',
            'Pepes Ikan Kedonganan.jpg' => 'pepes-ikan-kedonganan.jpg',
            'Ayam Bakar Madu.jpg' => 'ayam-bakar-madu.jpg',
            'Tuna Sambal Matah.jpg' => 'tuna-sambal-matah.jpg',
            'Tempe Bacem.jpg' => 'tempe-bacem.jpg',
        ];

        foreach ($images as $sourceName => $destinationName) {
            $sourcePath = $sourceDirectory.DIRECTORY_SEPARATOR.$sourceName;
            $destinationPath = $destinationDirectory.'/'.$destinationName;

            if (is_file($sourcePath) && ! Storage::disk('public')->exists($destinationPath)) {
                Storage::disk('public')->put($destinationPath, file_get_contents($sourcePath));
            }
        }

        $image = fn (string $name): string => $destinationDirectory.'/'.$name;
        $reservationUrl = config('dining.reservation_url')
            ?: 'https://wa.me/6281236871170?text='.rawurlencode('Hello, I would like to reserve the Nasi Jinggo $100 experience.');

        $detailContent = [[
            'hero_title' => 'Nasi Jinggo $100',
            'hero_description' => 'A premium sharing experience that transforms one of Bali’s most humble traditional meals into a memorable culinary journey — authentic in spirit, extraordinary in every detail.',
            'hero_image' => $image('hero.jpg'),
            'hero_image_alt' => 'Nasi Jinggo sharing experience at Nandini Jungle by Hanging Gardens',
            'story_visible' => true,
            'story_eyebrow' => 'Our Story',
            'story_title' => 'The Story of Nasi Jinggo',
            'story_description' => '<p>Nasi Jinggo is a beloved Balinese traditional meal known for its humble everyday character and its connection with banana-leaf presentation. Its story is commonly linked to the 1970s and Ni Ketut Ngasti, affectionately known as Men Jinggo.</p><p>At Nandini, the idea is not to replace that tradition, but to celebrate it differently. Our Nasi Jinggo $100 keeps the spirit of the original while transforming the experience through refined presentation, premium ingredients, traditional cooking techniques and warm Balinese hospitality.</p>',
            'story_image' => $image('hero.jpg'),
            'story_image_alt' => 'Nasi Jinggo presented as a refined Balinese sharing experience',
            'story_quote' => 'Same roots. A higher table.',
            'highlights_visible' => true,
            'highlights_eyebrow' => 'The Experience',
            'highlights_title' => 'From Humble Roots to a Signature Experience',
            'highlights' => [
                ['title' => 'Traditional Technique', 'description' => 'Selected components are grilled over coconut charcoal for a distinctive smoky character.'],
                ['title' => 'Local Sourcing', 'description' => 'Most ingredients are sourced locally from nearby farms and local suppliers.'],
                ['title' => 'Premium Ingredients', 'description' => 'Australian beef tenderloin, bamboo lobster, yellowfin tuna and organic rice.'],
                ['title' => 'Refined Presentation', 'description' => 'A familiar local favorite reimagined as an elegant sharing experience.'],
                ['title' => 'A Memorable Moment', 'description' => 'A generous dining experience for two, created to be shared slowly and remembered.'],
            ],
            'components_visible' => true,
            'components_eyebrow' => 'The Menu',
            'components_title' => 'Eight Iconic Components',
            'components_description' => 'A journey through Balinese flavors, from charcoal-grilled meats and fresh seafood to sambal, banana-leaf preparations and organic rice.',
            'components' => [
                ['title' => 'Pork Rib Bakar', 'description' => 'Baby back pork ribs cooked until tender, finished over coconut charcoal and glazed with soy.', 'image' => $image('pork-rib-bakar.jpg'), 'image_alt' => 'Pork Rib Bakar with soy glaze'],
                ['title' => 'Nasi Putih', 'description' => 'Organic steamed rice presented in banana-leaf parcels with a traditional visual character.', 'image' => $image('nasi-putih.jpg'), 'image_alt' => 'Nasi Putih in banana-leaf parcels'],
                ['title' => 'Saté', 'description' => 'Grilled calamari and beef tenderloin satay served with red sambal and peanut sauce.', 'image' => $image('sate.jpg'), 'image_alt' => 'Calamari and beef tenderloin satay'],
                ['title' => 'Lobster Sambal', 'description' => 'Grilled bamboo lobster marinated with a rich red spicy sambal for freshness, depth and umami.', 'image' => $image('lobster-sambal.jpg'), 'image_alt' => 'Grilled bamboo lobster with red sambal'],
                ['title' => 'Pepes Ikan Kedonganan', 'description' => 'Red snapper with Balinese spices, wrapped in banana leaf, steamed and then gently grilled.', 'image' => $image('pepes-ikan-kedonganan.jpg'), 'image_alt' => 'Pepes Ikan Kedonganan wrapped in banana leaf'],
                ['title' => 'Ayam Bakar Madu', 'description' => 'Tender chicken leg grilled with honey and red spicy sambal for a juicy, aromatic finish.', 'image' => $image('ayam-bakar-madu.jpg'), 'image_alt' => 'Ayam Bakar Madu with honey and sambal'],
                ['title' => 'Tuna Sambal Matah', 'description' => 'Yellowfin tuna paired with fresh sambal matah, shallot and kaffir lime for brightness and texture.', 'image' => $image('tuna-sambal-matah.jpg'), 'image_alt' => 'Yellowfin tuna with sambal matah'],
                ['title' => 'Tempe Bacem', 'description' => 'Tempe cooked with bacem seasoning and tamarind chili paste, balancing sweetness, savoriness and umami.', 'image' => $image('tempe-bacem.jpg'), 'image_alt' => 'Tempe Bacem with tamarind chili paste'],
            ],
            'premium_visible' => true,
            'premium_eyebrow' => 'Crafted at Nandini',
            'premium_title' => 'Tradition Elevated. Flavors Unforgettable.',
            'premium_description' => '<p>The experience brings together traditional tools, coconut-charcoal grilling, banana-leaf presentation and distinctive sambals with premium seafood, meat and carefully sourced ingredients. It is not intended to hide the humble origins of Nasi Jinggo — it celebrates them through a more generous and refined expression.</p>',
            'premium_image' => $image('hero.jpg'),
            'premium_image_alt' => 'The complete Nasi Jinggo sharing menu at Nandini Jungle',
            'reservation_visible' => true,
            'reservation_eyebrow' => 'Your Table Awaits',
            'reservation_title' => 'Reserve Nasi Jinggo $100 at Nandini',
            'reservation_description' => 'Discover a Balinese tradition through a signature sharing experience created for two. Available for lunch or dinner; advance reservation is recommended.',
            'reservation_button_label' => 'Reserve a Table',
            'reservation_button_url' => $reservationUrl,
            'reservation_image' => $image('hero.jpg'),
            'reservation_image_alt' => '',
        ]];

        DB::table('signature_dishes')->where('slug', 'nasi-jinggo')->update([
            'name' => 'Nasi Jinggo $100',
            'eyebrow' => 'Signature Dish',
            'subtitle' => 'A Signature Taste of Bali',
            'price' => null,
            'short_description' => 'A beloved Balinese street food, reimagined with premium ingredients and refined presentation, offering an authentic taste of Indonesia in the extraordinary setting of Nandini Jungle.',
            'cta_label' => 'View Signature Dish',
            'cta_url' => '/signature-dishes/nasi-jinggo',
            'image' => $image('hero.jpg'),
            'image_alt' => 'Nasi Jinggo signature sharing experience at Nandini Jungle',
            'detail_content' => json_encode($detailContent, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'meta_title' => 'Nasi Jinggo $100 | Nandini Jungle Dining',
            'meta_description' => 'Discover Nasi Jinggo $100, a refined Balinese sharing experience for two at Nandini Jungle by Hanging Gardens in Ubud.',
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('signature_dishes', function (Blueprint $table): void {
            $table->dropColumn('detail_content');
        });
    }
};
