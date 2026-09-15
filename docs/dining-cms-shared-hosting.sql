-- Dining Landing Page CMS schema and initial content for MySQL/MariaDB.
-- Back up the database first. This script is rerunnable and preserves every non-NULL CMS value.
SET NAMES utf8mb4;
SET @schema_name = DATABASE();

CREATE TABLE IF NOT EXISTS `dining_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reservation_whatsapp` varchar(255) NULL,
  `reservation_email` varchar(255) NULL,
  `reservation_url` text NULL,
  `food_menu_url` text NULL,
  `beverage_menu_url` text NULL,
  `opening_hours` varchar(255) NULL,
  `location` varchar(255) NULL,
  `cuisine` varchar(255) NULL,
  `dress_code` varchar(255) NULL,
  `outside_guest_information` text NULL,
  `meta_title` varchar(255) NULL,
  `meta_description` text NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DELIMITER $$
DROP PROCEDURE IF EXISTS add_column_if_missing$$
CREATE PROCEDURE add_column_if_missing(IN table_name_in varchar(64), IN column_name_in varchar(64), IN definition_in text)
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM information_schema.columns
    WHERE table_schema = @schema_name AND table_name = table_name_in AND column_name = column_name_in
  ) THEN
    SET @ddl = CONCAT('ALTER TABLE `', table_name_in, '` ADD COLUMN `', column_name_in, '` ', definition_in);
    PREPARE statement_to_run FROM @ddl;
    EXECUTE statement_to_run;
    DEALLOCATE PREPARE statement_to_run;
  END IF;
END$$

DROP PROCEDURE IF EXISTS add_index_if_missing$$
CREATE PROCEDURE add_index_if_missing(IN table_name_in varchar(64), IN index_name_in varchar(64), IN definition_in text)
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM information_schema.statistics
    WHERE table_schema = @schema_name AND table_name = table_name_in AND index_name = index_name_in
  ) THEN
    SET @ddl = CONCAT('ALTER TABLE `', table_name_in, '` ADD ', definition_in);
    PREPARE statement_to_run FROM @ddl;
    EXECUTE statement_to_run;
    DEALLOCATE PREPARE statement_to_run;
  END IF;
END$$
DELIMITER ;

-- Experience and review integration.
CALL add_column_if_missing('experiences', 'show_on_dining', 'tinyint(1) NOT NULL DEFAULT 0');
CALL add_column_if_missing('experiences', 'dining_slug', 'varchar(255) NULL');
CALL add_column_if_missing('experiences', 'dining_card_title', 'varchar(255) NULL');
CALL add_column_if_missing('experiences', 'dining_cta_label', 'varchar(255) NULL');
CALL add_column_if_missing('experiences', 'dining_short_description', 'text NULL');
CALL add_column_if_missing('experiences', 'hero_mobile_image', 'varchar(255) NULL');
CALL add_column_if_missing('experiences', 'intro_eyebrow', 'varchar(255) NULL');
CALL add_column_if_missing('experiences', 'page_heading', 'varchar(255) NULL');
CALL add_column_if_missing('experiences', 'menu_cta_label', 'varchar(255) NULL');
CALL add_column_if_missing('experiences', 'menu_url', 'text NULL');
CALL add_column_if_missing('experiences', 'reservation_cta_label', 'varchar(255) NULL');
CALL add_column_if_missing('experiences', 'reservation_url', 'text NULL');
CALL add_column_if_missing('experiences', 'opening_hours', 'varchar(255) NULL');
CALL add_column_if_missing('experiences', 'experience_type', 'varchar(255) NULL');
CALL add_column_if_missing('experiences', 'whatsapp_number', 'varchar(255) NULL');
CALL add_column_if_missing('guest_reviews', 'show_on_dining', 'tinyint(1) NOT NULL DEFAULT 0');
CALL add_index_if_missing('experiences', 'experiences_show_on_dining_index', 'INDEX `experiences_show_on_dining_index` (`show_on_dining`)');
CALL add_index_if_missing('experiences', 'experiences_dining_slug_unique', 'UNIQUE INDEX `experiences_dining_slug_unique` (`dining_slug`)');
CALL add_index_if_missing('guest_reviews', 'guest_reviews_show_on_dining_index', 'INDEX `guest_reviews_show_on_dining_index` (`show_on_dining`)');

CREATE TABLE IF NOT EXISTS `dining_experience_gallery` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `experience_id` bigint unsigned NOT NULL,
  `image` varchar(255) NOT NULL,
  `image_alt` varchar(255) NULL,
  `caption` varchar(255) NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `dining_gallery_display_index` (`experience_id`,`is_active`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- All Dining singleton fields.
CALL add_column_if_missing('dining_settings', 'philosophy_eyebrow', 'varchar(255) NULL');
CALL add_column_if_missing('dining_settings', 'philosophy_heading', 'text NULL');
CALL add_column_if_missing('dining_settings', 'philosophy_description', 'text NULL');
CALL add_column_if_missing('dining_settings', 'philosophy_image', 'varchar(255) NULL');
CALL add_column_if_missing('dining_settings', 'philosophy_image_alt', 'varchar(255) NULL');
CALL add_column_if_missing('dining_settings', 'philosophy_accent_text', 'text NULL');
CALL add_column_if_missing('dining_settings', 'why_dine_eyebrow', 'varchar(255) NULL');
CALL add_column_if_missing('dining_settings', 'why_dine_heading', 'text NULL');
CALL add_column_if_missing('dining_settings', 'why_dine_items', 'json NULL');
CALL add_column_if_missing('dining_settings', 'information_bar_items', 'json NULL');
CALL add_column_if_missing('dining_settings', 'private_dining_eyebrow', 'varchar(255) NULL');
CALL add_column_if_missing('dining_settings', 'private_dining_heading', 'text NULL');
CALL add_column_if_missing('dining_settings', 'private_dining_description', 'text NULL');
CALL add_column_if_missing('dining_settings', 'private_dining_image', 'varchar(255) NULL');
CALL add_column_if_missing('dining_settings', 'private_dining_image_alt', 'varchar(255) NULL');
CALL add_column_if_missing('dining_settings', 'private_dining_cta_label', 'varchar(255) NULL');
CALL add_column_if_missing('dining_settings', 'private_dining_cta_url', 'text NULL');
CALL add_column_if_missing('dining_settings', 'private_dining_tags', 'json NULL');
CALL add_column_if_missing('dining_settings', 'visit_eyebrow', 'varchar(255) NULL');
CALL add_column_if_missing('dining_settings', 'visit_heading', 'text NULL');
CALL add_column_if_missing('dining_settings', 'visit_information_items', 'json NULL');
CALL add_column_if_missing('dining_settings', 'visit_food_menu_label', 'varchar(255) NULL');
CALL add_column_if_missing('dining_settings', 'visit_food_menu_url', 'text NULL');
CALL add_column_if_missing('dining_settings', 'visit_beverage_menu_label', 'varchar(255) NULL');
CALL add_column_if_missing('dining_settings', 'visit_beverage_menu_url', 'text NULL');
CALL add_column_if_missing('dining_settings', 'faq_eyebrow', 'varchar(255) NULL');
CALL add_column_if_missing('dining_settings', 'faq_heading', 'text NULL');
CALL add_column_if_missing('dining_settings', 'faq_items', 'json NULL');
CALL add_column_if_missing('dining_settings', 'reservation_cta_background_image', 'varchar(255) NULL');
CALL add_column_if_missing('dining_settings', 'reservation_cta_background_image_alt', 'varchar(255) NULL');
CALL add_column_if_missing('dining_settings', 'reservation_cta_heading', 'text NULL');
CALL add_column_if_missing('dining_settings', 'reservation_cta_description', 'text NULL');
CALL add_column_if_missing('dining_settings', 'reservation_cta_label', 'varchar(255) NULL');
CALL add_column_if_missing('dining_settings', 'reservation_cta_url', 'text NULL');
CALL add_column_if_missing('dining_settings', 'meta_author', 'varchar(255) NULL');
CALL add_column_if_missing('dining_settings', 'meta_site_name', 'varchar(255) NULL');
CALL add_column_if_missing('dining_settings', 'hero_video_id', 'varchar(255) NULL');
CALL add_column_if_missing('dining_settings', 'hero_image', 'varchar(255) NULL');
CALL add_column_if_missing('dining_settings', 'hero_image_alt', 'varchar(255) NULL');
CALL add_column_if_missing('dining_settings', 'hero_eyebrow', 'varchar(255) NULL');
CALL add_column_if_missing('dining_settings', 'hero_heading', 'text NULL');
CALL add_column_if_missing('dining_settings', 'hero_subheading', 'text NULL');
CALL add_column_if_missing('dining_settings', 'hero_description', 'text NULL');
CALL add_column_if_missing('dining_settings', 'hero_primary_cta_label', 'varchar(255) NULL');
CALL add_column_if_missing('dining_settings', 'hero_primary_cta_url', 'text NULL');
CALL add_column_if_missing('dining_settings', 'hero_secondary_cta_label', 'varchar(255) NULL');
CALL add_column_if_missing('dining_settings', 'hero_secondary_cta_url', 'text NULL');
CALL add_column_if_missing('dining_settings', 'experiences_eyebrow', 'varchar(255) NULL');
CALL add_column_if_missing('dining_settings', 'experiences_heading', 'text NULL');
CALL add_column_if_missing('dining_settings', 'signature_dishes', 'json NULL');
CALL add_column_if_missing('dining_settings', 'signature_menu_label', 'varchar(255) NULL');
CALL add_column_if_missing('dining_settings', 'signature_menu_url', 'text NULL');
CALL add_column_if_missing('dining_settings', 'guest_reviews_heading', 'varchar(255) NULL');
CALL add_column_if_missing('dining_settings', 'guest_reviews_see_more_label', 'varchar(255) NULL');
CALL add_column_if_missing('dining_settings', 'guest_reviews_see_more_url', 'text NULL');

INSERT IGNORE INTO `dining_settings` (`id`, `created_at`, `updated_at`) VALUES (1, NOW(), NOW());

-- Populate only NULL values. Empty strings/arrays and prior admin edits are intentionally preserved.
UPDATE `dining_settings` SET
 `reservation_whatsapp`=COALESCE(`reservation_whatsapp`, '+62 812 3687 1170'),
 `reservation_email`=COALESCE(`reservation_email`, 'reservation@nandinibali.com'),
 `reservation_url`=COALESCE(`reservation_url`, 'https://wa.me/6281236871170'),
 `opening_hours`=COALESCE(`opening_hours`, '7.00 AM – 10.00 PM'),
 `location`=COALESCE(`location`, 'Nandini Jungle by Hanging Gardens, Ubud, Bali'),
 `cuisine`=COALESCE(`cuisine`, 'Indonesian & International'),
 `outside_guest_information`=COALESCE(`outside_guest_information`, 'Yes, all are welcome'),
 `meta_title`=COALESCE(`meta_title`, 'Luxury Dining in Ubud | Nandini Jungle by Hanging Gardens'),
 `meta_description`=COALESCE(`meta_description`, 'Discover luxury jungle dining in Ubud at Nandini Jungle by Hanging Gardens, featuring Wild Ginger Restaurant, refined Balinese flavours, romantic dining and curated culinary experiences.'),
 `meta_author`=COALESCE(`meta_author`, 'Nandini Jungle by Hanging Gardens'),
 `meta_site_name`=COALESCE(`meta_site_name`, 'Nandini Jungle by Hanging Gardens'),
 `hero_video_id`=COALESCE(`hero_video_id`, 'GZav9hOJKts'),
 `hero_image`=COALESCE(`hero_image`, 'https://i.ytimg.com/vi/GZav9hOJKts/maxresdefault.jpg'),
 `hero_image_alt`=COALESCE(`hero_image_alt`, ''),
 `hero_eyebrow`=COALESCE(`hero_eyebrow`, 'Dining at Nandini Jungle'),
 `hero_heading`=COALESCE(`hero_heading`, CONCAT('A Culinary Journey', CHAR(10), 'in the Heart of the Jungle')),
 `hero_subheading`=COALESCE(`hero_subheading`, 'Exquisite flavours. Enchanting surroundings. Unforgettable moments.'),
 `hero_description`=COALESCE(`hero_description`, 'Discover a dining experience where authentic Balinese ingredients meet international finesse, set within the lush jungle of Ubud.'),
 `hero_primary_cta_label`=COALESCE(`hero_primary_cta_label`, 'Reserve a table'),
 `hero_primary_cta_url`=COALESCE(`hero_primary_cta_url`, `reservation_url`),
 `hero_secondary_cta_label`=COALESCE(`hero_secondary_cta_label`, 'View menu'),
 `hero_secondary_cta_url`=COALESCE(`hero_secondary_cta_url`, `food_menu_url`, 'https://wa.me/6281236871170?text=Hello%2C%20may%20I%20view%20the%20dining%20menu%2C%20please%3F'),
 `philosophy_eyebrow`=COALESCE(`philosophy_eyebrow`, 'Our philosophy'),
 `philosophy_heading`=COALESCE(`philosophy_heading`, CONCAT('More Than a Meal,', CHAR(10), 'A Meaningful Experience')),
 `philosophy_description`=COALESCE(`philosophy_description`, 'At Nandini Jungle, dining is a celebration of nature, culture and connection. Our culinary philosophy is inspired by the richness of Indonesia, crafted with the finest ingredients, and served with heartfelt hospitality in an extraordinary jungle setting.'),
 `philosophy_image_alt`=COALESCE(`philosophy_image_alt`, 'A chef adds the finishing touches to a plated dish at Nandini Jungle.'),
 `philosophy_accent_text`=COALESCE(`philosophy_accent_text`, CONCAT('Flavours', CHAR(10), 'from the Heart', CHAR(10), 'of Bali')),
 `why_dine_eyebrow`=COALESCE(`why_dine_eyebrow`, 'Why dine at Nandini'),
 `why_dine_heading`=COALESCE(`why_dine_heading`, CONCAT('An Extraordinary Setting', CHAR(10), 'for Every Occasion')),
 `experiences_eyebrow`=COALESCE(`experiences_eyebrow`, 'Dining experiences'),
 `experiences_heading`=COALESCE(`experiences_heading`, 'Distinctive Dining, Made for You'),
 `signature_menu_label`=COALESCE(`signature_menu_label`, 'View full menu'),
 `signature_menu_url`=COALESCE(`signature_menu_url`, `food_menu_url`, 'https://drive.google.com/file/d/1XwHnNgEreiCA4mexe65GOc44Q9WSRSsg/view?usp=sharing'),
 `private_dining_eyebrow`=COALESCE(`private_dining_eyebrow`, 'Special occasions'),
 `private_dining_heading`=COALESCE(`private_dining_heading`, 'Moments to Treasure'),
 `private_dining_description`=COALESCE(`private_dining_description`, 'Whether it’s a honeymoon, anniversary, proposal or an intimate celebration, our bespoke dining experiences are designed to make your moments truly unforgettable.'),
 `private_dining_cta_label`=COALESCE(`private_dining_cta_label`, 'Enquire private dining'),
 `private_dining_cta_url`=COALESCE(`private_dining_cta_url`, `reservation_url`),
 `visit_eyebrow`=COALESCE(`visit_eyebrow`, 'Practical information'),
 `visit_heading`=COALESCE(`visit_heading`, 'Plan Your Visit'),
 `visit_food_menu_label`=COALESCE(`visit_food_menu_label`, 'View food menu'),
 `visit_food_menu_url`=COALESCE(`visit_food_menu_url`, 'https://drive.google.com/file/d/1XwHnNgEreiCA4mexe65GOc44Q9WSRSsg/view?usp=sharing'),
 `visit_beverage_menu_label`=COALESCE(`visit_beverage_menu_label`, 'View beverage list'),
 `visit_beverage_menu_url`=COALESCE(`visit_beverage_menu_url`, 'https://drive.google.com/file/d/1Xm5YhSbX18muQQTdrLvNLYd7cgFw5EaS/view?usp=sharing'),
 `faq_eyebrow`=COALESCE(`faq_eyebrow`, 'Frequently asked questions'),
 `faq_heading`=COALESCE(`faq_heading`, 'You May Wonder'),
 `guest_reviews_heading`=COALESCE(`guest_reviews_heading`, 'What Our Guests Say'),
 `guest_reviews_see_more_label`=COALESCE(`guest_reviews_see_more_label`, 'See More'),
 `guest_reviews_see_more_url`=COALESCE(`guest_reviews_see_more_url`, '/guest-reviews'),
 `reservation_cta_background_image`=COALESCE(`reservation_cta_background_image`, '/images/dining/romantic-dining-by-the-chapel.webp'),
 `reservation_cta_background_image_alt`=COALESCE(`reservation_cta_background_image_alt`, ''),
 `reservation_cta_heading`=COALESCE(`reservation_cta_heading`, 'A Table Awaits in the Jungle'),
 `reservation_cta_description`=COALESCE(`reservation_cta_description`, CONCAT('Let us create a memorable dining experience for you.', CHAR(10), 'Reserve your table and indulge in the flavours of Nandini Jungle.')),
 `reservation_cta_label`=COALESCE(`reservation_cta_label`, 'Reserve a table'),
 `reservation_cta_url`=COALESCE(`reservation_cta_url`, `reservation_url`),
 `updated_at`=NOW()
WHERE `id`=1;

UPDATE `dining_settings` SET `why_dine_items`=JSON_ARRAY(
 JSON_OBJECT('title', CONCAT('Breathtaking', CHAR(10), 'Jungle Setting'), 'description', 'Dine surrounded by the serene beauty of Ubud’s lush rainforest.', 'icon', 'leaves'),
 JSON_OBJECT('title', CONCAT('Authentic &', CHAR(10), 'Refined Cuisine'), 'description', 'A harmonious blend of Indonesian, Balinese and international flavours.', 'icon', 'bowl'),
 JSON_OBJECT('title', CONCAT('Award-Winning', CHAR(10), 'Wine Collection'), 'description', 'Curated wines from around the world.', 'icon', 'wine'),
 JSON_OBJECT('title', 'Romantic & Intimate', 'description', 'Perfect for couples, honeymooners and special celebrations.', 'icon', 'heart')
) WHERE `id`=1 AND `why_dine_items` IS NULL;

UPDATE `dining_settings` SET `information_bar_items`=JSON_ARRAY(
 JSON_OBJECT('icon','clock','label','Opening hours','value','7.00 AM – 10.00 PM','link',NULL),
 JSON_OBJECT('icon','dining','label','Cuisine style','value','Indonesian & International','link',NULL),
 JSON_OBJECT('icon','location','label','Location','value','Ubud, Bali','link',NULL),
 JSON_OBJECT('icon','whatsapp','label','WhatsApp reservation','value','+62 812 3687 1170','link','https://wa.me/6281236871170')
) WHERE `id`=1 AND `information_bar_items` IS NULL;

UPDATE `dining_settings` SET `private_dining_tags`=JSON_ARRAY(
 JSON_OBJECT('label','Honeymoon Dinner','url','/honeymoon'), JSON_OBJECT('label','Anniversary','url','https://wa.me/6281236871170'),
 JSON_OBJECT('label','Proposal','url','https://wa.me/6281236871170'), JSON_OBJECT('label','Celebrations','url','https://wa.me/6281236871170')
) WHERE `id`=1 AND `private_dining_tags` IS NULL;

UPDATE `dining_settings` SET `visit_information_items`=JSON_ARRAY(
 JSON_OBJECT('icon','clock','label','Opening Hours','value','7.00 AM – 10.00 PM','link',NULL),
 JSON_OBJECT('icon','dining','label','Cuisine Style','value','Indonesian & International','link',NULL),
 JSON_OBJECT('icon','location','label','Location','value',CONCAT('Nandini Jungle by Hanging Gardens', CHAR(10), 'Ubud, Bali'),'link',NULL),
 JSON_OBJECT('icon','whatsapp','label','WhatsApp','value','+62 812 3687 1170','link','https://wa.me/6281236871170'),
 JSON_OBJECT('icon','email','label','Email','value','reservation@nandinibali.com','link','mailto:reservation@nandinibali.com'),
 JSON_OBJECT('icon','guests','label','Open to Outside Guests','value','Yes, all are welcome','link',NULL)
) WHERE `id`=1 AND `visit_information_items` IS NULL;

UPDATE `dining_settings` SET `faq_items`=JSON_ARRAY(
 JSON_OBJECT('question','Do I need a reservation?','answer','Reservations are recommended, especially for dinner, romantic dining and special occasions. Walk-in availability may vary.'),
 JSON_OBJECT('question','Is the restaurant open to outside guests?','answer','Yes. Outside guests are welcome to dine at Nandini Jungle by Hanging Gardens. Advance reservation is recommended.'),
 JSON_OBJECT('question','Do you accommodate dietary preferences?','answer','Please share any dietary preferences, allergies or special requirements with our team before your visit so the kitchen can advise and assist where possible.'),
 JSON_OBJECT('question','Is afternoon tea available daily?','answer','Afternoon tea is offered daily from 3.00 PM to 5.00 PM. Please contact our team in advance to confirm availability and arrangements for your visit.'),
 JSON_OBJECT('question','Can I book a romantic or private dining experience?','answer','Yes. Romantic and private dining experiences are available by arrangement. Contact our team on WhatsApp to discuss your preferred occasion and date.')
) WHERE `id`=1 AND `faq_items` IS NULL;

UPDATE `dining_settings` SET `signature_dishes`=JSON_ARRAY(JSON_OBJECT(
 'eyebrow','Dish of the Month','heading','Rahang Tuna','introduction','Celebrating the culinary traditions of the archipelago, Rahang Tuna showcases the prized tuna cheek — tender, flavourful, and delicately grilled over natural fire.',
 'label','Ocean’s Finest Cut','title','Rahang Tuna','price_display','IDR 420,000++ per person','panel_description','Tender tuna cheek, delicately grilled over natural fire and served with crisp potato wedges, seasonal vegetables and house-made sauce.',
 'image','/images/dining/rahang-tuna.jpeg','alt','Rahang Tuna with potato wedges, seasonal vegetables and house-made sauce at Nandini Jungle'
)) WHERE `id`=1 AND `signature_dishes` IS NULL;

-- Map existing Experience records to the Dining landing page without replacing existing CMS copy.
UPDATE `experiences` SET `show_on_dining`=1, `dining_slug`='wild-ginger-restaurant', `sort_order`=1,
 `dining_card_title`=COALESCE(`dining_card_title`,'Wild Ginger Restaurant'), `dining_short_description`=COALESCE(`dining_short_description`,'Dine surrounded by nature, offering a refined à la carte menu inspired by Indonesian and international cuisine.'), `dining_cta_label`=COALESCE(`dining_cta_label`,'Explore Restaurant'), `card_image_alt`=COALESCE(`card_image_alt`,'Signature dish at Wild Ginger Restaurant, Nandini Jungle Ubud'), `updated_at`=NOW() WHERE `slug`='wild-ginger-restaurant';
UPDATE `experiences` SET `show_on_dining`=1, `dining_slug`='bar-and-lounge', `sort_order`=2,
 `dining_card_title`=COALESCE(`dining_card_title`,'Bar & Lounge'), `dining_short_description`=COALESCE(`dining_short_description`,'Relax with handcrafted cocktails, fine wines and light bites in a stylish open-air lounge.'), `dining_cta_label`=COALESCE(`dining_cta_label`,'Explore Bar & Lounge'), `card_image_alt`=COALESCE(`card_image_alt`,'Craft cocktail and light bites at Nandini Jungle Bar & Lounge'), `updated_at`=NOW() WHERE `slug`='bar-and-lounge';
UPDATE `experiences` SET `show_on_dining`=1, `dining_slug`='afternoon-tea', `sort_order`=3,
 `dining_card_title`=COALESCE(`dining_card_title`,'Afternoon Tea'), `dining_short_description`=COALESCE(`dining_short_description`,'A delightful selection of sweet and savoury creations, served in the heart of the jungle.'), `dining_cta_label`=COALESCE(`dining_cta_label`,'Discover Afternoon Tea'), `card_image_alt`=COALESCE(`card_image_alt`,'Afternoon tea selection at Nandini Jungle in Ubud'), `updated_at`=NOW() WHERE `slug`='luxe-high-tea';
UPDATE `experiences` SET `show_on_dining`=1, `dining_slug`='wine-cellar-experience', `sort_order`=4,
 `dining_card_title`=COALESCE(`dining_card_title`,'Wine Cellar Experience'), `dining_short_description`=COALESCE(`dining_short_description`,'An exclusive collection of premium wines, perfect for a refined evening or a private tasting.'), `dining_cta_label`=COALESCE(`dining_cta_label`,'Explore Wine Cellar'), `card_image_alt`=COALESCE(`card_image_alt`,'Wine being poured during the Nandini Jungle wine cellar experience'), `updated_at`=NOW() WHERE `slug`='wine-cellar-experience';
UPDATE `experiences` SET `show_on_dining`=1, `dining_slug`='romantic-dining', `sort_order`=5,
 `dining_card_title`=COALESCE(`dining_card_title`,'Romantic Dining'), `dining_short_description`=COALESCE(`dining_short_description`,'Create unforgettable moments with a candlelit dinner in a magical jungle atmosphere.'), `dining_cta_label`=COALESCE(`dining_cta_label`,'Discover Romantic Dining'), `card_image_alt`=COALESCE(`card_image_alt`,'Candlelit romantic dining experience at Nandini Jungle'), `updated_at`=NOW() WHERE `slug`='romantic-dining-by-the-chapel';

DROP PROCEDURE IF EXISTS add_column_if_missing;
DROP PROCEDURE IF EXISTS add_index_if_missing;

-- Mark the equivalent Laravel migrations as applied so a later `php artisan migrate`
-- does not try to add these columns a second time.
SET @dining_migration_batch = (SELECT COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`);
INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_09_08_000001_add_dining_cms_support', @dining_migration_batch
WHERE NOT EXISTS (SELECT 1 FROM `migrations` WHERE `migration`='2026_09_08_000001_add_dining_cms_support');
INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_09_08_000002_add_dining_card_copy_to_experiences', @dining_migration_batch
WHERE NOT EXISTS (SELECT 1 FROM `migrations` WHERE `migration`='2026_09_08_000002_add_dining_card_copy_to_experiences');
INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_09_09_000001_add_philosophy_section_to_dining_settings', @dining_migration_batch
WHERE NOT EXISTS (SELECT 1 FROM `migrations` WHERE `migration`='2026_09_09_000001_add_philosophy_section_to_dining_settings');
INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_09_09_000002_add_why_dine_section_to_dining_settings', @dining_migration_batch
WHERE NOT EXISTS (SELECT 1 FROM `migrations` WHERE `migration`='2026_09_09_000002_add_why_dine_section_to_dining_settings');
INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_09_09_000003_add_remaining_sections_to_dining_settings', @dining_migration_batch
WHERE NOT EXISTS (SELECT 1 FROM `migrations` WHERE `migration`='2026_09_09_000003_add_remaining_sections_to_dining_settings');
INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_09_09_000004_move_all_dining_landing_content_to_database', @dining_migration_batch
WHERE NOT EXISTS (SELECT 1 FROM `migrations` WHERE `migration`='2026_09_09_000004_move_all_dining_landing_content_to_database');

-- Verification queries.
SELECT `id`, `hero_heading`, `philosophy_heading`, `experiences_heading`, `faq_heading`, `reservation_cta_heading` FROM `dining_settings` WHERE `id`=1;
SELECT `id`, `slug`, `dining_slug`, `show_on_dining`, `dining_card_title` FROM `experiences` WHERE `show_on_dining`=1 ORDER BY `sort_order`;
