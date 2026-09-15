-- Separate Dining Experiences from the general Experiences feature.
-- Back up the database before importing. Existing Dining-specific rows are preserved.
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `dining_experiences` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `card_title` varchar(191) NULL,
  `short_description` text NULL,
  `card_cta_label` varchar(191) NULL,
  `description` longtext NULL,
  `card_image` varchar(191) NULL,
  `card_image_alt` varchar(191) NULL,
  `hero_image` varchar(191) NULL,
  `hero_mobile_image` varchar(191) NULL,
  `hero_image_alt` varchar(191) NULL,
  `intro_eyebrow` varchar(191) NULL,
  `page_heading` varchar(191) NULL,
  `menu_cta_label` varchar(191) NULL,
  `menu_url` text NULL,
  `reservation_cta_label` varchar(191) NULL,
  `reservation_url` text NULL,
  `opening_hours` varchar(191) NULL,
  `experience_type` varchar(191) NULL,
  `location` varchar(191) NULL,
  `whatsapp_number` varchar(191) NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int unsigned NOT NULL DEFAULT 0,
  `meta_title` varchar(191) NULL,
  `meta_description` text NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dining_experiences_slug_unique` (`slug`),
  KEY `dining_experiences_is_active_sort_order_index` (`is_active`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `dining_experiences` (
  `id`, `title`, `slug`, `card_title`, `short_description`, `card_cta_label`, `description`,
  `card_image`, `card_image_alt`, `hero_image`, `hero_mobile_image`, `hero_image_alt`,
  `intro_eyebrow`, `page_heading`, `menu_cta_label`, `menu_url`, `reservation_cta_label`,
  `reservation_url`, `opening_hours`, `experience_type`, `location`, `whatsapp_number`,
  `is_active`, `sort_order`, `meta_title`, `meta_description`, `created_at`, `updated_at`
)
SELECT
  `id`, `title`, COALESCE(`dining_slug`, `slug`), `dining_card_title`,
  `dining_short_description`, `dining_cta_label`, `description`, `card_image`, `card_image_alt`,
  `image`, `hero_mobile_image`, `image_alt`, `intro_eyebrow`, `page_heading`, `menu_cta_label`,
  `menu_url`, `reservation_cta_label`, `reservation_url`, `opening_hours`, `experience_type`,
  `location`, `whatsapp_number`, `is_active`, `sort_order`, `meta_title`, `meta_description`,
  `created_at`, `updated_at`
FROM `experiences`
WHERE `show_on_dining` = 1;

SET @database_name = DATABASE();
SET @gallery_column_exists = (
  SELECT COUNT(*) FROM information_schema.columns
  WHERE table_schema=@database_name AND table_name='dining_experience_gallery' AND column_name='dining_experience_id'
);
SET @gallery_column_sql = IF(
  @gallery_column_exists=0,
  'ALTER TABLE `dining_experience_gallery` ADD COLUMN `dining_experience_id` bigint unsigned NULL AFTER `experience_id`, ADD INDEX `dining_experience_gallery_dining_experience_id_index` (`dining_experience_id`)',
  'SELECT 1'
);
PREPARE gallery_statement FROM @gallery_column_sql;
EXECUTE gallery_statement;
DEALLOCATE PREPARE gallery_statement;

UPDATE `dining_experience_gallery`
SET `dining_experience_id`=`experience_id`
WHERE `dining_experience_id` IS NULL;

-- New Dining Experience gallery rows use dining_experience_id. The original
-- experience_id is retained only for backward compatibility.
ALTER TABLE `dining_experience_gallery`
  MODIFY COLUMN `experience_id` bigint unsigned NULL;

SET @migration_batch = (SELECT COALESCE(MAX(`batch`),0)+1 FROM `migrations`);
INSERT INTO `migrations` (`migration`,`batch`)
SELECT '2026_09_10_000001_separate_dining_experiences', @migration_batch
WHERE NOT EXISTS (
  SELECT 1 FROM `migrations` WHERE `migration`='2026_09_10_000001_separate_dining_experiences'
);

SELECT `id`, `title`, `slug`, `card_title`, `card_image`, `sort_order`
FROM `dining_experiences` ORDER BY `sort_order`;
