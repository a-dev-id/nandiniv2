-- Nandini Partner Circle / Affiliate module
-- Import into the EXISTING Nandini Laravel database using phpMyAdmin.
--
-- This script creates missing Affiliate tables, seeds roles and permissions,
-- adds the Affiliate booking-code column, and records the matching Laravel
-- migrations. It does not include local Affiliate accounts or test analytics.
-- Existing tables and records are not dropped.
--
-- IMPORTANT: Replace the administrator email below before importing.

SET NAMES utf8mb4;
SET @affiliate_admin_email = 'REPLACE_WITH_YOUR_ADMIN_EMAIL';

-- Add the Affiliate voucher/code field used by booking synchronization.
SET @affiliate_booking_table_exists = (
    SELECT COUNT(*)
    FROM information_schema.tables
    WHERE table_schema = DATABASE()
      AND table_name = 'synced_webhotelier_bookings'
);

SET @affiliate_code_column_exists = (
    SELECT COUNT(*)
    FROM information_schema.columns
    WHERE table_schema = DATABASE()
      AND table_name = 'synced_webhotelier_bookings'
      AND column_name = 'affiliate_code'
);

SET @affiliate_sql = IF(
    @affiliate_booking_table_exists = 1 AND @affiliate_code_column_exists = 0,
    'ALTER TABLE `synced_webhotelier_bookings` ADD COLUMN `affiliate_code` VARCHAR(191) COLLATE utf8mb4_unicode_ci NULL AFTER `source_name`',
    'SELECT 1'
);
PREPARE affiliate_statement FROM @affiliate_sql;
EXECUTE affiliate_statement;
DEALLOCATE PREPARE affiliate_statement;

SET @affiliate_code_index_exists = (
    SELECT COUNT(*)
    FROM information_schema.statistics
    WHERE table_schema = DATABASE()
      AND table_name = 'synced_webhotelier_bookings'
      AND index_name = 'synced_webhotelier_bookings_affiliate_code_index'
);

SET @affiliate_sql = IF(
    @affiliate_booking_table_exists = 1
        AND @affiliate_code_column_exists + (
            SELECT COUNT(*)
            FROM information_schema.columns
            WHERE table_schema = DATABASE()
              AND table_name = 'synced_webhotelier_bookings'
              AND column_name = 'affiliate_code'
        ) > 0
        AND @affiliate_code_index_exists = 0,
    'ALTER TABLE `synced_webhotelier_bookings` ADD INDEX `synced_webhotelier_bookings_affiliate_code_index` (`affiliate_code`)',
    'SELECT 1'
);
PREPARE affiliate_statement FROM @affiliate_sql;
EXECUTE affiliate_statement;
DEALLOCATE PREPARE affiliate_statement;

CREATE TABLE IF NOT EXISTS `roles` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `slug` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `roles_name_unique` (`name`),
    UNIQUE KEY `roles_slug_unique` (`slug`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `permissions` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `permissions_name_unique` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `role_has_permissions` (
    `role_id` BIGINT UNSIGNED NOT NULL,
    `permission_id` BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (`role_id`, `permission_id`),
    KEY `role_has_permissions_permission_id_foreign` (`permission_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `model_has_roles` (
    `role_id` BIGINT UNSIGNED NOT NULL,
    `model_type` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `model_id` BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (`role_id`, `model_type`, `model_id`),
    KEY `model_has_roles_model_type_model_id_index` (`model_type`, `model_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `affiliate_program_settings` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `program_name` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Nandini Partner Circle',
    `affiliate_commission_percentage` DECIMAL(5,2) NOT NULL DEFAULT 10.00,
    `guest_discount_percentage` DECIMAL(5,2) NOT NULL DEFAULT 3.00,
    `payment_cycle` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'monthly',
    `preferred_payment_method` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'wise',
    `alternative_payment_method` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bank_transfer',
    `minimum_payout_amount` DECIMAL(14,2) NOT NULL DEFAULT 500000.00,
    `currency` CHAR(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IDR',
    `review_time_message` TEXT COLLATE utf8mb4_unicode_ci NULL,
    `review_time_expectation_hours` SMALLINT UNSIGNED NOT NULL DEFAULT 48,
    `booking_engine_base_url` VARCHAR(191) COLLATE utf8mb4_unicode_ci NULL,
    `affiliate_domain` VARCHAR(191) COLLATE utf8mb4_unicode_ci NULL,
    `short_link_domain` VARCHAR(191) COLLATE utf8mb4_unicode_ci NULL,
    `payout_release_days` SMALLINT UNSIGNED NOT NULL DEFAULT 30,
    `commission_validation_start_day` TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `commission_validation_end_day` TINYINT UNSIGNED NOT NULL DEFAULT 7,
    `preferred_payment_method_requires_finance_confirmation` TINYINT(1) NOT NULL DEFAULT 1,
    `minimum_payout_requires_finance_confirmation` TINYINT(1) NOT NULL DEFAULT 1,
    `click_unique_window` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'daily',
    `click_event_retention_days` SMALLINT UNSIGNED NOT NULL DEFAULT 395,
    `registration_confirmation_enabled` TINYINT(1) NOT NULL DEFAULT 1,
    `internal_invitation_enabled` TINYINT(1) NOT NULL DEFAULT 1,
    `approval_notification_enabled` TINYINT(1) NOT NULL DEFAULT 1,
    `rejection_notification_enabled` TINYINT(1) NOT NULL DEFAULT 1,
    `payment_details_needed_notification_enabled` TINYINT(1) NOT NULL DEFAULT 1,
    `payout_paid_notification_enabled` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `affiliates` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `email` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
    `password` VARCHAR(191) COLLATE utf8mb4_unicode_ci NULL,
    `phone_whatsapp` VARCHAR(191) COLLATE utf8mb4_unicode_ci NULL,
    `instagram` VARCHAR(191) COLLATE utf8mb4_unicode_ci NULL,
    `facebook` VARCHAR(191) COLLATE utf8mb4_unicode_ci NULL,
    `tiktok` VARCHAR(191) COLLATE utf8mb4_unicode_ci NULL,
    `x` VARCHAR(191) COLLATE utf8mb4_unicode_ci NULL,
    `threads` VARCHAR(191) COLLATE utf8mb4_unicode_ci NULL,
    `status` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
    `last_login_at` TIMESTAMP NULL DEFAULT NULL,
    `registration_source` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'created_by_nandini',
    `created_by` BIGINT UNSIGNED NULL,
    `approved_by` BIGINT UNSIGNED NULL,
    `approved_at` TIMESTAMP NULL DEFAULT NULL,
    `rejected_by` BIGINT UNSIGNED NULL,
    `rejected_at` TIMESTAMP NULL DEFAULT NULL,
    `suspended_by` BIGINT UNSIGNED NULL,
    `suspended_at` TIMESTAMP NULL DEFAULT NULL,
    `status_note` TEXT COLLATE utf8mb4_unicode_ci NULL,
    `rejection_reason` TEXT COLLATE utf8mb4_unicode_ci NULL,
    `affiliate_code` VARCHAR(191) COLLATE utf8mb4_unicode_ci NULL,
    `affiliate_code_generated_at` TIMESTAMP NULL DEFAULT NULL,
    `short_link_slug` VARCHAR(191) COLLATE utf8mb4_unicode_ci NULL,
    `short_link_activated_at` TIMESTAMP NULL DEFAULT NULL,
    `remember_token` VARCHAR(100) COLLATE utf8mb4_unicode_ci NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `affiliates_email_unique` (`email`),
    UNIQUE KEY `affiliates_affiliate_code_unique` (`affiliate_code`),
    UNIQUE KEY `affiliates_short_link_slug_unique` (`short_link_slug`),
    KEY `affiliates_status_index` (`status`),
    KEY `affiliates_created_by_foreign` (`created_by`),
    KEY `affiliates_approved_by_foreign` (`approved_by`),
    KEY `affiliates_rejected_by_foreign` (`rejected_by`),
    KEY `affiliates_suspended_by_foreign` (`suspended_by`),
    KEY `affiliates_phone_whatsapp_index` (`phone_whatsapp`),
    KEY `affiliates_registration_source_index` (`registration_source`),
    KEY `affiliates_approved_at_index` (`approved_at`),
    KEY `affiliates_rejected_at_index` (`rejected_at`),
    KEY `affiliates_suspended_at_index` (`suspended_at`),
    KEY `affiliates_last_login_at_index` (`last_login_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `affiliate_audit_events` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `affiliate_id` BIGINT UNSIGNED NULL,
    `actor_user_id` BIGINT UNSIGNED NULL,
    `actor_affiliate_id` BIGINT UNSIGNED NULL,
    `event` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `subject_type` VARCHAR(191) COLLATE utf8mb4_unicode_ci NULL,
    `subject_id` BIGINT UNSIGNED NULL,
    `metadata` JSON NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `affiliate_audit_events_actor_user_id_foreign` (`actor_user_id`),
    KEY `affiliate_audit_events_affiliate_id_created_at_index` (`affiliate_id`, `created_at`),
    KEY `affiliate_audit_events_event_index` (`event`),
    KEY `affiliate_audit_subject_index` (`subject_type`, `subject_id`),
    KEY `affiliate_audit_events_actor_affiliate_id_foreign` (`actor_affiliate_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `affiliate_click_events` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `affiliate_id` BIGINT UNSIGNED NOT NULL,
    `clicked_at` TIMESTAMP NOT NULL,
    `click_date` DATE NOT NULL,
    `country_code` CHAR(2) COLLATE utf8mb4_unicode_ci NULL,
    `country_name` VARCHAR(100) COLLATE utf8mb4_unicode_ci NULL,
    `device_type` VARCHAR(20) COLLATE utf8mb4_unicode_ci NOT NULL,
    `referrer_domain` VARCHAR(191) COLLATE utf8mb4_unicode_ci NULL,
    `visitor_hash` CHAR(64) COLLATE utf8mb4_unicode_ci NOT NULL,
    `is_unique` TINYINT(1) NOT NULL DEFAULT 0,
    `is_bot` TINYINT(1) NOT NULL DEFAULT 0,
    `bot_name` VARCHAR(50) COLLATE utf8mb4_unicode_ci NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `affiliate_clicks_affiliate_date_index` (`affiliate_id`, `click_date`),
    KEY `affiliate_clicks_public_date_index` (`affiliate_id`, `is_bot`, `click_date`),
    KEY `affiliate_click_events_clicked_at_index` (`clicked_at`),
    KEY `affiliate_click_events_click_date_index` (`click_date`),
    KEY `affiliate_click_events_country_code_index` (`country_code`),
    KEY `affiliate_click_events_device_type_index` (`device_type`),
    KEY `affiliate_click_events_is_unique_index` (`is_unique`),
    KEY `affiliate_click_events_is_bot_index` (`is_bot`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `affiliate_unique_clicks` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `affiliate_id` BIGINT UNSIGNED NOT NULL,
    `visitor_hash` CHAR(64) COLLATE utf8mb4_unicode_ci NOT NULL,
    `click_date` DATE NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `affiliate_unique_daily` (`affiliate_id`, `visitor_hash`, `click_date`),
    KEY `affiliate_unique_affiliate_date_index` (`affiliate_id`, `click_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `affiliate_bookings` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `affiliate_id` BIGINT UNSIGNED NOT NULL,
    `synced_webhotelier_booking_id` BIGINT UNSIGNED NULL,
    `source_system` VARCHAR(50) COLLATE utf8mb4_unicode_ci NOT NULL,
    `external_booking_id` VARCHAR(120) COLLATE utf8mb4_unicode_ci NOT NULL,
    `external_booking_reference` VARCHAR(191) COLLATE utf8mb4_unicode_ci NULL,
    `affiliate_code_snapshot` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `check_in_date` DATE NOT NULL,
    `check_out_date` DATE NOT NULL,
    `stay_nights` SMALLINT UNSIGNED NOT NULL,
    `room_revenue_amount` DECIMAL(15,2) NULL,
    `currency` VARCHAR(10) COLLATE utf8mb4_unicode_ci NULL,
    `booking_status` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unknown',
    `source_status` VARCHAR(191) COLLATE utf8mb4_unicode_ci NULL,
    `manual_booking_status` VARCHAR(191) COLLATE utf8mb4_unicode_ci NULL,
    `manual_status_reason` TEXT COLLATE utf8mb4_unicode_ci NULL,
    `manual_status_set_by` BIGINT UNSIGNED NULL,
    `manual_status_set_at` TIMESTAMP NULL DEFAULT NULL,
    `commission_rate_snapshot` DECIMAL(5,2) NOT NULL,
    `estimated_commission_amount` DECIMAL(15,2) NULL,
    `commission_state` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'calculation_unavailable',
    `attribution_warning` VARCHAR(191) COLLATE utf8mb4_unicode_ci NULL,
    `calculation_unavailable_reason` VARCHAR(191) COLLATE utf8mb4_unicode_ci NULL,
    `synchronization_warning` TEXT COLLATE utf8mb4_unicode_ci NULL,
    `source_created_at` TIMESTAMP NULL DEFAULT NULL,
    `source_updated_at` TIMESTAMP NULL DEFAULT NULL,
    `last_synced_at` TIMESTAMP NOT NULL,
    `data_fingerprint` CHAR(64) COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `affiliate_booking_source_unique` (`source_system`, `external_booking_id`),
    KEY `affiliate_bookings_synced_webhotelier_booking_id_foreign` (`synced_webhotelier_booking_id`),
    KEY `affiliate_bookings_manual_status_set_by_foreign` (`manual_status_set_by`),
    KEY `affiliate_booking_affiliate_checkin_index` (`affiliate_id`, `check_in_date`),
    KEY `affiliate_booking_affiliate_status_index` (`affiliate_id`, `booking_status`),
    KEY `affiliate_booking_affiliate_commission_index` (`affiliate_id`, `commission_state`),
    KEY `affiliate_bookings_affiliate_code_snapshot_index` (`affiliate_code_snapshot`),
    KEY `affiliate_bookings_check_in_date_index` (`check_in_date`),
    KEY `affiliate_bookings_check_out_date_index` (`check_out_date`),
    KEY `affiliate_bookings_booking_status_index` (`booking_status`),
    KEY `affiliate_bookings_commission_state_index` (`commission_state`),
    KEY `affiliate_bookings_source_updated_at_index` (`source_updated_at`),
    KEY `affiliate_bookings_last_synced_at_index` (`last_synced_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `affiliate_booking_rooms` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `affiliate_booking_id` BIGINT UNSIGNED NOT NULL,
    `external_room_id` VARCHAR(120) COLLATE utf8mb4_unicode_ci NOT NULL,
    `room_type_name` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `room_quantity` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    `stay_nights` SMALLINT UNSIGNED NOT NULL,
    `room_revenue_amount` DECIMAL(15,2) NULL,
    `currency` VARCHAR(10) COLLATE utf8mb4_unicode_ci NULL,
    `line_fingerprint` CHAR(64) COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `affiliate_booking_room_unique` (`affiliate_booking_id`, `external_room_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `affiliate_commission_periods` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `period_year` SMALLINT UNSIGNED NOT NULL,
    `period_month` TINYINT UNSIGNED NOT NULL,
    `period_start_date` DATE NOT NULL,
    `period_end_date` DATE NOT NULL,
    `status` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
    `prepared_at` TIMESTAMP NULL DEFAULT NULL,
    `prepared_by` BIGINT UNSIGNED NULL,
    `finalized_at` TIMESTAMP NULL DEFAULT NULL,
    `finalized_by` BIGINT UNSIGNED NULL,
    `notes` TEXT COLLATE utf8mb4_unicode_ci NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `affiliate_commission_period_unique` (`period_year`, `period_month`),
    KEY `affiliate_commission_periods_prepared_by_foreign` (`prepared_by`),
    KEY `affiliate_commission_periods_finalized_by_foreign` (`finalized_by`),
    KEY `affiliate_commission_periods_status_index` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `affiliate_commission_items` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `commission_period_id` BIGINT UNSIGNED NOT NULL,
    `affiliate_booking_id` BIGINT UNSIGNED NOT NULL,
    `affiliate_id` BIGINT UNSIGNED NOT NULL,
    `currency` VARCHAR(10) COLLATE utf8mb4_unicode_ci NOT NULL,
    `room_revenue_snapshot` DECIMAL(15,2) NOT NULL,
    `commission_rate_snapshot` DECIMAL(5,2) NOT NULL,
    `original_commission_amount` DECIMAL(15,2) NOT NULL,
    `approved_commission_amount` DECIMAL(15,2) NULL,
    `status` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending_review',
    `hold_reason` TEXT COLLATE utf8mb4_unicode_ci NULL,
    `exclusion_reason` TEXT COLLATE utf8mb4_unicode_ci NULL,
    `adjustment_reason` TEXT COLLATE utf8mb4_unicode_ci NULL,
    `source_changed_after_review` TINYINT(1) NOT NULL DEFAULT 0,
    `discrepancy_warning` TEXT COLLATE utf8mb4_unicode_ci NULL,
    `reviewed_at` TIMESTAMP NULL DEFAULT NULL,
    `reviewed_by` BIGINT UNSIGNED NULL,
    `approved_at` TIMESTAMP NULL DEFAULT NULL,
    `approved_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `affiliate_commission_items_affiliate_booking_id_unique` (`affiliate_booking_id`),
    KEY `affiliate_commission_items_reviewed_by_foreign` (`reviewed_by`),
    KEY `affiliate_commission_items_approved_by_foreign` (`approved_by`),
    KEY `affiliate_commission_items_commission_period_id_index` (`commission_period_id`),
    KEY `affiliate_commission_items_affiliate_id_index` (`affiliate_id`),
    KEY `affiliate_commission_items_status_index` (`status`),
    KEY `affiliate_commission_items_currency_index` (`currency`),
    KEY `affiliate_commission_item_affiliate_status_index` (`affiliate_id`, `status`),
    KEY `affiliate_commission_item_period_status_index` (`commission_period_id`, `status`),
    KEY `affiliate_commission_items_source_changed_after_review_index` (`source_changed_after_review`),
    KEY `affiliate_commission_items_approved_at_index` (`approved_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `affiliate_payment_profiles` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `affiliate_id` BIGINT UNSIGNED NOT NULL,
    `payment_method` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `account_holder_name` TEXT COLLATE utf8mb4_unicode_ci NOT NULL,
    `wise_email` TEXT COLLATE utf8mb4_unicode_ci NULL,
    `bank_name` TEXT COLLATE utf8mb4_unicode_ci NULL,
    `bank_account_name` TEXT COLLATE utf8mb4_unicode_ci NULL,
    `bank_account_number` TEXT COLLATE utf8mb4_unicode_ci NULL,
    `bank_country` TEXT COLLATE utf8mb4_unicode_ci NULL,
    `swift_bic` TEXT COLLATE utf8mb4_unicode_ci NULL,
    `is_complete` TINYINT(1) NOT NULL DEFAULT 0,
    `verified_at` TIMESTAMP NULL DEFAULT NULL,
    `verified_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `affiliate_payment_profiles_affiliate_id_unique` (`affiliate_id`),
    KEY `affiliate_payment_profiles_verified_by_foreign` (`verified_by`),
    KEY `affiliate_payment_profiles_is_complete_index` (`is_complete`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `affiliate_payout_minimums` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `currency` VARCHAR(10) COLLATE utf8mb4_unicode_ci NOT NULL,
    `minimum_amount` DECIMAL(15,2) NOT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `affiliate_payout_minimums_currency_unique` (`currency`),
    KEY `affiliate_payout_minimums_is_active_index` (`is_active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `affiliate_payout_number_sequences` (
    `sequence_year` SMALLINT UNSIGNED NOT NULL,
    `next_number` INT UNSIGNED NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`sequence_year`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `affiliate_payouts` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `payout_number` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `affiliate_id` BIGINT UNSIGNED NOT NULL,
    `currency` VARCHAR(10) COLLATE utf8mb4_unicode_ci NOT NULL,
    `gross_commission_amount` DECIMAL(15,2) NOT NULL,
    `adjustment_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `adjustment_reason` TEXT COLLATE utf8mb4_unicode_ci NULL,
    `net_payout_amount` DECIMAL(15,2) NOT NULL,
    `payment_method_snapshot` VARCHAR(40) COLLATE utf8mb4_unicode_ci NOT NULL,
    `payment_details_masked_snapshot` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `status` VARCHAR(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
    `due_at` TIMESTAMP NULL DEFAULT NULL,
    `prepared_at` TIMESTAMP NULL DEFAULT NULL,
    `prepared_by` BIGINT UNSIGNED NULL,
    `processing_at` TIMESTAMP NULL DEFAULT NULL,
    `processing_by` BIGINT UNSIGNED NULL,
    `paid_at` TIMESTAMP NULL DEFAULT NULL,
    `paid_by` BIGINT UNSIGNED NULL,
    `payment_reference` VARCHAR(191) COLLATE utf8mb4_unicode_ci NULL,
    `failure_reason` TEXT COLLATE utf8mb4_unicode_ci NULL,
    `cancelled_at` TIMESTAMP NULL DEFAULT NULL,
    `cancelled_by` BIGINT UNSIGNED NULL,
    `cancellation_reason` TEXT COLLATE utf8mb4_unicode_ci NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `affiliate_payouts_payout_number_unique` (`payout_number`),
    KEY `affiliate_payouts_prepared_by_foreign` (`prepared_by`),
    KEY `affiliate_payouts_processing_by_foreign` (`processing_by`),
    KEY `affiliate_payouts_paid_by_foreign` (`paid_by`),
    KEY `affiliate_payouts_cancelled_by_foreign` (`cancelled_by`),
    KEY `affiliate_payouts_affiliate_id_status_index` (`affiliate_id`, `status`),
    KEY `affiliate_payouts_currency_status_index` (`currency`, `status`),
    KEY `affiliate_payout_method_status_index` (`payment_method_snapshot`, `status`),
    KEY `affiliate_payouts_due_at_index` (`due_at`),
    KEY `affiliate_payouts_paid_at_index` (`paid_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `affiliate_payout_items` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `affiliate_payout_id` BIGINT UNSIGNED NOT NULL,
    `affiliate_commission_item_id` BIGINT UNSIGNED NOT NULL,
    `amount` DECIMAL(15,2) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `affiliate_payout_items_affiliate_commission_item_id_unique` (`affiliate_commission_item_id`),
    KEY `affiliate_payout_items_affiliate_payout_id_foreign` (`affiliate_payout_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `affiliate_marketing_assets` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `description` TEXT COLLATE utf8mb4_unicode_ci NULL,
    `asset_type` VARCHAR(40) COLLATE utf8mb4_unicode_ci NOT NULL,
    `file_path` VARCHAR(191) COLLATE utf8mb4_unicode_ci NULL,
    `external_url` VARCHAR(2048) COLLATE utf8mb4_unicode_ci NULL,
    `thumbnail_path` VARCHAR(191) COLLATE utf8mb4_unicode_ci NULL,
    `file_name` VARCHAR(191) COLLATE utf8mb4_unicode_ci NULL,
    `file_extension` VARCHAR(10) COLLATE utf8mb4_unicode_ci NULL,
    `file_size` BIGINT UNSIGNED NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 0,
    `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
    `available_from` TIMESTAMP NULL DEFAULT NULL,
    `available_until` TIMESTAMP NULL DEFAULT NULL,
    `sort_order` INT UNSIGNED NOT NULL DEFAULT 0,
    `created_by` BIGINT UNSIGNED NULL,
    `updated_by` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `affiliate_marketing_assets_created_by_foreign` (`created_by`),
    KEY `affiliate_marketing_assets_updated_by_foreign` (`updated_by`),
    KEY `affiliate_marketing_assets_asset_type_index` (`asset_type`),
    KEY `affiliate_marketing_assets_is_active_index` (`is_active`),
    KEY `affiliate_marketing_assets_is_featured_index` (`is_featured`),
    KEY `affiliate_marketing_assets_available_from_index` (`available_from`),
    KEY `affiliate_marketing_assets_available_until_index` (`available_until`),
    KEY `affiliate_marketing_assets_sort_order_index` (`sort_order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `affiliate_operational_states` (
    `key` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `status` VARCHAR(40) COLLATE utf8mb4_unicode_ci NULL,
    `summary` VARCHAR(191) COLLATE utf8mb4_unicode_ci NULL,
    `last_attempted_at` TIMESTAMP NULL DEFAULT NULL,
    `last_successful_at` TIMESTAMP NULL DEFAULT NULL,
    `metadata` JSON NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `affiliate_password_reset_tokens` (
    `email` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `token` VARCHAR(191) COLLATE utf8mb4_unicode_ci NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Foundation roles.
INSERT INTO `roles` (`name`, `slug`, `created_at`, `updated_at`) VALUES
    ('Administrator', 'administrator', NOW(), NOW()),
    ('Sales & Marketing', 'sales-marketing', NOW(), NOW()),
    ('Finance', 'finance', NOW(), NOW()),
    ('Affiliate', 'affiliate', NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `name` = VALUES(`name`),
    `updated_at` = VALUES(`updated_at`);

-- Current Affiliate permission set.
INSERT INTO `permissions` (`name`, `created_at`, `updated_at`) VALUES
    ('affiliate-dashboard.view-own', NOW(), NOW()),
    ('affiliate-profile.view-own', NOW(), NOW()),
    ('affiliate-profile.update-own', NOW(), NOW()),
    ('affiliate-booking.view-own', NOW(), NOW()),
    ('affiliate-commission.view-own', NOW(), NOW()),
    ('affiliate-click.view-own', NOW(), NOW()),
    ('affiliate-payout.view-own', NOW(), NOW()),
    ('affiliate.view', NOW(), NOW()),
    ('affiliate.create', NOW(), NOW()),
    ('affiliate.update', NOW(), NOW()),
    ('affiliate.approve', NOW(), NOW()),
    ('affiliate.reject', NOW(), NOW()),
    ('affiliate.suspend', NOW(), NOW()),
    ('affiliate.reactivate', NOW(), NOW()),
    ('affiliate-booking.view', NOW(), NOW()),
    ('affiliate-booking.manage', NOW(), NOW()),
    ('affiliate-commission.view', NOW(), NOW()),
    ('affiliate-commission.validate', NOW(), NOW()),
    ('affiliate-commission.approve', NOW(), NOW()),
    ('affiliate-payout.view', NOW(), NOW()),
    ('affiliate-payout.manage', NOW(), NOW()),
    ('affiliate-payment-profile.view', NOW(), NOW()),
    ('affiliate-payment-profile.manage', NOW(), NOW()),
    ('affiliate-payment-profile.update-own', NOW(), NOW()),
    ('affiliate-click.view', NOW(), NOW()),
    ('affiliate-report.view', NOW(), NOW()),
    ('affiliate-setting.manage', NOW(), NOW()),
    ('affiliate-marketing-asset.manage', NOW(), NOW()),
    ('affiliate-marketing-asset.view-own', NOW(), NOW()),
    ('affiliate-report.view-own', NOW(), NOW()),
    ('affiliate-system-health.view', NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `updated_at` = VALUES(`updated_at`);

-- Administrator receives all Affiliate permissions.
INSERT IGNORE INTO `role_has_permissions` (`role_id`, `permission_id`)
SELECT `roles`.`id`, `permissions`.`id`
FROM `roles`
CROSS JOIN `permissions`
WHERE `roles`.`slug` = 'administrator'
  AND `permissions`.`name` LIKE 'affiliate%';

-- Sales & Marketing permissions.
INSERT IGNORE INTO `role_has_permissions` (`role_id`, `permission_id`)
SELECT `roles`.`id`, `permissions`.`id`
FROM `roles`
CROSS JOIN `permissions`
WHERE `roles`.`slug` = 'sales-marketing'
  AND `permissions`.`name` IN (
      'affiliate.view', 'affiliate.create', 'affiliate.update',
      'affiliate.approve', 'affiliate.reject', 'affiliate.suspend',
      'affiliate.reactivate', 'affiliate-booking.view', 'affiliate-booking.manage',
      'affiliate-click.view', 'affiliate-report.view',
      'affiliate-marketing-asset.manage'
  );

-- Finance permissions.
INSERT IGNORE INTO `role_has_permissions` (`role_id`, `permission_id`)
SELECT `roles`.`id`, `permissions`.`id`
FROM `roles`
CROSS JOIN `permissions`
WHERE `roles`.`slug` = 'finance'
  AND `permissions`.`name` IN (
      'affiliate-booking.view', 'affiliate-commission.view',
      'affiliate-commission.validate', 'affiliate-commission.approve',
      'affiliate-payout.view', 'affiliate-payout.manage',
      'affiliate-setting.manage', 'affiliate-payment-profile.view',
      'affiliate-payment-profile.manage', 'affiliate-report.view'
  );

-- Affiliate portal permissions.
INSERT IGNORE INTO `role_has_permissions` (`role_id`, `permission_id`)
SELECT `roles`.`id`, `permissions`.`id`
FROM `roles`
CROSS JOIN `permissions`
WHERE `roles`.`slug` = 'affiliate'
  AND `permissions`.`name` IN (
      'affiliate-dashboard.view-own', 'affiliate-profile.view-own',
      'affiliate-profile.update-own', 'affiliate-booking.view-own',
      'affiliate-commission.view-own', 'affiliate-click.view-own',
      'affiliate-payout.view-own', 'affiliate-payment-profile.update-own',
      'affiliate-marketing-asset.view-own', 'affiliate-report.view-own'
  );

-- Grant the Administrator role only to the explicitly supplied admin email.
-- Leaving the placeholder unchanged grants access to nobody.
INSERT IGNORE INTO `model_has_roles` (`role_id`, `model_type`, `model_id`)
SELECT `roles`.`id`, 'App\\Models\\User', `users`.`id`
FROM `roles`
INNER JOIN `users` ON BINARY `users`.`email` = BINARY @affiliate_admin_email
WHERE `roles`.`slug` = 'administrator';

-- Production program defaults. Existing percentages and finance settings are preserved.
INSERT INTO `affiliate_program_settings` (
    `program_name`,
    `affiliate_commission_percentage`,
    `guest_discount_percentage`,
    `payment_cycle`,
    `preferred_payment_method`,
    `alternative_payment_method`,
    `minimum_payout_amount`,
    `currency`,
    `review_time_message`,
    `review_time_expectation_hours`,
    `booking_engine_base_url`,
    `affiliate_domain`,
    `short_link_domain`,
    `created_at`,
    `updated_at`
)
SELECT
    'Nandini Partner Circle',
    10.00,
    3.00,
    'monthly',
    'wise',
    'bank_transfer',
    500000.00,
    'IDR',
    'Your account is currently under review. The review process may take up to 48 hours.',
    48,
    'https://nandinijunglebyhanginggardens.reserve-online.net/',
    'affiliate.nandinibali.com',
    'go.nandinibali.com',
    NOW(),
    NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `affiliate_program_settings`);

UPDATE `affiliate_program_settings`
SET `affiliate_domain` = 'affiliate.nandinibali.com',
    `short_link_domain` = 'go.nandinibali.com',
    `updated_at` = NOW();

INSERT INTO `affiliate_payout_minimums` (`currency`, `minimum_amount`, `is_active`, `created_at`, `updated_at`)
VALUES ('IDR', 500000.00, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE `updated_at` = `updated_at`;

-- Register all Affiliate migrations so future Artisan migrations do not
-- attempt to recreate the manually imported schema.
SET @affiliate_migration_batch = (
    SELECT COALESCE(MAX(`batch`), 0) + 1
    FROM `migrations`
);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT `migration_name`, @affiliate_migration_batch
FROM (
    SELECT '2026_08_03_000001_add_affiliate_code_to_synced_webhotelier_bookings_table' AS `migration_name`
    UNION ALL SELECT '2026_08_03_000002_create_roles_and_permissions_tables'
    UNION ALL SELECT '2026_08_03_000003_create_affiliate_program_settings_table'
    UNION ALL SELECT '2026_08_03_000004_create_affiliates_table'
    UNION ALL SELECT '2026_08_04_000001_refactor_affiliates_as_user_profiles'
    UNION ALL SELECT '2026_08_04_000002_add_affiliate_part_two_workflow'
    UNION ALL SELECT '2026_08_04_000003_backfill_existing_affiliate_codes'
    UNION ALL SELECT '2026_08_04_000004_update_affiliate_short_link_domain'
    UNION ALL SELECT '2026_08_04_000005_create_affiliate_click_tracking_tables'
    UNION ALL SELECT '2026_08_04_000006_create_affiliate_booking_tracking_tables'
    UNION ALL SELECT '2026_08_04_000007_add_affiliate_finance_settings_and_audit_subjects'
    UNION ALL SELECT '2026_08_04_000008_create_affiliate_commission_workflow_tables'
    UNION ALL SELECT '2026_08_04_000009_create_affiliate_payment_profiles_and_minimums'
    UNION ALL SELECT '2026_08_04_000010_create_affiliate_payout_tables'
    UNION ALL SELECT '2026_08_04_000011_create_affiliate_operations_tables'
    UNION ALL SELECT '2026_08_05_000001_separate_affiliate_authentication'
) AS `affiliate_migrations`
WHERE NOT EXISTS (
    SELECT 1
    FROM `migrations`
    WHERE `migrations`.`migration` = `affiliate_migrations`.`migration_name`
);
