-- Jungle Spa Ubud SEO page deployment
-- Upload the matching application files before running this script.
-- This script is rerunnable and rebuilds only the sections owned by this page.

SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;
START TRANSACTION;

SET @page_slug := 'jungle-spa-ubud';
SET @page_sort_order := (SELECT COALESCE(MAX(`sort_order`), 0) + 1 FROM `pages`);

INSERT INTO `pages` (
    `site`, `page_name`, `title`, `slug`, `subtitle`, `excerpt`, `description`,
    `hero_image`, `hero_image_alt`, `hero_mobile_image`, `hero_mobile_image_alt`,
    `meta_title`, `meta_description`, `is_active`, `sort_order`, `created_at`, `updated_at`
) VALUES (
    'main',
    'SEO - Jungle Spa Ubud Page',
    'Jungle Spa in Ubud, Bali',
    @page_slug,
    NULL,
    'Escape into the quiet beauty of Bali’s tropical landscape and experience wellness surrounded by nature at Nandini Jungle by Hanging Gardens.',
    '<p>Escape into the quiet beauty of Bali’s tropical landscape and experience wellness surrounded by nature at Nandini Jungle by Hanging Gardens.</p><p>Set within a lush jungle environment near Ubud, our spa experiences invite you to slow down, reconnect and enjoy a deeper sense of relaxation. From traditional Balinese massage and restorative body treatments to immersive riverside rituals, each experience is designed to bring together nature, wellness and the timeless traditions of Bali.</p><p>For travelers searching for a <strong>jungle spa in Ubud</strong>, Nandini offers more than a place for a treatment. It is an opportunity to step away from the pace of everyday life and experience wellness within the natural rhythm of the jungle.</p>',
    'pages/hero/ubud-wellness-retreat-jungle-spa-terrace.webp',
    'Jungle spa experience surrounded by tropical greenery at Nandini Jungle in Ubud, Bali',
    'pages/hero-mobile/ubud-wellness-retreat-jungle-spa-terrace.webp',
    'Jungle spa experience surrounded by tropical greenery at Nandini Jungle in Ubud, Bali',
    'Jungle Spa Ubud | Spa in the Bali Jungle | Nandini',
    'Escape to a jungle spa in Ubud, Bali. Discover Balinese treatments, riverside spa rituals and relaxing wellness experiences at Nandini Jungle.',
    1,
    @page_sort_order,
    NOW(),
    NOW()
) ON DUPLICATE KEY UPDATE
    `site` = VALUES(`site`),
    `page_name` = VALUES(`page_name`),
    `title` = VALUES(`title`),
    `subtitle` = VALUES(`subtitle`),
    `excerpt` = VALUES(`excerpt`),
    `description` = VALUES(`description`),
    `hero_image` = VALUES(`hero_image`),
    `hero_image_alt` = VALUES(`hero_image_alt`),
    `hero_mobile_image` = VALUES(`hero_mobile_image`),
    `hero_mobile_image_alt` = VALUES(`hero_mobile_image_alt`),
    `meta_title` = VALUES(`meta_title`),
    `meta_description` = VALUES(`meta_description`),
    `is_active` = VALUES(`is_active`),
    `updated_at` = NOW();

SET @page_id := (SELECT `id` FROM `pages` WHERE `slug` = @page_slug LIMIT 1);

DELETE `page_section_images`
FROM `page_section_images`
INNER JOIN `page_sections`
    ON `page_sections`.`id` = `page_section_images`.`page_section_id`
WHERE `page_sections`.`page_id` = @page_id;

DELETE FROM `page_sections` WHERE `page_id` = @page_id;

INSERT INTO `page_sections` (
    `page_id`, `section_key`, `title`, `subtitle`, `excerpt`, `description`, `items`,
    `image`, `mobile_image`, `button_label`, `button_link_type`, `button_url`, `button_route`,
    `text_align`, `background_color`, `is_active`, `sort_order`, `created_at`, `updated_at`
) VALUES
(
    @page_id, 'seo_split_media_section', 'A Spa Experience Shaped by the Jungle', NULL, NULL,
    '<p>Nature is an essential part of the spa experience at Nandini Jungle.</p><p>Surrounded by tropical greenery, fresh jungle air and the natural sounds of the landscape, treatments unfold in an atmosphere created for rest. Rather than separating wellness from the destination, our approach allows the environment of Ubud to become part of the experience itself.</p><p>Traditional techniques, aromatic oils, local ingredients and carefully designed wellness rituals come together in a setting where guests can simply slow down.</p><p>Whether you are looking for a quiet massage after exploring Ubud, a spa experience for two or a longer wellness ritual, the jungle provides a naturally calming backdrop for your time at Nandini.</p>',
    NULL, NULL, NULL, 'Explore Spa & Wellness', 'manual', '/spa-wellness', NULL,
    'left', 'soft_gray', 1, 1, NOW(), NOW()
),
(
    @page_id, 'intro_text_section', 'Discover Our Jungle Spa Experiences', NULL, NULL, NULL,
    NULL, NULL, NULL, NULL, 'manual', NULL, NULL,
    'left', 'white', 1, 2, NOW(), NOW()
),
(
    @page_id, 'seo_split_media_reverse', 'Essence Spa', NULL, NULL,
    '<p>Nestled within the tropical landscape of Nandini Jungle, Essence Spa brings Balinese wellness traditions together with a peaceful natural setting.</p><p>Treatments are designed to encourage relaxation and renewal, incorporating traditional techniques, aromatic oils and carefully selected ingredients.</p><p>It is an ideal choice for guests who want to make spa and wellness part of their Ubud stay while remaining connected to the surrounding jungle.</p>',
    NULL, NULL, NULL, 'Discover Essence Spa', 'manual', '/spa-wellness', NULL,
    'left', 'white', 1, 3, NOW(), NOW()
),
(
    @page_id, 'seo_split_media_section', 'Signature Spa on the River', NULL, NULL,
    '<p>For an experience even closer to nature, descend into the jungle for one of Nandini’s signature wellness journeys beside the river.</p><p>The <strong>Signature Spa on the River</strong> begins with a soothing foot bath before continuing into a 180-minute treatment combining an Exotic Balinese Massage, body mask, body scrub and relaxing facial treatment.</p><p>The sound of flowing water and the surrounding jungle create an atmosphere that feels far removed from a conventional indoor spa.</p><p>It is one of Nandini’s most distinctive wellness experiences and a beautiful choice for guests seeking a memorable <strong>jungle spa experience in Ubud</strong>.</p>',
    NULL, NULL, NULL, 'Discover Signature Spa on the River', 'manual', '/holy-river/nandini-signature-spa-on-the-river', NULL,
    'left', 'soft_gray', 1, 4, NOW(), NOW()
),
(
    @page_id, 'seo_split_media_reverse', 'Wine Spa', NULL, NULL,
    '<p>Discover a different approach to relaxation with Nandini’s Wine Spa.</p><p>This 2.5-hour wellness ritual combines Balinese massage techniques with wine-inspired treatments designed to nourish the skin and encourage deep relaxation.</p><p>Rich in antioxidants and created as a complete wellness journey, the experience brings together indulgence, nature and restorative care within Nandini’s tranquil jungle surroundings.</p>',
    NULL, NULL, NULL, NULL, 'manual', NULL, NULL,
    'left', 'white', 1, 5, NOW(), NOW()
),
(
    @page_id, 'seo_split_media_section', 'Spa Jacuzzi', NULL, NULL,
    '<p>Take time to unwind in our Spa Jacuzzi, surrounded by tropical greenery and the peaceful sounds of nature.</p><p>It provides a gentle transition between activity and rest, giving you time to slow down and enjoy the calm atmosphere of the resort.</p>',
    NULL, NULL, NULL, NULL, 'manual', NULL, NULL,
    'left', 'soft_gray', 1, 6, NOW(), NOW()
),
(
    @page_id, 'intro_text_section', 'From Jungle Canopy to Riverside Calm', NULL, NULL,
    '<p>One of the defining characteristics of a spa experience at Nandini is the connection between different layers of the landscape.</p><p>Your journey may begin among the greenery of the resort before leading deeper into the valley toward our riverside wellness setting.</p><p>Along the way, the surrounding jungle gradually becomes quieter.</p><p>The experience is intentionally unhurried.</p><p>Instead of treating wellness as simply another activity on your itinerary, Nandini gives you the opportunity to dedicate part of your day to stillness, nature and restoration.</p>',
    NULL, NULL, NULL, NULL, 'manual', NULL, NULL,
    'left', 'white', 1, 7, NOW(), NOW()
),
(
    @page_id, 'seo_split_media_reverse', 'A Jungle Spa Experience for Couples', NULL, NULL,
    '<p>Ubud is a natural destination for couples who want to combine romance, nature and wellness.</p><p>A shared spa experience creates space to slow down together after days spent discovering temples, villages, restaurants and the landscapes of Bali.</p><p>Couples can choose from relaxing spa treatments or make the experience more memorable with one of Nandini’s signature riverside rituals.</p><p>A spa experience can also be combined with other moments at Nandini, creating an unhurried day centred around wellness, dining and time together in the jungle.</p><p>Whether you are visiting Ubud for a honeymoon, anniversary or simply a quiet escape for two, the jungle setting offers a different atmosphere from a traditional city spa.</p>',
    NULL, NULL, NULL, NULL, 'manual', NULL, NULL,
    'left', 'soft_gray', 1, 8, NOW(), NOW()
),
(
    @page_id, 'intro_text_section', 'Make Wellness Part of Your Ubud Escape', NULL, NULL,
    '<p>A spa treatment does not need to fill an entire itinerary.</p><p>Sometimes the most memorable part of a trip is simply having several hours without somewhere else to be.</p><p>Begin your day surrounded by the jungle, enjoy a restorative treatment and allow yourself time to remain within the slower rhythm of Nandini.</p><p>Guests looking for a more complete wellness experience can also discover yoga, riverside wellness rituals, Balinese purification experiences and other restorative activities available throughout the resort.</p><p><a href="/spa-wellness"><strong>Explore All Spa &amp; Wellness Experiences</strong></a></p>',
    NULL, NULL, NULL, NULL, 'manual', NULL, NULL,
    'left', 'white', 1, 9, NOW(), NOW()
),
(
    @page_id, 'intro_text_section', 'Why Experience a Jungle Spa at Nandini?', NULL, NULL,
    '<p>At Nandini Jungle by Hanging Gardens, wellness is closely connected to the environment.</p><p>Here, a spa experience can include:</p><ul><li>Traditional Balinese massage and wellness techniques</li><li>Jungle surroundings away from the bustle of central Ubud</li><li>Signature riverside spa experiences</li><li>Wellness rituals for individuals and couples</li><li>Wine-inspired spa treatments</li><li>Spa Jacuzzi surrounded by tropical greenery</li><li>Opportunities to combine spa treatments with yoga and other wellness experiences</li></ul><p>The result is a spa journey that feels distinctly connected to Bali and the natural landscape surrounding Nandini.</p>',
    NULL, NULL, NULL, NULL, 'manual', NULL, NULL,
    'left', 'soft_gray', 1, 10, NOW(), NOW()
),
(
    @page_id, 'intro_text_section', 'Frequently Asked Questions', NULL, NULL,
    '<h3>What makes Nandini a jungle spa in Ubud?</h3><p>Nandini Jungle by Hanging Gardens is surrounded by tropical vegetation in the Payangan area near Ubud. Our spa experiences are designed around this natural setting, with treatments available within the jungle resort as well as signature wellness experiences beside the river.</p><h3>What spa treatments are available?</h3><p>Guests can discover traditional Balinese massage, body treatments, facial treatments, the Wine Spa, Spa Jacuzzi and signature riverside wellness experiences. Treatment availability may vary, so we recommend checking the current spa menu when making a reservation.</p><h3>What is the Signature Spa on the River?</h3><p>Signature Spa on the River is a 180-minute Nandini wellness experience beside the river. The journey includes a foot bath, Exotic Balinese Massage, body mask, body scrub and relaxing facial treatment.</p><h3>Is the jungle spa suitable for couples?</h3><p>Yes. Couples can enjoy spa and wellness experiences together, including selected signature treatments and riverside experiences designed for shared relaxation.</p><h3>Can I combine a spa treatment with other wellness activities?</h3><p>Yes. Nandini offers a range of wellness experiences that can complement your spa journey, including yoga and selected riverside wellness rituals.</p><h3>Where is Nandini Jungle located?</h3><p>Nandini Jungle by Hanging Gardens is located in Banjar Susut, Desa Buahan, Payangan, Bali, within the greater Ubud area.</p>',
    NULL, NULL, NULL, NULL, 'manual', NULL, NULL,
    'left', 'white', 1, 11, NOW(), NOW()
),
(
    @page_id, 'seo_split_media_section', 'Find Your Moment of Calm in the Bali Jungle', NULL, NULL,
    '<p>Leave the busy pace of your itinerary behind and discover a quieter side of Ubud.</p><p>Surrounded by tropical greenery, restorative treatments and the sounds of nature, a spa experience at Nandini Jungle creates space to slow down and reconnect with yourself or someone special.</p><p>From a relaxing Balinese treatment at Essence Spa to an immersive Signature Spa on the River experience, choose the journey that feels right for you.</p><p><a href="/spa-wellness"><strong>Explore Spa &amp; Wellness</strong></a></p>',
    NULL, NULL, NULL, 'Book Your Jungle Spa Experience', 'manual', 'https://wa.me/6281236871170?text=Hello%2C%20I%20would%20like%20to%20book%20a%20jungle%20spa%20experience%20at%20Nandini%20Jungle.', NULL,
    'left', 'soft_gray', 1, 12, NOW(), NOW()
);

INSERT INTO `page_section_images` (
    `page_section_id`, `image`, `image_file_name`, `image_alt`,
    `mobile_image`, `mobile_image_file_name`, `mobile_image_alt`,
    `caption`, `is_active`, `sort_order`, `created_at`, `updated_at`
)
SELECT `id`,
    'pages/sections/ubud-jungle-spa-treatment-nandini-wellness-retreat.webp',
    'ubud-jungle-spa-treatment-nandini-wellness-retreat',
    'Jungle spa treatment at Nandini Jungle by Hanging Gardens in Ubud, Bali',
    'pages/sections/mobile/ubud-jungle-spa-treatment-nandini-wellness-retreat.webp',
    'ubud-jungle-spa-treatment-nandini-wellness-retreat',
    'Jungle spa treatment at Nandini Jungle by Hanging Gardens in Ubud, Bali',
    NULL, 1, 1, NOW(), NOW()
FROM `page_sections`
WHERE `page_id` = @page_id AND `sort_order` = 1
UNION ALL
SELECT `id`,
    'pages/sections/4791e636-30b1-4fed-acc0-55bcba176534.webp',
    '4791e636-30b1-4fed-acc0-55bcba176534',
    'Essence Spa at Nandini Jungle by Hanging Gardens',
    'pages/sections/mobile/2e1a7742-cd2c-439d-afb5-2c4aea9bcb84.webp',
    '2e1a7742-cd2c-439d-afb5-2c4aea9bcb84',
    'Essence Spa at Nandini Jungle by Hanging Gardens',
    NULL, 1, 1, NOW(), NOW()
FROM `page_sections`
WHERE `page_id` = @page_id AND `sort_order` = 3
UNION ALL
SELECT `id`,
    'pages/sections/ea97fbfe-7ca1-43fe-8e30-51b1e9dcea01.webp',
    'ea97fbfe-7ca1-43fe-8e30-51b1e9dcea01',
    'Signature spa treatment beside the river at Nandini Jungle',
    'pages/sections/mobile/16bb6272-730e-4082-a128-163e7b2b8705.webp',
    '16bb6272-730e-4082-a128-163e7b2b8705',
    'Signature spa treatment beside the river at Nandini Jungle',
    NULL, 1, 1, NOW(), NOW()
FROM `page_sections`
WHERE `page_id` = @page_id AND `sort_order` = 4
UNION ALL
SELECT `id`,
    'pages/sections/130d593f-e6dd-4741-8d41-15ab6c2af83b.webp',
    '130d593f-e6dd-4741-8d41-15ab6c2af83b',
    'Wine Spa wellness experience at Nandini Jungle',
    'pages/sections/mobile/50fa006f-1f8b-4f57-ae30-d2e6012293a7.webp',
    '50fa006f-1f8b-4f57-ae30-d2e6012293a7',
    'Wine Spa wellness experience at Nandini Jungle',
    NULL, 1, 1, NOW(), NOW()
FROM `page_sections`
WHERE `page_id` = @page_id AND `sort_order` = 5
UNION ALL
SELECT `id`,
    'pages/sections/7bdab6e8-62b3-416a-85fb-3419a6a15ee8.webp',
    '7bdab6e8-62b3-416a-85fb-3419a6a15ee8',
    'Spa Jacuzzi surrounded by tropical greenery at Nandini Jungle',
    'pages/sections/mobile/49aec01b-1c71-4236-8916-c99ccd4dea28.webp',
    '49aec01b-1c71-4236-8916-c99ccd4dea28',
    'Spa Jacuzzi surrounded by tropical greenery at Nandini Jungle',
    NULL, 1, 1, NOW(), NOW()
FROM `page_sections`
WHERE `page_id` = @page_id AND `sort_order` = 6
UNION ALL
SELECT `id`,
    'pages/sections/balinese-spiritual-wellness-ritual-ubud-jungle-resort.webp',
    'balinese-spiritual-wellness-ritual-ubud-jungle-resort',
    'Couple enjoying a Balinese wellness ritual in the Ubud jungle',
    'pages/sections/mobile/balinese-spiritual-wellness-ritual-ubud-jungle-resort.webp',
    'balinese-spiritual-wellness-ritual-ubud-jungle-resort',
    'Couple enjoying a Balinese wellness ritual in the Ubud jungle',
    NULL, 1, 1, NOW(), NOW()
FROM `page_sections`
WHERE `page_id` = @page_id AND `sort_order` = 8;

-- Prevent Laravel from rerunning the equivalent migration after manual deployment.
SET @migration_name := '2026_09_30_000001_create_jungle_spa_ubud_seo_page';
SET @migration_batch := (SELECT COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT @migration_name, @migration_batch
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations` WHERE `migration` = @migration_name
);

COMMIT;

-- Verification
SELECT `id`, `site`, `page_name`, `title`, `slug`, `meta_title`, `is_active`, `updated_at`
FROM `pages`
WHERE `slug` = @page_slug;

SELECT `id`, `section_key`, `title`, `sort_order`, `is_active`
FROM `page_sections`
WHERE `page_id` = @page_id
ORDER BY `sort_order`;

SELECT COUNT(*) AS `image_count`
FROM `page_section_images`
WHERE `page_section_id` IN (
    SELECT `id` FROM `page_sections` WHERE `page_id` = @page_id
);
