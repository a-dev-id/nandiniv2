-- Essence Spa homepage SEO/content patch for shared hosting
-- Target: MySQL 8+ / MariaDB with JSON support
-- Safe to rerun. Take a database backup before importing.

SET NAMES utf8mb4;

START TRANSACTION;

-- Update the singleton SPA CMS record that supplies the rendered metadata,
-- hero content, image alt text, opening hours and location.
UPDATE `spa_settings`
SET
    `meta_title` = 'Spa in Ubud, Bali | Essence Spa at Nandini Jungle',
    `meta_description` = 'Discover Essence Spa at Nandini Jungle, a jungle spa in Ubud, Bali offering Balinese treatments, riverside wellness experiences and restorative rituals.',
    `hero_heading` = 'ESSENCE SPA IN UBUD, BALI',
    `hero_image_alt` = 'Essence Spa at Nandini Jungle in Ubud, Bali',
    `hero_mobile_image_alt` = 'Essence Spa at Nandini Jungle in Ubud, Bali',
    `hero_secondary_cta_label` = 'EXPLORE TREATMENTS',
    `hero_secondary_cta_url` = '#treatments',
    `information_bar_items` = JSON_ARRAY(
        JSON_OBJECT(
            'icon', 'clock',
            'label', 'OPENING HOURS',
            'value', '09:00 AM – 10:00 PM',
            'link', NULL
        ),
        JSON_OBJECT(
            'icon', 'calendar',
            'label', 'BOOKING',
            'value', 'Advance booking recommended',
            'link', NULL
        ),
        JSON_OBJECT(
            'icon', 'location',
            'label', 'LOCATION',
            'value', 'Nandini Jungle, Payangan, Ubud, Bali',
            'link', NULL
        ),
        JSON_OBJECT(
            'icon', 'phone',
            'label', 'RESERVATIONS',
            'value', '+62 812 3687 1170',
            'link', 'https://wa.me/6281236871170'
        )
    ),
    `updated_at` = NOW()
ORDER BY `id`
LIMIT 1;

-- Keep the established main-domain SPA landing page public and eligible for
-- the main sitemap. The matching non-redirect route must also be deployed.
UPDATE `pages`
SET
    `is_active` = 1,
    `include_in_sitemap` = 1,
    `updated_at` = NOW()
WHERE `slug` = 'spa-wellness';

-- Remove the expired static check-in date from the authoritative package row.
UPDATE `spas`
SET
    `booking_url_override` = 'https://nandinijunglebyhanginggardens.reserve-online.net/?nights=3&bkcode=wellness',
    `button_url` = 'https://nandinijunglebyhanginggardens.reserve-online.net/?nights=3&bkcode=wellness',
    `updated_at` = NOW()
WHERE `slug` = 'a-mystical-journey-at-nandini-4d3n-wellness-retreat';

COMMIT;

-- Verification: the first result must show the new SEO, H1, alt text,
-- opening hours, location and #treatments anchor.
SELECT
    `id`,
    `meta_title`,
    `meta_description`,
    `hero_heading`,
    `hero_image_alt`,
    `hero_mobile_image_alt`,
    `hero_secondary_cta_label`,
    `hero_secondary_cta_url`,
    `information_bar_items`,
    `updated_at`
FROM `spa_settings`
ORDER BY `id`
LIMIT 1;

-- Verification: expect is_active = 1 and include_in_sitemap = 1.
SELECT
    `id`, `slug`, `is_active`, `include_in_sitemap`, `updated_at`
FROM `pages`
WHERE `slug` = 'spa-wellness';

-- Verification: expect no checkin or empty rate query parameters.
SELECT
    `id`, `slug`, `booking_url_override`, `button_url`, `updated_at`
FROM `spas`
WHERE `slug` = 'a-mystical-journey-at-nandini-4d3n-wellness-retreat';

-- Verification: these are the first six authoritative treatments that the
-- updated application renders on the SPA homepage.
SELECT
    `title`,
    `slug`,
    `excerpt`,
    `selling_price`,
    `currency`,
    `price_type`,
    `unit_type`,
    `sort_order`
FROM `vouchers`
WHERE `is_active` = 1
  AND `deleted_at` IS NULL
  AND `voucher_type` = 'spa'
ORDER BY `sort_order`, `title`
LIMIT 6;
