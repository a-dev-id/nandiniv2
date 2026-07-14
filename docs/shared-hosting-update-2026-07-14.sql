-- Nandini Membership shared-hosting schema update
-- Generated: 2026-07-14
--
-- Compared against:
-- nandini_membership.sql supplied from shared hosting.
--
-- This script is non-destructive:
-- - It does not delete or replace existing data.
-- - It synchronizes migration history only for schema changes already found
--   in the supplied dump.
-- - It creates the new guest_reviews table used by the homepage review slider.

SET NAMES utf8mb4;
SET @nandini_migration_batch := (SELECT COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`);

-- These schema changes already exist in the supplied shared-hosting dump, but
-- their migration records are missing. Recording them prevents duplicate-column
-- errors if `php artisan migrate` is run later.
INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_06_11_000004_add_welcome_email_sent_at_to_members_table', @nandini_migration_batch
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations`
    WHERE `migration` = '2026_06_11_000004_add_welcome_email_sent_at_to_members_table'
);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_06_12_000001_add_membership_expiry_reminder_sent_at_to_members_table', @nandini_migration_batch
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations`
    WHERE `migration` = '2026_06_12_000001_add_membership_expiry_reminder_sent_at_to_members_table'
);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_06_26_000001_add_file_names_to_page_section_images_table', @nandini_migration_batch
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations`
    WHERE `migration` = '2026_06_26_000001_add_file_names_to_page_section_images_table'
);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_06_30_000001_add_manual_member_assignment_to_synced_webhotelier_bookings_table', @nandini_migration_batch
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations`
    WHERE `migration` = '2026_06_30_000001_add_manual_member_assignment_to_synced_webhotelier_bookings_table'
);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_07_01_000001_add_file_names_to_offers_table', @nandini_migration_batch
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations`
    WHERE `migration` = '2026_07_01_000001_add_file_names_to_offers_table'
);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_07_02_000001_add_stay_dates_to_members_table', @nandini_migration_batch
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations`
    WHERE `migration` = '2026_07_02_000001_add_stay_dates_to_members_table'
);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_07_03_000001_add_last_login_at_to_members_table', @nandini_migration_batch
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations`
    WHERE `migration` = '2026_07_03_000001_add_last_login_at_to_members_table'
);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_07_06_000002_add_file_names_to_blog_news_images', @nandini_migration_batch
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations`
    WHERE `migration` = '2026_07_06_000002_add_file_names_to_blog_news_images'
);

-- Voucher module schema and catalog data.
-- Transactional order/payment/redemption data is intentionally not copied.
CREATE TABLE IF NOT EXISTS `voucher_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_alt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `voucher_categories_slug_unique` (`slug`),
  KEY `voucher_categories_is_active_index` (`is_active`),
  KEY `voucher_categories_sort_order_index` (`sort_order`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `vouchers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `voucher_category_id` bigint unsigned DEFAULT NULL,
  `experience_id` bigint unsigned DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `excerpt` text COLLATE utf8mb4_unicode_ci,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `inclusions` longtext COLLATE utf8mb4_unicode_ci,
  `terms_conditions` longtext COLLATE utf8mb4_unicode_ci,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `card_image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_alt` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `voucher_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'custom',
  `face_value` bigint unsigned DEFAULT NULL,
  `selling_price` bigint unsigned NOT NULL,
  `discount_percentage` tinyint unsigned NOT NULL DEFAULT '0',
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IDR',
  `price_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `validity_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'days_after_issue',
  `validity_days` int unsigned DEFAULT NULL,
  `fixed_valid_from` date DEFAULT NULL,
  `fixed_valid_until` date DEFAULT NULL,
  `minimum_quantity` int unsigned NOT NULL DEFAULT '1',
  `maximum_quantity` int unsigned DEFAULT NULL,
  `purchase_limit_per_order` int unsigned DEFAULT NULL,
  `allow_partial_redemption` tinyint(1) NOT NULL DEFAULT '0',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `meta_title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vouchers_slug_unique` (`slug`),
  UNIQUE KEY `vouchers_sku_unique` (`sku`),
  UNIQUE KEY `vouchers_experience_id_unique` (`experience_id`),
  KEY `vouchers_voucher_category_id_foreign` (`voucher_category_id`),
  KEY `vouchers_voucher_type_index` (`voucher_type`),
  KEY `vouchers_is_featured_index` (`is_featured`),
  KEY `vouchers_is_active_index` (`is_active`),
  KEY `vouchers_sort_order_index` (`sort_order`)
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `voucher_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint unsigned DEFAULT NULL,
  `order_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token_hash` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purchaser_first_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `purchaser_last_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `purchaser_email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `purchaser_phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `billing_country_code` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IDR',
  `subtotal` bigint unsigned NOT NULL,
  `discount_amount` bigint unsigned NOT NULL DEFAULT '0',
  `total_amount` bigint unsigned NOT NULL,
  `payment_gateway` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'flywire',
  `payment_status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `order_status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending_payment',
  `flywire_checkout_session_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `flywire_payment_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `flywire_payment_reference` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `flywire_status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `flywire_hosted_form_url` text COLLATE utf8mb4_unicode_ci,
  `paid_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `metadata` json DEFAULT NULL,
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
  KEY `voucher_orders_flywire_status_index` (`flywire_status`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `voucher_order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `voucher_order_id` bigint unsigned NOT NULL,
  `voucher_id` bigint unsigned DEFAULT NULL,
  `voucher_title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `voucher_sku` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `voucher_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int unsigned NOT NULL,
  `unit_price` bigint unsigned NOT NULL,
  `line_total` bigint unsigned NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IDR',
  `recipient_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `recipient_email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `personal_message` text COLLATE utf8mb4_unicode_ci,
  `delivery_method` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'email',
  `scheduled_delivery_at` timestamp NULL DEFAULT NULL,
  `voucher_snapshot` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `voucher_order_items_voucher_order_id_foreign` (`voucher_order_id`),
  KEY `voucher_order_items_voucher_id_foreign` (`voucher_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `issued_vouchers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `voucher_order_item_id` bigint unsigned NOT NULL,
  `voucher_id` bigint unsigned DEFAULT NULL,
  `member_id` bigint unsigned DEFAULT NULL,
  `voucher_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `verification_token_hash` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `recipient_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `recipient_email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_snapshot` longtext COLLATE utf8mb4_unicode_ci,
  `terms_snapshot` longtext COLLATE utf8mb4_unicode_ci,
  `original_value` bigint unsigned DEFAULT NULL,
  `remaining_value` bigint unsigned DEFAULT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IDR',
  `issued_at` timestamp NULL DEFAULT NULL,
  `valid_from` date DEFAULT NULL,
  `expires_at` date DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `pdf_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `redeemed_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `issued_vouchers_voucher_code_unique` (`voucher_code`),
  UNIQUE KEY `issued_vouchers_verification_token_hash_unique` (`verification_token_hash`),
  KEY `issued_vouchers_voucher_order_item_id_foreign` (`voucher_order_item_id`),
  KEY `issued_vouchers_voucher_id_foreign` (`voucher_id`),
  KEY `issued_vouchers_member_id_foreign` (`member_id`),
  KEY `issued_vouchers_recipient_email_index` (`recipient_email`),
  KEY `issued_vouchers_status_index` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `voucher_redemptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `issued_voucher_id` bigint unsigned NOT NULL,
  `redeemed_by_user_id` bigint unsigned DEFAULT NULL,
  `redemption_location` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` bigint unsigned DEFAULT NULL,
  `balance_before` bigint unsigned DEFAULT NULL,
  `balance_after` bigint unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `redeemed_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `voucher_redemptions_issued_voucher_id_foreign` (`issued_voucher_id`),
  KEY `voucher_redemptions_redeemed_by_user_id_foreign` (`redeemed_by_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS `voucher_payment_events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `voucher_order_id` bigint unsigned DEFAULT NULL,
  `gateway` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'flywire',
  `gateway_payment_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_fingerprint` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gateway_status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `signature_valid` tinyint(1) NOT NULL DEFAULT '0',
  `payload` json NOT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `processing_error` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `voucher_payment_events_event_fingerprint_unique` (`event_fingerprint`),
  KEY `voucher_payment_events_voucher_order_id_foreign` (`voucher_order_id`),
  KEY `voucher_payment_events_gateway_index` (`gateway`),
  KEY `voucher_payment_events_gateway_payment_id_index` (`gateway_payment_id`),
  KEY `voucher_payment_events_gateway_status_index` (`gateway_status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT  IGNORE INTO `voucher_categories` (`id`, `name`, `slug`, `description`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES (1,'Stay','stay','Accommodation and suite experiences.',NULL,NULL,0,0,'2026-07-10 07:40:24','2026-07-10 09:09:34'),(2,'Dining','dining','Romantic dining and culinary gifts.',NULL,NULL,0,0,'2026-07-10 07:40:24','2026-07-10 09:09:34'),(3,'Spa','spa','Wellness rituals at the jungle spa.',NULL,NULL,0,0,'2026-07-10 07:40:24','2026-07-10 09:09:34'),(4,'Experience','experience','Curated Nandini moments.',NULL,NULL,0,0,'2026-07-10 07:40:24','2026-07-10 09:09:34'),(5,'Gift','gift','Flexible monetary gift vouchers.',NULL,NULL,0,0,'2026-07-10 07:40:24','2026-07-10 09:09:34'),(6,'Holy River','holy-river',NULL,NULL,NULL,1,0,'2026-07-10 08:08:32','2026-07-10 08:16:28'),(7,'Jungle Romance','jungle-romance','Intimate moments crafted for couples, surrounded by the beauty of Ubud’s jungle and river.','experience-categories/c5e0deb3-cd14-4488-ba06-31efb046d0fd.webp','Jungle Romance',1,0,'2026-07-10 08:08:32','2026-07-10 08:16:28'),(8,'Jungle Wellness & Spa Rituals','jungle-wellness-spa-rituals','Relaxing rituals designed to restore balance, refresh the body, and calm the mind.','experience-categories/0f670856-8d39-49a1-97ff-96a88f13e2e2.webp','Jungle Wellness & Spa Rituals',1,0,'2026-07-10 08:08:32','2026-07-10 08:16:28'),(9,'Signature Dining Experiences','signature-dining-experiences','Memorable dining moments with scenic settings, refined flavors, and warm Balinese hospitality.','experience-categories/cff9f2ff-71e8-42c8-bcf4-a95c32488c53.webp','Signature Dining Experiences',1,0,'2026-07-10 08:08:32','2026-07-10 08:16:29'),(10,'Ubud Jungle Adventures','ubud-jungle-adventures','Explore nature, culture, and hidden jungle surroundings through unforgettable outdoor experiences.','experience-categories/0b764bc6-95d2-4fa7-bd1b-58f4eba5eb97.webp','Ubud Jungle Adventures',1,0,'2026-07-10 08:08:32','2026-07-10 08:16:29'),(11,'Curated Experience Packages','curated-experience-packages','Carefully curated packages combining Nandini’s most memorable activities and guest experiences.','experience-categories/8a08b1e7-4058-470c-a338-a03f8b57b2aa.webp','Curated Experience Packages',1,0,'2026-07-10 08:08:32','2026-07-10 08:16:29');
INSERT  IGNORE INTO `vouchers` (`id`, `voucher_category_id`, `experience_id`, `title`, `slug`, `sku`, `excerpt`, `description`, `inclusions`, `terms_conditions`, `image`, `card_image`, `image_alt`, `voucher_type`, `face_value`, `selling_price`, `discount_percentage`, `currency`, `price_type`, `unit_type`, `validity_type`, `validity_days`, `fixed_valid_from`, `fixed_valid_until`, `minimum_quantity`, `maximum_quantity`, `purchase_limit_per_order`, `allow_partial_redemption`, `is_featured`, `is_active`, `sort_order`, `meta_title`, `meta_description`, `created_at`, `updated_at`, `deleted_at`) VALUES (1,5,NULL,'Monetary Gift Voucher','monetary-gift-voucher','MONETARYGIFTVOUCHER','A refined Nandini Jungle by Hanging Gardens gift voucher.','<p>Share a memorable Nandini experience with a beautifully presented gift voucher.</p>','<p>Inclusions are confirmed according to the selected voucher and availability.</p>','<p>Advance reservation is required. Voucher is subject to availability and cannot be exchanged for cash.</p>',NULL,NULL,NULL,'monetary',1000000,1000000,0,'IDR',NULL,NULL,'days_after_issue',365,NULL,NULL,1,NULL,10,0,0,0,0,NULL,NULL,'2026-07-10 07:40:24','2026-07-10 09:09:34',NULL),(2,2,NULL,'Romantic Dining Voucher','romantic-dining-voucher','ROMANTICDININGVOUCHER','A refined Nandini Jungle by Hanging Gardens gift voucher.','<p>Share a memorable Nandini experience with a beautifully presented gift voucher.</p>','<p>Inclusions are confirmed according to the selected voucher and availability.</p>','<p>Advance reservation is required. Voucher is subject to availability and cannot be exchanged for cash.</p>',NULL,NULL,NULL,'dining',NULL,1500000,0,'IDR',NULL,NULL,'days_after_issue',365,NULL,NULL,1,NULL,10,0,0,0,0,NULL,NULL,'2026-07-10 07:40:24','2026-07-10 09:09:34',NULL),(3,3,NULL,'Spa Experience Voucher','spa-experience-voucher','SPAEXPERIENCEVOUCHER','A refined Nandini Jungle by Hanging Gardens gift voucher.','<p>Share a memorable Nandini experience with a beautifully presented gift voucher.</p>','<p>Inclusions are confirmed according to the selected voucher and availability.</p>','<p>Advance reservation is required. Voucher is subject to availability and cannot be exchanged for cash.</p>',NULL,NULL,NULL,'spa',NULL,1200000,0,'IDR',NULL,NULL,'days_after_issue',365,NULL,NULL,1,NULL,10,0,0,0,0,NULL,NULL,'2026-07-10 07:40:24','2026-07-10 09:09:34',NULL),(4,1,NULL,'Jungle Stay Voucher','jungle-stay-voucher','JUNGLESTAYVOUCHER','A refined Nandini Jungle by Hanging Gardens gift voucher.','<p>Share a memorable Nandini experience with a beautifully presented gift voucher.</p>','<p>Inclusions are confirmed according to the selected voucher and availability.</p>','<p>Advance reservation is required. Voucher is subject to availability and cannot be exchanged for cash.</p>',NULL,NULL,NULL,'accommodation',NULL,4500000,0,'IDR',NULL,NULL,'days_after_issue',365,NULL,NULL,1,NULL,10,0,0,0,0,NULL,NULL,'2026-07-10 07:40:24','2026-07-10 09:09:34',NULL),(5,1,NULL,'Panoramic Jacuzzi Royal Suite Voucher','panoramic-jacuzzi-royal-suite-voucher','PANORAMICJACUZZIROYALSUITEVOUCHER','A refined Nandini Jungle by Hanging Gardens gift voucher.','<p>Share a memorable Nandini experience with a beautifully presented gift voucher.</p>','<p>Inclusions are confirmed according to the selected voucher and availability.</p>','<p>Advance reservation is required. Voucher is subject to availability and cannot be exchanged for cash.</p>',NULL,NULL,NULL,'accommodation',NULL,6500000,0,'IDR',NULL,NULL,'days_after_issue',365,NULL,NULL,1,NULL,10,0,0,0,0,NULL,NULL,'2026-07-10 07:40:24','2026-07-10 09:09:34',NULL),(6,4,NULL,'Nandini Experience Voucher','nandini-experience-voucher','NANDINIEXPERIENCEVOUCHER','A refined Nandini Jungle by Hanging Gardens gift voucher.','<p>Share a memorable Nandini experience with a beautifully presented gift voucher.</p>','<p>Inclusions are confirmed according to the selected voucher and availability.</p>','<p>Advance reservation is required. Voucher is subject to availability and cannot be exchanged for cash.</p>',NULL,NULL,NULL,'experience',NULL,2500000,0,'IDR',NULL,NULL,'days_after_issue',365,NULL,NULL,1,NULL,10,0,0,0,0,NULL,NULL,'2026-07-10 07:40:24','2026-07-10 09:09:34',NULL),(7,6,NULL,'Balinese blessing purification at the holy river','balinese-blessing-purification-at-the-holy-river','EXP-BALINESEBLESSINGPURIFICATIONATTHEHOLYRIVER','Surrender to the embrace of Bali\'s healing waters with a sacred purification ritual led by a Balinese priest. This riverside wellness retreat renews your body and soul in a soulful, restorative escape.','<p>Surrender to the embrace of Bali&#039;s healing waters with a sacred purification ritual led by a Balinese priest. This riverside wellness retreat renews your body and soul in a soulful, restorative escape.<br><br><strong>Inclusions:</strong><br>A healthy welcome drink upon arrival<br>Traditional Balinese sarong and towels provided<br>Balinese healing and purification ritual with energy transfer led by Pemangku</p>','<p></p>','<p>Advance reservation is required. Voucher is subject to availability and cannot be exchanged for cash.</p>',NULL,NULL,'Balinese blessing purification at the holy river','experience',NULL,2500000,10,'IDR','plus_plus','per_couple','days_after_issue',365,NULL,NULL,1,NULL,10,0,1,1,1,'Balinese Blessing Purification at the Holy River | Nandini Bali','Experience a Balinese blessing purification at Nandini’s Holy River in Ubud, with healing rituals, traditional sarong, welcome drink, and priest-led cleansing.','2026-07-10 08:08:32','2026-07-11 03:56:47',NULL),(8,6,NULL,'sacred waters: Half-Day Ubud Healing Retreat','sacred-waters-half-day-ubud-healing-retreat','EXP-SACREDWATERSHALFDAYUBUDHEALINGRETREAT','Reconnect with nature and your inner self through a serene half-day wellness journey by the river. Begin with our signature Spa at the River treatment designed to relax and restore body and mind. Continue with a Melukat purification ritual, a sacred Balinese blessing led by a traditional priest to cleanse and renew your spirit.','<p>Reconnect with nature and your inner self through a serene half-day wellness journey by the river. Begin with our signature Spa at the River treatment designed to relax and restore body and mind. Continue with a Melukat purification ritual, a sacred Balinese blessing led by a traditional priest to cleanse and renew your spirit.<br><br><strong>Inclusions:</strong><br>Traditional Balinese sarong and towels<br>Melukat purification and blessing ceremony by the river<br>Signature Spa at the River treatment for two<br>60-minutes Exotic Balinese massage<br>30-minutes Body mask<br>30-minutes Body Scrub treatment<br>Couple of tea at the spa reception</p>','<p></p>','<p>Advance reservation is required. Voucher is subject to availability and cannot be exchanged for cash.</p>','experiences/2020a644-e920-4642-8fb0-7abb772b6301.webp','experiences/2020a644-e920-4642-8fb0-7abb772b6301.webp','sacred waters: Half-Day Ubud Healing Retreat','experience',NULL,9000000,0,'IDR','plus_plus','per_couple','days_after_issue',365,NULL,NULL,1,NULL,10,0,0,1,2,'Half-Day Wellness Journey by the River | Nandini Bali','Reconnect with nature through a half-day wellness journey at Nandini Bali, featuring Spa at the River, Melukat purification, Balinese massage, and body treatments.','2026-07-10 08:08:32','2026-07-10 09:09:34',NULL),(9,6,NULL,'Nandini Signature:  Spa On The River','nandini-signature-spa-on-the-river','EXP-NANDINISIGNATURESPAONTHERIVER','Indulge in the ultimate riverside retreat by the sacred Ayung River. Begin your journey with a soothing foot bath, followed by a 180-minute signature spa treatment, serenaded by the sounds of the rushing waters.','<p>Indulge in the ultimate riverside retreat by the sacred Ayung River. Begin your journey with a soothing foot bath, followed by a 180-minute signature spa treatment, serenaded by the sounds of the rushing waters.<br><br><strong>Inclusions:</strong><br>60-minutes Exotic Balinese massage<br>30-minutes Body mask<br>30-minutes Body Scrub treatment<br>60-minutes Relaxing Facial Treatments</p>','<p></p>','<p>Advance reservation is required. Voucher is subject to availability and cannot be exchanged for cash.</p>',NULL,NULL,'Riverside Spa Retreat by Ayung River | Nandini Bali','spa',NULL,4000000,0,'IDR','plus_plus','per_person','days_after_issue',365,NULL,NULL,1,NULL,10,0,1,1,3,'Riverside Spa Retreat by Ayung River | Nandini Bali','Indulge in a riverside spa retreat by the sacred Ayung River at Nandini Bali, with a soothing foot bath, signature spa treatment, massage, scrub, mask, and facial.','2026-07-10 08:08:32','2026-07-11 04:00:16',NULL),(10,7,NULL,'Moonlit Jungle Romance','moonlit-jungle-romance','EXP-MOONLITJUNGLEROMANCE','Bask in an evening of romance under the moonlit sky, savoring a four-course dinner by the pool. As the sun sets beyond the jungle’s canopy, the soft candlelight envelops you, transforming your night into an intimate, dreamlike escape—a memory to cherish forever.','<p>Bask in an evening of romance under the moonlit sky, savoring a four-course dinner by the pool. As the sun sets beyond the jungle’s canopy, the soft candlelight envelops you, transforming your night into an intimate, dreamlike escape—a memory to cherish forever.</p>','<p></p>','<p>Advance reservation is required. Voucher is subject to availability and cannot be exchanged for cash.</p>',NULL,NULL,'Moonlit Jungle Romance | Candlelit Poolside Dinner Experience','experience',NULL,8500000,0,'IDR','plus_plus','per_couple','days_after_issue',365,NULL,NULL,1,NULL,10,0,1,1,4,'Moonlit Jungle Romance | Candlelit Poolside Dinner Experience','Enjoy a magical moonlit jungle romance with an intimate four-course candlelit dinner by the pool. Surrounded by soft lights and jungle serenity, this enchanting evening is made.','2026-07-10 08:08:32','2026-07-11 04:07:24',NULL),(11,7,NULL,'Riverside Romance','riverside-romance','EXP-RIVERSIDEROMANCE','Descend through lush jungle paths to the riverbank, where a truly intimate, six-course dinner awaits you. As you approach the wooden deck suspended above the Ayung River, the scene unfolds: hundreds of breathtaking, flickering candlelights.','<p>Descend through lush jungle paths to the riverbank, where a truly intimate, six-course dinner awaits you. As you approach the wooden deck suspended above the Ayung River, the scene unfolds: hundreds of breathtaking, flickering candlelights. The sound of the rushing water drowns out the world beyond, drawing your body and spirit into a timeless moment with your loved one.</p>','<p></p>','<p>Advance reservation is required. Voucher is subject to availability and cannot be exchanged for cash.</p>','experiences/main/79b27fd8-8a3b-435d-a2b4-a88be75d645b.webp','experiences/cards/c7329aa9-3ce8-45fa-9076-4e68a3b9cbe9.webp','Riverside Romance | Private Candlelit Dinner by the Ayung River','experience',NULL,13225000,0,'IDR','plus_plus','per_couple','days_after_issue',365,NULL,NULL,1,NULL,10,0,0,1,5,'Riverside Romance | Private Candlelit Dinner by the Ayung River','Experience an unforgettable riverside romance with a private six-course candlelit dinner above the Ayung River. Surrounded by jungle serenity, flickering lights, and flowing.','2026-07-10 08:08:32','2026-07-10 09:09:34',NULL),(12,7,NULL,'Romantic Dining by The Chapel','romantic-dining-by-the-chapel','EXP-ROMANTICDININGBYTHECHAPEL','Treat your love to an unforgettable romantic dinner under the moonlight at Nandini Jungle. Experience royal treatment and a magical evening by our iconic chapel overlooking the mystical jungle pool.','<p>Treat your love to an unforgettable romantic dinner under the moonlight at Nandini Jungle. Experience royal treatment and a magical evening by our iconic chapel overlooking the mystical jungle pool.</p>','<p></p>','<p>Advance reservation is required. Voucher is subject to availability and cannot be exchanged for cash.</p>','experiences/main/a912b278-02dd-4f6d-afb0-0153b8281862.webp','experiences/cards/249fc0ed-3b36-4e4c-a38d-a5f9bd003313.webp','Romantic Dining by the Chapel | Private Candlelit Dinner Experience','dining',NULL,6500000,0,'IDR','plus_plus','per_couple','days_after_issue',365,NULL,NULL,1,NULL,10,0,0,1,6,'Romantic Dining by the Chapel | Private Candlelit Dinner Experience','Enjoy an unforgettable romantic dining experience by the chapel with a private candlelit dinner under the moonlight, surrounded by tranquil jungle ambiance and timeless elegance.','2026-07-10 08:08:32','2026-07-10 09:09:34',NULL),(13,8,NULL,'Riverside Sanctuary Spa Package','riverside-sanctuary-spa-package','EXP-RIVERSIDESANCTUARYSPAPACKAGE','Indulge in the ultimate riverside retreat by the sacred Ayung River. Begin your journey with a soothing foot bath, followed by a combination of our signature spa treatments—traditional Balinese massage, body mask, and body scrub, serenaded by the sounds of the rushing waters.','<p>Indulge in the ultimate riverside retreat by the sacred Ayung River. Begin your journey with a soothing foot bath, followed by a combination of our signature spa treatments—traditional Balinese massage, body mask, and body scrub, serenaded by the sounds of the rushing waters.</p>','<p></p>','<p>Advance reservation is required. Voucher is subject to availability and cannot be exchanged for cash.</p>','experiences/main/9b3cf57f-ac67-4dd7-9871-ca2a055b6343.webp','experiences/cards/21025695-2987-4ad7-a8d2-f628791cc323.webp','Riverside Sanctuary Spa Package | Ultimate Ayung River Retreat','spa',NULL,6606000,0,'IDR','plus_plus','per_couple','days_after_issue',365,NULL,NULL,1,NULL,10,0,0,1,7,'Riverside Sanctuary Spa Package | Ultimate Ayung River Retreat','Indulge in a serene riverside spa journey by the sacred Ayung River, featuring a soothing foot bath, traditional Balinese massage, body scrub, and body mask, all accompanied by.','2026-07-10 08:08:32','2026-07-10 09:09:34',NULL),(14,8,NULL,'Luxury Spa Package: Wine Spa','luxury-spa-package-wine-spa','EXP-LUXURYSPAPACKAGEWINESPA','Designed to renew both body and spirit, this 2.5-hour wellness ritual blends ancient Balinese massage techniques with the potent antioxidants of wine, deeply nourishing the skin.','<p>Designed to renew both body and spirit, this 2.5-hour wellness ritual blends ancient Balinese massage techniques with the potent antioxidants of wine, deeply nourishing the skin. The journey features a gentle flower bath and a ceremonial wine-pouring, complemented by celebratory glasses of red wine and canapés amidst the ambiance of the jungle and the sound of the river.<br><br><strong>Includes:</strong></p><ul><li><p>60-minute traditional Balinese massage</p></li><li><p>wine-infused body scrub</p></li><li><p>wine-infused body mask</p></li><li><p>a luxe flower bath</p></li><li><p>2 glasses of red wine and chef’s curated canapés</p></li></ul>','<p></p>','<p>Advance reservation is required. Voucher is subject to availability and cannot be exchanged for cash.</p>','experiences/main/28a2dfe0-aaea-4c5c-b596-8cf51d025779.webp','experiences/cards/78e8cc44-0a2b-49e5-8e87-102827601ff1.webp','Luxury Wine Spa Package | Indulgent Wellness Ritual','spa',NULL,7704000,0,'IDR','plus_plus','per_couple','days_after_issue',365,NULL,NULL,1,NULL,10,0,0,1,8,'Luxury Wine Spa Package | Indulgent Wellness Ritual','Indulge in a luxurious wine spa experience featuring Balinese massage, wine-infused body treatments, a flower bath, and celebratory wine and canapés in a serene jungle setting.','2026-07-10 08:08:32','2026-07-10 09:09:34',NULL),(15,8,NULL,'Riverside Bliss: Half-Day Picnic and Wellness Escape at the River','riverside-bliss-half-day-picnic-and-wellness-escape-at-the-river','EXP-RIVERSIDEBLISSHALFDAYPICNICANDWELLNESSESCAPEATTHERIVER','Treat your loved one to an intimate riverside picnic by the scenic river\'s edge for moments of leisure and connection amidst the pristine Ubud jungle. ','<p>Treat your loved one to an intimate riverside picnic by the scenic river&#039;s edge for moments of leisure and connection amidst the pristine Ubud jungle. Cooled by the river’s touch and enriched by a traditional Balinese massage for two, this restorative retreat invites you to slow down, savor the present, and breathe in harmony with the heart of nature.</p>','<p></p>','<p>Advance reservation is required. Voucher is subject to availability and cannot be exchanged for cash.</p>','experiences/main/e113296d-0015-4549-a4e4-d9495cc50169.webp','experiences/cards/0acc3f2d-b6d6-4dd0-81fe-ca97ab164990.webp','Riverside Bliss Half-Day Picnic & Wellness Escape | Romantic Retreat','spa',NULL,5500000,0,'IDR','plus_plus','per_couple','days_after_issue',365,NULL,NULL,1,NULL,10,0,0,1,9,'Riverside Bliss Half-Day Picnic & Wellness Escape | Romantic Retreat','Enjoy a serene half-day riverside picnic paired with a relaxing wellness experience. Unwind by the river, savor intimate moments, and reconnect amidst Ubud’s pristine jungle.','2026-07-10 08:08:32','2026-07-10 09:09:34',NULL),(16,8,NULL,'Yoga at DJiwa Shala','yoga-at-djiwa-shala','EXP-YOGAATDJIWASHALA','Ubud and yoga is inseparable, the tranquility of rainforest and rice fields bring the great zen that is great for mind and body.','<p>With the tranquility of rainforests and rice fields, Ubud is synonymous with yoga, a place for mind and body to find great zen.<br><br>Among the rice fields of Ubud, a 97-square-meter Djiwa Shala is designed for Yoga and peace. With the sound of the breeze and the hymn of the birds in the rice fields, you&#039;ll experience a relaxing Yoga with a unique experience.</p>','<p></p>','<p>Advance reservation is required. Voucher is subject to availability and cannot be exchanged for cash.</p>','experiences/main/46e88c77-a2e2-4ab4-b10f-f2745c0665e3.webp','experiences/cards/1132dfc6-93ee-488c-95c0-40a0d8e4aed9.webp','Yoga at Djiwa Shala | Tranquil Yoga Experience in Ubud','spa',NULL,1200000,0,'IDR','plus_plus','per_person','days_after_issue',365,NULL,NULL,1,NULL,10,0,0,1,10,'Yoga at Djiwa Shala | Tranquil Yoga Experience in Ubud','Experience yoga at Djiwa Shala, surrounded by Ubud’s rainforest and rice fields. A peaceful practice designed to restore balance, clarity, and inner calm in nature.','2026-07-10 08:08:32','2026-07-10 09:09:34',NULL),(17,8,NULL,'Hatha Yoga & Lunch','hatha-yoga-lunch','EXP-HATHAYOGALUNCH','Centered on breathwork and alignment, the Hatha Yoga session invites you to breathe in rhythm with nature, cultivating balance and inner calm amid the lush rice paddies.','<p>Centered on breathwork and alignment, the Hatha Yoga session invites you to breathe in rhythm with nature, cultivating balance and inner calm amid the lush rice paddies. Conclude your healing journey with a nourishing lunch for two, thoughtfully prepared to enrich and rejuvenate your afternoon at our serene wellness resort.</p>','<p></p>','<p>Advance reservation is required. Voucher is subject to availability and cannot be exchanged for cash.</p>','experiences/main/fdabd4ef-864d-46cc-8256-de3bbb6a5586.webp','experiences/cards/d367325f-a4e4-4415-a7f6-41d2a2141f2f.webp','Hatha Yoga & Lunch | Wellness Experience in Nature','spa',NULL,3000000,0,'IDR','plus_plus','per_couple','days_after_issue',365,NULL,NULL,1,NULL,10,0,0,1,11,'Hatha Yoga & Lunch | Wellness Experience in Nature','Enjoy a rejuvenating Hatha Yoga session focused on breath and balance, followed by a nourishing lunch. A serene wellness experience set amid lush rice paddies and tranquil nature.','2026-07-10 08:08:32','2026-07-10 09:09:34',NULL),(18,8,NULL,'Yoga or Chakra Meditation at Sacred River','yoga-or-chakra-meditation-at-sacred-river','EXP-YOGAORCHAKRAMEDITATIONATSACREDRIVER','Awaken your spirit by the sacred river, where the gentle flow of water and the heartbeat of the jungle create a sanctuary of renewal. Embrace yoga that moves in harmony with nature’s rhythm, restoring balance, vitality, and inner calm.','<p>Awaken your spirit by the sacred river, where the gentle flow of water and the heartbeat of the jungle create a sanctuary of renewal. Embrace yoga that moves in harmony with nature’s rhythm, restoring balance, vitality, and inner calm. Or journey within through chakra meditation, a powerful practice that purifies and aligns the seven chakras. When your energy centers are in harmony, you become more attuned to the natural flow of the universe, opening the path to clarity, well-being, and deep inner peace.</p>','<p></p>','<p>Advance reservation is required. Voucher is subject to availability and cannot be exchanged for cash.</p>','experiences/main/2c1ace46-a39b-4a85-a604-88c80da56159.webp','experiences/cards/9d8c9dc9-3224-47a8-a0cc-082e095b1938.webp','Yoga or Chakra Meditation at Sacred River | Wellness Experience','spa',NULL,1800000,0,'IDR','plus_plus','per_person','days_after_issue',365,NULL,NULL,1,NULL,10,0,0,1,12,'Yoga or Chakra Meditation at Sacred River | Wellness Experience','Reconnect mind and body with yoga or chakra meditation by a sacred river. Experience deep relaxation, energy alignment, and inner balance surrounded by tranquil jungle nature.','2026-07-10 08:08:32','2026-07-10 09:09:34',NULL),(19,8,NULL,'Jungle Wellness & Retreat Day Pass','jungle-wellness-retreat-day-pass','EXP-JUNGLEWELLNESSRETREATDAYPASS','Reconnect with your body and spirit in the peaceful embrace of the jungle. Start with rejuvenating yoga and guided meditation, then indulge in a traditional Balinese massage for two. ','<p>Reconnect with your body and spirit in the peaceful embrace of the jungle. Start with rejuvenating yoga and guided meditation, then indulge in a traditional Balinese massage for two. Savor a hearty 3-course meal and afternoon high tea. Enjoy exclusive access to the sacred Ayung River, where the healing energy flows through this secluded wellness retreat in Ubud’s lush jungle.</p>','<p></p>','<p>Advance reservation is required. Voucher is subject to availability and cannot be exchanged for cash.</p>','experiences/main/ead96bac-7be7-4932-bbb4-94784b5119c1.webp','experiences/cards/eda10a25-98ac-4f1d-979e-fc6bea24d342.webp','Jungle Wellness & Retreat Day Pass | Holistic Escape in Ubud','spa',NULL,6000000,0,'IDR','plus_plus','per_couple','days_after_issue',365,NULL,NULL,1,NULL,10,0,0,1,13,'Jungle Wellness & Retreat Day Pass | Holistic Escape in Ubud','Rejuvenate mind and body with a jungle wellness day pass featuring yoga, guided meditation, traditional Balinese massage, dining, and serene river access in Ubud’s lush.','2026-07-10 08:08:32','2026-07-10 09:09:34',NULL),(20,8,NULL,'Self-Healing Day Pass','self-healing-day-pass','EXP-SELFHEALINGDAYPASS','Begin your journey to inner peace with a sacred Balinese Blessing Purification by the healing waters of the Ayung River. Follow with an indulgent couples\' massage and afternoon high tea. ','<p>Begin your journey to inner peace with a sacred Balinese Blessing Purification by the healing waters of the Ayung River. Follow with an indulgent couples&#039; massage and afternoon high tea. With exclusive access to the river and full-day pool access, this package invites you to experience the deep spiritual wellness that only our Ubud healing retreat can offer.</p>','<p></p>','<p>Advance reservation is required. Voucher is subject to availability and cannot be exchanged for cash.</p>','experiences/main/8999dc72-63b2-444d-8164-06f6754e8d76.webp','experiences/cards/1b539885-56df-4e99-be49-79a306c2c7f9.webp','Self-Healing Day Pass | Sacred River Wellness Experience','spa',NULL,6120000,0,'IDR','plus_plus','per_couple','days_after_issue',365,NULL,NULL,1,NULL,10,0,0,1,14,'Self-Healing Day Pass | Sacred River Wellness Experience','Restore inner peace with a Self-Healing Day Pass featuring sacred river purification, a relaxing couples’ massage, and refined afternoon high tea in a serene jungle setting.','2026-07-10 08:08:32','2026-07-10 09:09:34',NULL),(21,9,NULL,'The Floating Boat Experience','the-floating-boat-experience','EXP-THEFLOATINGBOATEXPERIENCE','Indulge in a moment of pure tranquility aboard our handcrafted floating boat, gently drifting atop the iconic main pool at Nandini Jungle by Hanging Gardens. ','<p>Indulge in a moment of pure tranquility aboard our handcrafted floating boat, gently drifting atop the iconic main pool at Nandini Jungle by Hanging Gardens. Surrounded by the emerald canopy of the Ubud rainforest, this exclusive experience invites you to unwind in timeless elegance, whether basking in the golden hour or enjoying a bespoke floating breakfast beneath the open sky. Seamlessly blending natural beauty with opulent comfort, this is where jungle serenity meets unparalleled sophistication.</p>','<p></p>','<p>Advance reservation is required. Voucher is subject to availability and cannot be exchanged for cash.</p>','experiences/main/ab427c14-c48e-4ff5-9e58-7ff322919a3f.webp','experiences/cards/5b41a051-0dbb-4a53-88ae-01661da6b89f.webp','The Floating Boat Experience | Luxury Pool Dining in Ubud','dining',NULL,2700000,0,'IDR','plus_plus','per_couple','days_after_issue',365,NULL,NULL,1,NULL,10,0,0,1,15,'The Floating Boat Experience | Luxury Pool Dining in Ubud','Indulge in a serene floating boat experience, drifting gently across a tranquil jungle pool. Enjoy bespoke dining in an elegant setting surrounded by Ubud’s lush rainforest.','2026-07-10 08:08:32','2026-07-10 09:09:34',NULL),(22,9,NULL,'Luxe High Tea','luxe-high-tea','EXP-LUXEHIGHTEA','Relish a refined afternoon with our Luxe High Tea amidst the verdant jungles of Ubud. Delight in premium teas, freshly brewed coffee, and a chef-curated selection of sweet and savory bites blending flavors from East and West. An elegant indulgence for two, this experience offers a perfect pause as your day drifts into a tranquil dusk.','<p>Relish a refined afternoon with our Luxe High Tea amidst the verdant jungles of Ubud. Delight in premium teas, freshly brewed coffee, and a chef-curated selection of sweet and savory bites blending flavors from East and West. An elegant indulgence for two, this experience offers a perfect pause as your day drifts into a tranquil dusk.</p>','<p></p>','<p>Advance reservation is required. Voucher is subject to availability and cannot be exchanged for cash.</p>','experiences/main/d84ba74e-87a1-4b3b-a904-3c2142a58d33.webp','experiences/cards/d10a3431-a83f-4735-b419-e4561f3d7dfb.webp','Luxe High Tea in Ubud | Elegant Afternoon Tea Experience','dining',NULL,750000,0,'IDR','plus_plus','per_couple','days_after_issue',365,NULL,NULL,1,NULL,10,0,0,1,16,'Luxe High Tea in Ubud | Elegant Afternoon Tea Experience','Indulge in a refined Luxe High Tea set amidst Ubud’s lush jungle surroundings. Enjoy premium teas, freshly brewed coffee, and curated sweet and savory delights for a relaxing.','2026-07-10 08:08:32','2026-07-10 09:09:34',NULL),(23,9,NULL,'Cooking Class','cooking-class','EXP-COOKINGCLASS','You are what you eat. Our executive chef Gustu brings this philosophy to heart and invites all our guests to enjoy the excellence of Balinese cooking made by themselves.','<p>You are what you eat. Our executive chef Gustu brings this philosophy to heart and invites all our guests to enjoy the excellence of Balinese cooking made by themselves.<br><br>Join Nandini Cooking Class and learn how to cook like a Balinese, bringing a culinary journey you experienced to your home.<br><br><strong>24 hours booking is required in advance.</strong></p>','<p></p>','<p>Advance reservation is required. Voucher is subject to availability and cannot be exchanged for cash.</p>','experiences/main/54986383-f3ff-445e-97e0-fee0fd4f173a.webp','experiences/cards/0a328525-7f53-43f9-b4e0-c1481e36ac4b.webp','Balinese Cooking Class | Authentic Culinary Experience in Bali','dining',NULL,3500000,0,'IDR','plus_plus','per_couple','days_after_issue',365,NULL,NULL,1,NULL,10,0,0,1,17,'Balinese Cooking Class | Authentic Culinary Experience in Bali','Join an immersive Balinese cooking class guided by an expert chef. Learn traditional recipes, cook with fresh local ingredients, and bring the flavors of Bali home with you.','2026-07-10 08:08:32','2026-07-10 09:09:34',NULL),(24,10,NULL,'Regular Day Pass','regular-day-pass','EXP-REGULARDAYPASS','Escape into tranquility with our Regular Day Pass. Relish IDR 500,000 in resort credit, perfect for indulging in gourmet dining, spa rituals, or immersive experiences. Surrounded by the spiritual energy of the sacred Ayung River, your day of rejuvenation awaits at Bali’s most serene healing resort.','<p>Escape into tranquility with our Regular Day Pass. Relish IDR 500,000 in resort credit, perfect for indulging in gourmet dining, spa rituals, or immersive experiences. Surrounded by the spiritual energy of the sacred Ayung River, your day of rejuvenation awaits at Bali’s most serene healing resort.</p>','<p></p>','<p>Advance reservation is required. Voucher is subject to availability and cannot be exchanged for cash.</p>','experiences/main/40cba6e2-8e1a-46d5-afa7-448c9673eb6a.webp','experiences/cards/f75fc53c-efc3-49a4-b48d-56afbd480f5d.webp','Regular Day Pass | Relaxing Resort Day Experience in Bali','experience',NULL,500000,0,'IDR','plus_plus','per_person','days_after_issue',365,NULL,NULL,1,NULL,10,0,0,1,19,'Regular Day Pass | Relaxing Resort Day Experience in Bali','Enjoy a peaceful escape with a Regular Day Pass featuring resort credit for dining, spa treatments, and immersive experiences, all set beside the serene Ayung River.','2026-07-10 08:08:32','2026-07-10 09:09:34',NULL),(25,10,NULL,'Balinese Village Cultural Experience Day Pass','balinese-village-cultural-experience-day-pass','EXP-BALINESEVILLAGECULTURALEXPERIENCEDAYPASS','Step into the heart of Bali’s rich traditions with a guided sunset walk through the village, traditional bamboo kite-making, and an exclusive \"Nasi Jinggo One Hundred Dollars\" meal. ','<p>Step into the heart of Bali’s rich traditions with a guided sunset walk through the village, traditional bamboo kite-making, and an exclusive &quot;Nasi Jinggo One Hundred Dollars&quot; meal. Immerse yourself in the sacred ambiance of the jungle, with all-day access to the Mystical Jungle Pool, and exclusive entry to the Ayung River, a true cultural and spiritual retreat.</p>','<p></p>','<p>Advance reservation is required. Voucher is subject to availability and cannot be exchanged for cash.</p>','experiences/main/096f774c-0c26-4c21-ba7d-043fb713fb93.webp','experiences/cards/6d5dc5eb-ffd1-4f8c-9cc2-3ed2a3c0f6f7.webp','Balinese Village Cultural Experience Day Pass | Authentic Traditions','experience',NULL,4910000,0,'IDR','plus_plus','per_couple','days_after_issue',365,NULL,NULL,1,NULL,10,0,0,1,20,'Balinese Village Cultural Experience Day Pass | Authentic Traditions','Immerse yourself in Balinese village life with a cultural day pass featuring a guided sunset walk, traditional bamboo kite-making, exclusive dining, and serene jungle surroundings.','2026-07-10 08:08:32','2026-07-10 09:09:34',NULL),(26,10,NULL,'Ayung White Water Rafting','ayung-white-water-rafting','EXP-AYUNGWHITEWATERRAFTING','Elevate your Bali adventure with the ultimate river rafting experience on the Ayung River. This thrilling journey combines adrenaline, breathtaking nature, and pure excitement as you navigate through exhilarating rapids, glide past cascading waterfalls, and immerse yourself in the lush tropical jungle.','<p>Elevate your Bali adventure with the ultimate river rafting experience on the Ayung River. This thrilling journey combines adrenaline, breathtaking nature, and pure excitement as you navigate through exhilarating rapids, glide past cascading waterfalls, and immerse yourself in the lush tropical jungle.<br><br>Perfect for both beginners and adventure enthusiasts, expert guides ensure a safe and unforgettable ride. Get ready for splashes, laughter, and stunning scenery at every twist and turn of Bali’s most iconic river</p>','<p></p>','<p>Advance reservation is required. Voucher is subject to availability and cannot be exchanged for cash.</p>','experiences/main/9bee4b86-a1ba-421d-904a-112db1277ead.webp','experiences/cards/14f47f42-52c6-469e-a72f-dd9dcb481f09.webp','Ayung White Water Rafting | Bali River Adventure Experience','experience',NULL,995000,0,'IDR','plus_plus','per_person','days_after_issue',365,NULL,NULL,1,NULL,10,0,0,1,21,'Ayung White Water Rafting | Bali River Adventure Experience','Experience an exhilarating white water rafting adventure on one of Bali’s most iconic rivers. Navigate thrilling rapids, pass waterfalls, and immerse yourself in lush tropical.','2026-07-10 08:08:32','2026-07-10 09:09:34',NULL),(27,10,NULL,'ATV Adventure','atv-adventure','EXP-ATVADVENTURE','Embark on an exhilarating off-road ATV journey through Bali’s lush jungles, picturesque rice fields, and challenging muddy trails.','<p>Embark on an exhilarating off-road ATV journey through Bali’s lush jungles, picturesque rice fields, and challenging muddy trails. Designed for all skill levels, this guided adventure includes a comprehensive safety briefing before you navigate rivers, rugged terrains, and breathtaking landscapes. Prepare for an unforgettable ride filled with adventure and natural beauty</p>','<p></p>','<p>Advance reservation is required. Voucher is subject to availability and cannot be exchanged for cash.</p>','experiences/main/220b6fb1-d202-40a7-8363-896957c89391.webp','experiences/cards/5ba42cac-fd20-4ffa-ad77-5af06b8a2561.webp','ATV Adventure in Bali | Off-Road Jungle & Rice Field Ride','experience',NULL,1300000,0,'IDR','net','single_ride','days_after_issue',365,NULL,NULL,1,NULL,10,0,0,1,22,'ATV Adventure in Bali | Off-Road Jungle & Rice Field Ride','Experience an exhilarating ATV adventure through Bali’s lush jungles, scenic rice fields, and muddy off-road trails. A guided ride combining adrenaline, nature, and unforgettable.','2026-07-10 08:08:32','2026-07-10 09:09:34',NULL),(28,10,NULL,'Land Rover Jeep Sunrise Tour at Mount Batur','land-rover-jeep-sunrise-tour-at-mount-batur','EXP-LANDROVERJEEPSUNRISETOURATMOUNTBATUR','Experience the ultimate Land Rover Jeep adventure, traversing Bali’s rugged terrain on an unforgettable journey to witness the breathtaking sunrise over Mount Batur, Kintamani. This is more than just a sunrise tour—it’s a premier off-road expedition into the heart of nature.','<p>Experience the ultimate Land Rover Jeep adventure, traversing Bali’s rugged terrain on an unforgettable journey to witness the breathtaking sunrise over Mount Batur, Kintamani. This is more than just a sunrise tour—it’s a premier off-road expedition into the heart of nature.</p>','<p></p>','<p>Advance reservation is required. Voucher is subject to availability and cannot be exchanged for cash.</p>','experiences/main/4219406f-b5bc-4045-bd82-85b67e7de61f.webp','experiences/cards/b2bada05-e03b-40eb-b7c5-8c0576c7305d.webp','Land Rover Jeep Sunrise Tour at Mount Batur | Off-Road Adventure','experience',NULL,2500000,0,'IDR','plus_plus','per_car','days_after_issue',365,NULL,NULL,1,NULL,10,0,0,1,23,'Land Rover Jeep Sunrise Tour at Mount Batur | Off-Road Adventure','Discover Mount Batur at sunrise on a thrilling Land Rover Jeep adventure. Traverse rugged volcanic terrain and enjoy panoramic Kintamani views on an unforgettable off-road journey.','2026-07-10 08:08:32','2026-07-10 09:09:34',NULL),(29,10,NULL,'VW Safari Adventure','vw-safari-adventure','EXP-VWSAFARIADVENTURE','Embark on a thrilling open-air adventure in a vintage VW Safari to the breathtaking landscapes of Mount Batur, Kintamani.','<p>Embark on a thrilling open-air adventure in a vintage VW Safari to the breathtaking landscapes of Mount Batur, Kintamani. Explore the sacred Tirta Empul Holy Spring Temple, take in panoramic views of the majestic Mount Batur, stroll through a lush coffee plantation, and marvel at the iconic Tegallalang Rice Terraces. Immerse yourself in Bali’s natural beauty and cultural heritage on this unforgettable journey</p>','<p></p>','<p>Advance reservation is required. Voucher is subject to availability and cannot be exchanged for cash.</p>','experiences/main/45d90d81-012e-4c72-8ee0-eb64f4a2efba.webp','experiences/cards/ad1a42c5-abf0-4942-a752-e1fee9d9f361.webp','VW Safari Adventure | Open-Air Journey Around Mount Batur','experience',NULL,2500000,0,'IDR','plus_plus','per_person','days_after_issue',365,NULL,NULL,1,NULL,10,0,0,1,24,'VW Safari Adventure | Open-Air Journey Around Mount Batur','Explore Bali in a classic open-air VW safari, discovering Mount Batur, scenic Kintamani views, sacred springs, coffee plantations, and iconic rice terraces on one unforgettable.','2026-07-10 08:08:32','2026-07-10 09:09:34',NULL),(30,10,NULL,'Mount Batur Sunrise Trekking Adventure','mount-batur-sunrise-trekking-adventure','EXP-MOUNTBATURSUNRISETREKKINGADVENTURE','Step into the stillness of the night and begin an unforgettable journey to the summit of Mount Batur. Guided by the glow of the stars, each step brings you closer to an awe-inspiring reward. ','<p>Step into the stillness of the night and begin an unforgettable journey to the summit of Mount Batur. Guided by the glow of the stars, each step brings you closer to an awe-inspiring reward. As dawn breaks, watch in wonder as the sky transforms into a masterpiece of fiery hues, painting the landscape below. A breathtaking sunrise, crisp mountain air, and the thrill of adventure.</p>','<p></p>','<p>Advance reservation is required. Voucher is subject to availability and cannot be exchanged for cash.</p>','experiences/main/6e161eb5-8630-4020-82ca-6c33e3bca878.webp','experiences/cards/cb40c61e-aa62-43a4-9261-3d5af308b91c.webp','Mount Batur Sunrise Trekking Adventure | Iconic Bali Volcano Hike','experience',NULL,1660000,0,'IDR','plus_plus','per_couple','days_after_issue',365,NULL,NULL,1,NULL,10,0,0,1,25,'Mount Batur Sunrise Trekking Adventure | Iconic Bali Volcano Hike','Embark on a Mount Batur sunrise trekking adventure and witness a breathtaking dawn from the summit. Experience crisp mountain air, dramatic volcanic views, and an unforgettable.','2026-07-10 08:08:32','2026-07-10 09:09:34',NULL),(31,11,NULL,'From Ride to Relief','from-ride-to-relief','EXP-FROMRIDETORELIEF','Sunrise cycling with relaxing foot massage. A graceful journey of motion and calm at Payangan, guided through the nature—featuring a 60-minute cycling experience with guide and a 15-minute soothing foot massage.','<p>A graceful journey of motion and calm at Payangan, guided through the nature—featuring a 60-minute cycling experience with guide and a 15-minute soothing foot massage.</p>','<p></p>','<p>Advance reservation is required. Voucher is subject to availability and cannot be exchanged for cash.</p>','experiences/main/5071e8c8-4859-4cba-b8d6-07a14aab9342.webp','experiences/cards/f1d7c301-b2f1-42da-b48d-25cb69c33f02.webp','Sunrise Cycling & Foot Massage in Payangan | Nandini Jungle Bali','experience',NULL,1000000,0,'IDR','plus_plus','per_person','days_after_issue',365,NULL,NULL,1,NULL,10,0,0,1,27,'Sunrise Cycling & Foot Massage in Payangan | Nandini Jungle Bali','Experience a 60-minute guided sunrise cycling tour in Payangan followed by a 15-minute relaxing foot massage. A perfect blend of nature, movement, and wellness at Nandini Jungle.','2026-07-10 08:08:32','2026-07-10 09:09:34',NULL),(32,11,NULL,'From Bike Trails to Bar Skills','from-bike-trails-to-bar-skills','EXP-FROMBIKETRAILSTOBARSKILLS','Where movement meets mixology. A graceful balance of adventure and elegance, beginning with a 60-minute morning cycling journey and continuing with a 60-minute cocktails class creating three cocktails—where jungle air meets the art of mixology.','<p>A graceful balance of adventure and elegance, beginning with a 60-minute morning cycling journey and continuing with a 60-minute cocktails class creating three cocktails—where jungle air meets the art of mixology.</p>','<p></p>','<p>Advance reservation is required. Voucher is subject to availability and cannot be exchanged for cash.</p>','experiences/main/cde2b6ef-723b-4b5b-ab39-b45a920628c0.webp','experiences/cards/836ada4d-2595-4d64-b3a1-f9197792b4f2.webp','Bike Trails to Bar Skills Experience in Bali | Cycling & Cocktail Class','experience',NULL,2000000,0,'IDR','plus_plus','per_person','days_after_issue',365,NULL,NULL,1,NULL,10,0,0,1,28,'Bike Trails to Bar Skills in Bali | Nandini Jungle','Enjoy a unique Bali experience combining a 60-minute morning cycling tour with a 60-minute cocktail-making class. Discover where jungle adventure meets the art of mixology.','2026-07-10 08:08:32','2026-07-10 09:09:34',NULL),(33,11,NULL,'A Sacred Moment of Renewal','a-sacred-moment-of-renewal','EXP-ASACREDMOMENTOFRENEWAL','A guided purification and meditation experience rooted in tradition. A timeless ritual of renewal for body and spirit, guided by water and embraced by jungle serenity—featuring a 30-minute blessing purification followed by a 45-minute meditation by the river.','<p>A timeless ritual of renewal for body and spirit, guided by water and embraced by jungle serenity—featuring a 30-minute blessing purification followed by a 45-minute meditation by the river.</p>','<p></p>','<p>Advance reservation is required. Voucher is subject to availability and cannot be exchanged for cash.</p>','experiences/main/5c4c2115-48a0-4fce-bdda-abe75c5fcc75.webp','experiences/cards/ea58e6b3-2d4a-4f0a-84c6-ebed8284887b.webp','Sacred Renewal Ritual in Bali | Blessing & River Meditation Experience','experience',NULL,2500000,0,'IDR','plus_plus','per_person','days_after_issue',365,NULL,NULL,1,NULL,10,0,0,1,29,'Sacred Renewal Ritual in Bali | Blessing & River Meditation Experience','Join a sacred purification ritual in Bali featuring a 30-minute blessing ceremony and 45-minute river meditation. A guided spiritual renewal experience surrounded by serene.','2026-07-10 08:08:32','2026-07-10 09:09:34',NULL),(34,11,NULL,'Intimate Wellness Rituals','intimate-wellness-rituals','EXP-INTIMATEWELLNESSRITUALS','Where stillness heals and energy aligns. A timeless ritual of movement and restoration, beginning with a 60-minute private yoga practice and continuing with a 30-minute energy healing session—held within the hidden calm of river and jungle.','<p>A timeless ritual of movement and restoration, beginning with a 60-minute private yoga practice and continuing with a 30-minute energy healing session—held within the hidden calm of river and jungle.</p>','<p></p>','<p>Advance reservation is required. Voucher is subject to availability and cannot be exchanged for cash.</p>','experiences/main/a6c8d729-1fbc-49ac-99c3-eddc007f1f81.webp','experiences/cards/873daa1d-4111-43ec-8095-8be818c29ff6.webp','Intimate Wellness Rituals in Bali | Private Yoga & Energy Healing','experience',NULL,4000000,0,'IDR','plus_plus','per_person','days_after_issue',365,NULL,NULL,1,NULL,10,0,0,1,30,'Intimate Wellness Rituals in Bali | Private Yoga & Energy Healing','Reconnect through a 60-minute private yoga session followed by a 30-minute energy healing ritual in serene jungle surroundings. A restorative wellness experience in Bali.','2026-07-10 08:08:32','2026-07-10 09:09:34',NULL),(35,11,NULL,'Blissful Calm','blissful-calm','EXP-BLISSFULCALM','A Journey into well-being. A serene wellness experience designed with care for your body, mind, and soul—set within the calming embrace of the jungle.','<p>A serene wellness experience designed with care for your body, mind, and soul—set within the calming embrace of the jungle.<br><br><strong>Inclusions:</strong><br>45 minutes of Private Yoga Session<br>60 minutes of Exotic Spa Treatment<br>30 minutes of Complimentary Reflexology</p>','<p></p>','<p>Advance reservation is required. Voucher is subject to availability and cannot be exchanged for cash.</p>','experiences/main/ad3abde6-b5d6-4a8e-949f-e8bf104760da.webp','experiences/cards/ab484850-1d4d-402a-bbfb-a1ee743d38b1.webp','Blissful Calm Wellness Experience in Bali | Yoga, Spa & Reflexology','experience',NULL,2500000,0,'IDR','plus_plus','per_person','days_after_issue',365,NULL,NULL,1,NULL,10,0,0,1,31,'Blissful Calm Wellness Experience in Bali | Yoga, Spa & Reflexology','Indulge in a serene jungle wellness journey featuring a 45-minute private yoga session, 60-minute exotic spa treatment, and 30-minute complimentary reflexology for complete body.','2026-07-10 08:08:32','2026-07-10 09:09:34',NULL);

-- Record voucher migrations after schema and catalog import.
INSERT INTO `migrations` (`migration`, `batch`) SELECT '2026_07_10_000001_create_voucher_tables', @nandini_migration_batch WHERE NOT EXISTS (SELECT 1 FROM `migrations` WHERE `migration` = '2026_07_10_000001_create_voucher_tables');
INSERT INTO `migrations` (`migration`, `batch`) SELECT '2026_07_10_000002_add_experience_price_metadata_to_vouchers', @nandini_migration_batch WHERE NOT EXISTS (SELECT 1 FROM `migrations` WHERE `migration` = '2026_07_10_000002_add_experience_price_metadata_to_vouchers');
INSERT INTO `migrations` (`migration`, `batch`) SELECT '2026_07_11_000001_add_experience_and_discount_to_vouchers', @nandini_migration_batch WHERE NOT EXISTS (SELECT 1 FROM `migrations` WHERE `migration` = '2026_07_11_000001_add_experience_and_discount_to_vouchers');

-- New CMS table for the homepage "What Our Guests Say" slider.
CREATE TABLE IF NOT EXISTS `guest_reviews` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `reviewer_name` varchar(255) NOT NULL,
    `review_text` text NOT NULL,
    `rating` tinyint(3) UNSIGNED NOT NULL DEFAULT 5,
    `reviewed_at` date DEFAULT NULL,
    `source` varchar(255) DEFAULT NULL,
    `is_active` tinyint(1) NOT NULL DEFAULT 1,
    `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `guest_reviews_is_active_index` (`is_active`),
    KEY `guest_reviews_sort_order_index` (`sort_order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Guest reviews already entered in the local Filament CMS. Re-importing this
-- script updates these records instead of creating duplicates.
INSERT INTO `guest_reviews` (
    `id`,
    `reviewer_name`,
    `review_text`,
    `rating`,
    `reviewed_at`,
    `source`,
    `is_active`,
    `sort_order`,
    `created_at`,
    `updated_at`
) VALUES
(
    1,
    'Nick Z',
    'Best jungle experience - The most amazing relaxing experience in a jungle setting. The service of the staff is second to none and they were so accommodating with every request, always with a smile. We recommend this resort to anyone wanting a magical getaway. Special thanks to Agus and Tirta for being so friendly.',
    5,
    '2026-07-10',
    'TripAdvisor',
    1,
    0,
    '2026-07-14 12:49:30',
    '2026-07-14 12:49:30'
),
(
    2,
    'Maria P',
    'Such tranquility - Had a wonderful experience at this resort. The rooms are very spacious and have a great view of the jungle. The staff is extremely attentive and sweet. Highly recommend this place.',
    5,
    '2026-06-01',
    'TripAdvisor',
    1,
    0,
    '2026-07-14 12:51:52',
    '2026-07-14 12:54:21'
),
(
    3,
    'Celine D',
    'Highly recommend staying here! Loved our stay. Relaxing atmosphere, beautiful hotel and location, food was incredible. Attentive service. Had one of the best dinners of my life here.',
    5,
    '2026-06-01',
    'TripAdvisor',
    1,
    0,
    '2026-07-14 12:53:41',
    '2026-07-14 12:53:41'
)
ON DUPLICATE KEY UPDATE
    `reviewer_name` = VALUES(`reviewer_name`),
    `review_text` = VALUES(`review_text`),
    `rating` = VALUES(`rating`),
    `reviewed_at` = VALUES(`reviewed_at`),
    `source` = VALUES(`source`),
    `is_active` = VALUES(`is_active`),
    `sort_order` = VALUES(`sort_order`),
    `updated_at` = VALUES(`updated_at`);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_07_14_000001_create_guest_reviews_table', @nandini_migration_batch
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations`
    WHERE `migration` = '2026_07_14_000001_create_guest_reviews_table'
);


