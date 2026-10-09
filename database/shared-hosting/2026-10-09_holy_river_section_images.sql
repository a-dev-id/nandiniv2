-- Nandini Jungle by Hanging Gardens
-- Store the four Holy River page images in the CMS database.
--
-- Import this file into the website database using phpMyAdmin after deploying
-- the corresponding files in public/images/holy-river.
-- The script is idempotent: it updates the first active image for each target
-- section and inserts the section/image only when it is missing.
-- Take a database backup before importing.

SET NAMES utf8mb4;

START TRANSACTION;

SET @holy_river_page_id := (
    SELECT `id`
    FROM `pages`
    WHERE `page_name` = 'Holy River Page'
       OR `slug` = 'holy-river'
    ORDER BY CASE WHEN `page_name` = 'Holy River Page' THEN 0 ELSE 1 END, `id`
    LIMIT 1
);

SET @next_page_section_id := COALESCE((SELECT MAX(`id`) FROM `page_sections`), 0);
SET @next_page_section_image_id := COALESCE((SELECT MAX(`id`) FROM `page_section_images`), 0);

-- A Sacred Setting by the Ayung River.
SET @ayung_section_id := (
    SELECT `id`
    FROM `page_sections`
    WHERE `page_id` = @holy_river_page_id
      AND `title` = 'A SACRED SETTING BY THE AYUNG RIVER'
    ORDER BY `id`
    LIMIT 1
);

SET @ayung_image_id := (
    SELECT `id`
    FROM `page_section_images`
    WHERE `page_section_id` = @ayung_section_id
      AND `is_active` = 1
    ORDER BY `sort_order`, `id`
    LIMIT 1
);

UPDATE `page_section_images`
SET `image` = '/images/holy-river/A-SACRED-SETTING-BY-THE-AYUNG-RIVER.jpg',
    `image_file_name` = 'A-SACRED-SETTING-BY-THE-AYUNG-RIVER',
    `image_alt` = 'Sacred riverside deck surrounded by tropical jungle at Nandini Jungle',
    `mobile_image` = NULL,
    `mobile_image_file_name` = NULL,
    `mobile_image_alt` = NULL,
    `is_active` = 1,
    `sort_order` = 0,
    `updated_at` = NOW()
WHERE `id` = @ayung_image_id;

INSERT INTO `page_section_images`
    (`id`, `page_section_id`, `image`, `image_file_name`, `image_alt`, `mobile_image`, `mobile_image_file_name`, `mobile_image_alt`, `caption`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    (@next_page_section_image_id := @next_page_section_image_id + 1),
    @ayung_section_id,
    '/images/holy-river/A-SACRED-SETTING-BY-THE-AYUNG-RIVER.jpg',
    'A-SACRED-SETTING-BY-THE-AYUNG-RIVER',
    'Sacred riverside deck surrounded by tropical jungle at Nandini Jungle',
    NULL, NULL, NULL, NULL, 1, 0, NOW(), NOW()
WHERE @ayung_section_id IS NOT NULL
  AND @ayung_image_id IS NULL;

-- Balinese Blessing & Purification.
SET @blessing_section_id := (
    SELECT `id`
    FROM `page_sections`
    WHERE `page_id` = @holy_river_page_id
      AND `title` = 'BALINESE BLESSING & PURIFICATION'
    ORDER BY `id`
    LIMIT 1
);

SET @blessing_image_id := (
    SELECT `id`
    FROM `page_section_images`
    WHERE `page_section_id` = @blessing_section_id
      AND `is_active` = 1
    ORDER BY `sort_order`, `id`
    LIMIT 1
);

UPDATE `page_section_images`
SET `image` = '/images/holy-river/BALINESE-BLESSING-&-PURIFICATION.jpg',
    `image_file_name` = 'BALINESE-BLESSING-&-PURIFICATION',
    `image_alt` = 'Balinese blessing and purification ceremony beside the Ayung River',
    `mobile_image` = NULL,
    `mobile_image_file_name` = NULL,
    `mobile_image_alt` = NULL,
    `is_active` = 1,
    `sort_order` = 0,
    `updated_at` = NOW()
WHERE `id` = @blessing_image_id;

INSERT INTO `page_section_images`
    (`id`, `page_section_id`, `image`, `image_file_name`, `image_alt`, `mobile_image`, `mobile_image_file_name`, `mobile_image_alt`, `caption`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    (@next_page_section_image_id := @next_page_section_image_id + 1),
    @blessing_section_id,
    '/images/holy-river/BALINESE-BLESSING-&-PURIFICATION.jpg',
    'BALINESE-BLESSING-&-PURIFICATION',
    'Balinese blessing and purification ceremony beside the Ayung River',
    NULL, NULL, NULL, NULL, 1, 0, NOW(), NOW()
WHERE @blessing_section_id IS NOT NULL
  AND @blessing_image_id IS NULL;

-- Spa on the River.
SET @spa_section_id := (
    SELECT `id`
    FROM `page_sections`
    WHERE `page_id` = @holy_river_page_id
      AND `title` = 'SPA ON THE RIVER'
    ORDER BY `id`
    LIMIT 1
);

SET @spa_image_id := (
    SELECT `id`
    FROM `page_section_images`
    WHERE `page_section_id` = @spa_section_id
      AND `is_active` = 1
    ORDER BY `sort_order`, `id`
    LIMIT 1
);

UPDATE `page_section_images`
SET `image` = '/images/holy-river/SPA ON THE RIVER.webp',
    `image_file_name` = 'SPA ON THE RIVER',
    `image_alt` = 'Spa treatment beds beside the Ayung River at Nandini Jungle',
    `mobile_image` = NULL,
    `mobile_image_file_name` = NULL,
    `mobile_image_alt` = NULL,
    `is_active` = 1,
    `sort_order` = 0,
    `updated_at` = NOW()
WHERE `id` = @spa_image_id;

INSERT INTO `page_section_images`
    (`id`, `page_section_id`, `image`, `image_file_name`, `image_alt`, `mobile_image`, `mobile_image_file_name`, `mobile_image_alt`, `caption`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    (@next_page_section_image_id := @next_page_section_image_id + 1),
    @spa_section_id,
    '/images/holy-river/SPA ON THE RIVER.webp',
    'SPA ON THE RIVER',
    'Spa treatment beds beside the Ayung River at Nandini Jungle',
    NULL, NULL, NULL, NULL, 1, 0, NOW(), NOW()
WHERE @spa_section_id IS NOT NULL
  AND @spa_image_id IS NULL;

-- Create the editable final booking CTA section when it does not exist.
INSERT INTO `page_sections`
    (`id`, `page_id`, `section_key`, `title`, `subtitle`, `excerpt`, `description`, `items`, `button_label`, `button_link_type`, `button_url`, `button_route`, `text_align`, `background_color`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    (@next_page_section_id := @next_page_section_id + 1),
    @holy_river_page_id,
    'holy_river_booking_cta',
    'PLAN YOUR HOLY RIVER EXPERIENCE',
    'HOLY RIVER AT NANDINI',
    NULL,
    '<p>Experience Balinese purification, traditional blessings and the peaceful setting of the Ayung River at Nandini Jungle.</p>',
    NULL,
    'RESERVE',
    'manual',
    'https://wa.me/6281236871170?text=Hello%2C%20I%20would%20like%20to%20enquire%20about%20the%20Holy%20River%20and%20Balinese%20purification%20experiences%20at%20Nandini%20Jungle.',
    NULL,
    'center',
    NULL,
    1,
    6,
    NOW(),
    NOW()
WHERE @holy_river_page_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1
      FROM `page_sections`
      WHERE `page_id` = @holy_river_page_id
        AND `section_key` = 'holy_river_booking_cta'
  );

SET @booking_cta_section_id := (
    SELECT `id`
    FROM `page_sections`
    WHERE `page_id` = @holy_river_page_id
      AND `section_key` = 'holy_river_booking_cta'
    ORDER BY `id`
    LIMIT 1
);

SET @booking_cta_image_id := (
    SELECT `id`
    FROM `page_section_images`
    WHERE `page_section_id` = @booking_cta_section_id
      AND `is_active` = 1
    ORDER BY `sort_order`, `id`
    LIMIT 1
);

UPDATE `page_section_images`
SET `image` = '/images/holy-river/PLAN YOUR HOLY RIVER EXPERIENCE.webp',
    `image_file_name` = 'PLAN YOUR HOLY RIVER EXPERIENCE',
    `image_alt` = 'Guest meditating beside the Holy River at Nandini Jungle',
    `mobile_image` = NULL,
    `mobile_image_file_name` = NULL,
    `mobile_image_alt` = NULL,
    `is_active` = 1,
    `sort_order` = 0,
    `updated_at` = NOW()
WHERE `id` = @booking_cta_image_id;

INSERT INTO `page_section_images`
    (`id`, `page_section_id`, `image`, `image_file_name`, `image_alt`, `mobile_image`, `mobile_image_file_name`, `mobile_image_alt`, `caption`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    (@next_page_section_image_id := @next_page_section_image_id + 1),
    @booking_cta_section_id,
    '/images/holy-river/PLAN YOUR HOLY RIVER EXPERIENCE.webp',
    'PLAN YOUR HOLY RIVER EXPERIENCE',
    'Guest meditating beside the Holy River at Nandini Jungle',
    NULL, NULL, NULL, NULL, 1, 0, NOW(), NOW()
WHERE @booking_cta_section_id IS NOT NULL
  AND @booking_cta_image_id IS NULL;

-- Mark the matching Laravel migration as applied when this SQL is imported
-- directly, preventing a later deployment from applying it a second time.
INSERT INTO `migrations` (`id`, `migration`, `batch`)
SELECT
    COALESCE((SELECT MAX(`m`.`id`) FROM `migrations` AS `m`), 0) + 1,
    '2026_10_09_000001_store_holy_river_section_images',
    COALESCE((SELECT MAX(`m`.`batch`) FROM `migrations` AS `m`), 0) + 1
WHERE NOT EXISTS (
    SELECT 1
    FROM `migrations`
    WHERE `migration` = '2026_10_09_000001_store_holy_river_section_images'
)
  AND @holy_river_page_id IS NOT NULL;

COMMIT;

-- Verification: these queries should return one page, four section rows and
-- four active image rows using /images/holy-river paths.
SELECT `id`, `page_name`, `slug`
FROM `pages`
WHERE `id` = @holy_river_page_id;

SELECT `id`, `section_key`, `title`, `sort_order`, `is_active`
FROM `page_sections`
WHERE `id` IN (
    @ayung_section_id,
    @blessing_section_id,
    @spa_section_id,
    @booking_cta_section_id
)
ORDER BY `sort_order`, `id`;

SELECT
    `page_sections`.`title`,
    `page_section_images`.`image`,
    `page_section_images`.`image_alt`,
    `page_section_images`.`is_active`
FROM `page_section_images`
INNER JOIN `page_sections`
    ON `page_sections`.`id` = `page_section_images`.`page_section_id`
WHERE `page_sections`.`page_id` = @holy_river_page_id
  AND `page_section_images`.`image` LIKE '/images/holy-river/%'
ORDER BY `page_sections`.`sort_order`, `page_section_images`.`sort_order`;
