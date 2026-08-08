-- Nandini Jungle Events module
-- Import this file into the existing Laravel database with phpMyAdmin.
-- This script is safe to import more than once and does not delete event data.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `events` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `subtitle` VARCHAR(255) COLLATE utf8mb4_unicode_ci NULL,
    `excerpt` TEXT COLLATE utf8mb4_unicode_ci NULL,
    `description` LONGTEXT COLLATE utf8mb4_unicode_ci NULL,
    `image` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `image_name` VARCHAR(255) COLLATE utf8mb4_unicode_ci NULL,
    `alt_text` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `status` VARCHAR(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
    `event_start_at` DATETIME NULL,
    `event_end_at` DATETIME NULL,
    `event_type` VARCHAR(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'regular',
    `schedule_text` VARCHAR(255) COLLATE utf8mb4_unicode_ci NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,

    PRIMARY KEY (`id`),
    KEY `events_status_index` (`status`),
    KEY `events_event_start_at_index` (`event_start_at`),
    KEY `events_event_end_at_index` (`event_end_at`),
    KEY `events_event_type_index` (`event_type`),
    KEY `events_status_event_start_at_index` (`status`, `event_start_at`),
    KEY `events_status_event_type_index` (`status`, `event_type`)
) ENGINE=InnoDB
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- Upgrade an events table created by the earlier Events SQL package.
ALTER TABLE `events`
    MODIFY COLUMN `event_start_at` DATETIME NULL,
    MODIFY COLUMN `event_end_at` DATETIME NULL;

SET @event_schedule_text_exists = (
    SELECT COUNT(*)
    FROM information_schema.columns
    WHERE table_schema = DATABASE()
      AND table_name = 'events'
      AND column_name = 'schedule_text'
);

SET @event_sql = IF(
    @event_schedule_text_exists = 0,
    'ALTER TABLE `events` ADD COLUMN `schedule_text` VARCHAR(255) COLLATE utf8mb4_unicode_ci NULL AFTER `event_type`',
    'SELECT 1'
);
PREPARE event_statement FROM @event_sql;
EXECUTE event_statement;
DEALLOCATE PREPARE event_statement;

-- Mark the matching Laravel migration as completed so a future
-- `php artisan migrate` command will not try to create the table again.
INSERT INTO `migrations` (`migration`, `batch`)
SELECT
    '2026_08_05_000002_create_events_table',
    (SELECT COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`)
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1
    FROM `migrations`
    WHERE `migration` = '2026_08_05_000002_create_events_table'
);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT
    '2026_08_05_000003_add_schedule_text_and_nullable_dates_to_events_table',
    (SELECT COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`)
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1
    FROM `migrations`
    WHERE `migration` = '2026_08_05_000003_add_schedule_text_and_nullable_dates_to_events_table'
);
