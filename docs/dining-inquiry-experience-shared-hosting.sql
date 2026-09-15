-- Safe whether experience_id is missing or incorrectly linked to dining_experiences.
SET @database_name := DATABASE();

SET @occasion_exists := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@database_name AND TABLE_NAME='inquiries' AND COLUMN_NAME='occasion');
SET @sql := IF(@occasion_exists=0, 'ALTER TABLE `inquiries` ADD COLUMN `occasion` varchar(100) NULL AFTER `experience_id`', 'SELECT 1');
PREPARE statement FROM @sql; EXECUTE statement; DEALLOCATE PREPARE statement;
SET @column_exists := (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=@database_name AND TABLE_NAME='inquiries' AND COLUMN_NAME='experience_id');
SET @sql := IF(@column_exists=0, 'ALTER TABLE `inquiries` ADD COLUMN `experience_id` bigint unsigned NULL AFTER `inquiry_title`', 'SELECT 1');
PREPARE statement FROM @sql; EXECUTE statement; DEALLOCATE PREPARE statement;

SET @foreign_key_name := (SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=@database_name AND TABLE_NAME='inquiries' AND COLUMN_NAME='experience_id' AND REFERENCED_TABLE_NAME IS NOT NULL LIMIT 1);
SET @referenced_table := (SELECT REFERENCED_TABLE_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=@database_name AND TABLE_NAME='inquiries' AND COLUMN_NAME='experience_id' AND REFERENCED_TABLE_NAME IS NOT NULL LIMIT 1);
SET @sql := IF(@foreign_key_name IS NULL, 'SELECT 1', CONCAT('ALTER TABLE `inquiries` DROP FOREIGN KEY `', REPLACE(@foreign_key_name, '`', '``'), '`'));
PREPARE statement FROM @sql; EXECUTE statement; DEALLOCATE PREPARE statement;

UPDATE `inquiries` SET `experience_id`=NULL
WHERE `experience_id` IS NOT NULL
AND (
  @referenced_table='dining_experiences'
  OR NOT EXISTS (SELECT 1 FROM `experiences` WHERE `experiences`.`id`=`inquiries`.`experience_id`)
);

SET @index_exists := (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=@database_name AND TABLE_NAME='inquiries' AND INDEX_NAME='inquiries_experience_id_foreign');
SET @sql := IF(@index_exists=0, 'ALTER TABLE `inquiries` ADD INDEX `inquiries_experience_id_foreign` (`experience_id`)', 'SELECT 1');
PREPARE statement FROM @sql; EXECUTE statement; DEALLOCATE PREPARE statement;

ALTER TABLE `inquiries`
  ADD CONSTRAINT `inquiries_experience_id_foreign`
  FOREIGN KEY (`experience_id`) REFERENCES `experiences` (`id`)
  ON DELETE SET NULL;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_09_11_000003_add_dining_experience_to_inquiries', COALESCE(MAX(`batch`), 0) + 1
FROM `migrations`
WHERE NOT EXISTS (
  SELECT 1 FROM `migrations` AS `recorded`
  WHERE `recorded`.`migration` = '2026_09_11_000003_add_dining_experience_to_inquiries'
);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_09_11_000004_correct_inquiry_experience_foreign_key', COALESCE(MAX(`batch`), 0) + 1
FROM `migrations`
WHERE NOT EXISTS (
  SELECT 1 FROM `migrations` AS `recorded`
  WHERE `recorded`.`migration` = '2026_09_11_000004_correct_inquiry_experience_foreign_key'
);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_09_11_000005_add_occasion_to_inquiries', COALESCE(MAX(`batch`), 0) + 1
FROM `migrations`
WHERE NOT EXISTS (
  SELECT 1 FROM `migrations` AS `recorded`
  WHERE `recorded`.`migration` = '2026_09_11_000005_add_occasion_to_inquiries'
);

-- The dropdown query. Its result must match the active Signature Dining cards.
SELECT `experiences`.`id`, `experiences`.`title`, `experiences`.`sort_order`
FROM `experiences`
WHERE `experiences`.`is_active`=1
AND `experiences`.`slug` IN (
  'romantic-dining-by-the-chapel',
  'moonlit-jungle-romance',
  'riverside-romance'
)
AND EXISTS (
  SELECT 1 FROM `vouchers`
  INNER JOIN `voucher_categories` ON `voucher_categories`.`id`=`vouchers`.`voucher_category_id`
  WHERE `vouchers`.`experience_id`=`experiences`.`id`
    AND `vouchers`.`is_active`=1
    AND `vouchers`.`deleted_at` IS NULL
    AND `voucher_categories`.`slug`='signature-dining-experiences'
    AND `voucher_categories`.`is_active`=1
)
ORDER BY `experiences`.`sort_order`, `experiences`.`title`;
