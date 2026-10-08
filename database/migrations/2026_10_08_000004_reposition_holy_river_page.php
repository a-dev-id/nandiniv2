<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const META_TITLE = 'Balinese Purification & Holy River in Ubud | Nandini Jungle';

    private const META_DESCRIPTION = 'Experience a Balinese purification ritual by the sacred Ayung River at Nandini Jungle in Ubud, with Melukat, traditional blessings and riverside experiences.';

    public function up(): void
    {
        $pageId = $this->holyRiverPageId();

        if (! $pageId) {
            return;
        }

        DB::table('pages')->where('id', $pageId)->update([
            'title' => 'Sacred Holy River & Balinese Purification in Ubud',
            'subtitle' => 'A SPIRITUAL JOURNEY BY THE AYUNG RIVER',
            'excerpt' => "Along the Ayung River below Nandini Jungle by Hanging Gardens, guests can discover a quieter side of Balinese spirituality through sacred water, traditional blessings and moments of reflection surrounded by the jungle.\n\nThe Holy River experience is centred on Balinese purification traditions, including Melukat, a ritual associated with spiritual cleansing and renewal. Guided experiences by the river offer guests an opportunity to connect with Balinese culture and the natural setting of Nandini in Payangan, within the greater Ubud area.",
            'description' => '<p>Along the Ayung River below Nandini Jungle by Hanging Gardens, guests can discover a quieter side of Balinese spirituality through sacred water, traditional blessings and moments of reflection surrounded by the jungle.</p><p>The Holy River experience is centred on Balinese purification traditions, including Melukat, a ritual associated with spiritual cleansing and renewal. Guided experiences by the river offer guests an opportunity to connect with Balinese culture and the natural setting of Nandini in Payangan, within the greater Ubud area.</p>',
            'hero_image_alt' => 'Balinese priest preparing a blessing ritual at Nandini Jungle',
            'hero_mobile_image_alt' => 'Balinese priest preparing a blessing ritual at Nandini Jungle',
            'meta_title' => self::META_TITLE,
            'meta_description' => self::META_DESCRIPTION,
            'updated_at' => now(),
        ]);

        $this->updateSection($pageId, 8, [
            'title' => 'THE MEANING OF MELUKAT',
            'subtitle' => 'BALINESE PURIFICATION',
            'excerpt' => null,
            'description' => '<p>Melukat is a Balinese purification ritual associated with cleansing, renewal and spiritual reflection. At Nandini, the Holy River setting offers guests the opportunity to experience this tradition beside the Ayung River in a peaceful jungle environment.</p>',
            'button_label' => null,
            'button_url' => null,
            'button_route' => null,
            'sort_order' => 1,
        ]);

        $this->updateSection($pageId, 9, [
            'title' => 'A SACRED SETTING BY THE AYUNG RIVER',
            'subtitle' => 'THE AYUNG RIVER',
            'excerpt' => null,
            'description' => '<p>Descend through Nandini\'s tropical landscape to the banks of the Ayung River, where the sound of flowing water and surrounding jungle create a peaceful setting for reflection, Balinese rituals and meaningful moments in nature.</p>',
            'button_label' => null,
            'button_url' => null,
            'button_route' => null,
            'sort_order' => 2,
        ]);

        $this->updateSection($pageId, 11, [
            'title' => 'HOLY RIVER EXPERIENCES',
            'subtitle' => 'SACRED RITUALS BY THE RIVER',
            'excerpt' => null,
            'description' => '<p>Discover experiences that bring together the Ayung River setting and Balinese tradition, from Melukat purification and blessing rituals to a half-day journey that pairs the ceremony with a riverside spa treatment.</p>',
            'button_label' => null,
            'button_url' => null,
            'button_route' => null,
            'sort_order' => 3,
        ]);

        $this->updateSection($pageId, 10, [
            'title' => 'BALINESE BLESSING & PURIFICATION',
            'subtitle' => 'BALINESE TRADITION',
            'excerpt' => null,
            'description' => '<p>The Balinese Blessing Purification at the Holy River is led by a Pemangku beside the Ayung River. The experience includes a traditional Balinese sarong, towels and a welcome drink, with advance reservation recommended.</p>',
            'button_label' => 'MORE DETAILS',
            'button_link_type' => 'manual',
            'button_url' => '/holy-river/balinese-blessing-purification-at-the-holy-river',
            'button_route' => null,
            'sort_order' => 4,
        ]);

        $this->updateSection($pageId, 12, [
            'title' => 'SPA ON THE RIVER',
            'subtitle' => 'RIVERSIDE WELLNESS',
            'excerpt' => null,
            'description' => '<p>Nandini\'s 180-minute Spa on the River experience begins with a soothing foot bath before a signature treatment beside the Ayung River. Explore the dedicated Essence Spa site for treatments and spa enquiries.</p>',
            'button_label' => 'EXPLORE ESSENCE SPA',
            'button_link_type' => 'manual',
            'button_url' => 'https://spa.nandinibali.com/',
            'button_route' => null,
            'sort_order' => 5,
        ]);

        $this->updateImageAlt($pageId, 8, 'Ayung River flowing through the tropical jungle at Nandini Jungle');
        $this->updateImageAlt($pageId, 9, 'Sacred river setting surrounded by tropical jungle');
        $this->updateImageAlt($pageId, 10, 'Balinese blessing experience in Nandini Jungle\'s riverside setting');
        $this->updateImageAlt($pageId, 12, 'Spa on the River setting beside the Ayung River at Nandini Jungle');

        $this->updateExperience('sacred-waters-half-day-ubud-healing-retreat', [
            'title' => 'Sacred Waters: Half-Day Ubud Healing Retreat',
            'excerpt' => 'A half-day riverside experience combining Nandini\'s signature Spa on the River treatment with Melukat purification and a Balinese blessing led by a traditional priest.',
            'description' => '<p>A half-day experience beside the Ayung River combining Nandini\'s signature Spa on the River treatment with Melukat purification and a Balinese blessing led by a traditional priest.</p><p><strong>Inclusions:</strong><br>Traditional Balinese sarong and towels<br>Melukat purification and blessing ceremony by the river<br>Signature Spa on the River treatment for two<br>60-minute Exotic Balinese massage<br>30-minute body mask<br>30-minute body scrub treatment<br>Tea for two at the spa reception</p>',
            'image_alt' => 'Sacred Waters purification and blessing experience beside the Ayung River',
            'card_image_alt' => 'Sacred Waters Holy River experience at Nandini Jungle',
            'meta_title' => 'Sacred Waters & Melukat Experience in Ubud | Nandini Jungle',
            'meta_description' => 'Discover a half-day Holy River experience at Nandini Jungle combining Melukat purification, a Balinese blessing and a Spa on the River treatment.',
        ]);

        $this->updateExperience('balinese-blessing-purification-at-the-holy-river', [
            'title' => 'Balinese Blessing Purification at the Holy River',
            'excerpt' => 'A Balinese purification and blessing experience beside the Ayung River, led by a Pemangku and accompanied by traditional sarong, towels and a welcome drink.',
            'description' => '<p>Experience a Balinese purification and blessing ritual beside the Ayung River, led by a Pemangku in Nandini\'s peaceful jungle setting.</p><p><strong>Inclusions:</strong><br>Healthy welcome drink on arrival<br>Traditional Balinese sarong and towels<br>Balinese purification and blessing ritual led by a Pemangku</p>',
            'image_alt' => 'Balinese blessing and purification ritual beside the Ayung River',
            'card_image_alt' => 'Balinese Blessing Purification at the Holy River',
            'meta_title' => 'Balinese Blessing & Purification in Ubud | Nandini Jungle',
            'meta_description' => 'Experience a Balinese purification and blessing ritual led by a Pemangku beside the Ayung River at Nandini Jungle in Ubud.',
        ]);

        $this->updateExperience('nandini-signature-spa-on-the-river', [
            'title' => 'Nandini Signature: Spa on the River',
            'excerpt' => 'A 180-minute Spa on the River experience beside the Ayung River, beginning with a soothing foot bath and continuing with Nandini\'s signature riverside treatment.',
            'description' => '<p>A 180-minute Spa on the River experience beside the Ayung River, beginning with a soothing foot bath and continuing with Nandini\'s signature riverside treatment.</p><p><strong>Inclusions:</strong><br>Soothing foot bath<br>60-minute Exotic Balinese massage<br>30-minute body mask<br>30-minute body scrub treatment<br>60-minute facial</p>',
            'image_alt' => 'Spa on the River treatment beside the Ayung River at Nandini Jungle',
            'card_image_alt' => 'Nandini Signature Spa on the River experience',
            'meta_title' => 'Nandini Signature Spa on the River | Nandini Jungle',
            'meta_description' => 'Discover Nandini\'s 180-minute Spa on the River experience in a peaceful riverside setting beside the Ayung River.',
        ]);
    }

    public function down(): void
    {
        // This content migration intentionally avoids restoring older promotional
        // and unsupported cultural claims during rollback.
    }

    private function holyRiverPageId(): ?int
    {
        $id = DB::table('pages')
            ->where(function ($query): void {
                $query
                    ->where('page_name', 'Holy River Page')
                    ->orWhere('slug', 'holy-river');
            })
            ->orderByRaw("CASE WHEN page_name = 'Holy River Page' THEN 0 ELSE 1 END")
            ->value('id');

        return $id ? (int) $id : null;
    }

    private function updateSection(int $pageId, int $sectionId, array $values): void
    {
        DB::table('page_sections')
            ->where('id', $sectionId)
            ->where('page_id', $pageId)
            ->update(array_merge($values, ['updated_at' => now()]));
    }

    private function updateImageAlt(int $pageId, int $sectionId, string $alt): void
    {
        $belongsToPage = DB::table('page_sections')
            ->where('id', $sectionId)
            ->where('page_id', $pageId)
            ->exists();

        if (! $belongsToPage) {
            return;
        }

        DB::table('page_section_images')
            ->where('page_section_id', $sectionId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->limit(1)
            ->update([
                'image_alt' => $alt,
                'mobile_image_alt' => $alt,
                'updated_at' => now(),
            ]);
    }

    private function updateExperience(string $slug, array $values): void
    {
        DB::table('experiences')
            ->where('slug', $slug)
            ->update(array_merge($values, ['updated_at' => now()]));
    }
};
