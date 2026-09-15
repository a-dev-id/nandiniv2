ALTER TABLE `dining_settings`
  ADD COLUMN `visit_premium_menu_label` varchar(255) NULL AFTER `visit_food_menu_url`,
  ADD COLUMN `visit_premium_menu_url` text NULL AFTER `visit_premium_menu_label`;

UPDATE `dining_settings`
SET
  `visit_information_items` = JSON_SET(
    `visit_information_items`,
    REPLACE(
      JSON_UNQUOTE(JSON_SEARCH(`visit_information_items`, 'one', 'Opening Hours', NULL, '$[*].label')),
      '.label',
      '.value'
    ),
    'Breakfast: 07:00 AM – 10:30 AM\nLunch: 12:00 PM – 03:00 PM\nDinner: 06:30 PM – 10:30 PM'
  ),
  `visit_food_menu_label` = 'View Menu',
  `visit_food_menu_url` = 'https://drive.google.com/file/d/1XwHnNgEreiCA4mexe65GOc44Q9WSRSsg/view?usp=sharing',
  `visit_premium_menu_label` = 'Premium Menu',
  `visit_premium_menu_url` = 'https://drive.google.com/file/d/16XoyEOdlRzFfN2Ca30THHhNfw8OCxWDN/view?usp=sharing',
  `visit_beverage_menu_label` = 'Beverage List',
  `visit_beverage_menu_url` = 'https://drive.google.com/file/d/1Xm5YhSbX18muQQTdrLvNLYd7cgFw5EaS/view?usp=sharing',
  `updated_at` = NOW()
WHERE `id` = 1
  AND JSON_SEARCH(`visit_information_items`, 'one', 'Opening Hours', NULL, '$[*].label') IS NOT NULL;

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_09_11_000002_add_premium_menu_and_meal_hours_to_dining_settings', COALESCE(MAX(`batch`), 0) + 1
FROM `migrations`
WHERE NOT EXISTS (
  SELECT 1 FROM `migrations` AS `recorded`
  WHERE `recorded`.`migration` = '2026_09_11_000002_add_premium_menu_and_meal_hours_to_dining_settings'
);
