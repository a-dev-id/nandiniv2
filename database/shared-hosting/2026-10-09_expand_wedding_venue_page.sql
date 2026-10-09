-- Nandini Jungle by Hanging Gardens
-- Expand the existing /weddings page and store all new sections in the CMS.
-- The script is idempotent and is intended for phpMyAdmin/shared hosting.
-- Take a database backup before importing.

SET NAMES utf8mb4;
START TRANSACTION;

SET @wedding_page_id := (
    SELECT `id`
    FROM `pages`
    WHERE `id` = 8 OR `page_name` = 'Wedding Page' OR `slug` = 'wedding'
    ORDER BY CASE WHEN `id` = 8 THEN 0 WHEN `page_name` = 'Wedding Page' THEN 1 ELSE 2 END, `id`
    LIMIT 1
);

SET @next_page_section_id := COALESCE((SELECT MAX(`id`) FROM `page_sections`), 0);
SET @next_page_section_image_id := COALESCE((SELECT MAX(`id`) FROM `page_section_images`), 0);
SET @wedding_hero_image := (SELECT `hero_image` FROM `pages` WHERE `id` = @wedding_page_id);
SET @wedding_hero_mobile_image := (SELECT `hero_mobile_image` FROM `pages` WHERE `id` = @wedding_page_id);

UPDATE `pages`
SET `title` = 'Jungle Wedding Venue in Ubud, Bali',
    `subtitle` = 'Celebrate Your Story Surrounded by Bali''s Jungle',
    `description` = '<p>Set within the tropical landscape of Payangan, in the greater Ubud area, Nandini Jungle by Hanging Gardens offers an intimate destination wedding setting surrounded by rainforest and the Ayung River valley. Couples can choose between a jungle chapel ceremony and a riverside celebration, with accommodation, dining and personalised wedding arrangements available within the resort.</p><p>Here, each celebration unfolds in the quiet rhythm of the jungle. Exchange vows framed by tropical greenery, gather with the people closest to you and continue your story with a stay surrounded by nature.</p>',
    `meta_title` = 'Jungle Wedding Venue in Ubud, Bali | Nandini Jungle',
    `meta_description` = 'Celebrate your wedding at Nandini Jungle, a jungle wedding venue in Ubud, Bali with a private chapel, Ayung River ceremony setting, dining and accommodation.',
    `hero_image_alt` = 'Destination wedding in the tropical jungle at Nandini Bali',
    `hero_mobile_image_alt` = 'Destination wedding in the tropical jungle at Nandini Bali',
    `updated_at` = NOW()
WHERE `id` = @wedding_page_id;

SET @chapel_section_id := (
    SELECT `id`
    FROM `page_sections`
    WHERE `page_id` = @wedding_page_id
      AND (`id` = 60 OR `section_key` = 'wedding_chapel' OR `title` IN ('Wedding by the Chapel', 'WEDDING BY THE CHAPEL'))
    ORDER BY CASE WHEN `section_key` = 'wedding_chapel' THEN 0 WHEN `id` = 60 THEN 1 ELSE 2 END, `id`
    LIMIT 1
);

UPDATE `page_sections`
SET `section_key` = 'wedding_chapel',
    `subtitle` = 'JUNGLE WEDDING CHAPEL',
    `title` = 'WEDDING BY THE CHAPEL',
    `description` = '<p>Set within Nandini''s tropical landscape, the jungle chapel offers an intimate ceremony environment framed by natural surroundings and forest views. Fresh blooms and the chapel''s quiet setting create an elegant place to exchange vows.</p><p>Couples can speak with the Nandini team about the details of their celebration and how the chapel setting can reflect their preferred ceremony style.</p>',
    `button_label` = NULL,
    `button_url` = NULL,
    `button_route` = NULL,
    `background_color` = 'white',
    `sort_order` = 2,
    `updated_at` = NOW()
WHERE `id` = @chapel_section_id;

UPDATE `page_section_images`
SET `image_alt` = 'Jungle wedding chapel at Nandini Jungle in Ubud',
    `mobile_image_alt` = 'Jungle wedding chapel at Nandini Jungle in Ubud',
    `updated_at` = NOW()
WHERE `id` = (
    SELECT `image_id`
    FROM (
        SELECT `id` AS `image_id`
        FROM `page_section_images`
        WHERE `page_section_id` = @chapel_section_id
        ORDER BY `is_active` DESC, `sort_order`, `id`
        LIMIT 1
    ) AS `chapel_image`
);

SET @river_section_id := (
    SELECT `id`
    FROM `page_sections`
    WHERE `page_id` = @wedding_page_id
      AND (`id` = 59 OR `section_key` = 'wedding_river' OR `title` IN ('Wedding By the River', 'WEDDING BY THE RIVER'))
    ORDER BY CASE WHEN `section_key` = 'wedding_river' THEN 0 WHEN `id` = 59 THEN 1 ELSE 2 END, `id`
    LIMIT 1
);

UPDATE `page_sections`
SET `section_key` = 'wedding_river',
    `subtitle` = 'AYUNG RIVER WEDDING',
    `title` = 'WEDDING BY THE RIVER',
    `description` = '<p>Beside the Ayung River in Payangan, within the greater Ubud area, Nandini offers a wedding setting surrounded by rainforest, green hills and the gentle movement of the river. Couples can exchange vows beneath the trees in an atmosphere shaped by nature.</p><p>After the ceremony, Nandini''s culinary team can create a custom menu for the celebration, bringing the riverside occasion together through food, place and time shared with family and friends.</p>',
    `button_label` = NULL,
    `button_url` = NULL,
    `button_route` = NULL,
    `background_color` = 'soft_gray',
    `sort_order` = 3,
    `updated_at` = NOW()
WHERE `id` = @river_section_id;

UPDATE `page_section_images`
SET `image_alt` = 'Wedding ceremony setting beside the Ayung River at Nandini Jungle',
    `mobile_image_alt` = 'Wedding ceremony setting beside the Ayung River at Nandini Jungle',
    `updated_at` = NOW()
WHERE `id` = (
    SELECT `image_id`
    FROM (
        SELECT `id` AS `image_id`
        FROM `page_section_images`
        WHERE `page_section_id` = @river_section_id
        ORDER BY `is_active` DESC, `sort_order`, `id`
        LIMIT 1
    ) AS `river_image`
);

-- New wedding sections. Each INSERT only runs if the CMS section does not exist.
INSERT INTO `page_sections`
    (`id`, `page_id`, `section_key`, `title`, `subtitle`, `description`, `items`, `button_link_type`, `text_align`, `background_color`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    (@next_page_section_id := @next_page_section_id + 1), @wedding_page_id,
    'wedding_ceremony_options', 'YOUR CEREMONY, YOUR STORY', 'CEREMONY EXPERIENCES',
    '<p>Choose a ceremony direction that feels true to your story, then speak with the Nandini team about the setting and arrangements for your day.</p>',
    '[{"title":"BALINESE-INSPIRED CEREMONY","description":"A ceremony direction inspired by Balinese tradition and the natural atmosphere of Nandini Jungle."},{"title":"CLASSIC WESTERN CEREMONY","description":"A classic ceremony style for couples who want to exchange vows in Nandini''s chapel or riverside setting."},{"title":"SIGNATURE ENCHANTING WEDDING","description":"Nandini''s signature wedding concept, created for a celebration surrounded by the beauty of the jungle."}]',
    'manual', 'center', 'white', 1, 4, NOW(), NOW()
WHERE @wedding_page_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM `page_sections` WHERE `page_id` = @wedding_page_id AND `section_key` = 'wedding_ceremony_options');

INSERT INTO `page_sections`
    (`id`, `page_id`, `section_key`, `title`, `subtitle`, `description`, `items`, `button_label`, `button_link_type`, `button_url`, `text_align`, `background_color`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    (@next_page_section_id := @next_page_section_id + 1), @wedding_page_id,
    'wedding_dining', 'WEDDING DINING & CELEBRATIONS', 'DINING & CELEBRATION',
    '<p>Bring your celebration together around a menu created for the occasion. Nandini''s culinary team can design a custom wedding menu, while the resort''s romantic dining experiences offer further inspiration for time together before or after the wedding day.</p>',
    NULL, 'EXPLORE DINING', 'manual', 'https://dining.nandinibali.com/', 'center', 'soft_gray', 1, 5, NOW(), NOW()
WHERE @wedding_page_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM `page_sections` WHERE `page_id` = @wedding_page_id AND `section_key` = 'wedding_dining');

INSERT INTO `page_sections`
    (`id`, `page_id`, `section_key`, `title`, `subtitle`, `description`, `items`, `button_link_type`, `text_align`, `background_color`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    (@next_page_section_id := @next_page_section_id + 1), @wedding_page_id,
    'wedding_accommodation', 'STAY TOGETHER IN THE JUNGLE', 'DESTINATION WEDDING STAY',
    '<p>Extend your wedding celebration into a destination stay at Nandini Jungle. The couple and their guests can explore private jungle villas and Royal Suites, with space to slow down and enjoy the rainforest setting before and after the ceremony.</p><p>For a romantic continuation after the celebration, discover a <a href="https://nandinibali.com/honeymoon">honeymoon at Nandini Jungle</a>.</p>',
    '[{"title":"JUNGLE VILLAS","description":"Private villas surrounded by Nandini''s tropical landscape.","url":"https://nandinibali.com/jungle-villas","link_label":"EXPLORE JUNGLE VILLAS"},{"title":"ROYAL SUITES","description":"Spacious suites for an elevated stay in the Ubud jungle.","url":"https://nandinibali.com/the-royal-suites","link_label":"EXPLORE ROYAL SUITES"}]',
    'manual', 'center', 'white', 1, 6, NOW(), NOW()
WHERE @wedding_page_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM `page_sections` WHERE `page_id` = @wedding_page_id AND `section_key` = 'wedding_accommodation');

INSERT INTO `page_sections`
    (`id`, `page_id`, `section_key`, `title`, `subtitle`, `description`, `items`, `button_link_type`, `text_align`, `background_color`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    (@next_page_section_id := @next_page_section_id + 1), @wedding_page_id,
    'wedding_planning', 'PLANNING YOUR WEDDING AT NANDINI', 'WEDDING PLANNING',
    '<p>Begin with the setting and ceremony style that feel right for you. The Nandini team can then help you explore the wedding arrangements available within the resort.</p>',
    '[{"title":"VENUE","description":"Choose between the intimate jungle chapel and a celebration beside the Ayung River."},{"title":"CEREMONY","description":"Explore a Balinese-inspired ceremony, a classic Western ceremony or Nandini''s Signature Enchanting Wedding."},{"title":"DINING","description":"Discuss a custom wedding menu created by Nandini''s culinary team for your celebration."},{"title":"STAY","description":"Explore jungle villas and Royal Suites for the couple and guests joining the destination celebration."}]',
    'manual', 'center', 'soft_gray', 1, 7, NOW(), NOW()
WHERE @wedding_page_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM `page_sections` WHERE `page_id` = @wedding_page_id AND `section_key` = 'wedding_planning');

INSERT INTO `page_sections`
    (`id`, `page_id`, `section_key`, `title`, `subtitle`, `description`, `items`, `button_link_type`, `text_align`, `background_color`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    (@next_page_section_id := @next_page_section_id + 1), @wedding_page_id,
    'wedding_final_cta', 'BEGIN YOUR WEDDING JOURNEY', 'YOUR CELEBRATION BEGINS HERE',
    '<p>Tell us how you imagine your celebration, and our team will help you explore the most suitable venue, ceremony style and wedding arrangements at Nandini Jungle.</p>',
    '[{"label":"PLAN YOUR WEDDING","url":"#wedding-inquiry","style":"solid"},{"label":"WHATSAPP OUR TEAM","url":"https://wa.me/6281236871170?text=Hello%2C%20I%20would%20like%20to%20enquire%20about%20planning%20a%20wedding%20at%20Nandini%20Jungle%20by%20Hanging%20Gardens%20in%20Ubud%2C%20Bali.","style":"white-outline"}]',
    'manual', 'center', 'dark', 1, 8, NOW(), NOW()
WHERE @wedding_page_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM `page_sections` WHERE `page_id` = @wedding_page_id AND `section_key` = 'wedding_final_cta');

-- Keep existing CMS records synchronized if the script is imported again.
UPDATE `page_sections` SET `sort_order` = 4, `updated_at` = NOW() WHERE `page_id` = @wedding_page_id AND `section_key` = 'wedding_ceremony_options';
UPDATE `page_sections` SET `sort_order` = 5, `updated_at` = NOW() WHERE `page_id` = @wedding_page_id AND `section_key` = 'wedding_dining';
UPDATE `page_sections` SET `sort_order` = 6, `updated_at` = NOW() WHERE `page_id` = @wedding_page_id AND `section_key` = 'wedding_accommodation';
UPDATE `page_sections` SET `sort_order` = 7, `updated_at` = NOW() WHERE `page_id` = @wedding_page_id AND `section_key` = 'wedding_planning';
UPDATE `page_sections` SET `sort_order` = 8, `updated_at` = NOW() WHERE `page_id` = @wedding_page_id AND `section_key` = 'wedding_final_cta';

SET @wedding_dining_section_id := (SELECT `id` FROM `page_sections` WHERE `page_id` = @wedding_page_id AND `section_key` = 'wedding_dining' ORDER BY `id` LIMIT 1);
SET @wedding_final_cta_section_id := (SELECT `id` FROM `page_sections` WHERE `page_id` = @wedding_page_id AND `section_key` = 'wedding_final_cta' ORDER BY `id` LIMIT 1);
SET @wedding_dining_image_id := (SELECT `id` FROM `page_section_images` WHERE `page_section_id` = @wedding_dining_section_id ORDER BY `is_active` DESC, `sort_order`, `id` LIMIT 1);
SET @wedding_final_cta_image_id := (SELECT `id` FROM `page_section_images` WHERE `page_section_id` = @wedding_final_cta_section_id ORDER BY `is_active` DESC, `sort_order`, `id` LIMIT 1);

UPDATE `page_section_images`
SET `image` = '/images/dining/romantic-dining-by-the-chapel.webp',
    `image_alt` = 'Romantic candlelit dining by the chapel at Nandini Jungle',
    `mobile_image_alt` = 'Romantic candlelit dining by the chapel at Nandini Jungle',
    `is_active` = 1, `sort_order` = 1, `updated_at` = NOW()
WHERE `id` = @wedding_dining_image_id;

INSERT INTO `page_section_images`
    (`id`, `page_section_id`, `image`, `image_alt`, `mobile_image`, `mobile_image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT (@next_page_section_image_id := @next_page_section_image_id + 1), @wedding_dining_section_id,
    '/images/dining/romantic-dining-by-the-chapel.webp', 'Romantic candlelit dining by the chapel at Nandini Jungle',
    NULL, 'Romantic candlelit dining by the chapel at Nandini Jungle', 1, 1, NOW(), NOW()
WHERE @wedding_dining_section_id IS NOT NULL AND @wedding_dining_image_id IS NULL;

UPDATE `page_section_images`
SET `image` = @wedding_hero_image,
    `image_alt` = 'Destination wedding in the tropical jungle at Nandini Bali',
    `mobile_image` = COALESCE(@wedding_hero_mobile_image, @wedding_hero_image),
    `mobile_image_alt` = 'Destination wedding in the tropical jungle at Nandini Bali',
    `is_active` = 1, `sort_order` = 1, `updated_at` = NOW()
WHERE `id` = @wedding_final_cta_image_id;

INSERT INTO `page_section_images`
    (`id`, `page_section_id`, `image`, `image_alt`, `mobile_image`, `mobile_image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT (@next_page_section_image_id := @next_page_section_image_id + 1), @wedding_final_cta_section_id,
    @wedding_hero_image, 'Destination wedding in the tropical jungle at Nandini Bali',
    COALESCE(@wedding_hero_mobile_image, @wedding_hero_image), 'Destination wedding in the tropical jungle at Nandini Bali', 1, 1, NOW(), NOW()
WHERE @wedding_final_cta_section_id IS NOT NULL AND @wedding_final_cta_image_id IS NULL;

INSERT INTO `migrations` (`id`, `migration`, `batch`)
SELECT COALESCE((SELECT MAX(`m`.`id`) FROM `migrations` AS `m`), 0) + 1,
    '2026_10_09_000002_expand_wedding_venue_page',
    COALESCE((SELECT MAX(`m`.`batch`) FROM `migrations` AS `m`), 0) + 1
WHERE @wedding_page_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM `migrations` WHERE `migration` = '2026_10_09_000002_expand_wedding_venue_page');

COMMIT;

-- Verification: one page, seven active wedding sections and the CMS image records.
SELECT `id`, `title`, `meta_title`, `meta_description` FROM `pages` WHERE `id` = @wedding_page_id;
SELECT `id`, `section_key`, `subtitle`, `title`, `sort_order`, `is_active`
FROM `page_sections`
WHERE `page_id` = @wedding_page_id
ORDER BY `sort_order`, `id`;
SELECT `page_sections`.`section_key`, `page_section_images`.`image`, `page_section_images`.`image_alt`
FROM `page_section_images`
INNER JOIN `page_sections` ON `page_sections`.`id` = `page_section_images`.`page_section_id`
WHERE `page_sections`.`page_id` = @wedding_page_id
ORDER BY `page_sections`.`sort_order`, `page_section_images`.`sort_order`;
