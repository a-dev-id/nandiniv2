-- Add room price options to vouchers.
-- Safe to run more than once on MySQL 5.7+ / MySQL 8+.

SET @price_options_column_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'vouchers'
      AND COLUMN_NAME = 'price_options'
);

SET @add_price_options_sql = IF(
    @price_options_column_exists = 0,
    'ALTER TABLE `vouchers` ADD COLUMN `price_options` JSON NULL AFTER `selling_price`',
    'SELECT ''The vouchers.price_options column already exists.'' AS message'
);

PREPARE add_price_options_statement FROM @add_price_options_sql;
EXECUTE add_price_options_statement;
DEALLOCATE PREPARE add_price_options_statement;

-- Optional example for the voucher shown in the reference screenshot.
-- Change the slug if your voucher uses a different one, then remove the
-- leading comment markers and run the UPDATE.
--
-- UPDATE `vouchers`
-- SET
--     `voucher_type` = 'accommodation',
--     `selling_price` = 7438017,
--     `currency` = 'IDR',
--     `price_type` = 'plus_plus',
--     `unit_type` = 'per_booking',
--     `price_options` = JSON_ARRAY(
--         JSON_OBJECT(
--             'key', 'jungle-view-villa',
--             'label', 'Jungle View Villa',
--             'additional_price', 0
--         ),
--         JSON_OBJECT(
--             'key', 'sunrise-view-villa',
--             'label', 'Sunrise View Villa',
--             'additional_price', 330579
--         ),
--         JSON_OBJECT(
--             'key', 'panoramic-jungle-view-villa',
--             'label', 'Panoramic Jungle View Villa',
--             'additional_price', 661157
--         )
--     ),
--     `updated_at` = CURRENT_TIMESTAMP
-- WHERE `slug` = 'endless-jungle-escape-3-days-2-nights';

