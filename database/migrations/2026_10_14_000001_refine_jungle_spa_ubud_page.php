<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const PAGE_SLUG = 'jungle-spa-ubud';

    public function up(): void
    {
        if (! Schema::hasTable('pages') || ! Schema::hasTable('page_sections')) {
            return;
        }

        $pageId = DB::table('pages')
            ->where('site', 'main')
            ->where('slug', self::PAGE_SLUG)
            ->value('id');

        if (! $pageId) {
            return;
        }

        DB::table('pages')->where('id', $pageId)->update([
            'excerpt' => 'Discover a refined jungle spa in Ubud, where Balinese treatments and riverside rituals unfold within Nandini’s rainforest setting.',
            'description' => '<p>At Nandini Jungle by Hanging Gardens, wellness unfolds between Bali’s rainforest canopy and the Ayung River landscape. Our jungle spa in Ubud offers a quiet setting for Balinese treatments, restorative rituals and time to slow down.</p><p>Choose an intimate treatment at Essence Spa or journey deeper into the landscape for a riverside spa experience in Ubud, each shaped by attentive care and the natural rhythm of the surroundings.</p>',
            'updated_at' => now(),
        ]);

        DB::table('page_sections')
            ->where('page_id', $pageId)
            ->whereIn('title', [
                'A Spa Experience Shaped by the Jungle',
                'From Jungle Canopy to Riverside Calm',
                'Make Wellness Part of Your Ubud Escape',
                'Why Experience a Jungle Spa at Nandini?',
                'Frequently Asked Questions',
            ])
            ->update([
                'is_active' => false,
                'updated_at' => now(),
            ]);

        $this->updateSection($pageId, 'Discover Our Jungle Spa Experiences', [
            'title' => 'Discover Our Spa Experiences',
            'description' => '<p>Discover four distinctive ways to experience wellness at Nandini, from quiet treatments among the trees to a signature ritual beside the Ayung River.</p>',
            'text_align' => 'center',
            'background_color' => 'soft_gray',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->updateSection($pageId, 'Essence Spa', [
            'description' => '<p>Essence Spa brings Balinese wellness traditions into an intimate setting surrounded by tropical greenery. Skilled therapies, aromatic oils and thoughtfully chosen ingredients create a restorative pause within your stay.</p><p>For guests seeking a jungle spa in Bali, it is a tranquil place to release tension and return to the day feeling renewed.</p>',
            'button_label' => 'Explore Essence Spa',
            'button_link_type' => 'manual',
            'button_url' => 'https://spa.nandinibali.com/',
            'background_color' => 'white',
            'is_active' => true,
            'sort_order' => 2,
        ], 'Balinese spa treatment at Essence Spa in Ubud, Bali');

        $this->updateSection($pageId, 'Signature Spa on the River', [
            'title' => 'Nandini Signature Spa on the River',
            'description' => '<p>Set beside the Ayung River, Nandini’s signature 180-minute ritual begins with a soothing foot bath before an Exotic Balinese Massage, body mask, body scrub and relaxing facial.</p><p>Flowing water and the forested riverbank shape an immersive riverside spa in Ubud, created for guests who want to linger in the landscape.</p>',
            'button_label' => 'View Spa on the River',
            'button_link_type' => 'manual',
            'button_url' => '/holy-river/nandini-signature-spa-on-the-river',
            'background_color' => 'soft_gray',
            'is_active' => true,
            'sort_order' => 3,
        ], 'Riverside spa treatment beside the Ayung River at Nandini Jungle');

        $this->updateSection($pageId, 'Wine Spa', [
            'description' => '<p>This 2.5-hour ritual pairs a Traditional Balinese Massage with a wine body scrub, body mask and wine bath. Antioxidant-rich ingredients and an unhurried pace make it an indulgent spa experience in Ubud.</p>',
            'button_label' => 'View Wine Spa',
            'button_link_type' => 'manual',
            'button_url' => 'https://voucher.nandinibali.com/voucher/luxury-spa-package-wine-spa',
            'background_color' => 'white',
            'is_active' => true,
            'sort_order' => 4,
        ], 'Wine Spa treatment in the jungle at Nandini Jungle, Bali');

        $this->updateSection($pageId, 'Spa Jacuzzi', [
            'description' => '<p>Pause in the Spa Jacuzzi while tropical greenery frames the view. The warm water and quiet setting offer a gentle interlude before or after a treatment.</p>',
            'button_label' => 'Book Spa Jacuzzi',
            'button_link_type' => 'manual',
            'button_url' => 'https://wa.me/6281236871170?text=Hello%2C%20I%20would%20like%20to%20make%20an%20inquiry%20about%20the%20Spa%20Jacuzzi%20at%20Nandini%20Jungle.',
            'background_color' => 'soft_gray',
            'is_active' => true,
            'sort_order' => 5,
        ], 'Spa Jacuzzi overlooking tropical greenery at Nandini Jungle');

        $this->updateSection($pageId, 'A Jungle Spa Experience for Couples', [
            'title' => 'A Romantic Spa Escape',
            'description' => '<p>Share unhurried time together with a couples’ spa ritual shaped by the stillness of the rainforest and river.</p><p>Pair your treatment with a <a href="/honeymoon"><strong>honeymoon experience</strong></a>, or discover a longer <a href="/ubud-wellness-retreat"><strong>Ubud wellness retreat</strong></a> centred on rest, nature and reconnection.</p>',
            'button_label' => 'Explore Honeymoon Experiences',
            'button_link_type' => 'manual',
            'button_url' => '/honeymoon',
            'background_color' => 'white',
            'is_active' => true,
            'sort_order' => 6,
        ], 'Couples spa experience at Nandini Jungle in Ubud, Bali');

        $this->updateSection($pageId, 'Find Your Moment of Calm in the Bali Jungle', [
            'title' => 'Plan Your Spa Experience',
            'description' => '<p>Explore the <a href="https://spa.nandinibali.com/"><strong>Essence Spa menu</strong></a>, then contact our team to reserve the treatment and time that suit your stay.</p>',
            'button_label' => 'Book a Spa Experience',
            'button_link_type' => 'manual',
            'button_url' => 'https://wa.me/6281236871170?text=Hello%2C%20I%20would%20like%20to%20make%20an%20inquiry%20about%20a%20spa%20experience%20at%20Nandini%20Jungle.',
            'background_color' => 'soft_gray',
            'is_active' => true,
            'sort_order' => 7,
        ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('pages') || ! Schema::hasTable('page_sections')) {
            return;
        }

        $pageId = DB::table('pages')
            ->where('site', 'main')
            ->where('slug', self::PAGE_SLUG)
            ->value('id');

        if (! $pageId) {
            return;
        }

        DB::table('page_sections')
            ->where('page_id', $pageId)
            ->update([
                'is_active' => true,
                'updated_at' => now(),
            ]);
    }

    private function updateSection(int $pageId, string $currentTitle, array $values, ?string $imageAlt = null): void
    {
        $sectionId = DB::table('page_sections')
            ->where('page_id', $pageId)
            ->where('title', $currentTitle)
            ->value('id');

        if (! $sectionId) {
            return;
        }

        DB::table('page_sections')->where('id', $sectionId)->update($values + [
            'updated_at' => now(),
        ]);

        if ($imageAlt && Schema::hasTable('page_section_images')) {
            DB::table('page_section_images')
                ->where('page_section_id', $sectionId)
                ->update([
                    'image_alt' => $imageAlt,
                    'mobile_image_alt' => $imageAlt,
                    'updated_at' => now(),
                ]);
        }
    }
};
