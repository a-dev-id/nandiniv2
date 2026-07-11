-- Nandini voucher module SQL
-- Run this on the target MySQL/MariaDB database after the base site tables exist.
-- Required existing tables: users, members, experiences, experience_categories, experience_prices.

CREATE TABLE IF NOT EXISTS `voucher_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text NULL,
  `image` varchar(255) NULL,
  `image_alt` varchar(255) NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `voucher_categories_slug_unique` (`slug`),
  KEY `voucher_categories_is_active_index` (`is_active`),
  KEY `voucher_categories_sort_order_index` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `vouchers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `voucher_category_id` bigint unsigned NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `sku` varchar(255) NULL,
  `excerpt` text NULL,
  `description` longtext NULL,
  `inclusions` longtext NULL,
  `terms_conditions` longtext NULL,
  `image` varchar(255) NULL,
  `card_image` varchar(255) NULL,
  `image_alt` varchar(255) NULL,
  `voucher_type` varchar(255) NOT NULL DEFAULT 'custom',
  `face_value` bigint unsigned NULL,
  `selling_price` bigint unsigned NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'IDR',
  `price_type` varchar(255) NULL,
  `unit_type` varchar(255) NULL,
  `validity_type` varchar(255) NOT NULL DEFAULT 'days_after_issue',
  `validity_days` int unsigned NULL,
  `fixed_valid_from` date NULL,
  `fixed_valid_until` date NULL,
  `minimum_quantity` int unsigned NOT NULL DEFAULT 1,
  `maximum_quantity` int unsigned NULL,
  `purchase_limit_per_order` int unsigned NULL,
  `allow_partial_redemption` tinyint(1) NOT NULL DEFAULT 0,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int NOT NULL DEFAULT 0,
  `meta_title` varchar(255) NULL,
  `meta_description` text NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vouchers_slug_unique` (`slug`),
  UNIQUE KEY `vouchers_sku_unique` (`sku`),
  KEY `vouchers_voucher_category_id_foreign` (`voucher_category_id`),
  KEY `vouchers_voucher_type_index` (`voucher_type`),
  KEY `vouchers_is_featured_index` (`is_featured`),
  KEY `vouchers_is_active_index` (`is_active`),
  KEY `vouchers_sort_order_index` (`sort_order`),
  CONSTRAINT `vouchers_voucher_category_id_foreign`
    FOREIGN KEY (`voucher_category_id`) REFERENCES `voucher_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- If the vouchers table already existed from an earlier voucher install, add the Experience price metadata columns.
ALTER TABLE `vouchers` ADD COLUMN IF NOT EXISTS `price_type` varchar(255) NULL AFTER `currency`;
ALTER TABLE `vouchers` ADD COLUMN IF NOT EXISTS `unit_type` varchar(255) NULL AFTER `price_type`;

CREATE TABLE IF NOT EXISTS `voucher_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint unsigned NULL,
  `order_number` varchar(255) NOT NULL,
  `access_token_hash` varchar(255) NULL,
  `purchaser_first_name` varchar(255) NOT NULL,
  `purchaser_last_name` varchar(255) NOT NULL,
  `purchaser_email` varchar(255) NOT NULL,
  `purchaser_phone` varchar(255) NULL,
  `billing_country_code` varchar(2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'IDR',
  `subtotal` bigint unsigned NOT NULL,
  `discount_amount` bigint unsigned NOT NULL DEFAULT 0,
  `total_amount` bigint unsigned NOT NULL,
  `payment_gateway` varchar(255) NOT NULL DEFAULT 'flywire',
  `payment_status` varchar(255) NOT NULL DEFAULT 'pending',
  `order_status` varchar(255) NOT NULL DEFAULT 'pending_payment',
  `flywire_checkout_session_id` varchar(255) NULL,
  `flywire_payment_id` varchar(255) NULL,
  `flywire_payment_reference` varchar(255) NULL,
  `flywire_status` varchar(255) NULL,
  `flywire_hosted_form_url` text NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `metadata` json NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `voucher_orders_order_number_unique` (`order_number`),
  KEY `voucher_orders_member_id_foreign` (`member_id`),
  KEY `voucher_orders_access_token_hash_index` (`access_token_hash`),
  KEY `voucher_orders_purchaser_email_index` (`purchaser_email`),
  KEY `voucher_orders_payment_status_index` (`payment_status`),
  KEY `voucher_orders_order_status_index` (`order_status`),
  KEY `voucher_orders_flywire_checkout_session_id_index` (`flywire_checkout_session_id`),
  KEY `voucher_orders_flywire_payment_id_index` (`flywire_payment_id`),
  KEY `voucher_orders_flywire_payment_reference_index` (`flywire_payment_reference`),
  KEY `voucher_orders_flywire_status_index` (`flywire_status`),
  CONSTRAINT `voucher_orders_member_id_foreign`
    FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `voucher_order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `voucher_order_id` bigint unsigned NOT NULL,
  `voucher_id` bigint unsigned NULL,
  `voucher_title` varchar(255) NOT NULL,
  `voucher_sku` varchar(255) NULL,
  `voucher_type` varchar(255) NOT NULL,
  `quantity` int unsigned NOT NULL,
  `unit_price` bigint unsigned NOT NULL,
  `line_total` bigint unsigned NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'IDR',
  `recipient_name` varchar(255) NOT NULL,
  `recipient_email` varchar(255) NOT NULL,
  `personal_message` text NULL,
  `delivery_method` varchar(255) NOT NULL DEFAULT 'email',
  `scheduled_delivery_at` timestamp NULL DEFAULT NULL,
  `voucher_snapshot` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `voucher_order_items_voucher_order_id_foreign` (`voucher_order_id`),
  KEY `voucher_order_items_voucher_id_foreign` (`voucher_id`),
  CONSTRAINT `voucher_order_items_voucher_order_id_foreign`
    FOREIGN KEY (`voucher_order_id`) REFERENCES `voucher_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `voucher_order_items_voucher_id_foreign`
    FOREIGN KEY (`voucher_id`) REFERENCES `vouchers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `issued_vouchers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `voucher_order_item_id` bigint unsigned NOT NULL,
  `voucher_id` bigint unsigned NULL,
  `member_id` bigint unsigned NULL,
  `voucher_code` varchar(255) NOT NULL,
  `verification_token_hash` varchar(255) NOT NULL,
  `recipient_name` varchar(255) NOT NULL,
  `recipient_email` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description_snapshot` longtext NULL,
  `terms_snapshot` longtext NULL,
  `original_value` bigint unsigned NULL,
  `remaining_value` bigint unsigned NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'IDR',
  `issued_at` timestamp NULL DEFAULT NULL,
  `valid_from` date NULL,
  `expires_at` date NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `pdf_path` varchar(255) NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `redeemed_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `metadata` json NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `issued_vouchers_voucher_code_unique` (`voucher_code`),
  UNIQUE KEY `issued_vouchers_verification_token_hash_unique` (`verification_token_hash`),
  KEY `issued_vouchers_voucher_order_item_id_foreign` (`voucher_order_item_id`),
  KEY `issued_vouchers_voucher_id_foreign` (`voucher_id`),
  KEY `issued_vouchers_member_id_foreign` (`member_id`),
  KEY `issued_vouchers_recipient_email_index` (`recipient_email`),
  KEY `issued_vouchers_status_index` (`status`),
  CONSTRAINT `issued_vouchers_voucher_order_item_id_foreign`
    FOREIGN KEY (`voucher_order_item_id`) REFERENCES `voucher_order_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `issued_vouchers_voucher_id_foreign`
    FOREIGN KEY (`voucher_id`) REFERENCES `vouchers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `issued_vouchers_member_id_foreign`
    FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `voucher_redemptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `issued_voucher_id` bigint unsigned NOT NULL,
  `redeemed_by_user_id` bigint unsigned NULL,
  `redemption_location` varchar(255) NULL,
  `department` varchar(255) NULL,
  `reference_number` varchar(255) NULL,
  `amount` bigint unsigned NULL,
  `balance_before` bigint unsigned NULL,
  `balance_after` bigint unsigned NULL,
  `notes` text NULL,
  `redeemed_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `voucher_redemptions_issued_voucher_id_foreign` (`issued_voucher_id`),
  KEY `voucher_redemptions_redeemed_by_user_id_foreign` (`redeemed_by_user_id`),
  CONSTRAINT `voucher_redemptions_issued_voucher_id_foreign`
    FOREIGN KEY (`issued_voucher_id`) REFERENCES `issued_vouchers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `voucher_redemptions_redeemed_by_user_id_foreign`
    FOREIGN KEY (`redeemed_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `voucher_payment_events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `voucher_order_id` bigint unsigned NULL,
  `gateway` varchar(255) NOT NULL DEFAULT 'flywire',
  `gateway_payment_id` varchar(255) NULL,
  `event_fingerprint` varchar(255) NOT NULL,
  `event_type` varchar(255) NULL,
  `gateway_status` varchar(255) NULL,
  `signature_valid` tinyint(1) NOT NULL DEFAULT 0,
  `payload` json NOT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `processing_error` text NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `voucher_payment_events_event_fingerprint_unique` (`event_fingerprint`),
  KEY `voucher_payment_events_voucher_order_id_foreign` (`voucher_order_id`),
  KEY `voucher_payment_events_gateway_index` (`gateway`),
  KEY `voucher_payment_events_gateway_payment_id_index` (`gateway_payment_id`),
  KEY `voucher_payment_events_gateway_status_index` (`gateway_status`),
  CONSTRAINT `voucher_payment_events_voucher_order_id_foreign`
    FOREIGN KEY (`voucher_order_id`) REFERENCES `voucher_orders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Mark old placeholder vouchers/categories inactive, if they exist.
UPDATE `vouchers`
SET `is_active` = 0, `is_featured` = 0, `updated_at` = NOW()
WHERE `slug` IN (
  'monetary-gift-voucher',
  'romantic-dining-voucher',
  'spa-experience-voucher',
  'jungle-stay-voucher',
  'panoramic-jacuzzi-royal-suite-voucher',
  'nandini-experience-voucher'
);

UPDATE `voucher_categories`
SET `is_active` = 0, `updated_at` = NOW()
WHERE `slug` IN ('stay', 'gift');

-- Create/update voucher categories from active Experience categories.
INSERT INTO `voucher_categories` (
  `name`, `slug`, `description`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`
)
SELECT
  COALESCE(ec.`name`, 'Experience') AS `name`,
  COALESCE(NULLIF(ec.`slug`, ''), 'experience') AS `slug`,
  NULLIF(TRIM(REGEXP_REPLACE(COALESCE(NULLIF(ec.`excerpt`, ''), ec.`description`, ''), '<[^>]*>', '')), '') AS `description`,
  ec.`image`,
  ec.`image_alt`,
  1 AS `is_active`,
  COALESCE(ec.`sort_order`, 0) AS `sort_order`,
  NOW(),
  NOW()
FROM `experiences` e
LEFT JOIN `experience_categories` ec ON ec.`id` = e.`experience_category_id`
WHERE e.`is_active` = 1
  AND EXISTS (
    SELECT 1
    FROM `experience_prices` ep
    WHERE ep.`experience_id` = e.`id`
      AND ep.`is_active` = 1
      AND ep.`price` > 0
  )
GROUP BY ec.`id`, ec.`name`, ec.`slug`, ec.`excerpt`, ec.`description`, ec.`image`, ec.`image_alt`, ec.`sort_order`
ON DUPLICATE KEY UPDATE
  `name` = VALUES(`name`),
  `description` = VALUES(`description`),
  `image` = VALUES(`image`),
  `image_alt` = VALUES(`image_alt`),
  `is_active` = 1,
  `sort_order` = VALUES(`sort_order`),
  `updated_at` = NOW();

-- Create/update vouchers from active Experiences using the first active price by sort order.
INSERT INTO `vouchers` (
  `voucher_category_id`, `title`, `slug`, `sku`, `excerpt`, `description`, `inclusions`, `terms_conditions`,
  `image`, `card_image`, `image_alt`, `voucher_type`, `face_value`, `selling_price`, `currency`,
  `price_type`, `unit_type`, `validity_type`, `validity_days`, `minimum_quantity`, `purchase_limit_per_order`,
  `is_featured`, `is_active`, `sort_order`, `meta_title`, `meta_description`, `created_at`, `updated_at`
)
SELECT
  vc.`id` AS `voucher_category_id`,
  e.`title`,
  e.`slug`,
  CONCAT('EXP-', UPPER(REPLACE(REPLACE(REPLACE(e.`slug`, '-', ''), '_', ''), ' ', ''))) AS `sku`,
  COALESCE(NULLIF(e.`excerpt`, ''), e.`subtitle`) AS `excerpt`,
  e.`description`,
  e.`inclusions`,
  '<p>Advance reservation is required. Voucher is subject to availability and cannot be exchanged for cash.</p>' AS `terms_conditions`,
  e.`image`,
  COALESCE(NULLIF(e.`card_image`, ''), e.`image`) AS `card_image`,
  COALESCE(NULLIF(e.`card_image_alt`, ''), NULLIF(e.`image_alt`, ''), e.`title`) AS `image_alt`,
  CASE
    WHEN LOWER(CONCAT(COALESCE(ec.`name`, ''), ' ', e.`title`)) LIKE '%spa%' THEN 'spa'
    WHEN LOWER(CONCAT(COALESCE(ec.`name`, ''), ' ', e.`title`)) LIKE '%dining%' THEN 'dining'
    ELSE 'experience'
  END AS `voucher_type`,
  NULL AS `face_value`,
  ROUND(ep.`price`) AS `selling_price`,
  LEFT(COALESCE(NULLIF(ep.`currency`, ''), 'IDR'), 3) AS `currency`,
  ep.`price_type`,
  ep.`unit_type`,
  'days_after_issue' AS `validity_type`,
  365 AS `validity_days`,
  1 AS `minimum_quantity`,
  COALESCE(ep.`max_qty`, 10) AS `purchase_limit_per_order`,
  e.`is_featured`,
  1 AS `is_active`,
  e.`sort_order`,
  e.`meta_title`,
  e.`meta_description`,
  NOW(),
  NOW()
FROM `experiences` e
LEFT JOIN `experience_categories` ec ON ec.`id` = e.`experience_category_id`
JOIN `voucher_categories` vc ON vc.`slug` = COALESCE(NULLIF(ec.`slug`, ''), 'experience')
JOIN `experience_prices` ep ON ep.`id` = (
  SELECT ep2.`id`
  FROM `experience_prices` ep2
  WHERE ep2.`experience_id` = e.`id`
    AND ep2.`is_active` = 1
    AND ep2.`price` > 0
  ORDER BY ep2.`sort_order`, ep2.`id`
  LIMIT 1
)
WHERE e.`is_active` = 1
ON DUPLICATE KEY UPDATE
  `voucher_category_id` = VALUES(`voucher_category_id`),
  `title` = VALUES(`title`),
  `sku` = VALUES(`sku`),
  `excerpt` = VALUES(`excerpt`),
  `description` = VALUES(`description`),
  `inclusions` = VALUES(`inclusions`),
  `terms_conditions` = VALUES(`terms_conditions`),
  `image` = VALUES(`image`),
  `card_image` = VALUES(`card_image`),
  `image_alt` = VALUES(`image_alt`),
  `voucher_type` = VALUES(`voucher_type`),
  `face_value` = VALUES(`face_value`),
  `selling_price` = VALUES(`selling_price`),
  `currency` = VALUES(`currency`),
  `price_type` = VALUES(`price_type`),
  `unit_type` = VALUES(`unit_type`),
  `validity_type` = VALUES(`validity_type`),
  `validity_days` = VALUES(`validity_days`),
  `minimum_quantity` = VALUES(`minimum_quantity`),
  `purchase_limit_per_order` = VALUES(`purchase_limit_per_order`),
  `is_featured` = VALUES(`is_featured`),
  `is_active` = VALUES(`is_active`),
  `sort_order` = VALUES(`sort_order`),
  `meta_title` = VALUES(`meta_title`),
  `meta_description` = VALUES(`meta_description`),
  `updated_at` = NOW();

-- Hide voucher categories that have no active vouchers.
UPDATE `voucher_categories` vc
SET vc.`is_active` = 0, vc.`updated_at` = NOW()
WHERE NOT EXISTS (
  SELECT 1
  FROM `vouchers` v
  WHERE v.`voucher_category_id` = vc.`id`
    AND v.`is_active` = 1
    AND v.`deleted_at` IS NULL
);

-- Optional: record the Laravel migration as already applied.
INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_07_10_000001_create_voucher_tables',
       COALESCE((SELECT MAX(m.`batch`) + 1 FROM `migrations` m), 1)
WHERE EXISTS (
  SELECT 1
  FROM information_schema.tables
  WHERE table_schema = DATABASE()
    AND table_name = 'migrations'
)
AND NOT EXISTS (
  SELECT 1
  FROM `migrations`
  WHERE `migration` = '2026_07_10_000001_create_voucher_tables'
);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_07_10_000002_add_experience_price_metadata_to_vouchers',
       COALESCE((SELECT MAX(m.`batch`) + 1 FROM `migrations` m), 1)
WHERE EXISTS (
  SELECT 1
  FROM information_schema.tables
  WHERE table_schema = DATABASE()
    AND table_name = 'migrations'
)
AND NOT EXISTS (
  SELECT 1
  FROM `migrations`
  WHERE `migration` = '2026_07_10_000002_add_experience_price_metadata_to_vouchers'
);
