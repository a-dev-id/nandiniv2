-- Nandini: adventure travel time and Settings > Homepage (MySQL / MariaDB).
-- Covers the three 2026_11_06 Laravel migrations created for these features.
-- Select the Nandini database in phpMyAdmin before importing this file.
-- Deploy the corresponding Laravel/Filament/Blade files as well; SQL alone
-- cannot install the Settings > Homepage menu or change the rendered cards.
-- After deployment, run: php artisan optimize:clear
--
-- Existing homepage edits and populated travel times are preserved.
-- Only Ayung White Water Rafting has a confirmed stored value: 15 minutes.
-- Mount Batur and other activities remain unset pending value confirmation.
-- Migration history is recorded so later artisan migrate runs skip these changes.
-- Safe to rerun, including after a travel time was intentionally cleared in CMS.
-- DDL commits implicitly in MySQL/MariaDB. Stop the import on any SQL error.

SET NAMES utf8mb4;

-- 1. Add the generic numeric travel-time column if it is not already installed.
SET @nandini_travel_column_exists := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'experiences'
      AND COLUMN_NAME = 'travel_time_minutes'
);
SET @nandini_travel_ddl := IF(
    @nandini_travel_column_exists = 0,
    'ALTER TABLE `experiences` ADD COLUMN `travel_time_minutes` SMALLINT UNSIGNED NULL DEFAULT NULL AFTER `duration`',
    'SELECT ''travel_time_minutes already exists; preserved'' AS `status`'
);
PREPARE nandini_travel_statement FROM @nandini_travel_ddl;
EXECUTE nandini_travel_statement;
DEALLOCATE PREPARE nandini_travel_statement;

-- 2. Store homepage copy separately from existing page and experience content.
CREATE TABLE IF NOT EXISTS `homepage_settings` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `villas_heading` VARCHAR(255) NOT NULL,
    `villas_description` TEXT NOT NULL,
    `villas_action_label` VARCHAR(100) NOT NULL,
    `experiences_heading` VARCHAR(255) NOT NULL,
    `experiences_description` TEXT NOT NULL,
    `experiences_action_label` VARCHAR(100) NOT NULL,
    `offers_heading` VARCHAR(255) NOT NULL,
    `offers_description` TEXT NOT NULL,
    `offers_action_label` VARCHAR(100) NOT NULL,
    `dining_heading` VARCHAR(255) NOT NULL,
    `dining_description` TEXT NOT NULL,
    `dining_action_label` VARCHAR(100) NOT NULL,
    `spa_heading` VARCHAR(255) NOT NULL,
    `spa_description` TEXT NOT NULL,
    `spa_action_label` VARCHAR(100) NOT NULL,
    `adventures_heading` VARCHAR(255) NOT NULL,
    `adventures_description` TEXT NOT NULL,
    `adventures_action_label` VARCHAR(100) NOT NULL,
    `experiences_footer_label` VARCHAR(100) NOT NULL,
    `offers_footer_label` VARCHAR(100) NOT NULL,
    `adventures_footer_label` VARCHAR(100) NOT NULL,
    `featured_suite_action_label` VARCHAR(100) NOT NULL,
    `guest_reviews_heading` VARCHAR(255) NOT NULL,
    `guest_reviews_action_label` VARCHAR(100) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

START TRANSACTION;
SET @nandini_update_now := UTC_TIMESTAMP();

-- 3. Apply the approved travel-time backfill only on its first installation.
SET @nandini_apply_travel_backfill := NOT EXISTS (
    SELECT 1 FROM `migrations`
    WHERE `migration` = '2026_11_06_000002_backfill_approved_adventure_travel_times'
);

UPDATE `experiences` AS e
INNER JOIN `experience_categories` AS c ON c.`id` = e.`experience_category_id`
SET e.`travel_time_minutes` = 15
WHERE @nandini_apply_travel_backfill = 1
  AND e.`slug` = 'ayung-white-water-rafting'
  AND c.`slug` = 'ubud-jungle-adventures'
  AND e.`travel_time_minutes` IS NULL;

-- 4. Prefill all 24 editable text fields only if settings record 1 is absent.
INSERT INTO `homepage_settings` (
    `id`,
    `villas_heading`, `villas_description`, `villas_action_label`,
    `experiences_heading`, `experiences_description`, `experiences_action_label`,
    `offers_heading`, `offers_description`, `offers_action_label`,
    `dining_heading`, `dining_description`, `dining_action_label`,
    `spa_heading`, `spa_description`, `spa_action_label`,
    `adventures_heading`, `adventures_description`, `adventures_action_label`,
    `experiences_footer_label`, `offers_footer_label`, `adventures_footer_label`,
    `featured_suite_action_label`, `guest_reviews_heading`, `guest_reviews_action_label`,
    `created_at`, `updated_at`
)
SELECT
    1,
    'Private Jungle Villas & Royal Suites',
    'Stay in private jungle villas in Ubud and spacious royal suites surrounded by rainforest, tropical greenery, and the calm atmosphere of Bali. Each accommodation at Nandini Jungle by Hanging Gardens is designed for comfort, privacy, and warm Balinese character, creating a refined jungle retreat for couples, families, and guests seeking space to reconnect with nature.',
    'More Details',
    'Experiences Beyond the Stay',
    'Enhance your stay at Nandini Jungle by Hanging Gardens with curated experiences in the heart of Bali''s rainforest. From romantic dining and jungle celebrations to floating breakfast and calming spa rituals, each experience is designed to enrich your Ubud retreat with nature, romance, and authentic Balinese hospitality.',
    'Explore More',
    'Curated Escapes at Nandini Jungle',
    'Discover exclusive offers at Nandini Jungle by Hanging Gardens, created for guests who want more than a short getaway. From extended stays to twin-island escapes, each package combines nature, comfort, and meaningful experiences for a memorable Bali jungle resort stay.',
    'More Details',
    'Culinary Journeys',
    'Dine surrounded by rainforest views and warm Balinese hospitality at Nandini Jungle by Hanging Gardens. From authentic flavors to afternoon tea and relaxed drinks, every culinary experience is crafted to complement your stay at a peaceful jungle resort in Ubud.',
    'Explore More',
    'Jungle Spa & Wellness',
    'Restore body, mind, and spirit at Essence Spa, where each treatment is inspired by the natural surroundings of Nandini Jungle by Hanging Gardens. Set within a quiet rainforest environment, our spa experiences combine nature, relaxation, and Balinese wellness traditions for a calming wellness retreat in Ubud.',
    'Explore More',
    'Bali Adventure Journeys',
    'Discover Bali''s natural landscapes beyond Nandini Jungle by Hanging Gardens. From Ayung River rafting and ATV adventures to Mount Batur sunrise tours, each journey offers a different way to explore the island, with convenient access from your jungle retreat in Payangan, near Ubud.',
    'More Details',
    'Explore More', 'Explore More', 'Explore More',
    'More Details', 'What Our Guests Say', 'See More',
    @nandini_update_now, @nandini_update_now
WHERE NOT EXISTS (SELECT 1 FROM `homepage_settings` WHERE `id` = 1);

-- 5. Record equivalent Laravel migrations. Explicit IDs also support older
-- shared-hosting migration tables that have no AUTO_INCREMENT attribute.
SET @nandini_update_batch := (SELECT COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`);
SET @nandini_migration_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `migrations`);
INSERT INTO `migrations` (`id`, `migration`, `batch`)
SELECT @nandini_migration_id, '2026_11_06_000001_add_travel_time_minutes_to_experiences_table', @nandini_update_batch
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations`
    WHERE `migration` = '2026_11_06_000001_add_travel_time_minutes_to_experiences_table'
);

SET @nandini_migration_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `migrations`);
INSERT INTO `migrations` (`id`, `migration`, `batch`)
SELECT @nandini_migration_id, '2026_11_06_000002_backfill_approved_adventure_travel_times', @nandini_update_batch
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations`
    WHERE `migration` = '2026_11_06_000002_backfill_approved_adventure_travel_times'
);

SET @nandini_migration_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `migrations`);
INSERT INTO `migrations` (`id`, `migration`, `batch`)
SELECT @nandini_migration_id, '2026_11_06_000003_create_homepage_settings_table', @nandini_update_batch
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations`
    WHERE `migration` = '2026_11_06_000003_create_homepage_settings_table'
);

COMMIT;

-- 6. Verification: public experience data and homepage copy only.
SELECT e.`id`, e.`title`, e.`slug`, e.`travel_time_minutes`
FROM `experiences` AS e
INNER JOIN `experience_categories` AS c ON c.`id` = e.`experience_category_id`
WHERE c.`slug` = 'ubud-jungle-adventures'
ORDER BY e.`id`;

SELECT * FROM `homepage_settings` WHERE `id` = 1;

SELECT `migration`, `batch` FROM `migrations`
WHERE `migration` IN (
    '2026_11_06_000001_add_travel_time_minutes_to_experiences_table',
    '2026_11_06_000002_backfill_approved_adventure_travel_times',
    '2026_11_06_000003_create_homepage_settings_table'
)
ORDER BY `migration`;
