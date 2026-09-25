-- Nandini Jungle by Hanging Gardens
-- About Us story page data for shared hosting (MySQL / MariaDB)
--
-- IMPORTANT:
-- 1. Back up the database before running this script.
-- 2. This replaces only the 12 `about_story_*` sections belonging to the
--    active main-site page whose slug is `about-us`.
-- 3. Existing unrelated pages and sections are not changed.
-- 4. The script also adds the required `pages.site` column and its index when
--    importing into an older database that does not have them yet.

SET NAMES utf8mb4;

-- Shared-hosting compatibility: older databases may predate the multi-site
-- column now required by the Page model and AboutUsController.
SET @has_pages_site := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'pages'
      AND COLUMN_NAME = 'site'
);

SET @schema_sql := IF(
    @has_pages_site = 0,
    'ALTER TABLE `pages` ADD COLUMN `site` VARCHAR(10) NOT NULL DEFAULT ''main'' AFTER `id`',
    'SELECT 1'
);

PREPARE schema_statement FROM @schema_sql;
EXECUTE schema_statement;
DEALLOCATE PREPARE schema_statement;

SET @has_pages_site_index := (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'pages'
      AND INDEX_NAME = 'pages_site_slug_is_active_index'
);

SET @schema_sql := IF(
    @has_pages_site_index = 0,
    'CREATE INDEX `pages_site_slug_is_active_index` ON `pages` (`site`, `slug`, `is_active`)',
    'SELECT 1'
);

PREPARE schema_statement FROM @schema_sql;
EXECUTE schema_statement;
DEALLOCATE PREPARE schema_statement;

START TRANSACTION;

SET @about_page_id := (
    SELECT id
    FROM pages
    WHERE slug = 'about-us'
      AND site = 'main'
      AND is_active = 1
    ORDER BY id
    LIMIT 1
);

-- Remove only the managed About story content so this script can be rerun.
DELETE psi
FROM page_section_images AS psi
INNER JOIN page_sections AS ps ON ps.id = psi.page_section_id
WHERE ps.page_id = @about_page_id
  AND ps.section_key IN (
      'about_story_hero',
      'about_story_origins',
      'about_story_timeline',
      'about_story_chapter',
      'about_story_chapter_reverse',
      'about_story_comparison',
      'about_story_growth',
      'about_story_mosaic',
      'about_story_gallery',
      'about_story_values',
      'about_story_today',
      'about_story_final'
  );

DELETE FROM page_sections
WHERE page_id = @about_page_id
  AND section_key IN (
      'about_story_hero',
      'about_story_origins',
      'about_story_timeline',
      'about_story_chapter',
      'about_story_chapter_reverse',
      'about_story_comparison',
      'about_story_growth',
      'about_story_mosaic',
      'about_story_gallery',
      'about_story_values',
      'about_story_today',
      'about_story_final'
  );

-- 1. Hero
INSERT INTO page_sections (
    page_id, section_key, title, subtitle, excerpt, description, items,
    button_label, button_link_type, button_url, button_route, text_align,
    background_color, is_active, sort_order, created_at, updated_at
)
SELECT
    @about_page_id,
    'about_story_hero',
    CONCAT('Rooted in the Jungle', CHAR(10), 'Since 2005'),
    'Our Story',
    'Susut, Payangan · Bali',
    '<p>What began as an intimate hillside retreat has evolved over two decades into Nandini Jungle by Hanging Gardens — while remaining deeply connected to the landscape, community and traditions that shaped its beginning.</p>',
    NULL,
    'Discover Our Story',
    'manual',
    '#our-story',
    NULL,
    'left',
    NULL,
    1,
    1,
    NOW(),
    NOW()
WHERE @about_page_id IS NOT NULL;

SET @section_id := LAST_INSERT_ID();

INSERT INTO page_section_images (
    page_section_id, image, image_file_name, image_alt, mobile_image,
    mobile_image_file_name, mobile_image_alt, caption, is_active,
    sort_order, created_at, updated_at
)
SELECT
    @section_id,
    'https://nandinibali.com/storage/images/gallery/pool%20okl.jpg',
    NULL,
    'Nandini Jungle infinity pool surrounded by tropical rainforest in Ubud, Bali',
    NULL,
    NULL,
    NULL,
    NULL,
    1,
    0,
    NOW(),
    NOW()
WHERE @about_page_id IS NOT NULL;

-- 2. Origins
INSERT INTO page_sections (
    page_id, section_key, title, subtitle, excerpt, description, items,
    button_label, button_link_type, button_url, button_route, text_align,
    background_color, is_active, sort_order, created_at, updated_at
)
SELECT
    @about_page_id,
    'about_story_origins',
    CONCAT('A Chance Encounter', CHAR(10), 'That Became Nandini'),
    'Where It Began',
    NULL,
    '<p>In the late 1990s and early 2000s, Swedish entrepreneur Magnus Falk frequently travelled through the Ubud region. While exploring the area by bicycle, a chance meeting with a local villager led him to the hillside that would eventually become Nandini.</p><p>The steep terrain was challenging, but instead of removing the character of the landscape, the resort was built into it — allowing villas, pathways and tropical vegetation to follow the natural contours of the hillside.</p>',
    '[{"quote":"I like to think that the land came to me rather than the other way around.","attribution":"Magnus Falk"}]',
    NULL,
    'manual',
    NULL,
    NULL,
    'left',
    NULL,
    1,
    2,
    NOW(),
    NOW()
WHERE @about_page_id IS NOT NULL;

SET @section_id := LAST_INSERT_ID();

INSERT INTO page_section_images (
    page_section_id, image, image_file_name, image_alt, mobile_image,
    mobile_image_file_name, mobile_image_alt, caption, is_active,
    sort_order, created_at, updated_at
)
SELECT @section_id, 'https://nandinibali.com/storage/images/gallery/villa%2081%20Jungle%20View%20Villa.jpg', NULL, 'Jungle View Villa at Nandini Jungle by Hanging Gardens', NULL, NULL, NULL, NULL, 1, 0, NOW(), NOW()
WHERE @about_page_id IS NOT NULL
UNION ALL
SELECT @section_id, 'https://nandinibali.com/storage/images/gallery/Nandini%20Funicular%20-%20Gondola.jpg', NULL, 'Nandini Jungle funicular travelling through the rainforest', NULL, NULL, NULL, NULL, 1, 1, NOW(), NOW()
WHERE @about_page_id IS NOT NULL;

-- 3. Timeline
INSERT INTO page_sections (
    page_id, section_key, title, subtitle, excerpt, description, items,
    button_label, button_link_type, button_url, button_route, text_align,
    background_color, is_active, sort_order, created_at, updated_at
)
SELECT
    @about_page_id,
    'about_story_timeline',
    'A Story in Time',
    'Two Decades of Nandini',
    NULL,
    NULL,
    '[{"year":"Late 1990s","title":"The Beginning","description":"A chance encounter during Magnus Falk’s travels through the Ubud region leads to the land that would become Nandini."},{"year":"2005","title":"Nandini Opens","description":"Nandini opens as an intimate hillside retreat."},{"year":"2007","title":"The River","description":"Access through the jungle is created to reach the secluded riverside below the resort."},{"year":"2019","title":"A New Chapter","description":"Nandini thoughtfully expands while preserving the character of the original hillside retreat."},{"year":"2022","title":"Renewing Nandini","description":"Major renovation and upgrades refresh villas and resort facilities."},{"year":"2024","title":"New & Upgraded","description":"Royal Suites and new dining and wellness experiences mark another evolution."},{"year":"Today","title":"The Story Continues","description":"Nandini continues to evolve in harmony with the jungle."}]',
    NULL,
    'manual',
    NULL,
    NULL,
    'left',
    NULL,
    1,
    3,
    NOW(),
    NOW()
WHERE @about_page_id IS NOT NULL;

-- 4. The Beginning
INSERT INTO page_sections (
    page_id, section_key, title, subtitle, excerpt, description, items,
    button_label, button_link_type, button_url, button_route, text_align,
    background_color, is_active, sort_order, created_at, updated_at
)
SELECT
    @about_page_id,
    'about_story_chapter',
    CONCAT('The Beginning.', CHAR(10), 'One Remarkable Hillside.'),
    'The Beginning',
    '2005',
    '<p>Nandini officially opened in 2005 as an intimate retreat built along the steep jungle hillside. Inspired by traditional Balinese architecture, the original villas were created to sit naturally within their surroundings.</p>',
    '[]',
    NULL,
    'manual',
    NULL,
    NULL,
    'left',
    NULL,
    1,
    4,
    NOW(),
    NOW()
WHERE @about_page_id IS NOT NULL;

SET @section_id := LAST_INSERT_ID();

INSERT INTO page_section_images (
    page_section_id, image, image_file_name, image_alt, mobile_image,
    mobile_image_file_name, mobile_image_alt, caption, is_active,
    sort_order, created_at, updated_at
)
SELECT @section_id, 'https://nandinibali.com/storage/images/gallery/villa%2081%20Jungle%20View%20Villa.jpg', NULL, 'Original-style Jungle View Villa on Nandini’s hillside', NULL, NULL, NULL, NULL, 1, 0, NOW(), NOW()
WHERE @about_page_id IS NOT NULL;

-- 5. The River
INSERT INTO page_sections (
    page_id, section_key, title, subtitle, excerpt, description, items,
    button_label, button_link_type, button_url, button_route, text_align,
    background_color, is_active, sort_order, created_at, updated_at
)
SELECT
    @about_page_id,
    'about_story_chapter_reverse',
    CONCAT('The Journey', CHAR(10), 'Down to the River'),
    'A Hidden World Below',
    '2007',
    '<p>When Nandini first opened, the river far below the resort was not yet part of the guest experience. Access was created in 2007 through a series of steep steps descending through the jungle, with a lift later assisting guests for part of the journey.</p>',
    NULL,
    'Explore Nandini Experiences',
    'route',
    NULL,
    'experiences.index',
    'left',
    NULL,
    1,
    5,
    NOW(),
    NOW()
WHERE @about_page_id IS NOT NULL;

SET @section_id := LAST_INSERT_ID();

INSERT INTO page_section_images (
    page_section_id, image, image_file_name, image_alt, mobile_image,
    mobile_image_file_name, mobile_image_alt, caption, is_active,
    sort_order, created_at, updated_at
)
SELECT @section_id, 'https://nandinibali.com/storage/images/gallery/yoga-by-the-river.jpg', NULL, 'Yoga and wellness by the river at Nandini Jungle', NULL, NULL, NULL, NULL, 1, 0, NOW(), NOW()
WHERE @about_page_id IS NOT NULL;

-- 6. Then and Now
INSERT INTO page_sections (
    page_id, section_key, title, subtitle, excerpt, description, items,
    button_label, button_link_type, button_url, button_route, text_align,
    background_color, is_active, sort_order, created_at, updated_at
)
SELECT
    @about_page_id,
    'about_story_comparison',
    'The Evolution of Nandini',
    'Then & Now',
    NULL,
    '<p>The resort has changed considerably since opening in 2005, but the landscape that shaped Nandini remains at the heart of its identity.</p>',
    '[{"label":"Then","title":"The Early Years","description":"Nandini began as an intimate collection of hillside villas, shaped by the jungle and rooted in Balinese character."},{"label":"Now","title":"Nandini Today","description":"The resort continues to evolve in harmony with the jungle."}]',
    NULL,
    'manual',
    NULL,
    NULL,
    'left',
    NULL,
    1,
    6,
    NOW(),
    NOW()
WHERE @about_page_id IS NOT NULL;

SET @section_id := LAST_INSERT_ID();

INSERT INTO page_section_images (
    page_section_id, image, image_file_name, image_alt, mobile_image,
    mobile_image_file_name, mobile_image_alt, caption, is_active,
    sort_order, created_at, updated_at
)
SELECT @section_id, NULL, NULL, 'Reserved for a genuine historical Nandini archive image', NULL, NULL, NULL, NULL, 1, 0, NOW(), NOW()
WHERE @about_page_id IS NOT NULL
UNION ALL
SELECT @section_id, 'https://nandinibali.com/storage/images/gallery/pool.jpg', NULL, 'Nandini Jungle pool today', NULL, NULL, NULL, NULL, 1, 1, NOW(), NOW()
WHERE @about_page_id IS NOT NULL;

-- 7. Growth
INSERT INTO page_sections (
    page_id, section_key, title, subtitle, excerpt, description, items,
    button_label, button_link_type, button_url, button_route, text_align,
    background_color, is_active, sort_order, created_at, updated_at
)
SELECT
    @about_page_id,
    'about_story_growth',
    CONCAT('Growing Without', CHAR(10), 'Losing the Landscape'),
    'A New Chapter',
    '2019',
    '<p>The 2019 expansion marked one of the most significant chapters in Nandini’s history, introducing new accommodation while retaining the jungle setting and character of the original hillside retreat.</p>',
    '[]',
    NULL,
    'manual',
    NULL,
    NULL,
    'left',
    NULL,
    1,
    7,
    NOW(),
    NOW()
WHERE @about_page_id IS NOT NULL;

SET @section_id := LAST_INSERT_ID();

INSERT INTO page_section_images (
    page_section_id, image, image_file_name, image_alt, mobile_image,
    mobile_image_file_name, mobile_image_alt, caption, is_active,
    sort_order, created_at, updated_at
)
SELECT @section_id, 'https://nandinibali.com/storage/images/gallery/Mystical%20Jungle%20Pool.jpg', NULL, 'Mystical jungle pool at Nandini Jungle by Hanging Gardens', NULL, NULL, NULL, NULL, 1, 0, NOW(), NOW()
WHERE @about_page_id IS NOT NULL;

-- 8. 2022 Renewal Mosaic
INSERT INTO page_sections (
    page_id, section_key, title, subtitle, excerpt, description, items,
    button_label, button_link_type, button_url, button_route, text_align,
    background_color, is_active, sort_order, created_at, updated_at
)
SELECT
    @about_page_id,
    'about_story_mosaic',
    'Renewing the Nandini Experience',
    'A Major Renewal',
    '2022',
    '<p>A major renovation and upgrade refreshed the original villas and expanded spaces throughout the resort, including enhancements to the lounge together with additions such as the pool bar, riverside yoga area, gym and wedding chapel.</p>',
    NULL,
    NULL,
    'manual',
    NULL,
    NULL,
    'left',
    NULL,
    1,
    8,
    NOW(),
    NOW()
WHERE @about_page_id IS NOT NULL;

SET @section_id := LAST_INSERT_ID();

INSERT INTO page_section_images (
    page_section_id, image, image_file_name, image_alt, mobile_image,
    mobile_image_file_name, mobile_image_alt, caption, is_active,
    sort_order, created_at, updated_at
)
SELECT @section_id, 'https://nandinibali.com/storage/images/gallery/villa%2081%20Jungle%20View%20Villa.jpg', NULL, 'Renovated Jungle Villa', NULL, NULL, NULL, 'Jungle Villa', 1, 0, NOW(), NOW()
WHERE @about_page_id IS NOT NULL
UNION ALL
SELECT @section_id, 'https://nandinibali.com/storage/images/gallery/JUNGLE%20POOL%20BAR%20%283%29.jpg', NULL, 'Jungle Pool Bar', NULL, NULL, NULL, 'Pool Bar', 1, 1, NOW(), NOW()
WHERE @about_page_id IS NOT NULL
UNION ALL
SELECT @section_id, 'https://nandinibali.com/storage/images/gallery/DJIWA%20SHALA%20%284%29.jpg', NULL, 'Djiwa Shala yoga pavilion', NULL, NULL, NULL, 'Djiwa Shala', 1, 2, NOW(), NOW()
WHERE @about_page_id IS NOT NULL
UNION ALL
SELECT @section_id, 'https://nandinibali.com/storage/gallery/images/heritage-lounge-balinese-dance-nandini-jungle-bali.webp', NULL, 'Balinese dance at Heritage Lounge', NULL, NULL, NULL, 'Heritage Lounge', 1, 3, NOW(), NOW()
WHERE @about_page_id IS NOT NULL
UNION ALL
SELECT @section_id, 'https://nandinibali.com/storage/images/gallery/Nandini%20Funicular%20-%20Gondola.jpg', NULL, 'Nandini Jungle funicular', NULL, NULL, NULL, 'Funicular', 1, 4, NOW(), NOW()
WHERE @about_page_id IS NOT NULL;

-- 9. 2024 Gallery, including the gym image from the blog article
INSERT INTO page_sections (
    page_id, section_key, title, subtitle, excerpt, description, items,
    button_label, button_link_type, button_url, button_route, text_align,
    background_color, is_active, sort_order, created_at, updated_at
)
SELECT
    @about_page_id,
    'about_story_gallery',
    'New & Upgraded Nandini',
    'The Next Chapter',
    '2024',
    '<p>Nandini introduced another significant evolution of the resort experience, with upgraded accommodation and expanded dining and wellness concepts alongside the original Jungle Villas.</p>',
    '[{"eyebrow":"Royal Suites","title":"Jungle Living"},{"eyebrow":"Wine Cellar","title":"A New Dining Chapter"},{"eyebrow":"Wine Spa","title":"Wellness Reimagined"},{"eyebrow":"Fitness Centre","title":"Wellness in Motion"}]',
    NULL,
    'manual',
    NULL,
    NULL,
    'left',
    NULL,
    1,
    9,
    NOW(),
    NOW()
WHERE @about_page_id IS NOT NULL;

SET @section_id := LAST_INSERT_ID();

INSERT INTO page_section_images (
    page_section_id, image, image_file_name, image_alt, mobile_image,
    mobile_image_file_name, mobile_image_alt, caption, is_active,
    sort_order, created_at, updated_at
)
SELECT @section_id, 'https://nandinibali.com/storage/images/gallery/teras%20bed.jpg', NULL, 'Royal Suite terrace and bedroom', NULL, NULL, NULL, NULL, 1, 0, NOW(), NOW()
WHERE @about_page_id IS NOT NULL
UNION ALL
SELECT @section_id, 'https://nandinibali.com/storage/images/gallery/wine%203.jpg', NULL, 'Wine cellar dining experience', NULL, NULL, NULL, NULL, 1, 1, NOW(), NOW()
WHERE @about_page_id IS NOT NULL
UNION ALL
SELECT @section_id, 'https://nandinibali.com/storage/images/gallery/berendam%20wine.jpg', NULL, 'Wine spa experience', NULL, NULL, NULL, NULL, 1, 2, NOW(), NOW()
WHERE @about_page_id IS NOT NULL
UNION ALL
SELECT @section_id, 'https://nandinibali.com/storage/images/blog/attachment/jGWBRtzYE4r3WceXgxiXXylUJZNXOq9bJcIbHgAP.jpg', NULL, 'Gym and fitness centre at Nandini Jungle by Hanging Gardens', NULL, NULL, NULL, NULL, 1, 3, NOW(), NOW()
WHERE @about_page_id IS NOT NULL;

-- 10. Values
INSERT INTO page_sections (
    page_id, section_key, title, subtitle, excerpt, description, items,
    button_label, button_link_type, button_url, button_route, text_align,
    background_color, is_active, sort_order, created_at, updated_at
)
SELECT
    @about_page_id,
    'about_story_values',
    'What Has Never Changed',
    'Beyond the Years',
    NULL,
    '<p>Nandini has evolved, but the principles behind it remain closely connected to its surroundings.</p>',
    '[{"value":"01","title":"The Land","description":"Respecting the jungle landscape and allowing architecture to follow the natural hillside."},{"value":"02","title":"The Community","description":"Working with local team members and communities whose knowledge and traditions remain part of the Nandini experience."},{"value":"03","title":"Balinese Character","description":"Natural materials, craftsmanship and Balinese traditions continue to influence the way Nandini looks, feels and welcomes its guests."}]',
    NULL,
    'manual',
    NULL,
    NULL,
    'left',
    NULL,
    1,
    10,
    NOW(),
    NOW()
WHERE @about_page_id IS NOT NULL;

SET @section_id := LAST_INSERT_ID();

INSERT INTO page_section_images (
    page_section_id, image, image_file_name, image_alt, mobile_image,
    mobile_image_file_name, mobile_image_alt, caption, is_active,
    sort_order, created_at, updated_at
)
SELECT @section_id, 'https://nandinibali.com/storage/images/gallery/JUNGLE%20POOL%20%288%29.jpg', NULL, 'Nandini’s architecture within the jungle landscape', NULL, NULL, NULL, NULL, 1, 0, NOW(), NOW()
WHERE @about_page_id IS NOT NULL
UNION ALL
SELECT @section_id, 'https://nandinibali.com/storage/gallery/images/heritage-lounge-balinese-dance-nandini-jungle-bali.webp', NULL, 'Balinese community and cultural performance at Nandini', NULL, NULL, NULL, NULL, 1, 1, NOW(), NOW()
WHERE @about_page_id IS NOT NULL
UNION ALL
SELECT @section_id, 'https://nandinibali.com/storage/images/gallery/DJIWA%20SHALA%20%284%29.jpg', NULL, 'Balinese-inspired Djiwa Shala architecture', NULL, NULL, NULL, NULL, 1, 2, NOW(), NOW()
WHERE @about_page_id IS NOT NULL;

-- 11. Nandini Today
INSERT INTO page_sections (
    page_id, section_key, title, subtitle, excerpt, description, items,
    button_label, button_link_type, button_url, button_route, text_align,
    background_color, is_active, sort_order, created_at, updated_at
)
SELECT
    @about_page_id,
    'about_story_today',
    'The Story Continues',
    'Nandini Today',
    NULL,
    '<p>From the original hillside villas to contemporary Royal Suites, riverside wellness, dining and immersive jungle experiences, today’s Nandini continues the story that began more than two decades ago.</p>',
    '[{"kind":"link","eyebrow":"Stay","title":"Jungle Villas & Royal Suites","url":"/jungle-villas"},{"kind":"link","eyebrow":"Experience","title":"Nandini Experiences","url":"/experiences"},{"kind":"link","eyebrow":"Explore","title":"Nandini Gallery","url":"/gallery"}]',
    NULL,
    'manual',
    NULL,
    NULL,
    'left',
    NULL,
    1,
    11,
    NOW(),
    NOW()
WHERE @about_page_id IS NOT NULL;

SET @section_id := LAST_INSERT_ID();

INSERT INTO page_section_images (
    page_section_id, image, image_file_name, image_alt, mobile_image,
    mobile_image_file_name, mobile_image_alt, caption, is_active,
    sort_order, created_at, updated_at
)
SELECT @section_id, 'https://nandinibali.com/storage/images/gallery/pool%20okl.jpg', NULL, 'Nandini Jungle by Hanging Gardens today', NULL, NULL, NULL, NULL, 1, 0, NOW(), NOW()
WHERE @about_page_id IS NOT NULL;

-- 12. Final CTA. Only Plan Your Stay is retained.
INSERT INTO page_sections (
    page_id, section_key, title, subtitle, excerpt, description, items,
    button_label, button_link_type, button_url, button_route, text_align,
    background_color, is_active, sort_order, created_at, updated_at
)
SELECT
    @about_page_id,
    'about_story_final',
    CONCAT('Be Part of', CHAR(10), 'Our Continuing Story'),
    'The Next Chapter',
    NULL,
    '<p>More than two decades after opening its doors, Nandini continues to evolve — shaped by the jungle, its people and every guest who becomes part of its story.</p>',
    '[{"label":"Plan Your Stay","url":"https://nandinijunglebyhanginggardens.reserve-online.net/"}]',
    NULL,
    'manual',
    NULL,
    NULL,
    'left',
    NULL,
    1,
    12,
    NOW(),
    NOW()
WHERE @about_page_id IS NOT NULL;

SET @section_id := LAST_INSERT_ID();

INSERT INTO page_section_images (
    page_section_id, image, image_file_name, image_alt, mobile_image,
    mobile_image_file_name, mobile_image_alt, caption, is_active,
    sort_order, created_at, updated_at
)
SELECT @section_id, 'https://nandinibali.com/storage/images/gallery/JUNGLE%20POOL%20%288%29.jpg', NULL, 'Nandini Jungle pool surrounded by rainforest', NULL, NULL, NULL, NULL, 1, 0, NOW(), NOW()
WHERE @about_page_id IS NOT NULL;

COMMIT;

-- Verification: page_id must not be NULL and section_count should be 12.
SELECT
    @about_page_id AS about_page_id,
    COUNT(*) AS section_count
FROM page_sections
WHERE page_id = @about_page_id
  AND section_key LIKE 'about_story_%';
