-- Nandini Jungle Spa: consolidated shared-hosting database update
-- Target: MySQL 8+ / MariaDB with JSON support
--
-- Includes the complete Spa CMS schema and final database-backed content:
-- hero video/content, SEO metadata, media URLs, opening hours, philosophy,
-- wellness journeys, Spa on the River, benefits, testimonial, booking CTA,
-- WhatsApp links, legacy page state and the current wellness package URL.
--
-- It is safe to rerun. It does not drop or truncate data, but it does reapply
-- the canonical Spa CMS content below to the singleton row with id = 1.
-- Take a database backup before importing any production SQL script.
--
-- Blade/CSS/controller changes (header layout, removed dividers, treatment
-- card display, duration formatting and journey detail routing) must also be
-- deployed with the application files; SQL cannot apply those code changes.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `spa_settings` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `reservation_whatsapp` VARCHAR(255) NULL,
    `reservation_url` TEXT NULL,
    `meta_title` VARCHAR(255) NULL,
    `meta_description` TEXT NULL,
    `meta_author` VARCHAR(255) NULL,
    `meta_site_name` VARCHAR(255) NULL,
    `hero_visible` TINYINT(1) NOT NULL DEFAULT 1,
    `hero_video_id` VARCHAR(255) NULL,
    `hero_image` VARCHAR(255) NULL,
    `hero_mobile_image` VARCHAR(255) NULL,
    `hero_image_alt` VARCHAR(255) NULL,
    `hero_mobile_image_alt` VARCHAR(255) NULL,
    `hero_eyebrow` VARCHAR(255) NULL,
    `hero_heading` TEXT NULL,
    `hero_subheading` TEXT NULL,
    `hero_description` TEXT NULL,
    `hero_primary_cta_label` VARCHAR(255) NULL,
    `hero_primary_cta_url` TEXT NULL,
    `hero_secondary_cta_label` VARCHAR(255) NULL,
    `hero_secondary_cta_url` TEXT NULL,
    `information_bar_visible` TINYINT(1) NOT NULL DEFAULT 1,
    `information_bar_items` JSON NULL,
    `wellness_philosophy_visible` TINYINT(1) NOT NULL DEFAULT 1,
    `wellness_philosophy_eyebrow` VARCHAR(255) NULL,
    `wellness_philosophy_heading` TEXT NULL,
    `wellness_philosophy_description` TEXT NULL,
    `wellness_philosophy_image` VARCHAR(255) NULL,
    `wellness_philosophy_image_alt` VARCHAR(255) NULL,
    `wellness_philosophy_link_label` VARCHAR(255) NULL,
    `wellness_philosophy_link_url` TEXT NULL,
    `wellness_journeys_visible` TINYINT(1) NOT NULL DEFAULT 1,
    `wellness_journeys_eyebrow` VARCHAR(255) NULL,
    `wellness_journeys_heading` TEXT NULL,
    `wellness_journeys_description` TEXT NULL,
    `wellness_journeys_items` JSON NULL,
    `signature_visible` TINYINT(1) NOT NULL DEFAULT 1,
    `signature_eyebrow` VARCHAR(255) NULL,
    `signature_heading` TEXT NULL,
    `signature_description` TEXT NULL,
    `signature_image` VARCHAR(255) NULL,
    `signature_image_alt` VARCHAR(255) NULL,
    `signature_link_label` VARCHAR(255) NULL,
    `signature_link_url` TEXT NULL,
    `why_nandini_visible` TINYINT(1) NOT NULL DEFAULT 1,
    `why_nandini_eyebrow` VARCHAR(255) NULL,
    `why_nandini_heading` TEXT NULL,
    `why_nandini_items` JSON NULL,
    `guest_review_visible` TINYINT(1) NOT NULL DEFAULT 1,
    `guest_review_quote` TEXT NULL,
    `guest_review_label` VARCHAR(255) NULL,
    `guest_review_image` VARCHAR(255) NULL,
    `guest_review_image_alt` VARCHAR(255) NULL,
    `booking_cta_visible` TINYINT(1) NOT NULL DEFAULT 1,
    `booking_cta_eyebrow` VARCHAR(255) NULL,
    `booking_cta_heading` TEXT NULL,
    `booking_cta_description` TEXT NULL,
    `booking_cta_button_label` VARCHAR(255) NULL,
    `booking_cta_button_url` TEXT NULL,
    `booking_cta_image` VARCHAR(255) NULL,
    `booking_cta_image_alt` VARCHAR(255) NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

START TRANSACTION;

INSERT INTO `spa_settings` (
    `id`,
    `reservation_whatsapp`, `reservation_url`,
    `meta_title`, `meta_description`, `meta_author`, `meta_site_name`,
    `hero_visible`, `hero_video_id`, `hero_image`, `hero_mobile_image`,
    `hero_image_alt`, `hero_mobile_image_alt`, `hero_eyebrow`, `hero_heading`,
    `hero_subheading`, `hero_description`, `hero_primary_cta_label`,
    `hero_primary_cta_url`, `hero_secondary_cta_label`, `hero_secondary_cta_url`,
    `information_bar_visible`, `information_bar_items`,
    `wellness_philosophy_visible`, `wellness_philosophy_eyebrow`,
    `wellness_philosophy_heading`, `wellness_philosophy_description`,
    `wellness_philosophy_image`, `wellness_philosophy_image_alt`,
    `wellness_philosophy_link_label`, `wellness_philosophy_link_url`,
    `wellness_journeys_visible`, `wellness_journeys_eyebrow`,
    `wellness_journeys_heading`, `wellness_journeys_description`,
    `wellness_journeys_items`,
    `signature_visible`, `signature_eyebrow`, `signature_heading`,
    `signature_description`, `signature_image`, `signature_image_alt`,
    `signature_link_label`, `signature_link_url`,
    `why_nandini_visible`, `why_nandini_eyebrow`, `why_nandini_heading`,
    `why_nandini_items`,
    `guest_review_visible`, `guest_review_quote`, `guest_review_label`,
    `guest_review_image`, `guest_review_image_alt`,
    `booking_cta_visible`, `booking_cta_eyebrow`, `booking_cta_heading`,
    `booking_cta_description`, `booking_cta_button_label`,
    `booking_cta_button_url`, `booking_cta_image`, `booking_cta_image_alt`,
    `created_at`, `updated_at`
) VALUES (
    1,
    '+62 812 3687 1170',
    'https://wa.me/6281236871170',
    'Spa in Ubud, Bali | Essence Spa at Nandini Jungle',
    'Discover Essence Spa at Nandini Jungle, a jungle spa in Ubud, Bali offering Balinese treatments, riverside wellness experiences and restorative rituals.',
    'Nandini Jungle by Hanging Gardens',
    'Nandini Jungle by Hanging Gardens',
    1,
    'jafQbgUnfL4',
    'https://nandinibali.com/storage/pages/hero/fb4a52d4-35a1-4c29-804d-100e00dd6b89.webp',
    'https://nandinibali.com/storage/pages/hero/fb4a52d4-35a1-4c29-804d-100e00dd6b89.webp',
    'Essence Spa at Nandini Jungle in Ubud, Bali',
    'Essence Spa at Nandini Jungle in Ubud, Bali',
    'WELLNESS AT NANDINI JUNGLE',
    'ESSENCE SPA IN UBUD, BALI',
    'Wellness in the Heart of Nature',
    'Experience deeply restorative spa rituals inspired by Bali, nature and the surrounding jungle. A serene sanctuary to rebalance your body, mind and soul.',
    'BOOK A SPA EXPERIENCE',
    'https://wa.me/6281236871170?text=Hello%2C%20I%20would%20like%20to%20book%20a%20spa%20experience%20at%20Nandini%20Jungle.',
    'EXPLORE TREATMENTS',
    '#treatments',
    1,
    JSON_ARRAY(
        JSON_OBJECT('icon', 'clock', 'label', 'OPENING HOURS', 'value', '09:00 AM – 10:00 PM', 'link', NULL),
        JSON_OBJECT('icon', 'calendar', 'label', 'BOOKING', 'value', 'Advance booking recommended', 'link', NULL),
        JSON_OBJECT('icon', 'location', 'label', 'LOCATION', 'value', 'Nandini Jungle, Payangan, Ubud, Bali', 'link', NULL),
        JSON_OBJECT('icon', 'phone', 'label', 'RESERVATIONS', 'value', '+62 812 3687 1170', 'link', 'https://wa.me/6281236871170')
    ),
    1,
    'OUR WELLNESS PHILOSOPHY',
    CONCAT('A SACRED PAUSE', CHAR(10), 'IN THE JUNGLE'),
    'At Nandini Jungle, wellness is a harmonious journey of body, mind and spirit, inspired by Balinese traditions and the healing power of nature. Our spa experiences invite you to slow down, reconnect and embrace a deeper sense of wellbeing.',
    'https://nandinibali.com/storage/pages/sections/7bdab6e8-62b3-416a-85fb-3419a6a15ee8.webp',
    'Nandini Spa jacuzzi surrounded by tropical jungle',
    NULL,
    NULL,
    1,
    'WELLNESS JOURNEYS',
    'SACRED JUNGLE WELLNESS JOURNEYS',
    'Reconnect with your inner self through immersive multi-day experiences, combining traditional Balinese therapy, natural healing and the serene beauty of Nandini Jungle.',
    JSON_ARRAY(
        JSON_OBJECT(
            'title', '2-DAY BALINESE WELLNESS ESCAPE',
            'description', 'A two-day journey to revive your energy through a curated blend of Balinese massage, herbal rituals and time in nature.',
            'image', 'https://nandinibali.com/storage/spas/hero/68d37345-f6e6-4f1d-a962-725cf049fe62.webp',
            'image_alt', 'Balinese massage treatment surrounded by the Nandini jungle',
            'details_label', 'MORE DETAILS',
            'details_url', 'https://spa.nandinibali.com/spa-wellness/2-day-balinese-wellness-escape',
            'book_label', 'BOOK NOW',
            'book_url', 'https://wa.me/6281236871170'
        ),
        JSON_OBJECT(
            'title', '3-DAY INNER HARMONY RETREAT',
            'description', 'A three-day retreat to restore balance and reconnect with yourself through signature treatments, holistic therapies and mindful rituals.',
            'image', 'https://nandinibali.com/storage/spas/hero/b5490cd9-d622-4ce2-b483-992ef4ea0c3c.webp',
            'image_alt', 'Jungle spa treatment beds prepared for an inner harmony retreat',
            'details_label', 'MORE DETAILS',
            'details_url', 'https://spa.nandinibali.com/spa-wellness/3-day-inner-harmony-retreat',
            'book_label', 'BOOK NOW',
            'book_url', 'https://wa.me/6281236871170'
        ),
        JSON_OBJECT(
            'title', '4-DAY DEEP BALINESE WELLNESS IMMERSION',
            'description', 'A four-day immersive experience designed for deep relaxation and renewal, with a combination of traditional therapies, wellness rituals and personalised care.',
            'image', 'https://nandinibali.com/storage/spas/hero/19d9f7d8-6a93-422d-8a13-b333a2384ff8.webp',
            'image_alt', 'Flower bath ritual for a deep Balinese wellness immersion',
            'details_label', 'MORE DETAILS',
            'details_url', 'https://spa.nandinibali.com/spa-wellness/4-day-deep-balinese-wellness-immersion',
            'book_label', 'BOOK NOW',
            'book_url', 'https://wa.me/6281236871170'
        )
    ),
    1,
    'A UNIQUE SETTING',
    'SPA ON THE RIVER',
    'Our signature riverside spa brings wellness closer to the natural rhythm of the jungle. Surrounded by tropical greenery and the sound of flowing water, each treatment becomes a deeply immersive moment of calm, connection and renewal.',
    'https://nandinibali.com/storage/pages/sections/ea97fbfe-7ca1-43fe-8e30-51b1e9dcea01.webp',
    'Spa on the River at Nandini Jungle',
    'BOOK NOW',
    'https://wa.me/6281236871170?text=Hello%2C%20I%20would%20like%20to%20book%20the%20Spa%20on%20the%20River%20experience%20at%20Nandini%20Jungle.',
    1,
    'WHY NANDINI',
    'WELLNESS ROOTED IN NATURE',
    JSON_ARRAY(
        JSON_OBJECT('icon', 'jungle', 'title', 'JUNGLE SANCTUARY', 'description', 'Treatments surrounded by tropical nature.'),
        JSON_OBJECT('icon', 'ritual', 'title', 'BALINESE RITUALS', 'description', 'Wellness inspired by traditional Balinese practices.'),
        JSON_OBJECT('icon', 'care', 'title', 'PERSONALISED CARE', 'description', 'Experiences tailored to individual wellbeing.'),
        JSON_OBJECT('icon', 'river', 'title', 'RIVER-SIDE SERENITY', 'description', 'A unique spa environment shaped by the jungle landscape.')
    ),
    1,
    'The most peaceful and healing spa experience. The sound of the river, the jungle, and the care from the therapists made it truly special.',
    'GUEST EXPERIENCE',
    'https://nandinibali.com/storage/pages/sections/7bdab6e8-62b3-416a-85fb-3419a6a15ee8.webp',
    'Spa experience at Nandini Jungle',
    1,
    'YOUR WELLNESS JOURNEY AWAITS',
    'BOOK YOUR SPA EXPERIENCE',
    'Step away from the everyday and reconnect with nature through a restorative Nandini Jungle Spa experience.',
    'BOOK NOW',
    'https://wa.me/6281236871170?text=Hello%2C%20I%20would%20like%20to%20book%20a%20spa%20experience%20at%20Nandini%20Jungle.',
    'https://nandinibali.com/storage/pages/sections/ea97fbfe-7ca1-43fe-8e30-51b1e9dcea01.webp',
    'Spa on the River surrounded by tropical jungle',
    NOW(),
    NOW()
)
ON DUPLICATE KEY UPDATE
    `reservation_whatsapp` = VALUES(`reservation_whatsapp`),
    `reservation_url` = VALUES(`reservation_url`),
    `meta_title` = VALUES(`meta_title`),
    `meta_description` = VALUES(`meta_description`),
    `meta_author` = VALUES(`meta_author`),
    `meta_site_name` = VALUES(`meta_site_name`),
    `hero_visible` = VALUES(`hero_visible`),
    `hero_video_id` = VALUES(`hero_video_id`),
    `hero_image` = VALUES(`hero_image`),
    `hero_mobile_image` = VALUES(`hero_mobile_image`),
    `hero_image_alt` = VALUES(`hero_image_alt`),
    `hero_mobile_image_alt` = VALUES(`hero_mobile_image_alt`),
    `hero_eyebrow` = VALUES(`hero_eyebrow`),
    `hero_heading` = VALUES(`hero_heading`),
    `hero_subheading` = VALUES(`hero_subheading`),
    `hero_description` = VALUES(`hero_description`),
    `hero_primary_cta_label` = VALUES(`hero_primary_cta_label`),
    `hero_primary_cta_url` = VALUES(`hero_primary_cta_url`),
    `hero_secondary_cta_label` = VALUES(`hero_secondary_cta_label`),
    `hero_secondary_cta_url` = VALUES(`hero_secondary_cta_url`),
    `information_bar_visible` = VALUES(`information_bar_visible`),
    `information_bar_items` = VALUES(`information_bar_items`),
    `wellness_philosophy_visible` = VALUES(`wellness_philosophy_visible`),
    `wellness_philosophy_eyebrow` = VALUES(`wellness_philosophy_eyebrow`),
    `wellness_philosophy_heading` = VALUES(`wellness_philosophy_heading`),
    `wellness_philosophy_description` = VALUES(`wellness_philosophy_description`),
    `wellness_philosophy_image` = VALUES(`wellness_philosophy_image`),
    `wellness_philosophy_image_alt` = VALUES(`wellness_philosophy_image_alt`),
    `wellness_philosophy_link_label` = VALUES(`wellness_philosophy_link_label`),
    `wellness_philosophy_link_url` = VALUES(`wellness_philosophy_link_url`),
    `wellness_journeys_visible` = VALUES(`wellness_journeys_visible`),
    `wellness_journeys_eyebrow` = VALUES(`wellness_journeys_eyebrow`),
    `wellness_journeys_heading` = VALUES(`wellness_journeys_heading`),
    `wellness_journeys_description` = VALUES(`wellness_journeys_description`),
    `wellness_journeys_items` = VALUES(`wellness_journeys_items`),
    `signature_visible` = VALUES(`signature_visible`),
    `signature_eyebrow` = VALUES(`signature_eyebrow`),
    `signature_heading` = VALUES(`signature_heading`),
    `signature_description` = VALUES(`signature_description`),
    `signature_image` = VALUES(`signature_image`),
    `signature_image_alt` = VALUES(`signature_image_alt`),
    `signature_link_label` = VALUES(`signature_link_label`),
    `signature_link_url` = VALUES(`signature_link_url`),
    `why_nandini_visible` = VALUES(`why_nandini_visible`),
    `why_nandini_eyebrow` = VALUES(`why_nandini_eyebrow`),
    `why_nandini_heading` = VALUES(`why_nandini_heading`),
    `why_nandini_items` = VALUES(`why_nandini_items`),
    `guest_review_visible` = VALUES(`guest_review_visible`),
    `guest_review_quote` = VALUES(`guest_review_quote`),
    `guest_review_label` = VALUES(`guest_review_label`),
    `guest_review_image` = VALUES(`guest_review_image`),
    `guest_review_image_alt` = VALUES(`guest_review_image_alt`),
    `booking_cta_visible` = VALUES(`booking_cta_visible`),
    `booking_cta_eyebrow` = VALUES(`booking_cta_eyebrow`),
    `booking_cta_heading` = VALUES(`booking_cta_heading`),
    `booking_cta_description` = VALUES(`booking_cta_description`),
    `booking_cta_button_label` = VALUES(`booking_cta_button_label`),
    `booking_cta_button_url` = VALUES(`booking_cta_button_url`),
    `booking_cta_image` = VALUES(`booking_cta_image`),
    `booking_cta_image_alt` = VALUES(`booking_cta_image_alt`),
    `updated_at` = NOW();

-- Keep the existing main-domain landing page public until its SEO migration
-- is approved separately.
UPDATE `pages`
SET `is_active` = 1, `include_in_sitemap` = 1, `updated_at` = NOW()
WHERE `slug` = 'spa-wellness';

-- Remove the expired static check-in date from the authoritative package row.
UPDATE `spas`
SET
    `booking_url_override` = 'https://nandinijunglebyhanginggardens.reserve-online.net/?nights=3&bkcode=wellness',
    `button_url` = 'https://nandinijunglebyhanginggardens.reserve-online.net/?nights=3&bkcode=wellness',
    `updated_at` = NOW()
WHERE `slug` = 'a-mystical-journey-at-nandini-4d3n-wellness-retreat';

-- Prevent Laravel from replaying the equivalent migrations after this manual
-- shared-hosting import. Existing migration records are left untouched.
SET @spa_migration_batch := (SELECT COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT `pending`.`migration`, @spa_migration_batch
FROM (
    SELECT '2026_09_15_000003_create_spa_settings_table' AS `migration`
    UNION ALL SELECT '2026_09_15_000004_add_wellness_philosophy_to_spa_settings'
    UNION ALL SELECT '2026_09_15_000005_remove_whatsapp_suffix_from_spa_information_bar'
    UNION ALL SELECT '2026_09_15_000006_add_why_nandini_to_spa_settings'
    UNION ALL SELECT '2026_09_15_000007_add_wellness_journeys_to_spa_settings'
    UNION ALL SELECT '2026_09_22_000002_complete_spa_landing_page'
    UNION ALL SELECT '2026_09_22_000003_normalize_spa_seed_media_urls'
    UNION ALL SELECT '2026_09_22_000004_rename_spa_hero_to_essence_spa'
    UNION ALL SELECT '2026_09_25_000001_add_video_to_spa_hero'
    UNION ALL SELECT '2026_10_08_000001_update_spa_signature_booking_cta'
    UNION ALL SELECT '2026_10_08_000003_standardize_spa_opening_hours'
) AS `pending`
LEFT JOIN `migrations` AS `existing`
    ON `existing`.`migration` = `pending`.`migration`
WHERE `existing`.`migration` IS NULL;

COMMIT;

-- Verification result: expect the canonical SEO/content values and the
-- existing media paths on the singleton row.
SELECT
    `id`,
    `reservation_whatsapp`,
    `reservation_url`,
    `meta_title`,
    `meta_description`,
    `hero_video_id`,
    `hero_heading`,
    `hero_image_alt`,
    `hero_primary_cta_label`,
    `hero_primary_cta_url`,
    `hero_secondary_cta_label`,
    `hero_secondary_cta_url`,
    `information_bar_items`,
    `hero_image`,
    `wellness_philosophy_image`,
    `wellness_journeys_items`,
    `signature_image`,
    `signature_link_label`,
    `signature_link_url`,
    `guest_review_image`,
    `booking_cta_image`,
    `booking_cta_button_label`,
    `booking_cta_button_url`
FROM `spa_settings`
WHERE `id` = 1;

-- These are the authoritative records rendered in the homepage treatment
-- section. The application displays the first six active spa vouchers in
-- sort order, with no invented treatment data.
SELECT
    `title`, `slug`, `excerpt`, `selling_price`, `currency`, `price_type`,
    `unit_type`, `sort_order`
FROM `vouchers`
WHERE `is_active` = 1
  AND `deleted_at` IS NULL
  AND `voucher_type` = 'spa'
ORDER BY `sort_order`, `title`
LIMIT 6;
