-- Nandini voucher update: experience synchronization and discounts
-- Run this ONCE on the shared-hosting MySQL/MariaDB database.
-- Prerequisites: `vouchers` and `experiences` tables already exist.

ALTER TABLE `vouchers`
  ADD COLUMN `experience_id` BIGINT UNSIGNED NULL AFTER `voucher_category_id`,
  ADD COLUMN `discount_percentage` TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER `selling_price`;

-- Link existing experience vouchers using their current matching slugs.
UPDATE `vouchers` AS `v`
INNER JOIN `experiences` AS `e` ON `e`.`slug` = `v`.`slug`
SET `v`.`experience_id` = `e`.`id`
WHERE `v`.`experience_id` IS NULL;

ALTER TABLE `vouchers`
  ADD UNIQUE KEY `vouchers_experience_id_unique` (`experience_id`),
  ADD CONSTRAINT `vouchers_experience_id_foreign`
    FOREIGN KEY (`experience_id`) REFERENCES `experiences` (`id`)
    ON DELETE SET NULL;

-- Mark the equivalent Laravel migration as applied so `php artisan migrate`
-- does not attempt to add the same columns again.
INSERT INTO `migrations` (`migration`, `batch`)
SELECT
  '2026_07_11_000001_add_experience_and_discount_to_vouchers',
  COALESCE((SELECT MAX(`existing`.`batch`) + 1 FROM `migrations` AS `existing`), 1)
WHERE NOT EXISTS (
  SELECT 1
  FROM `migrations`
  WHERE `migration` = '2026_07_11_000001_add_experience_and_discount_to_vouchers'
);
