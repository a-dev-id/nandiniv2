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
            $table->json('information_bar_items')->nullable()->after('why_dine_items');
            $table->string('private_dining_eyebrow')->nullable()->after('information_bar_items');
            $table->text('private_dining_heading')->nullable()->after('private_dining_eyebrow');
            $table->text('private_dining_description')->nullable()->after('private_dining_heading');
            $table->string('private_dining_image')->nullable()->after('private_dining_description');
            $table->string('private_dining_image_alt')->nullable()->after('private_dining_image');
            $table->string('private_dining_cta_label')->nullable()->after('private_dining_image_alt');
            $table->text('private_dining_cta_url')->nullable()->after('private_dining_cta_label');
            $table->json('private_dining_tags')->nullable()->after('private_dining_cta_url');
            $table->string('visit_eyebrow')->nullable()->after('private_dining_tags');
            $table->text('visit_heading')->nullable()->after('visit_eyebrow');
            $table->json('visit_information_items')->nullable()->after('visit_heading');
            $table->string('visit_food_menu_label')->nullable()->after('visit_information_items');
            $table->text('visit_food_menu_url')->nullable()->after('visit_food_menu_label');
            $table->string('visit_beverage_menu_label')->nullable()->after('visit_food_menu_url');
            $table->text('visit_beverage_menu_url')->nullable()->after('visit_beverage_menu_label');
            $table->string('faq_eyebrow')->nullable()->after('visit_beverage_menu_url');
            $table->text('faq_heading')->nullable()->after('faq_eyebrow');
            $table->json('faq_items')->nullable()->after('faq_heading');
            $table->string('reservation_cta_background_image')->nullable()->after('faq_items');
            $table->string('reservation_cta_background_image_alt')->nullable()->after('reservation_cta_background_image');
            $table->text('reservation_cta_heading')->nullable()->after('reservation_cta_background_image_alt');
            $table->text('reservation_cta_description')->nullable()->after('reservation_cta_heading');
            $table->string('reservation_cta_label')->nullable()->after('reservation_cta_description');
            $table->text('reservation_cta_url')->nullable()->after('reservation_cta_label');
        });

        $reservationUrl = 'https://wa.me/6281236871170';
        DB::table('dining_settings')->where('id', 1)->update([
            'information_bar_items' => json_encode([
                ['icon' => 'clock', 'label' => 'Opening hours', 'value' => '7.00 AM – 10.00 PM', 'link' => null],
                ['icon' => 'dining', 'label' => 'Cuisine style', 'value' => 'Indonesian & International', 'link' => null],
                ['icon' => 'location', 'label' => 'Location', 'value' => 'Ubud, Bali', 'link' => null],
                ['icon' => 'whatsapp', 'label' => 'WhatsApp reservation', 'value' => '+62 812 3687 1170', 'link' => $reservationUrl],
            ], JSON_UNESCAPED_UNICODE),
            'private_dining_eyebrow' => 'Special occasions',
            'private_dining_heading' => 'Moments to Treasure',
            'private_dining_description' => 'Whether it’s a honeymoon, anniversary, proposal or an intimate celebration, our bespoke dining experiences are designed to make your moments truly unforgettable.',
            'private_dining_cta_label' => 'Enquire private dining',
            'private_dining_cta_url' => $reservationUrl,
            'private_dining_tags' => json_encode([
                ['label' => 'Honeymoon Dinner', 'url' => '/honeymoon'],
                ['label' => 'Anniversary', 'url' => $reservationUrl],
                ['label' => 'Proposal', 'url' => $reservationUrl],
                ['label' => 'Celebrations', 'url' => $reservationUrl],
            ], JSON_UNESCAPED_UNICODE),
            'visit_eyebrow' => 'Practical information',
            'visit_heading' => 'Plan Your Visit',
            'visit_information_items' => json_encode([
                ['icon' => 'clock', 'label' => 'Opening Hours', 'value' => '7.00 AM – 10.00 PM', 'link' => null],
                ['icon' => 'dining', 'label' => 'Cuisine Style', 'value' => 'Indonesian & International', 'link' => null],
                ['icon' => 'location', 'label' => 'Location', 'value' => "Nandini Jungle by Hanging Gardens\nUbud, Bali", 'link' => null],
                ['icon' => 'whatsapp', 'label' => 'WhatsApp', 'value' => '+62 812 3687 1170', 'link' => $reservationUrl],
                ['icon' => 'email', 'label' => 'Email', 'value' => 'reservation@nandinibali.com', 'link' => 'mailto:reservation@nandinibali.com'],
                ['icon' => 'guests', 'label' => 'Open to Outside Guests', 'value' => 'Yes, all are welcome', 'link' => null],
            ], JSON_UNESCAPED_UNICODE),
            'visit_food_menu_label' => 'View food menu',
            'visit_food_menu_url' => 'https://drive.google.com/file/d/1XwHnNgEreiCA4mexe65GOc44Q9WSRSsg/view?usp=sharing',
            'visit_beverage_menu_label' => 'View beverage list',
            'visit_beverage_menu_url' => 'https://drive.google.com/file/d/1Xm5YhSbX18muQQTdrLvNLYd7cgFw5EaS/view?usp=sharing',
            'faq_eyebrow' => 'Frequently asked questions',
            'faq_heading' => 'You May Wonder',
            'faq_items' => json_encode([
                ['question' => 'Do I need a reservation?', 'answer' => 'Reservations are recommended, especially for dinner, romantic dining and special occasions. Walk-in availability may vary.'],
                ['question' => 'Is the restaurant open to outside guests?', 'answer' => 'Yes. Outside guests are welcome to dine at Nandini Jungle by Hanging Gardens. Advance reservation is recommended.'],
                ['question' => 'Do you accommodate dietary preferences?', 'answer' => 'Please share any dietary preferences, allergies or special requirements with our team before your visit so the kitchen can advise and assist where possible.'],
                ['question' => 'Is afternoon tea available daily?', 'answer' => 'Afternoon tea is offered daily from 3.00 PM to 5.00 PM. Please contact our team in advance to confirm availability and arrangements for your visit.'],
                ['question' => 'Can I book a romantic or private dining experience?', 'answer' => 'Yes. Romantic and private dining experiences are available by arrangement. Contact our team on WhatsApp to discuss your preferred occasion and date.'],
            ], JSON_UNESCAPED_UNICODE),
            'reservation_cta_heading' => 'A Table Awaits in the Jungle',
            'reservation_cta_description' => "Let us create a memorable dining experience for you.\nReserve your table and indulge in the flavours of Nandini Jungle.",
            'reservation_cta_label' => 'Reserve a table',
            'reservation_cta_url' => $reservationUrl,
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('dining_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'information_bar_items', 'private_dining_eyebrow', 'private_dining_heading',
                'private_dining_description', 'private_dining_image', 'private_dining_image_alt',
                'private_dining_cta_label', 'private_dining_cta_url', 'private_dining_tags',
                'visit_eyebrow', 'visit_heading', 'visit_information_items', 'visit_food_menu_label',
                'visit_food_menu_url', 'visit_beverage_menu_label', 'visit_beverage_menu_url',
                'faq_eyebrow', 'faq_heading', 'faq_items', 'reservation_cta_background_image',
                'reservation_cta_background_image_alt', 'reservation_cta_heading',
                'reservation_cta_description', 'reservation_cta_label', 'reservation_cta_url',
            ]);
        });
    }
};
