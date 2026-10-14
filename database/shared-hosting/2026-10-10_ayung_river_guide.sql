-- Ayung River editorial guide update for shared hosting.
-- Target: /blog-news/ayung-river-ubud-guide
--
-- Safe to run more than once. The script locates the article and its sections
-- by slug/title, updates existing records, and inserts only missing records.
-- Select the production database in phpMyAdmin before importing this file.
-- Take a database backup before applying any production database change.

SET NAMES utf8mb4;
START TRANSACTION;

SET @blog_id := (
    SELECT `id`
    FROM `blog_news`
    WHERE `slug` = 'ayung-river-ubud-guide'
    LIMIT 1
);

UPDATE `blog_news`
SET
    `title` = 'Ayung River in Ubud, Bali: Rafting, Nature & River Experiences',
    `excerpt` = 'A practical guide to the Ayung River in Bali, including its Payangan and greater Ubud setting, white-water rafting, Balinese purification and riverside wellness experiences.',
    `description` = '<p>The Ayung River is one of Bali''s major river systems and a defining natural feature of the Ubud and Payangan landscape. Flowing through deep valleys, tropical vegetation and rural areas, the river is especially well known around Ubud for white-water rafting and nature-based experiences.</p><p>At Nandini Jungle by Hanging Gardens in Payangan, the Ayung River also forms the setting for quieter experiences, including Balinese purification, riverside wellness and moments of reflection surrounded by the jungle.</p>',
    `hero_image_alt` = 'White-water rafting on the Ayung River in Bali',
    `hero_mobile_image_alt` = 'White-water rafting on the Ayung River in Bali',
    `card_image_alt` = 'White-water rafting on the Ayung River in Bali',
    `button_label` = NULL,
    `button_url` = NULL,
    `meta_title` = 'Ayung River Ubud Guide: Rafting & River Experiences | Nandini',
    `meta_description` = 'Discover the Ayung River in Ubud, Bali, from white-water rafting and jungle scenery to Balinese purification, Holy River and riverside experiences.',
    `updated_at` = NOW()
WHERE `id` = @blog_id;

-- 1. Ayung River at a Glance. Insert only when this section is missing.
SET @facts_section_id := (
    SELECT `id`
    FROM `blog_news_sections`
    WHERE `blog_news_id` = @blog_id
      AND `title` = 'QUICK FACTS ABOUT THE AYUNG RIVER'
    LIMIT 1
);

SET @next_section_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_sections`);

INSERT INTO `blog_news_sections` (
    `id`, `blog_news_id`, `section_key`, `title`, `subtitle`, `excerpt`, `description`,
    `button_label`, `button_link_type`, `button_url`, `button_route`,
    `text_align`, `background_color`, `items`, `is_active`, `sort_order`,
    `created_at`, `updated_at`
)
SELECT
    @next_section_id,
    @blog_id,
    'intro_text_section',
    'QUICK FACTS ABOUT THE AYUNG RIVER',
    'AYUNG RIVER AT A GLANCE',
    NULL,
    '<p>The Ayung connects Bali''s northern uplands with the island''s south and is closely associated with the river valleys around Payangan and Ubud.</p>',
    NULL,
    'manual',
    NULL,
    NULL,
    'left',
    'warm_ivory',
    '{"gtm_section":"ayung_facts","presentation":"facts","entries":[{"label":"APPROX. 75 KM","description":"Official Indonesian tourism information describes the Ayung as stretching for about 75 kilometres."},{"label":"NORTH TO SOUTH","description":"Its course runs from Bali''s northern mountain area toward the Badung Strait."},{"label":"PAYANGAN LANDSCAPE","description":"The Ayung River valley is part of Payangan, within the greater Ubud area."},{"label":"RAFTING IN UBUD","description":"The Ubud section is one of Bali''s established white-water rafting destinations."}],"source":{"label":"Indonesia Travel, Ministry of Tourism of the Republic of Indonesia","url":"https://www.indonesia.travel/gb/en/travel-ideas/marine/5-reasons-travelers-love-white-water-rafting-at-ayung-river-in-ubud"}}',
    1,
    1,
    NOW(),
    NOW()
WHERE @blog_id IS NOT NULL
  AND @facts_section_id IS NULL;

UPDATE `blog_news_sections`
SET
    `section_key` = 'intro_text_section',
    `title` = 'QUICK FACTS ABOUT THE AYUNG RIVER',
    `subtitle` = 'AYUNG RIVER AT A GLANCE',
    `excerpt` = NULL,
    `description` = '<p>The Ayung connects Bali''s northern uplands with the island''s south and is closely associated with the river valleys around Payangan and Ubud.</p>',
    `button_label` = NULL,
    `button_link_type` = 'manual',
    `button_url` = NULL,
    `button_route` = NULL,
    `text_align` = 'left',
    `background_color` = 'warm_ivory',
    `items` = '{"gtm_section":"ayung_facts","presentation":"facts","entries":[{"label":"APPROX. 75 KM","description":"Official Indonesian tourism information describes the Ayung as stretching for about 75 kilometres."},{"label":"NORTH TO SOUTH","description":"Its course runs from Bali''s northern mountain area toward the Badung Strait."},{"label":"PAYANGAN LANDSCAPE","description":"The Ayung River valley is part of Payangan, within the greater Ubud area."},{"label":"RAFTING IN UBUD","description":"The Ubud section is one of Bali''s established white-water rafting destinations."}],"source":{"label":"Indonesia Travel, Ministry of Tourism of the Republic of Indonesia","url":"https://www.indonesia.travel/gb/en/travel-ideas/marine/5-reasons-travelers-love-white-water-rafting-at-ayung-river-in-ubud"}}',
    `is_active` = 1,
    `sort_order` = 1,
    `updated_at` = NOW()
WHERE `blog_news_id` = @blog_id
  AND `title` = 'QUICK FACTS ABOUT THE AYUNG RIVER';

-- 2. Location. Reuse the former FAQ section.
UPDATE `blog_news_sections`
SET
    `section_key` = 'intro_text_section',
    `title` = 'WHERE IS THE AYUNG RIVER IN BALI?',
    `subtitle` = 'LOCATION',
    `excerpt` = NULL,
    `description` = '<p>The Ayung River runs from Bali''s northern mountain area through the centre of the island and toward the Badung Strait on the south coast. Sections around Payangan and greater Ubud are known for tropical valleys, riverbank scenery and rafting.</p><p>Nandini Jungle by Hanging Gardens is in Banjar Susut, Desa Buahan, Payangan, within the greater Ubud area. It is not in central Ubud town.</p>',
    `button_label` = NULL,
    `button_link_type` = 'manual',
    `button_url` = NULL,
    `button_route` = NULL,
    `text_align` = 'left',
    `background_color` = 'white',
    `items` = '{"gtm_section":"ayung_location"}',
    `is_active` = 1,
    `sort_order` = 2,
    `updated_at` = NOW()
WHERE `blog_news_id` = @blog_id
  AND `title` IN ('FAQ', 'WHERE IS THE AYUNG RIVER IN BALI?');

-- 3. Ayung overview.
UPDATE `blog_news_sections`
SET
    `section_key` = 'split_media_section',
    `title` = 'WHAT MAKES THE AYUNG RIVER SPECIAL?',
    `subtitle` = 'LANDSCAPE & EXPERIENCE',
    `excerpt` = NULL,
    `description` = '<p>The Ayung is distinguished by its tropical valley landscape, dense vegetation and the contrast between active and slower ways to experience the river. Rafting offers a journey through moving water and changing scenery, while riverside cultural and wellness experiences invite a quieter connection with the same environment.</p><p>Around Payangan, the river also gives Nandini a strong sense of place: the jungle, valley and sound of flowing water shape experiences beside the river without turning the setting into a single-purpose attraction.</p>',
    `button_label` = NULL,
    `button_link_type` = 'manual',
    `button_url` = NULL,
    `button_route` = NULL,
    `text_align` = 'left',
    `background_color` = 'white',
    `items` = '{"gtm_section":"ayung_overview"}',
    `is_active` = 1,
    `sort_order` = 3,
    `updated_at` = NOW()
WHERE `blog_news_id` = @blog_id
  AND `title` IN ('What Makes the Ayung River Special?', 'WHAT MAKES THE AYUNG RIVER SPECIAL?');

-- 4. Rafting.
UPDATE `blog_news_sections`
SET
    `section_key` = 'intro_text_section',
    `title` = 'AYUNG RIVER RAFTING IN UBUD',
    `subtitle` = 'WHITE-WATER ADVENTURE',
    `excerpt` = NULL,
    `description` = '<p>White-water rafting is one of the best-known activities on the Ayung River. Guided trips use different river sections and routes, so the character and difficulty of an outing depend on the operator, the selected stretch and current river conditions.</p><p>Rainfall can affect water levels and the pace of the river. Guests should review the latest conditions, follow the operator''s safety briefing and guidance, and choose an experience appropriate to their circumstances. Booking details belong on the dedicated rafting page rather than in this editorial guide.</p>',
    `button_label` = 'EXPLORE AYUNG RIVER RAFTING',
    `button_link_type` = 'manual',
    `button_url` = 'https://nandinibali.com/experience/ayung-white-water-rafting',
    `button_route` = NULL,
    `text_align` = 'left',
    `background_color` = 'white',
    `items` = '{"gtm_section":"ayung_rafting","preserve_button_label":true}',
    `is_active` = 1,
    `sort_order` = 4,
    `updated_at` = NOW()
WHERE `blog_news_id` = @blog_id
  AND `title` IN ('Ayung River Rafting: The Adventure Side', 'AYUNG RIVER RAFTING IN UBUD');

-- 5. Holy River and Melukat.
UPDATE `blog_news_sections`
SET
    `section_key` = 'split_media_reverse',
    `title` = 'AYUNG RIVER FOR MELUKAT & BALINESE PURIFICATION',
    `subtitle` = 'BALINESE PURIFICATION',
    `excerpt` = NULL,
    `description` = '<p>Melukat is a Balinese purification tradition using water and associated with cleansing, renewal and spiritual reflection. At Nandini''s Holy River setting beside the Ayung, guided purification and blessing experiences offer a respectful way to encounter this tradition in the surrounding jungle landscape.</p><p>The experience is cultural and reflective rather than a medical treatment or an alternative form of rafting. Guests should approach the ritual with respect and follow the guidance provided by the people leading it.</p>',
    `button_label` = 'EXPLORE THE HOLY RIVER',
    `button_link_type` = 'manual',
    `button_url` = 'https://nandinibali.com/holy-river',
    `button_route` = NULL,
    `text_align` = 'left',
    `background_color` = 'white',
    `items` = '{"gtm_section":"holy_river","preserve_button_label":true}',
    `is_active` = 1,
    `sort_order` = 5,
    `updated_at` = NOW()
WHERE `blog_news_id` = @blog_id
  AND `title` IN ('Holy River Ubud: A More Peaceful Experience', 'AYUNG RIVER FOR MELUKAT & BALINESE PURIFICATION');

-- 6. Rafting versus Holy River.
UPDATE `blog_news_sections`
SET
    `section_key` = 'intro_text_section',
    `title` = 'RAFTING VS HOLY RIVER: WHICH EXPERIENCE FITS?',
    `subtitle` = 'TWO WAYS TO EXPERIENCE THE RIVER',
    `excerpt` = NULL,
    `description` = '<p>Both experiences are connected to the Ayung River, but they meet different interests. Neither is presented as better; the choice depends on whether you want an active river journey or a quieter cultural experience.</p>',
    `button_label` = NULL,
    `button_link_type` = 'manual',
    `button_url` = NULL,
    `button_route` = NULL,
    `text_align` = 'left',
    `background_color` = 'warm_ivory',
    `items` = '{"gtm_section":"rafting_vs_holy_river","presentation":"blocks","entries":[{"title":"RAFTING","description":"An active, guided journey on the river, shaped by the route and current water conditions."},{"title":"HOLY RIVER","description":"A cultural and reflective experience centred on Balinese purification and blessing traditions beside the river."}]}',
    `is_active` = 1,
    `sort_order` = 6,
    `updated_at` = NOW()
WHERE `blog_news_id` = @blog_id
  AND `title` IN (
      'Rafting vs Holy River Experience: Which One Should You Choose?',
      'RAFTING VS HOLY RIVER: WHICH EXPERIENCE FITS?'
  );

-- 7. Spa on the River.
UPDATE `blog_news_sections`
SET
    `section_key` = 'split_media_section',
    `title` = 'SPA ON THE RIVER',
    `subtitle` = 'RIVERSIDE WELLNESS',
    `excerpt` = NULL,
    `description` = '<p>Nandini''s Spa on the River is a 180-minute riverside wellness experience beside the Ayung. The current experience begins with a soothing foot bath and continues with Nandini''s signature treatment in the jungle setting.</p><p>This is a secondary, slower way to experience the riverside. Treatment details and enquiries remain on the dedicated Essence Spa website.</p>',
    `button_label` = 'EXPLORE ESSENCE SPA',
    `button_link_type` = 'manual',
    `button_url` = 'https://spa.nandinibali.com/',
    `button_route` = NULL,
    `text_align` = 'left',
    `background_color` = 'white',
    `items` = '{"gtm_section":"spa_on_river","preserve_button_label":true}',
    `is_active` = 1,
    `sort_order` = 7,
    `updated_at` = NOW()
WHERE `blog_news_id` = @blog_id
  AND `title` IN ('Experiencing the River During a Stay at Nandini', 'SPA ON THE RIVER');

SET @spa_section_id := (
    SELECT `id`
    FROM `blog_news_sections`
    WHERE `blog_news_id` = @blog_id
      AND `title` = 'SPA ON THE RIVER'
    LIMIT 1
);

SET @spa_image_id := (
    SELECT `id`
    FROM `blog_news_section_images`
    WHERE `blog_news_section_id` = @spa_section_id
      AND `is_active` = 1
    ORDER BY `sort_order`, `id`
    LIMIT 1
);

SET @next_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (
    `id`, `blog_news_section_id`, `image`, `image_file_name`, `image_alt`,
    `mobile_image`, `mobile_image_file_name`, `mobile_image_alt`, `caption`,
    `is_active`, `sort_order`, `created_at`, `updated_at`
)
SELECT
    @next_image_id,
    @spa_section_id,
    '/images/holy-river/SPA ON THE RIVER.webp',
    'SPA ON THE RIVER',
    'Spa on the River treatment deck beside the Ayung River at Nandini Jungle',
    NULL,
    NULL,
    NULL,
    NULL,
    1,
    0,
    NOW(),
    NOW()
WHERE @spa_section_id IS NOT NULL
  AND @spa_image_id IS NULL;

UPDATE `blog_news_section_images`
SET
    `image` = '/images/holy-river/SPA ON THE RIVER.webp',
    `image_file_name` = 'SPA ON THE RIVER',
    `image_alt` = 'Spa on the River treatment deck beside the Ayung River at Nandini Jungle',
    `mobile_image` = NULL,
    `mobile_image_file_name` = NULL,
    `mobile_image_alt` = NULL,
    `caption` = NULL,
    `is_active` = 1,
    `sort_order` = 0,
    `updated_at` = NOW()
WHERE `id` = @spa_image_id;

-- 8. River conditions.
UPDATE `blog_news_sections`
SET
    `section_key` = 'intro_text_section',
    `title` = 'BEST TIME TO EXPERIENCE THE AYUNG RIVER',
    `subtitle` = 'RIVER CONDITIONS',
    `excerpt` = NULL,
    `description` = '<p>The Ayung can be experienced at different times of year, but conditions are not fixed. Rainfall can influence water level, current and the way rafting operators manage a route. Always follow current operator guidance rather than relying on a seasonal guarantee.</p><p>Quieter riverside experiences have different considerations from rafting and may depend on weather, access and availability. Nandini''s team can advise on the latest arrangements for Holy River and Spa on the River experiences.</p>',
    `button_label` = NULL,
    `button_link_type` = 'manual',
    `button_url` = NULL,
    `button_route` = NULL,
    `text_align` = 'left',
    `background_color` = 'white',
    `items` = '{"gtm_section":"river_conditions"}',
    `is_active` = 1,
    `sort_order` = 8,
    `updated_at` = NOW()
WHERE `blog_news_id` = @blog_id
  AND `title` IN ('Best Time to Experience the Ayung River', 'BEST TIME TO EXPERIENCE THE AYUNG RIVER');

-- 9. Planning section.
UPDATE `blog_news_sections`
SET
    `section_key` = 'intro_text_section',
    `title` = 'PLANNING AN AYUNG RIVER EXPERIENCE',
    `subtitle` = 'PLAN YOUR VISIT',
    `excerpt` = NULL,
    `description` = '<p>Start with the type of river experience you want, then check the latest conditions and practical guidance with the relevant operator or Nandini team.</p>',
    `button_label` = NULL,
    `button_link_type` = 'manual',
    `button_url` = NULL,
    `button_route` = NULL,
    `text_align` = 'left',
    `background_color` = 'soft_gray',
    `items` = '{"gtm_section":"planning","presentation":"blocks","entries":[{"title":"RAFTING CONDITIONS","description":"River conditions can vary. Follow the operator''s current route, equipment and safety guidance."},{"title":"CHOOSING YOUR EXPERIENCE","description":"Choose rafting for active adventure, Holy River for cultural purification and reflection, or Spa on the River for riverside wellness."},{"title":"LOCATION","description":"Nandini is in Payangan, within the greater Ubud area, rather than central Ubud town. Allow for local travel time when planning."}]}',
    `is_active` = 1,
    `sort_order` = 9,
    `updated_at` = NOW()
WHERE `blog_news_id` = @blog_id
  AND `title` IN ('How to Plan Your Ayung River Experience in Ubud', 'PLANNING AN AYUNG RIVER EXPERIENCE');

-- 10. Final editorial/commercial bridge.
UPDATE `blog_news_sections`
SET
    `section_key` = 'intro_text_section',
    `title` = 'EXPERIENCE THE AYUNG RIVER AT NANDINI',
    `subtitle` = 'THE AYUNG RIVER AT NANDINI',
    `excerpt` = NULL,
    `description` = '<p>From white-water rafting to Balinese purification and quiet riverside wellness, the Ayung River forms an important part of the experience at Nandini Jungle by Hanging Gardens in Payangan, within the greater Ubud area.</p><p>Use the dedicated pages below to explore the experience that fits your visit or to plan a stay in the surrounding jungle landscape.</p>',
    `button_label` = NULL,
    `button_link_type` = 'manual',
    `button_url` = NULL,
    `button_route` = NULL,
    `text_align` = 'left',
    `background_color` = 'warm_ivory',
    `items` = '{"gtm_section":"article_cta","presentation":"links","entries":[{"label":"AYUNG RIVER RAFTING","url":"https://nandinibali.com/experience/ayung-white-water-rafting"},{"label":"HOLY RIVER","url":"https://nandinibali.com/holy-river"},{"label":"ESSENCE SPA","url":"https://spa.nandinibali.com/"},{"label":"STAY AT NANDINI","url":"https://nandinibali.com/ubud-jungle-resort-in-bali"}]}',
    `is_active` = 1,
    `sort_order` = 10,
    `updated_at` = NOW()
WHERE `blog_news_id` = @blog_id
  AND `title` IN (
      'Experience the Ayung River at Nandini Jungle by Hanging Gardens',
      'EXPERIENCE THE AYUNG RIVER AT NANDINI'
  );

-- Update meaningful image alternatives without changing the existing media.
SET @overview_section_id := (
    SELECT `id` FROM `blog_news_sections`
    WHERE `blog_news_id` = @blog_id
      AND `title` = 'WHAT MAKES THE AYUNG RIVER SPECIAL?'
    LIMIT 1
);

SET @holy_section_id := (
    SELECT `id` FROM `blog_news_sections`
    WHERE `blog_news_id` = @blog_id
      AND `title` = 'AYUNG RIVER FOR MELUKAT & BALINESE PURIFICATION'
    LIMIT 1
);

SET @location_section_id := (
    SELECT `id` FROM `blog_news_sections`
    WHERE `blog_news_id` = @blog_id
      AND `title` = 'WHERE IS THE AYUNG RIVER IN BALI?'
    LIMIT 1
);

UPDATE `blog_news_section_images`
SET
    `image_alt` = 'White-water rafting on the Ayung River through Bali''s tropical valley landscape',
    `mobile_image_alt` = 'White-water rafting on the Ayung River through Bali''s tropical valley landscape',
    `updated_at` = NOW()
WHERE `id` = (
    SELECT `image_id`
    FROM (
        SELECT `id` AS `image_id`
        FROM `blog_news_section_images`
        WHERE `blog_news_section_id` = @overview_section_id
          AND `is_active` = 1
        ORDER BY `sort_order`, `id`
        LIMIT 1
    ) AS `first_overview_image`
);

UPDATE `blog_news_section_images`
SET
    `image_alt` = 'Jungle river and waterfall setting beside the Ayung River at Nandini Jungle',
    `mobile_image_alt` = 'Jungle river and waterfall setting beside the Ayung River at Nandini Jungle',
    `updated_at` = NOW()
WHERE `id` = (
    SELECT `image_id`
    FROM (
        SELECT `id` AS `image_id`
        FROM `blog_news_section_images`
        WHERE `blog_news_section_id` = @holy_section_id
          AND `is_active` = 1
        ORDER BY `sort_order`, `id`
        LIMIT 1
    ) AS `first_holy_image`
);

UPDATE `blog_news_section_images`
SET
    `image_alt` = 'White-water rafting on the Ayung River in Bali',
    `mobile_image_alt` = 'White-water rafting on the Ayung River in Bali',
    `updated_at` = NOW()
WHERE `id` = (
    SELECT `image_id`
    FROM (
        SELECT `id` AS `image_id`
        FROM `blog_news_section_images`
        WHERE `blog_news_section_id` = @location_section_id
          AND `is_active` = 1
        ORDER BY `sort_order`, `id`
        LIMIT 1
    ) AS `first_location_image`
);

COMMIT;

-- Verification: the first result must return one article row.
SELECT
    `id`, `slug`, `title`, `meta_title`, `meta_description`, `updated_at`
FROM `blog_news`
WHERE `slug` = 'ayung-river-ubud-guide';

-- Verification: this must return 10 active sections in sort order 1 through 10.
SELECT
    `id`, `sort_order`, `section_key`, `subtitle`, `title`,
    `button_label`, `button_url`, `is_active`
FROM `blog_news_sections`
WHERE `blog_news_id` = @blog_id
ORDER BY `sort_order`, `id`;

-- Verification: FAQ should return zero rows for this article.
SELECT `id`, `title`
FROM `blog_news_sections`
WHERE `blog_news_id` = @blog_id
  AND `title` = 'FAQ';
