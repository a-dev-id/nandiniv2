-- Nandini Membership shared-hosting database update
-- Target: MySQL / MariaDB
-- Back up the database before running this script.

SET @schema_name = DATABASE();

-- 1. Update the Riverside Bliss homepage button label.
UPDATE `page_sections`
SET `button_label` = 'More Details'
WHERE `page_id` = 1
  AND `section_key` = 'image_overlay_section'
  AND `title` = 'Riverside Bliss: Half-Day Picnic and Wellness Escape at the River';

-- 2. Add the Guest Review excerpt column when it does not already exist.
SET @excerpt_exists = (
    SELECT COUNT(*)
    FROM `information_schema`.`COLUMNS`
    WHERE `TABLE_SCHEMA` = @schema_name
      AND `TABLE_NAME` = 'guest_reviews'
      AND `COLUMN_NAME` = 'excerpt'
);

SET @sql = IF(
    @excerpt_exists = 0,
    'ALTER TABLE `guest_reviews` ADD COLUMN `excerpt` TEXT NULL AFTER `review_text`',
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Preserve existing review content in the new homepage excerpt field.
UPDATE `guest_reviews`
SET `excerpt` = `review_text`
WHERE `excerpt` IS NULL
   OR TRIM(`excerpt`) = '';

-- 3. Add the Featured flag when it does not already exist.
-- The existing sort_order column is intentionally retained for table sorting.
SET @featured_exists = (
    SELECT COUNT(*)
    FROM `information_schema`.`COLUMNS`
    WHERE `TABLE_SCHEMA` = @schema_name
      AND `TABLE_NAME` = 'guest_reviews'
      AND `COLUMN_NAME` = 'is_featured'
);

SET @sql = IF(
    @featured_exists = 0,
    'ALTER TABLE `guest_reviews` ADD COLUMN `is_featured` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_active`',
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Preserve the current homepage reviews on the first run.
-- Future featured selections can be managed from Filament.
UPDATE `guest_reviews`
SET `is_featured` = 1
WHERE @featured_exists = 0
  AND `is_active` = 1;

-- Add the Featured index when it does not already exist.
SET @featured_index_exists = (
    SELECT COUNT(*)
    FROM `information_schema`.`STATISTICS`
    WHERE `TABLE_SCHEMA` = @schema_name
      AND `TABLE_NAME` = 'guest_reviews'
      AND `INDEX_NAME` = 'guest_reviews_is_featured_index'
);

SET @sql = IF(
    @featured_index_exists = 0,
    'CREATE INDEX `guest_reviews_is_featured_index` ON `guest_reviews` (`is_featured`)',
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 4. Mark the equivalent Laravel migrations as applied.
-- This prevents a later php artisan migrate from adding the same columns again.
SET @migration_batch = (
    SELECT COALESCE(MAX(`batch`), 0) + 1
    FROM `migrations`
);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_07_29_000001_update_riverside_bliss_button_label', @migration_batch
WHERE NOT EXISTS (
    SELECT 1
    FROM `migrations`
    WHERE `migration` = '2026_07_29_000001_update_riverside_bliss_button_label'
);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_07_29_000002_add_excerpt_to_guest_reviews_table', @migration_batch
WHERE NOT EXISTS (
    SELECT 1
    FROM `migrations`
    WHERE `migration` = '2026_07_29_000002_add_excerpt_to_guest_reviews_table'
);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_07_29_000003_add_is_featured_to_guest_reviews_table', @migration_batch
WHERE NOT EXISTS (
    SELECT 1
    FROM `migrations`
    WHERE `migration` = '2026_07_29_000003_add_is_featured_to_guest_reviews_table'
);

-- Verification output.
SELECT
    `id`,
    `reviewer_name`,
    `is_active`,
    `is_featured`,
    `sort_order`,
    LEFT(`excerpt`, 100) AS `excerpt_preview`
FROM `guest_reviews`
ORDER BY `sort_order`, `id`;
