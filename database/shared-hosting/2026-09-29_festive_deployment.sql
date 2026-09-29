-- Nandini Jungle festive 2026 deployment
-- Target: MySQL 8+ / MariaDB with JSON support
-- This script can be rerun without creating duplicate records. Every run reapplies
-- the canonical content below and therefore replaces later Filament edits to it.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `festive_settings` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `meta_title` VARCHAR(255) NULL,
    `meta_description` TEXT NULL,
    `meta_author` VARCHAR(255) NULL,
    `meta_site_name` VARCHAR(255) NULL,
    `hero_visible` TINYINT(1) NOT NULL DEFAULT 1,
    `hero_image` VARCHAR(255) NULL,
    `hero_image_alt` VARCHAR(255) NULL,
    `hero_eyebrow` VARCHAR(255) NULL,
    `hero_heading` TEXT NULL,
    `hero_subheading` VARCHAR(255) NULL,
    `hero_description` TEXT NULL,
    `hero_primary_cta_label` VARCHAR(255) NULL,
    `hero_primary_cta_url` TEXT NULL,
    `hero_secondary_cta_label` VARCHAR(255) NULL,
    `hero_secondary_cta_url` TEXT NULL,
    `introduction_visible` TINYINT(1) NOT NULL DEFAULT 1,
    `introduction_eyebrow` VARCHAR(255) NULL,
    `introduction_heading` TEXT NULL,
    `introduction_description` TEXT NULL,
    `celebrations_visible` TINYINT(1) NOT NULL DEFAULT 1,
    `celebrations` JSON NULL,
    `programme_visible` TINYINT(1) NOT NULL DEFAULT 1,
    `programme_eyebrow` VARCHAR(255) NULL,
    `programme_heading` TEXT NULL,
    `programme_days` JSON NULL,
    `booking_cta_visible` TINYINT(1) NOT NULL DEFAULT 1,
    `booking_cta_image` VARCHAR(255) NULL,
    `booking_cta_image_alt` VARCHAR(255) NULL,
    `booking_cta_eyebrow` VARCHAR(255) NULL,
    `booking_cta_heading` TEXT NULL,
    `booking_cta_description` TEXT NULL,
    `booking_cta_button_label` VARCHAR(255) NULL,
    `booking_cta_button_url` TEXT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `festive_events` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `sort_order` INT UNSIGNED NOT NULL DEFAULT 1,
    `meta_title` VARCHAR(255) NULL,
    `meta_description` TEXT NULL,
    `hero_image` VARCHAR(255) NULL,
    `hero_image_alt` VARCHAR(255) NULL,
    `hero_eyebrow` VARCHAR(255) NULL,
    `hero_heading` TEXT NULL,
    `hero_subheading` VARCHAR(255) NULL,
    `hero_description` TEXT NULL,
    `hero_price` VARCHAR(255) NULL,
    `hero_button_label` VARCHAR(255) NULL,
    `hero_button_url` TEXT NULL,
    `information_visible` TINYINT(1) NOT NULL DEFAULT 1,
    `information_items` JSON NULL,
    `menu_visible` TINYINT(1) NOT NULL DEFAULT 1,
    `menu_eyebrow` VARCHAR(255) NULL,
    `menu_heading` TEXT NULL,
    `menu_description` TEXT NULL,
    `menu_items` JSON NULL,
    `programme_visible` TINYINT(1) NOT NULL DEFAULT 0,
    `programme_image` VARCHAR(255) NULL,
    `programme_image_alt` VARCHAR(255) NULL,
    `programme_eyebrow` VARCHAR(255) NULL,
    `programme_heading` TEXT NULL,
    `programme_items` JSON NULL,
    `reservation_visible` TINYINT(1) NOT NULL DEFAULT 1,
    `reservation_eyebrow` VARCHAR(255) NULL,
    `reservation_heading` TEXT NULL,
    `reservation_description` TEXT NULL,
    `reservation_button_label` VARCHAR(255) NULL,
    `reservation_button_url` TEXT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `festive_events_slug_unique` (`slug`),
    KEY `festive_events_is_active_index` (`is_active`),
    KEY `festive_events_sort_order_index` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

START TRANSACTION;

INSERT INTO `festive_settings` (
    `id`, `meta_title`, `meta_description`, `meta_author`, `meta_site_name`,
    `hero_visible`, `hero_image`, `hero_image_alt`, `hero_eyebrow`, `hero_heading`,
    `hero_subheading`, `hero_description`, `hero_primary_cta_label`,
    `hero_primary_cta_url`, `hero_secondary_cta_label`, `hero_secondary_cta_url`,
    `introduction_visible`, `introduction_eyebrow`, `introduction_heading`,
    `introduction_description`, `celebrations_visible`, `celebrations`,
    `programme_visible`, `programme_eyebrow`, `programme_heading`, `programme_days`,
    `booking_cta_visible`, `booking_cta_image`, `booking_cta_image_alt`,
    `booking_cta_eyebrow`, `booking_cta_heading`, `booking_cta_description`,
    `booking_cta_button_label`, `booking_cta_button_url`, `created_at`, `updated_at`
) VALUES (
    1,
    'Festive Season | Nandini Jungle by Hanging Gardens',
    'Celebrate Christmas and New Year in the heart of Bali with festive dining and meaningful moments at Nandini Jungle.',
    'Nandini Jungle by Hanging Gardens',
    'Nandini Jungle by Hanging Gardens',
    1,
    '/images/festive/2026/header-landing.jpg',
    'Guest overlooking the tropical jungle from a private pool at Nandini Jungle',
    'FESTIVE SEASON',
    CONCAT('A Festive Season', CHAR(10), 'at Nandini Jungle'),
    'CHRISTMAS & NEW YEAR CELEBRATION',
    'Celebrate the season in the heart of the jungle with thoughtfully prepared dining experiences, warm moments, and meaningful celebrations at Nandini Jungle.',
    'CHRISTMAS DINNER',
    '/festive-season/christmas-dinner',
    'NEW YEAR DINNER',
    '/festive-season/new-year-dinner',
    1,
    'A SEASON TO REMEMBER',
    'Festive Celebrations in the Heart of Bali',
    'This festive season, slow down and celebrate with the people who matter most. Discover specially curated Christmas and New Year dining experiences surrounded by the quiet beauty of Nandini Jungle.',
    1,
    JSON_ARRAY(
        JSON_OBJECT(
            'anchor', 'christmas',
            'image', '/images/festive/2026/christmas-dining.jpg',
            'image_alt', 'Christmas dinner at Nandini Jungle',
            'date', '24 DECEMBER 2026',
            'heading', 'Christmas at Nandini Jungle',
            'description', 'A warm Christmas evening of refined dining, Balinese performances, festive traditions, and meaningful moments together.',
            'price', 'IDR 1,800,000++ per person',
            'button_label', 'VIEW CHRISTMAS DINNER',
            'button_url', '/festive-season/christmas-dinner'
        ),
        JSON_OBJECT(
            'anchor', 'new-year',
            'image', '/images/festive/2026/new-year-dining.jpg',
            'image_alt', 'New Year dinner at Nandini Jungle',
            'date', '31 DECEMBER 2026',
            'heading', 'A Night to Begin Anew',
            'description', 'Welcome the year ahead with an elegant dining experience, thoughtful flavours, and an intimate evening in the heart of the jungle.',
            'price', 'IDR 2,200,000++ per person',
            'button_label', 'VIEW NEW YEAR DINNER',
            'button_url', '/festive-season/new-year-dinner'
        )
    ),
    1,
    'FESTIVE PROGRAMME',
    'Festive Programme at Nandini Jungle',
    JSON_ARRAY(
        JSON_OBJECT(
            'date', '24 December 2026',
            'items', JSON_ARRAY(
                JSON_OBJECT('time', '07:00 AM – 08:00 AM', 'activity', 'Village Morning Walk'),
                JSON_OBJECT('time', '08:00 AM – 09:10 AM', 'activity', 'Making Canangsari / Balinese Offering'),
                JSON_OBJECT('time', '06:00 PM – 07:00 PM', 'activity', 'Cocktail & Canape Soiree at Bar & Lounge'),
                JSON_OBJECT('time', '07:00 PM – 08:00 PM', 'activity', 'Christmas Dinner & Balinese Dance Performance'),
                JSON_OBJECT('time', '07:45 PM – 08:00 PM', 'activity', 'GM’s Speech & Tree Lighting Ceremony'),
                JSON_OBJECT('time', '08:00 PM – 08:30 PM', 'activity', 'Nandini Jungle’s Angels Choir'),
                JSON_OBJECT('time', '08:30 PM – 09:00 PM', 'activity', 'Balinese Social Dance')
            )
        ),
        JSON_OBJECT(
            'date', '25 December 2026',
            'items', JSON_ARRAY(
                JSON_OBJECT('time', '08:00 AM – 09:00 AM', 'activity', 'Meet Santa at Wild Ginger Restaurant'),
                JSON_OBJECT('time', '02:00 PM – 04:00 PM', 'activity', '“JINGLE & JIGGER” Christmas Private Mixology Class at Mystical Jungle Pool Bar'),
                JSON_OBJECT('time', '03:00 PM – 05:00 PM', 'activity', 'Exclusive Balinese Afternoon Tea at Bar & Lounge')
            )
        ),
        JSON_OBJECT(
            'date', '31 December 2026',
            'items', JSON_ARRAY(
                JSON_OBJECT('time', '07:00 PM – 07:15 PM', 'activity', 'Cocktail & Canape Soiree'),
                JSON_OBJECT('time', '07:15 PM – 08:00 PM', 'activity', 'Dinner with Balinese Dance Performance'),
                JSON_OBJECT('time', '08:00 PM – 08:10 PM', 'activity', 'GM''s Speech'),
                JSON_OBJECT('time', '08:10 PM – 10:00 PM', 'activity', 'Balinese Dance Performance'),
                JSON_OBJECT('time', '10:00 PM – 11:45 PM', 'activity', 'Cocktails & Social Party'),
                JSON_OBJECT('time', '11:00 PM – 12:00 PM', 'activity', 'GM''s Speech & Countdown to 2027')
            )
        )
    ),
    1,
    '/images/festive/2026/christmas-dining.jpg',
    'Festive table at Nandini Jungle',
    'CELEBRATE TOGETHER',
    CONCAT('Celebrate the Festive Season', CHAR(10), 'in the Heart of Bali'),
    'Create meaningful moments with festive dining, warm hospitality, and the natural beauty of Nandini Jungle.',
    'RESERVE A TABLE',
    'https://wa.me/6281236871170?text=Hello%2C%20I%20would%20like%20to%20reserve%20a%20table%20for%20the%20festive%20season%20at%20Nandini%20Jungle.',
    NOW(),
    NOW()
) 
ON DUPLICATE KEY UPDATE
    `meta_title` = VALUES(`meta_title`),
    `meta_description` = VALUES(`meta_description`),
    `meta_author` = VALUES(`meta_author`),
    `meta_site_name` = VALUES(`meta_site_name`),
    `hero_visible` = VALUES(`hero_visible`),
    `hero_image` = VALUES(`hero_image`),
    `hero_image_alt` = VALUES(`hero_image_alt`),
    `hero_eyebrow` = VALUES(`hero_eyebrow`),
    `hero_heading` = VALUES(`hero_heading`),
    `hero_subheading` = VALUES(`hero_subheading`),
    `hero_description` = VALUES(`hero_description`),
    `hero_primary_cta_label` = VALUES(`hero_primary_cta_label`),
    `hero_primary_cta_url` = VALUES(`hero_primary_cta_url`),
    `hero_secondary_cta_label` = VALUES(`hero_secondary_cta_label`),
    `hero_secondary_cta_url` = VALUES(`hero_secondary_cta_url`),
    `introduction_visible` = VALUES(`introduction_visible`),
    `introduction_eyebrow` = VALUES(`introduction_eyebrow`),
    `introduction_heading` = VALUES(`introduction_heading`),
    `introduction_description` = VALUES(`introduction_description`),
    `celebrations_visible` = VALUES(`celebrations_visible`),
    `celebrations` = VALUES(`celebrations`),
    `programme_visible` = VALUES(`programme_visible`),
    `programme_eyebrow` = VALUES(`programme_eyebrow`),
    `programme_heading` = VALUES(`programme_heading`),
    `programme_days` = VALUES(`programme_days`),
    `booking_cta_visible` = VALUES(`booking_cta_visible`),
    `booking_cta_image` = VALUES(`booking_cta_image`),
    `booking_cta_image_alt` = VALUES(`booking_cta_image_alt`),
    `booking_cta_eyebrow` = VALUES(`booking_cta_eyebrow`),
    `booking_cta_heading` = VALUES(`booking_cta_heading`),
    `booking_cta_description` = VALUES(`booking_cta_description`),
    `booking_cta_button_label` = VALUES(`booking_cta_button_label`),
    `booking_cta_button_url` = VALUES(`booking_cta_button_url`),
    `updated_at` = NOW();

INSERT INTO `festive_events` (
    `title`, `slug`, `is_active`, `sort_order`, `meta_title`, `meta_description`,
    `hero_image`, `hero_image_alt`, `hero_eyebrow`, `hero_heading`, `hero_subheading`,
    `hero_description`, `hero_price`, `hero_button_label`, `hero_button_url`,
    `information_visible`, `information_items`, `menu_visible`, `menu_eyebrow`,
    `menu_heading`, `menu_description`, `menu_items`, `programme_visible`,
    `programme_image`, `programme_image_alt`, `programme_eyebrow`, `programme_heading`,
    `programme_items`, `reservation_visible`, `reservation_eyebrow`,
    `reservation_heading`, `reservation_description`, `reservation_button_label`,
    `reservation_button_url`, `created_at`, `updated_at`
) VALUES (
    'Christmas Dinner',
    'christmas-dinner',
    1,
    1,
    'Christmas Dinner | Nandini Jungle by Hanging Gardens',
    'Celebrate Christmas Eve with a festive multi-course dinner, Balinese performances and warm moments at Nandini Jungle.',
    '/images/festive/2026/christmas-dining.jpg',
    'A Christmas in the Jungle',
    '24 DECEMBER 2026',
    CONCAT('A Christmas', CHAR(10), 'in the Jungle'),
    'CHRISTMAS CELEBRATION AT NANDINI JUNGLE',
    'Surrounded by the quiet beauty of the jungle, share an evening of beautifully prepared dishes, warm conversations, and festive moments together.',
    'IDR 1,800,000++ per person',
    'RESERVE NOW',
    '#reserve',
    1,
    JSON_ARRAY(
        JSON_OBJECT('label', 'DATE', 'value', '24 December 2026'),
        JSON_OBJECT('label', 'EXPERIENCE', 'value', 'Christmas Eve Dinner'),
        JSON_OBJECT('label', 'LOCATION', 'value', 'Wild Ginger Restaurant / Nandini Jungle'),
        JSON_OBJECT('label', 'DINING', 'value', 'Festive Multi-Course Dinner')
    ),
    1,
    'CHRISTMAS EVE',
    'Modern Surf and Turf',
    'A festive multi-course experience combining refined flavours, premium ingredients, and thoughtful presentation.',
    JSON_ARRAY(
        JSON_OBJECT('type', 'dish', 'title', 'Chawanmusi Egg', 'description', 'Salmon Row – Egg Custard – Spring Onion', 'image', '/images/festive/2026/Dish/CHAWANMUSI-EGG.jpg', 'image_alt', 'Chawanmusi Egg'),
        JSON_OBJECT('type', 'dish', 'title', 'Octopus Dumpling', 'description', 'Marinated Octopus – Chili Emulsion – Celery Compote', 'image', '/images/festive/2026/Dish/OCTOPUS%20DUMPLING.jpg', 'image_alt', 'Octopus Dumpling'),
        JSON_OBJECT('type', 'dish', 'title', 'Cured Salmon', 'description', 'Mushroom Jelly – Comfit Tomato Cherry – Onion Puree – Pickle Simeji', 'image', '/images/festive/2026/Dish/CURED%20SALMON.jpg', 'image_alt', 'Cured Salmon'),
        JSON_OBJECT('type', 'dish', 'title', 'Beet Root Tartare', 'description', 'Miso Tomato Cherry – So Vide Avocado – Pickle Red Onion', 'image', '/images/festive/2026/Dish/BEET%20ROOT%20TARTARE.jpg', 'image_alt', 'Beet Root Tartare'),
        JSON_OBJECT('type', 'dish', 'title', 'Magret de Canard', 'description', 'Sweet Potato Gnocchi – Duck Sauce – Baby Carrot – Asparagus', 'image', '/images/festive/2026/Dish/MAGRET%20DE%20CANARD.jpg', 'image_alt', 'Magret de Canard'),
        JSON_OBJECT('type', 'dish', 'title', '“48 Hours Wagyu” Short Rib', 'description', '“Ketan” Risotto – Roasted Bone Marrow – Broccolini – Soy Chili Emulsion – Edible', 'image', '/images/festive/2026/Dish/%E2%80%9C48%20HOURS%20WAGYU%E2%80%9D%20SHORT%20RIB.jpg', 'image_alt', '48 Hours Wagyu Short Rib'),
        JSON_OBJECT('type', 'dish', 'title', 'Dark Chocolate Jelly', 'description', 'Orange Puree – Chocolate Mousse – Cacao Crumble – Berry Salsa', 'image', '/images/festive/2026/Dish/DARK%20CHOCOLATE%20JELLY.jpg', 'image_alt', 'Dark Chocolate Jelly')
    ),
    1,
    '/images/festive/2026/christmas-dining.jpg',
    'Christmas festive programme',
    'CHRISTMAS EVENING',
    'Programme of the Evening',
    JSON_ARRAY(
        JSON_OBJECT('time', '06:00 PM – 07:00 PM', 'activity', 'Cocktail & Canape Soiree at Bar & Lounge'),
        JSON_OBJECT('time', '07:00 PM – 08:00 PM', 'activity', 'Christmas Dinner & Balinese Dance Performance'),
        JSON_OBJECT('time', '07:45 PM – 08:00 PM', 'activity', 'GM’s Speech & Tree Lighting Ceremony'),
        JSON_OBJECT('time', '08:00 PM – 08:30 PM', 'activity', 'Nandini Jungle’s Angels Choir'),
        JSON_OBJECT('time', '08:30 PM – 09:00 PM', 'activity', 'Balinese Social Dance')
    ),
    1,
    'CHRISTMAS AT NANDINI',
    'Reserve Your Table',
    'Join us for a memorable Christmas evening at Nandini Jungle.',
    'RESERVE NOW',
    'https://wa.me/6281236871170?text=Hello%2C%20I%20would%20like%20to%20reserve%20the%20Christmas%20Dinner%20at%20Nandini%20Jungle.',
    NOW(),
    NOW()
) 
ON DUPLICATE KEY UPDATE
    `title` = VALUES(`title`),
    `is_active` = VALUES(`is_active`),
    `sort_order` = VALUES(`sort_order`),
    `meta_title` = VALUES(`meta_title`),
    `meta_description` = VALUES(`meta_description`),
    `hero_image` = VALUES(`hero_image`),
    `hero_image_alt` = VALUES(`hero_image_alt`),
    `hero_eyebrow` = VALUES(`hero_eyebrow`),
    `hero_heading` = VALUES(`hero_heading`),
    `hero_subheading` = VALUES(`hero_subheading`),
    `hero_description` = VALUES(`hero_description`),
    `hero_price` = VALUES(`hero_price`),
    `hero_button_label` = VALUES(`hero_button_label`),
    `hero_button_url` = VALUES(`hero_button_url`),
    `information_visible` = VALUES(`information_visible`),
    `information_items` = VALUES(`information_items`),
    `menu_visible` = VALUES(`menu_visible`),
    `menu_eyebrow` = VALUES(`menu_eyebrow`),
    `menu_heading` = VALUES(`menu_heading`),
    `menu_description` = VALUES(`menu_description`),
    `menu_items` = VALUES(`menu_items`),
    `programme_visible` = VALUES(`programme_visible`),
    `programme_image` = VALUES(`programme_image`),
    `programme_image_alt` = VALUES(`programme_image_alt`),
    `programme_eyebrow` = VALUES(`programme_eyebrow`),
    `programme_heading` = VALUES(`programme_heading`),
    `programme_items` = VALUES(`programme_items`),
    `reservation_visible` = VALUES(`reservation_visible`),
    `reservation_eyebrow` = VALUES(`reservation_eyebrow`),
    `reservation_heading` = VALUES(`reservation_heading`),
    `reservation_description` = VALUES(`reservation_description`),
    `reservation_button_label` = VALUES(`reservation_button_label`),
    `reservation_button_url` = VALUES(`reservation_button_url`),
    `updated_at` = NOW();

INSERT INTO `festive_events` (
    `title`, `slug`, `is_active`, `sort_order`, `meta_title`, `meta_description`,
    `hero_image`, `hero_image_alt`, `hero_eyebrow`, `hero_heading`, `hero_subheading`,
    `hero_description`, `hero_price`, `hero_button_label`, `hero_button_url`,
    `information_visible`, `information_items`, `menu_visible`, `menu_eyebrow`,
    `menu_heading`, `menu_description`, `menu_items`, `programme_visible`,
    `programme_image`, `programme_image_alt`, `programme_eyebrow`, `programme_heading`,
    `programme_items`, `reservation_visible`, `reservation_eyebrow`,
    `reservation_heading`, `reservation_description`, `reservation_button_label`,
    `reservation_button_url`, `created_at`, `updated_at`
) VALUES (
    'New Year Dinner',
    'new-year-dinner',
    1,
    2,
    'New Year Dinner | Nandini Jungle by Hanging Gardens',
    'Welcome the new year with an exquisite multi-course dinner and an intimate evening in the heart of the jungle.',
    '/images/festive/2026/new-year-dining.jpg',
    'A Night to Begin Anew',
    '31 DECEMBER 2026',
    CONCAT('A Night to', CHAR(10), 'Begin Anew'),
    'NEW YEAR’S EVE CELEBRATION',
    'As the year comes to a close, slow down and enjoy the evening in the heart of the jungle. Gather around the table, share good food and conversation, and welcome the year ahead.',
    'IDR 2,200,000++ per person',
    'RESERVE NOW',
    '#reserve',
    1,
    JSON_ARRAY(
        JSON_OBJECT('label', 'DATE', 'value', '31 December 2026'),
        JSON_OBJECT('label', 'EXPERIENCE', 'value', 'New Year Eve'),
        JSON_OBJECT('label', 'LOCATION', 'value', 'Nandini Jungle'),
        JSON_OBJECT('label', 'DINING', 'value', 'Festive Multi-Course Dinner')
    ),
    1,
    'NEW YEAR EVE',
    'Dinner Menu',
    'An exquisite multi-course dining experience, crafted with premium ingredients and contemporary flavours.',
    JSON_ARRAY(
        JSON_OBJECT('type', 'dish', 'title', 'Blood Orange Fish Carpaccio', 'description', 'King Fish – Orange Dressing – Calamansi Jelly', 'image', '/images/festive/2026/Dish/BLOOD%20ORANGE%20FISH%20CARPACCIO.jpg', 'image_alt', 'Blood Orange Fish Carpaccio'),
        JSON_OBJECT('type', 'dish', 'title', 'Blue Crab', 'description', 'Jicama – Granny Smith Apple – Celery Pickle – Tomato Cherry', 'image', '/images/festive/2026/Dish/BLUE%20CRAB.jpg', 'image_alt', 'Blue Crab'),
        JSON_OBJECT('type', 'intermezzo', 'label', 'INTERMEZZO', 'title', 'Water Melon and Lemon Basil Sorbet', 'description', NULL, 'image', NULL, 'image_alt', NULL),
        JSON_OBJECT('type', 'dish', 'title', 'Surf and Turf', 'description', 'Mushroom Puree – Crispy Onion – Asparagus – Chimichurri – Nasturtium', 'image', '/images/festive/2026/Dish/SURF%20AND%20TURF.jpg', 'image_alt', 'Surf and Turf'),
        JSON_OBJECT('type', 'dish', 'title', 'Soy Black Cod', 'description', 'Chili Pepper Puree – Grilled Asparagus – Broccolini – Salsa Verde – Cress Salad', 'image', '/images/festive/2026/Dish/SOY%20BLACK%20COD.jpg', 'image_alt', 'Soy Black Cod'),
        JSON_OBJECT('type', 'dish', 'title', 'V3+ Wagyu Tenderloin', 'description', 'Sweet Potato Grattan – Truffle Demi Glass – Chard King Oyster Mushroom', 'image', '/images/festive/2026/Dish/V3%2B%20WAGYU%20TENDERLOIN.jpg', 'image_alt', 'V3+ Wagyu Tenderloin'),
        JSON_OBJECT('type', 'dish', 'label', 'DESSERT', 'title', 'Tape Ketan Panna Cotta', 'description', 'Fermented Glutinous Rice – Mango Compote – Yogurt Ice Cream', 'image', '/images/festive/2026/Dish/Tape%20ketan%20panna%20cotta.jpg', 'image_alt', 'Tape Ketan Panna Cotta')
    ),
    1,
    '/images/festive/2026/new-year-dining.jpg',
    'New Year Eve dinner at Nandini Jungle',
    'NEW YEAR EVE DINNER',
    CONCAT('Programme of the Evening', CHAR(10), '31 December 2026'),
    JSON_ARRAY(
        JSON_OBJECT('time', '07:00 PM – 07:15 PM', 'activity', 'Cocktail & Canape Soiree'),
        JSON_OBJECT('time', '07:15 PM – 08:00 PM', 'activity', 'Dinner with Balinese Dance Performance'),
        JSON_OBJECT('time', '08:00 PM – 08:10 PM', 'activity', 'GM''s Speech'),
        JSON_OBJECT('time', '08:10 PM – 10:00 PM', 'activity', 'Balinese Dance Performance'),
        JSON_OBJECT('time', '10:00 PM – 11:45 PM', 'activity', 'Cocktails & Social Party'),
        JSON_OBJECT('time', '11:00 PM – 12:00 PM', 'activity', 'GM''s Speech & Countdown to 2027')
    ),
    1,
    'NEW YEAR AT NANDINI',
    'Reserve Your Table',
    'Celebrate the new year with an exquisite dining experience at Nandini Jungle.',
    'RESERVE NOW',
    'https://wa.me/6281236871170?text=Hello%2C%20I%20would%20like%20to%20reserve%20the%20New%20Year%27s%20Eve%20Dinner%20at%20Nandini%20Jungle.',
    NOW(),
    NOW()
) 
ON DUPLICATE KEY UPDATE
    `title` = VALUES(`title`),
    `is_active` = VALUES(`is_active`),
    `sort_order` = VALUES(`sort_order`),
    `meta_title` = VALUES(`meta_title`),
    `meta_description` = VALUES(`meta_description`),
    `hero_image` = VALUES(`hero_image`),
    `hero_image_alt` = VALUES(`hero_image_alt`),
    `hero_eyebrow` = VALUES(`hero_eyebrow`),
    `hero_heading` = VALUES(`hero_heading`),
    `hero_subheading` = VALUES(`hero_subheading`),
    `hero_description` = VALUES(`hero_description`),
    `hero_price` = VALUES(`hero_price`),
    `hero_button_label` = VALUES(`hero_button_label`),
    `hero_button_url` = VALUES(`hero_button_url`),
    `information_visible` = VALUES(`information_visible`),
    `information_items` = VALUES(`information_items`),
    `menu_visible` = VALUES(`menu_visible`),
    `menu_eyebrow` = VALUES(`menu_eyebrow`),
    `menu_heading` = VALUES(`menu_heading`),
    `menu_description` = VALUES(`menu_description`),
    `menu_items` = VALUES(`menu_items`),
    `programme_visible` = VALUES(`programme_visible`),
    `programme_image` = VALUES(`programme_image`),
    `programme_image_alt` = VALUES(`programme_image_alt`),
    `programme_eyebrow` = VALUES(`programme_eyebrow`),
    `programme_heading` = VALUES(`programme_heading`),
    `programme_items` = VALUES(`programme_items`),
    `reservation_visible` = VALUES(`reservation_visible`),
    `reservation_eyebrow` = VALUES(`reservation_eyebrow`),
    `reservation_heading` = VALUES(`reservation_heading`),
    `reservation_description` = VALUES(`reservation_description`),
    `reservation_button_label` = VALUES(`reservation_button_label`),
    `reservation_button_url` = VALUES(`reservation_button_url`),
    `updated_at` = NOW();

-- Keep Laravel from attempting to rerun the equivalent migrations after this
-- manual SQL deployment. The application files must be uploaded at the same time.
SET @festive_migration_batch := (SELECT COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT `pending`.`migration`, @festive_migration_batch
FROM (
    SELECT '2026_09_29_000001_create_festive_settings_table' AS `migration`
    UNION ALL SELECT '2026_09_29_000002_create_festive_events_table'
    UNION ALL SELECT '2026_09_29_000003_link_festive_landing_to_detail_pages'
    UNION ALL SELECT '2026_09_29_000004_add_new_year_rundown_to_festive_pages'
    UNION ALL SELECT '2026_09_29_000005_correct_new_year_rundown_date'
    UNION ALL SELECT '2026_09_29_000006_fix_persisted_new_year_countdown'
    UNION ALL SELECT '2026_09_29_000007_use_festive_dish_images'
    UNION ALL SELECT '2026_09_29_000008_correct_festive_event_menu_assignments'
    UNION ALL SELECT '2026_09_29_000009_apply_updated_festive_menus'
    UNION ALL SELECT '2026_09_29_000010_update_festive_landing_hero_image'
) AS `pending`
WHERE NOT EXISTS (
    SELECT 1
    FROM `migrations`
    WHERE `migrations`.`migration` = `pending`.`migration`
);

COMMIT;

-- Verification
SELECT `id`, `hero_image`, `programme_heading`, `updated_at`
FROM `festive_settings`
WHERE `id` = 1;

SELECT `id`, `slug`, `hero_eyebrow`, `menu_heading`, `programme_visible`, `updated_at`
FROM `festive_events`
WHERE `slug` IN ('christmas-dinner', 'new-year-dinner')
ORDER BY `sort_order`;
