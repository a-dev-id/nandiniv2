-- Signature Dish landing section and Filament CMS support for MySQL/MariaDB.
-- Back up the database before importing. This script is rerunnable and preserves
-- content that no longer matches the known legacy seed values.
SET NAMES utf8mb4;
SET @schema_name = DATABASE();

CREATE TABLE IF NOT EXISTS `signature_dishes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `eyebrow` varchar(255) NULL,
  `subtitle` varchar(255) NULL,
  `price` varchar(255) NULL,
  `short_description` text NULL,
  `cta_label` varchar(255) NULL,
  `cta_url` text NULL,
  `image` varchar(255) NULL,
  `image_alt` varchar(255) NULL,
  `content` longtext NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int unsigned NOT NULL DEFAULT 1,
  `meta_title` varchar(70) NULL,
  `meta_description` varchar(180) NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `signature_dishes_slug_unique` (`slug`),
  KEY `signature_dishes_is_published_index` (`is_published`),
  KEY `signature_dishes_sort_order_index` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DELIMITER $$
DROP PROCEDURE IF EXISTS signature_dish_add_column_if_missing$$
CREATE PROCEDURE signature_dish_add_column_if_missing(
  IN table_name_in varchar(64),
  IN column_name_in varchar(64),
  IN definition_in text
)
BEGIN
  IF NOT EXISTS (
    SELECT 1
    FROM information_schema.columns
    WHERE table_schema = @schema_name
      AND table_name = table_name_in
      AND column_name = column_name_in
  ) THEN
    SET @ddl = CONCAT(
      'ALTER TABLE `', table_name_in, '` ADD COLUMN `',
      column_name_in, '` ', definition_in
    );
    PREPARE statement_to_run FROM @ddl;
    EXECUTE statement_to_run;
    DEALLOCATE PREPARE statement_to_run;
  END IF;
END$$
DELIMITER ;

CALL signature_dish_add_column_if_missing(
  'signature_dishes',
  'subtitle',
  'varchar(255) NULL AFTER `eyebrow`'
);
CALL signature_dish_add_column_if_missing(
  'signature_dishes',
  'cta_url',
  'text NULL AFTER `cta_label`'
);

DROP PROCEDURE IF EXISTS signature_dish_add_column_if_missing;

-- Upgrade only the exact legacy seed record. Manually edited CMS content is kept.
UPDATE `signature_dishes`
SET
  `name` = 'NASI JINGGO $100',
  `eyebrow` = 'SIGNATURE DISH',
  `subtitle` = 'A SIGNATURE TASTE OF BALI',
  `price` = NULL,
  `short_description` = 'A beloved Balinese street food, reimagined with premium ingredients and refined presentation, offering an authentic taste of Indonesia in the extraordinary setting of Nandini Jungle.',
  `cta_label` = 'VIEW SIGNATURE DISH',
  `cta_url` = '/signature-dishes/nasi-jinggo',
  `image_alt` = 'Nasi Jinggo signature dish at Nandini Jungle',
  `content` = '<p>A beloved Balinese street food, reimagined with premium ingredients and refined presentation, offering an authentic taste of Indonesia in the extraordinary setting of Nandini Jungle.</p>',
  `meta_title` = 'Nasi Jinggo | Nandini Jungle Dining',
  `meta_description` = 'Discover Nasi Jinggo, a signature Balinese dish presented with premium ingredients at Nandini Jungle by Hanging Gardens.',
  `is_published` = 1,
  `sort_order` = 1,
  `updated_at` = CURRENT_TIMESTAMP
WHERE `slug` = 'nasi-jinggo'
  AND `name` = 'Nasi Jinggo'
  AND `short_description` = 'A beloved Balinese favourite, reimagined with premium ingredients and a refined presentation. A small plate with a big story.';

INSERT INTO `signature_dishes` (
  `name`, `slug`, `eyebrow`, `subtitle`, `price`, `short_description`,
  `cta_label`, `cta_url`, `image`, `image_alt`, `content`, `is_published`,
  `sort_order`, `meta_title`, `meta_description`, `created_at`, `updated_at`
)
SELECT
  'NASI JINGGO $100',
  'nasi-jinggo',
  'SIGNATURE DISH',
  'A SIGNATURE TASTE OF BALI',
  NULL,
  'A beloved Balinese street food, reimagined with premium ingredients and refined presentation, offering an authentic taste of Indonesia in the extraordinary setting of Nandini Jungle.',
  'VIEW SIGNATURE DISH',
  '/signature-dishes/nasi-jinggo',
  '/images/dining/rahang-tuna.jpeg',
  'Nasi Jinggo signature dish at Nandini Jungle',
  '<p>A beloved Balinese street food, reimagined with premium ingredients and refined presentation, offering an authentic taste of Indonesia in the extraordinary setting of Nandini Jungle.</p>',
  1,
  1,
  'Nasi Jinggo | Nandini Jungle Dining',
  'Discover Nasi Jinggo, a signature Balinese dish presented with premium ingredients at Nandini Jungle by Hanging Gardens.',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP
WHERE NOT EXISTS (
  SELECT 1 FROM `signature_dishes` WHERE `slug` = 'nasi-jinggo'
);

CREATE TABLE IF NOT EXISTS `signature_dish_sections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `signature_dish_id` bigint unsigned NOT NULL,
  `section_key` varchar(255) NOT NULL DEFAULT 'split_media_section',
  `title` varchar(255) NULL,
  `subtitle` varchar(255) NULL,
  `excerpt` text NULL,
  `description` longtext NULL,
  `video_url` varchar(255) NULL,
  `video_label` varchar(255) NULL,
  `button_label` varchar(255) NULL,
  `button_link_type` varchar(255) NOT NULL DEFAULT 'manual',
  `button_url` varchar(500) NULL,
  `button_route` varchar(255) NULL,
  `text_align` varchar(255) NOT NULL DEFAULT 'center',
  `background_color` varchar(255) NOT NULL DEFAULT 'white',
  `items` json NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `signature_dish_sections_dish_key_index` (`signature_dish_id`,`section_key`),
  KEY `signature_dish_sections_active_order_index` (`is_active`,`sort_order`),
  CONSTRAINT `signature_dish_sections_signature_dish_id_foreign`
    FOREIGN KEY (`signature_dish_id`) REFERENCES `signature_dishes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `signature_dish_section_images` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `signature_dish_section_id` bigint unsigned NOT NULL,
  `image` varchar(255) NULL,
  `image_file_name` varchar(120) NULL,
  `image_alt` varchar(255) NULL,
  `mobile_image` varchar(255) NULL,
  `mobile_image_file_name` varchar(120) NULL,
  `mobile_image_alt` varchar(255) NULL,
  `caption` varchar(255) NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `signature_dish_section_images_active_order_index`
    (`signature_dish_section_id`,`is_active`,`sort_order`),
  CONSTRAINT `signature_dish_section_images_section_id_foreign`
    FOREIGN KEY (`signature_dish_section_id`) REFERENCES `signature_dish_sections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Register the equivalent Laravel migrations so a later artisan migrate does not
-- try to create or alter this table again.
SET @migration_batch = (SELECT COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_09_14_000001_create_signature_dishes_table', @migration_batch
WHERE NOT EXISTS (
  SELECT 1 FROM `migrations`
  WHERE `migration` = '2026_09_14_000001_create_signature_dishes_table'
);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_09_15_000001_add_subtitle_and_cta_url_to_signature_dishes', @migration_batch
WHERE NOT EXISTS (
  SELECT 1 FROM `migrations`
  WHERE `migration` = '2026_09_15_000001_add_subtitle_and_cta_url_to_signature_dishes'
);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_09_15_000002_create_signature_dish_content_sections', @migration_batch
WHERE NOT EXISTS (
  SELECT 1 FROM `migrations`
  WHERE `migration` = '2026_09_15_000002_create_signature_dish_content_sections'
);
