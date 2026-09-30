<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const PAGE_NAME = 'SEO - Jungle Spa Ubud Page';

    private const PAGE_SLUG = 'jungle-spa-ubud';

    public function up(): void
    {
        if (! Schema::hasTable('pages') || ! Schema::hasTable('page_sections')) {
            return;
        }

        $now = now();
        $pageId = DB::table('pages')
            ->where('site', 'main')
            ->where('slug', self::PAGE_SLUG)
            ->value('id');

        $page = [
            'site' => 'main',
            'page_name' => self::PAGE_NAME,
            'title' => 'Jungle Spa in Ubud, Bali',
            'slug' => self::PAGE_SLUG,
            'subtitle' => null,
            'excerpt' => 'Escape into the quiet beauty of Bali’s tropical landscape and experience wellness surrounded by nature at Nandini Jungle by Hanging Gardens.',
            'description' => '<p>Escape into the quiet beauty of Bali’s tropical landscape and experience wellness surrounded by nature at Nandini Jungle by Hanging Gardens.</p><p>Set within a lush jungle environment near Ubud, our spa experiences invite you to slow down, reconnect and enjoy a deeper sense of relaxation. From traditional Balinese massage and restorative body treatments to immersive riverside rituals, each experience is designed to bring together nature, wellness and the timeless traditions of Bali.</p><p>For travelers searching for a <strong>jungle spa in Ubud</strong>, Nandini offers more than a place for a treatment. It is an opportunity to step away from the pace of everyday life and experience wellness within the natural rhythm of the jungle.</p>',
            'hero_image' => 'pages/hero/ubud-wellness-retreat-jungle-spa-terrace.webp',
            'hero_image_alt' => 'Jungle spa experience surrounded by tropical greenery at Nandini Jungle in Ubud, Bali',
            'hero_mobile_image' => 'pages/hero-mobile/ubud-wellness-retreat-jungle-spa-terrace.webp',
            'hero_mobile_image_alt' => 'Jungle spa experience surrounded by tropical greenery at Nandini Jungle in Ubud, Bali',
            'meta_title' => 'Jungle Spa Ubud | Spa in the Bali Jungle | Nandini',
            'meta_description' => 'Escape to a jungle spa in Ubud, Bali. Discover Balinese treatments, riverside spa rituals and relaxing wellness experiences at Nandini Jungle.',
            'is_active' => true,
            'updated_at' => $now,
        ];

        if ($pageId) {
            DB::table('pages')->where('id', $pageId)->update($page);
        } else {
            $pageId = max(1000, ((int) DB::table('pages')->max('id')) + 1);

            $pageId = DB::table('pages')->insertGetId($page + [
                'id' => $pageId,
                'sort_order' => ((int) DB::table('pages')->max('sort_order')) + 1,
                'created_at' => $now,
            ]);
        }

        $existingSectionIds = DB::table('page_sections')
            ->where('page_id', $pageId)
            ->pluck('id');

        if (Schema::hasTable('page_section_images') && $existingSectionIds->isNotEmpty()) {
            DB::table('page_section_images')->whereIn('page_section_id', $existingSectionIds)->delete();
        }

        DB::table('page_sections')->where('page_id', $pageId)->delete();

        $sections = [
            [
                'section_key' => 'seo_split_media_section',
                'title' => 'A Spa Experience Shaped by the Jungle',
                'description' => '<p>Nature is an essential part of the spa experience at Nandini Jungle.</p><p>Surrounded by tropical greenery, fresh jungle air and the natural sounds of the landscape, treatments unfold in an atmosphere created for rest. Rather than separating wellness from the destination, our approach allows the environment of Ubud to become part of the experience itself.</p><p>Traditional techniques, aromatic oils, local ingredients and carefully designed wellness rituals come together in a setting where guests can simply slow down.</p><p>Whether you are looking for a quiet massage after exploring Ubud, a spa experience for two or a longer wellness ritual, the jungle provides a naturally calming backdrop for your time at Nandini.</p>',
                'button_label' => 'Explore Spa & Wellness',
                'button_url' => '/spa-wellness',
                'background_color' => 'soft_gray',
                'image' => 'pages/sections/ubud-jungle-spa-treatment-nandini-wellness-retreat.webp',
                'mobile_image' => 'pages/sections/mobile/ubud-jungle-spa-treatment-nandini-wellness-retreat.webp',
                'image_alt' => 'Jungle spa treatment at Nandini Jungle by Hanging Gardens in Ubud, Bali',
            ],
            [
                'section_key' => 'intro_text_section',
                'title' => 'Discover Our Jungle Spa Experiences',
                'description' => null,
                'background_color' => 'white',
            ],
            [
                'section_key' => 'seo_split_media_reverse',
                'title' => 'Essence Spa',
                'description' => '<p>Nestled within the tropical landscape of Nandini Jungle, Essence Spa brings Balinese wellness traditions together with a peaceful natural setting.</p><p>Treatments are designed to encourage relaxation and renewal, incorporating traditional techniques, aromatic oils and carefully selected ingredients.</p><p>It is an ideal choice for guests who want to make spa and wellness part of their Ubud stay while remaining connected to the surrounding jungle.</p>',
                'button_label' => 'Discover Essence Spa',
                'button_url' => '/spa-wellness',
                'background_color' => 'white',
                'image' => 'pages/sections/4791e636-30b1-4fed-acc0-55bcba176534.webp',
                'mobile_image' => 'pages/sections/mobile/2e1a7742-cd2c-439d-afb5-2c4aea9bcb84.webp',
                'image_alt' => 'Essence Spa at Nandini Jungle by Hanging Gardens',
            ],
            [
                'section_key' => 'seo_split_media_section',
                'title' => 'Signature Spa on the River',
                'description' => '<p>For an experience even closer to nature, descend into the jungle for one of Nandini’s signature wellness journeys beside the river.</p><p>The <strong>Signature Spa on the River</strong> begins with a soothing foot bath before continuing into a 180-minute treatment combining an Exotic Balinese Massage, body mask, body scrub and relaxing facial treatment.</p><p>The sound of flowing water and the surrounding jungle create an atmosphere that feels far removed from a conventional indoor spa.</p><p>It is one of Nandini’s most distinctive wellness experiences and a beautiful choice for guests seeking a memorable <strong>jungle spa experience in Ubud</strong>.</p>',
                'button_label' => 'Discover Signature Spa on the River',
                'button_url' => '/holy-river/nandini-signature-spa-on-the-river',
                'background_color' => 'soft_gray',
                'image' => 'pages/sections/ea97fbfe-7ca1-43fe-8e30-51b1e9dcea01.webp',
                'mobile_image' => 'pages/sections/mobile/16bb6272-730e-4082-a128-163e7b2b8705.webp',
                'image_alt' => 'Signature spa treatment beside the river at Nandini Jungle',
            ],
            [
                'section_key' => 'seo_split_media_reverse',
                'title' => 'Wine Spa',
                'description' => '<p>Discover a different approach to relaxation with Nandini’s Wine Spa.</p><p>This 2.5-hour wellness ritual combines Balinese massage techniques with wine-inspired treatments designed to nourish the skin and encourage deep relaxation.</p><p>Rich in antioxidants and created as a complete wellness journey, the experience brings together indulgence, nature and restorative care within Nandini’s tranquil jungle surroundings.</p>',
                'background_color' => 'white',
                'image' => 'pages/sections/130d593f-e6dd-4741-8d41-15ab6c2af83b.webp',
                'mobile_image' => 'pages/sections/mobile/50fa006f-1f8b-4f57-ae30-d2e6012293a7.webp',
                'image_alt' => 'Wine Spa wellness experience at Nandini Jungle',
            ],
            [
                'section_key' => 'seo_split_media_section',
                'title' => 'Spa Jacuzzi',
                'description' => '<p>Take time to unwind in our Spa Jacuzzi, surrounded by tropical greenery and the peaceful sounds of nature.</p><p>It provides a gentle transition between activity and rest, giving you time to slow down and enjoy the calm atmosphere of the resort.</p>',
                'background_color' => 'soft_gray',
                'image' => 'pages/sections/7bdab6e8-62b3-416a-85fb-3419a6a15ee8.webp',
                'mobile_image' => 'pages/sections/mobile/49aec01b-1c71-4236-8916-c99ccd4dea28.webp',
                'image_alt' => 'Spa Jacuzzi surrounded by tropical greenery at Nandini Jungle',
            ],
            [
                'section_key' => 'intro_text_section',
                'title' => 'From Jungle Canopy to Riverside Calm',
                'description' => '<p>One of the defining characteristics of a spa experience at Nandini is the connection between different layers of the landscape.</p><p>Your journey may begin among the greenery of the resort before leading deeper into the valley toward our riverside wellness setting.</p><p>Along the way, the surrounding jungle gradually becomes quieter.</p><p>The experience is intentionally unhurried.</p><p>Instead of treating wellness as simply another activity on your itinerary, Nandini gives you the opportunity to dedicate part of your day to stillness, nature and restoration.</p>',
                'background_color' => 'white',
            ],
            [
                'section_key' => 'seo_split_media_reverse',
                'title' => 'A Jungle Spa Experience for Couples',
                'description' => '<p>Ubud is a natural destination for couples who want to combine romance, nature and wellness.</p><p>A shared spa experience creates space to slow down together after days spent discovering temples, villages, restaurants and the landscapes of Bali.</p><p>Couples can choose from relaxing spa treatments or make the experience more memorable with one of Nandini’s signature riverside rituals.</p><p>A spa experience can also be combined with other moments at Nandini, creating an unhurried day centred around wellness, dining and time together in the jungle.</p><p>Whether you are visiting Ubud for a honeymoon, anniversary or simply a quiet escape for two, the jungle setting offers a different atmosphere from a traditional city spa.</p>',
                'background_color' => 'soft_gray',
                'image' => 'pages/sections/balinese-spiritual-wellness-ritual-ubud-jungle-resort.webp',
                'mobile_image' => 'pages/sections/mobile/balinese-spiritual-wellness-ritual-ubud-jungle-resort.webp',
                'image_alt' => 'Couple enjoying a Balinese wellness ritual in the Ubud jungle',
            ],
            [
                'section_key' => 'intro_text_section',
                'title' => 'Make Wellness Part of Your Ubud Escape',
                'description' => '<p>A spa treatment does not need to fill an entire itinerary.</p><p>Sometimes the most memorable part of a trip is simply having several hours without somewhere else to be.</p><p>Begin your day surrounded by the jungle, enjoy a restorative treatment and allow yourself time to remain within the slower rhythm of Nandini.</p><p>Guests looking for a more complete wellness experience can also discover yoga, riverside wellness rituals, Balinese purification experiences and other restorative activities available throughout the resort.</p><p><a href="/spa-wellness"><strong>Explore All Spa &amp; Wellness Experiences</strong></a></p>',
                'background_color' => 'white',
            ],
            [
                'section_key' => 'intro_text_section',
                'title' => 'Why Experience a Jungle Spa at Nandini?',
                'description' => '<p>At Nandini Jungle by Hanging Gardens, wellness is closely connected to the environment.</p><p>Here, a spa experience can include:</p><ul><li>Traditional Balinese massage and wellness techniques</li><li>Jungle surroundings away from the bustle of central Ubud</li><li>Signature riverside spa experiences</li><li>Wellness rituals for individuals and couples</li><li>Wine-inspired spa treatments</li><li>Spa Jacuzzi surrounded by tropical greenery</li><li>Opportunities to combine spa treatments with yoga and other wellness experiences</li></ul><p>The result is a spa journey that feels distinctly connected to Bali and the natural landscape surrounding Nandini.</p>',
                'background_color' => 'soft_gray',
            ],
            [
                'section_key' => 'intro_text_section',
                'title' => 'Frequently Asked Questions',
                'description' => '<h3>What makes Nandini a jungle spa in Ubud?</h3><p>Nandini Jungle by Hanging Gardens is surrounded by tropical vegetation in the Payangan area near Ubud. Our spa experiences are designed around this natural setting, with treatments available within the jungle resort as well as signature wellness experiences beside the river.</p><h3>What spa treatments are available?</h3><p>Guests can discover traditional Balinese massage, body treatments, facial treatments, the Wine Spa, Spa Jacuzzi and signature riverside wellness experiences. Treatment availability may vary, so we recommend checking the current spa menu when making a reservation.</p><h3>What is the Signature Spa on the River?</h3><p>Signature Spa on the River is a 180-minute Nandini wellness experience beside the river. The journey includes a foot bath, Exotic Balinese Massage, body mask, body scrub and relaxing facial treatment.</p><h3>Is the jungle spa suitable for couples?</h3><p>Yes. Couples can enjoy spa and wellness experiences together, including selected signature treatments and riverside experiences designed for shared relaxation.</p><h3>Can I combine a spa treatment with other wellness activities?</h3><p>Yes. Nandini offers a range of wellness experiences that can complement your spa journey, including yoga and selected riverside wellness rituals.</p><h3>Where is Nandini Jungle located?</h3><p>Nandini Jungle by Hanging Gardens is located in Banjar Susut, Desa Buahan, Payangan, Bali, within the greater Ubud area.</p>',
                'background_color' => 'white',
            ],
            [
                'section_key' => 'seo_split_media_section',
                'title' => 'Find Your Moment of Calm in the Bali Jungle',
                'description' => '<p>Leave the busy pace of your itinerary behind and discover a quieter side of Ubud.</p><p>Surrounded by tropical greenery, restorative treatments and the sounds of nature, a spa experience at Nandini Jungle creates space to slow down and reconnect with yourself or someone special.</p><p>From a relaxing Balinese treatment at Essence Spa to an immersive Signature Spa on the River experience, choose the journey that feels right for you.</p><p><a href="/spa-wellness"><strong>Explore Spa &amp; Wellness</strong></a></p>',
                'button_label' => 'Book Your Jungle Spa Experience',
                'button_url' => 'https://wa.me/6281236871170?text='.rawurlencode('Hello, I would like to book a jungle spa experience at Nandini Jungle.'),
                'background_color' => 'soft_gray',
            ],
        ];

        foreach ($sections as $sortOrder => $section) {
            $image = $section['image'] ?? null;
            $mobileImage = $section['mobile_image'] ?? null;
            $imageAlt = $section['image_alt'] ?? null;

            unset($section['image'], $section['mobile_image'], $section['image_alt']);

            $sectionId = DB::table('page_sections')->insertGetId($section + [
                'page_id' => $pageId,
                'button_link_type' => 'manual',
                'text_align' => 'left',
                'is_active' => true,
                'sort_order' => $sortOrder + 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            if (! Schema::hasTable('page_section_images') || ! $image) {
                continue;
            }

            DB::table('page_section_images')->insert([
                'page_section_id' => $sectionId,
                'image' => $image,
                'image_alt' => $imageAlt,
                'mobile_image' => $mobileImage,
                'mobile_image_alt' => $imageAlt,
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('pages')) {
            return;
        }

        $pageId = DB::table('pages')
            ->where('page_name', self::PAGE_NAME)
            ->where('slug', self::PAGE_SLUG)
            ->value('id');

        if (! $pageId) {
            return;
        }

        if (Schema::hasTable('page_sections')) {
            $sectionIds = DB::table('page_sections')->where('page_id', $pageId)->pluck('id');

            if (Schema::hasTable('page_section_images') && $sectionIds->isNotEmpty()) {
                DB::table('page_section_images')->whereIn('page_section_id', $sectionIds)->delete();
            }

            DB::table('page_sections')->where('page_id', $pageId)->delete();
        }

        DB::table('pages')->where('id', $pageId)->delete();
    }
};
