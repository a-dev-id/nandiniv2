-- Nandini Events: always-visible Regular events and custom schedule text
-- Import this file into the existing Laravel database with phpMyAdmin.
-- Existing event records are preserved.

SET NAMES utf8mb4;

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
