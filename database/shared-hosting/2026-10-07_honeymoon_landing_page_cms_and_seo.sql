-- Nandini Jungle by Hanging Gardens
-- Honeymoon landing page CMS content and SEO update for /honeymoon.
--
-- Import this file into the database selected for the website in phpMyAdmin.
-- It is safe to run more than once: missing sections/images are inserted and
-- existing honeymoon CMS records are updated in place.
-- Take a database backup before importing.

SET NAMES utf8mb4;

START TRANSACTION;

SET @honeymoon_page_id := (
    SELECT `id`
    FROM `pages`
    WHERE `page_name` = 'Honeymoon Page'
       OR `slug` IN ('honeymoon', 'honeymoon-bali-packages')
    ORDER BY CASE WHEN `page_name` = 'Honeymoon Page' THEN 0 ELSE 1 END, `id`
    LIMIT 1
);

SET @next_page_section_id := COALESCE((SELECT MAX(`id`) FROM `page_sections`), 0);
SET @next_page_section_image_id := COALESCE((SELECT MAX(`id`) FROM `page_section_images`), 0);

-- Create the editable Filament sections when they do not exist yet.
INSERT INTO `page_sections`
    (`id`, `page_id`, `section_key`, `title`, `subtitle`, `excerpt`, `description`, `items`, `button_label`, `button_link_type`, `button_url`, `button_route`, `text_align`, `background_color`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    (@next_page_section_id := @next_page_section_id + 1), @honeymoon_page_id,
    'honeymoon_hero', 'Honeymoon Resort in Ubud, Bali', 'Honeymoon', NULL,
    '<p>A Romantic Jungle Honeymoon at Nandini Jungle by Hanging Gardens</p>', NULL,
    NULL, 'manual', NULL, NULL, 'center', NULL, 1, 10, NOW(), NOW()
WHERE @honeymoon_page_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `page_sections`
      WHERE `page_id` = @honeymoon_page_id AND `section_key` = 'honeymoon_hero'
  );

INSERT INTO `page_sections`
    (`id`, `page_id`, `section_key`, `title`, `subtitle`, `excerpt`, `description`, `items`, `button_label`, `button_link_type`, `button_url`, `button_route`, `text_align`, `background_color`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    (@next_page_section_id := @next_page_section_id + 1), @honeymoon_page_id,
    'honeymoon_intro', 'Celebrate Your Honeymoon Surrounded by the Rainforest', 'A Honeymoon in Nature', NULL,
    '<p>Celebrate your honeymoon at Nandini Jungle by Hanging Gardens, a romantic jungle resort in Payangan, within the greater Ubud area of Bali. Set above the Ayung River valley, Nandini offers private jungle villas and Royal Suites, couples spa experiences, romantic dining and memorable moments designed for two.</p><p>Whether you are planning a Bali honeymoon, anniversary or romantic escape, the resort offers a peaceful setting where you can slow down, reconnect and experience Ubud together.</p>',
    NULL, 'Explore Our Resort', 'manual', '#why', NULL, 'left', NULL, 1, 20, NOW(), NOW()
WHERE @honeymoon_page_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `page_sections`
      WHERE `page_id` = @honeymoon_page_id AND `section_key` = 'honeymoon_intro'
  );

INSERT INTO `page_sections`
    (`id`, `page_id`, `section_key`, `title`, `subtitle`, `excerpt`, `description`, `items`, `button_label`, `button_link_type`, `button_url`, `button_route`, `text_align`, `background_color`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    (@next_page_section_id := @next_page_section_id + 1), @honeymoon_page_id,
    'honeymoon_features', 'Why Choose Nandini for Your Honeymoon in Ubud?', 'Why Choose Nandini', NULL,
    '<p>A honeymoon at Nandini is shaped by privacy, nature and time together. The resort sits along a tropical hillside overlooking the Ayung River valley, away from Bali''s busier coastal areas while remaining within the greater Ubud region. Couples can stay in private jungle accommodation, unwind with spa and wellness experiences, enjoy romantic dining surrounded by nature and discover cultural and riverside experiences together.</p>',
    '[{"icon":"home","title":"Rainforest Setting","description":"Overlooking the Ayung River valley."},{"icon":"diamond","title":"Private Villas & Suites","description":"Designed for privacy, comfort and special moments."},{"icon":"sparkles","title":"Couples Spa & Wellness","description":"Relaxing treatments in the jungle."},{"icon":"heart","title":"Romantic Dining","description":"Intimate dining experiences for two."},{"icon":"leaf","title":"Unique Experiences","description":"Holy River, culture and nature activities."},{"icon":"star","title":"Perfect for Occasions","description":"Honeymoons, anniversaries and proposals."}]',
    NULL, 'manual', NULL, NULL, 'center', NULL, 1, 30, NOW(), NOW()
WHERE @honeymoon_page_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `page_sections`
      WHERE `page_id` = @honeymoon_page_id AND `section_key` = 'honeymoon_features'
  );

INSERT INTO `page_sections`
    (`id`, `page_id`, `section_key`, `title`, `subtitle`, `excerpt`, `description`, `items`, `button_label`, `button_link_type`, `button_url`, `button_route`, `text_align`, `background_color`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    (@next_page_section_id := @next_page_section_id + 1), @honeymoon_page_id,
    'honeymoon_accommodations', 'Jungle Villas & Royal Suites for Your Honeymoon', 'Accommodation for Couples', NULL,
    '<p>From private jungle villas to spacious Royal Suites, each accommodation at Nandini is designed to offer privacy, comfort and a deeper connection with nature — perfect for a romantic stay in Ubud.</p>',
    '[{"title":"Panoramic Jungle View Villa","description":"The Panoramic Jungle View Villa is ideal for couples who want a deeper sense of privacy and connection with nature. Its elevated setting and wide jungle views create a peaceful atmosphere for honeymoon mornings, quiet afternoons and relaxed evenings together.","image":"accommodations/cards/panoramic-jungle-view-villa-private-balcony-ubud-bali.webp","image_alt":"Panoramic Jungle View Villa at Nandini Jungle in Ubud","url":"/jungle-villas/panoramic-jungle-view-villa","link_label":"View Details"},{"title":"Private Garden Royal Suite","description":"More space and privacy for a romantic escape.","image":"accommodations/cards/private-garden-royal-suite-living-area-garden-view-ubud-bali.webp","image_alt":"Private Garden Royal Suite at Nandini Jungle","url":"/the-royal-suites/private-garden-royal-suite","link_label":"View Details"},{"title":"Panoramic Corner Jacuzzi Royal Suite","description":"Ideal for honeymoons and special celebrations.","image":"accommodations/cards/panoramic-corner-jacuzzi-royal-suite-balcony-jacuzzi-ubud-bali-2.webp","image_alt":"Panoramic Corner Jacuzzi Royal Suite at Nandini Jungle","url":"/the-royal-suites/panoramic-corner-jacuzzi-royal-suite","link_label":"View Details"}]',
    'Explore Villas & Royal Suites', 'manual', '/the-royal-suites-and-jungle-villas', NULL, 'center', NULL, 1, 40, NOW(), NOW()
WHERE @honeymoon_page_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `page_sections`
      WHERE `page_id` = @honeymoon_page_id AND `section_key` = 'honeymoon_accommodations'
  );

INSERT INTO `page_sections`
    (`id`, `page_id`, `section_key`, `title`, `subtitle`, `excerpt`, `description`, `items`, `button_label`, `button_link_type`, `button_url`, `button_route`, `text_align`, `background_color`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    (@next_page_section_id := @next_page_section_id + 1), @honeymoon_page_id,
    'honeymoon_package', '4 Days / 3 Nights Honeymoon Package', 'Special Offer', NULL,
    '<p>Created for couples celebrating a honeymoon or romantic escape, our 4 Days / 3 Nights honeymoon experience brings together time to relax, reconnect and enjoy Nandini''s intimate jungle setting.</p>',
    '[{"label":"View Honeymoon Package","url":"/honeymoon/honeymoon-packages-4-days-3-nights","style":"solid"},{"label":"Reserve Your Honeymoon","url":"https://nandinijunglebyhanginggardens.reserve-online.net/?nights=3&bkcode=romance","style":"outline"}]',
    NULL, 'manual', NULL, NULL, 'center', 'soft_gray', 1, 50, NOW(), NOW()
WHERE @honeymoon_page_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `page_sections`
      WHERE `page_id` = @honeymoon_page_id AND `section_key` = 'honeymoon_package'
  );

INSERT INTO `page_sections`
    (`id`, `page_id`, `section_key`, `title`, `subtitle`, `excerpt`, `description`, `items`, `button_label`, `button_link_type`, `button_url`, `button_route`, `text_align`, `background_color`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    (@next_page_section_id := @next_page_section_id + 1), @honeymoon_page_id,
    'honeymoon_dining', 'Romantic Dining in the Jungle', 'Romantic Dining', NULL,
    '<p>Celebrate an evening together with romantic dining surrounded by Nandini''s tropical landscape. From intimate dinners to special settings created for honeymoons, anniversaries and proposals, dining can become one of the most memorable moments of your stay.</p>',
    '[{"label":"Explore Dining","url":"https://dining.nandinibali.com/","style":"solid"},{"label":"Romantic Experiences","url":"/experiences/jungle-romance","style":"outline"}]',
    NULL, 'manual', NULL, NULL, 'center', NULL, 1, 60, NOW(), NOW()
WHERE @honeymoon_page_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `page_sections`
      WHERE `page_id` = @honeymoon_page_id AND `section_key` = 'honeymoon_dining'
  );

INSERT INTO `page_sections`
    (`id`, `page_id`, `section_key`, `title`, `subtitle`, `excerpt`, `description`, `items`, `button_label`, `button_link_type`, `button_url`, `button_route`, `text_align`, `background_color`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    (@next_page_section_id := @next_page_section_id + 1), @honeymoon_page_id,
    'honeymoon_spa', 'Spa & Wellness for Two', 'Spa & Wellness', NULL,
    '<p>Slow down together with spa and wellness experiences inspired by Nandini''s rainforest setting. Couples can enjoy relaxing treatments, riverside wellness experiences and quiet time surrounded by nature as part of their honeymoon in Ubud.</p>',
    '[{"label":"Explore Spa & Wellness","url":"/spa-wellness","style":"solid"},{"label":"Jungle Spa Ubud","url":"/jungle-spa-ubud","style":"outline"},{"label":"Holy River","url":"/holy-river","style":"outline"}]',
    NULL, 'manual', NULL, NULL, 'center', 'soft_gray', 1, 70, NOW(), NOW()
WHERE @honeymoon_page_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `page_sections`
      WHERE `page_id` = @honeymoon_page_id AND `section_key` = 'honeymoon_spa'
  );

INSERT INTO `page_sections`
    (`id`, `page_id`, `section_key`, `title`, `subtitle`, `excerpt`, `description`, `items`, `button_label`, `button_link_type`, `button_url`, `button_route`, `text_align`, `background_color`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    (@next_page_section_id := @next_page_section_id + 1), @honeymoon_page_id,
    'honeymoon_itinerary', 'A Suggested 4-Day Honeymoon in Ubud', 'Example Itinerary', NULL,
    '<p>An example itinerary to inspire your stay. It can be adjusted around your interests, preferred pace and the experiences you would like to include.</p>',
    '[{"label":"Day 1","title":"Arrive & Slow Down","description":"Check in, settle into your villa or suite and enjoy a relaxed evening together.","image":"accommodations/cards/panoramic-jungle-view-villa-private-balcony-ubud-bali.webp","image_alt":"Jungle villa in Ubud for honeymoon couples"},{"label":"Day 2","title":"Spa & Romantic Dining","description":"Enjoy a couples wellness experience, followed by an intimate dinner.","image":"experience-categories/0f670856-8d39-49a1-97ff-96a88f13e2e2.webp","image_alt":"Spa and wellness experience at Nandini Jungle"},{"label":"Day 3","title":"Experience Bali Together","description":"Explore a Holy River experience, village activity or another curated experience.","image":"pages/sections/16cc904d-d6b3-4050-959d-82884d7d4268.webp","image_alt":"Riverside wellness experience at Nandini Jungle"},{"label":"Day 4","title":"A Slow Morning","description":"Enjoy breakfast and your final morning surrounded by the rainforest before departure.","image":"offers/cards/jungle-hideaway-dining-nandini-bali-2.webp","image_alt":"Romantic jungle dining at Nandini Jungle"}]',
    NULL, 'manual', NULL, NULL, 'center', NULL, 1, 80, NOW(), NOW()
WHERE @honeymoon_page_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `page_sections`
      WHERE `page_id` = @honeymoon_page_id AND `section_key` = 'honeymoon_itinerary'
  );

INSERT INTO `page_sections`
    (`id`, `page_id`, `section_key`, `title`, `subtitle`, `excerpt`, `description`, `items`, `button_label`, `button_link_type`, `button_url`, `button_route`, `text_align`, `background_color`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    (@next_page_section_id := @next_page_section_id + 1), @honeymoon_page_id,
    'honeymoon_celebrations', 'Proposals, Anniversaries & Romantic Celebrations', 'Special Celebrations', NULL,
    '<p>Nandini is not only for honeymoons. Couples can also celebrate proposals, anniversaries and other meaningful occasions with romantic dining, spa experiences and personalized moments in the jungle.</p>',
    NULL, 'Plan a Romantic Celebration', 'manual', 'https://dining.nandinibali.com/', NULL, 'center', NULL, 1, 90, NOW(), NOW()
WHERE @honeymoon_page_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `page_sections`
      WHERE `page_id` = @honeymoon_page_id AND `section_key` = 'honeymoon_celebrations'
  );

INSERT INTO `page_sections`
    (`id`, `page_id`, `section_key`, `title`, `subtitle`, `excerpt`, `description`, `items`, `button_label`, `button_link_type`, `button_url`, `button_route`, `text_align`, `background_color`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    (@next_page_section_id := @next_page_section_id + 1), @honeymoon_page_id,
    'honeymoon_faq', 'Honeymoon in Ubud — Frequently Asked Questions', 'Frequently Asked Questions', NULL, NULL,
    '[{"question":"Is Nandini Jungle by Hanging Gardens suitable for a honeymoon in Bali?","answer":"Yes. Nandini offers a secluded rainforest setting, private villas and Royal Suites, spa and wellness experiences, romantic dining and curated activities for couples."},{"question":"Where is Nandini located in relation to Ubud?","answer":"Nandini Jungle by Hanging Gardens is in Banjar Susut, Desa Buahan, Payangan, within the greater Ubud area of Bali."},{"question":"Which villa or suite is best for honeymoon couples?","answer":"Couples can consider the Panoramic Jungle View Villa, Private Garden Royal Suite or Panoramic Corner Jacuzzi Royal Suite depending on their preferred level of space, privacy and atmosphere."},{"question":"Does Nandini offer a honeymoon package?","answer":"Yes. The honeymoon page features a 4 Days / 3 Nights Honeymoon Package. Visit the package detail page for the latest inclusions and booking information."},{"question":"Can couples arrange romantic dining?","answer":"Yes. Nandini offers romantic and private dining experiences for couples, including experiences suited to honeymoons, anniversaries and proposals."},{"question":"Does Nandini offer couples spa experiences?","answer":"Nandini offers spa and wellness experiences in its rainforest setting, including experiences suitable for couples seeking time to relax together."},{"question":"Can Nandini help with proposals or anniversary celebrations?","answer":"Romantic and private dining experiences are available by arrangement. Contact the Nandini team to discuss the preferred occasion and date."},{"question":"What activities can honeymoon couples experience in Ubud?","answer":"Couples can explore Nandini''s curated experiences, including romantic dining, wellness, Holy River experiences and other nature and cultural activities."},{"question":"How many nights should couples stay for a honeymoon at Nandini?","answer":"The ideal stay depends on your plans. Nandini''s featured honeymoon package is designed around a 4 Days / 3 Nights stay."},{"question":"How can we reserve our honeymoon stay?","answer":"You can reserve directly through Nandini''s official booking engine or contact the reservations team for assistance."}]',
    NULL, 'manual', NULL, NULL, 'center', NULL, 1, 100, NOW(), NOW()
WHERE @honeymoon_page_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `page_sections`
      WHERE `page_id` = @honeymoon_page_id AND `section_key` = 'honeymoon_faq'
  );

INSERT INTO `page_sections`
    (`id`, `page_id`, `section_key`, `title`, `subtitle`, `excerpt`, `description`, `items`, `button_label`, `button_link_type`, `button_url`, `button_route`, `text_align`, `background_color`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    (@next_page_section_id := @next_page_section_id + 1), @honeymoon_page_id,
    'honeymoon_final_cta', 'Plan Your Honeymoon in the Ubud Jungle', 'Your Honeymoon Awaits', NULL,
    '<p>Celebrate your honeymoon surrounded by rainforest, river valley views and the quiet atmosphere of Nandini Jungle by Hanging Gardens. Explore our villas, Royal Suites and romantic experiences, or reserve your stay directly.</p>',
    '[{"label":"Explore Honeymoon Package","url":"/honeymoon/honeymoon-packages-4-days-3-nights","style":"solid"},{"label":"Reserve Your Stay","url":"https://nandinijunglebyhanginggardens.reserve-online.net/?nights=3&bkcode=romance","style":"white-outline"}]',
    NULL, 'manual', NULL, NULL, 'center', NULL, 1, 110, NOW(), NOW()
WHERE @honeymoon_page_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM `page_sections`
      WHERE `page_id` = @honeymoon_page_id AND `section_key` = 'honeymoon_final_cta'
  );

-- Apply the final metadata and CMS content to both new and existing records.
UPDATE `pages`
SET
    `meta_title` = 'Honeymoon Resort in Ubud, Bali | Nandini Jungle',
    `meta_description` = 'Celebrate your honeymoon at Nandini Jungle by Hanging Gardens, a romantic jungle resort in Ubud, Bali with private villas, spa, dining and couples experiences.',
    `updated_at` = NOW()
WHERE `id` = @honeymoon_page_id;

UPDATE `page_sections`
SET
    `title` = 'Honeymoon Resort in Ubud, Bali',
    `subtitle` = 'Honeymoon',
    `description` = '<p>A Romantic Jungle Honeymoon at Nandini Jungle by Hanging Gardens</p>',
    `is_active` = 1,
    `sort_order` = 10,
    `updated_at` = NOW()
WHERE `page_id` = @honeymoon_page_id AND `section_key` = 'honeymoon_hero';

UPDATE `page_sections`
SET
    `title` = 'Celebrate Your Honeymoon Surrounded by the Rainforest',
    `subtitle` = 'A Honeymoon in Nature',
    `description` = '<p>Celebrate your honeymoon at Nandini Jungle by Hanging Gardens, a romantic jungle resort in Payangan, within the greater Ubud area of Bali. Set above the Ayung River valley, Nandini offers private jungle villas and Royal Suites, couples spa experiences, romantic dining and memorable moments designed for two.</p><p>Whether you are planning a Bali honeymoon, anniversary or romantic escape, the resort offers a peaceful setting where you can slow down, reconnect and experience Ubud together.</p>',
    `button_label` = 'Explore Our Resort',
    `button_link_type` = 'manual',
    `button_url` = '#why',
    `button_route` = NULL,
    `is_active` = 1,
    `sort_order` = 20,
    `updated_at` = NOW()
WHERE `page_id` = @honeymoon_page_id AND `section_key` = 'honeymoon_intro';

UPDATE `page_sections`
SET
    `title` = 'Why Choose Nandini for Your Honeymoon in Ubud?',
    `subtitle` = 'Why Choose Nandini',
    `description` = '<p>A honeymoon at Nandini is shaped by privacy, nature and time together. The resort sits along a tropical hillside overlooking the Ayung River valley, away from Bali''s busier coastal areas while remaining within the greater Ubud region. Couples can stay in private jungle accommodation, unwind with spa and wellness experiences, enjoy romantic dining surrounded by nature and discover cultural and riverside experiences together.</p>',
    `items` = '[{"icon":"home","title":"Rainforest Setting","description":"Overlooking the Ayung River valley."},{"icon":"diamond","title":"Private Villas & Suites","description":"Designed for privacy, comfort and special moments."},{"icon":"sparkles","title":"Couples Spa & Wellness","description":"Relaxing treatments in the jungle."},{"icon":"heart","title":"Romantic Dining","description":"Intimate dining experiences for two."},{"icon":"leaf","title":"Unique Experiences","description":"Holy River, culture and nature activities."},{"icon":"star","title":"Perfect for Occasions","description":"Honeymoons, anniversaries and proposals."}]',
    `is_active` = 1,
    `sort_order` = 30,
    `updated_at` = NOW()
WHERE `page_id` = @honeymoon_page_id AND `section_key` = 'honeymoon_features';

UPDATE `page_sections`
SET
    `title` = 'Jungle Villas & Royal Suites for Your Honeymoon',
    `subtitle` = 'Accommodation for Couples',
    `description` = '<p>From private jungle villas to spacious Royal Suites, each accommodation at Nandini is designed to offer privacy, comfort and a deeper connection with nature — perfect for a romantic stay in Ubud.</p>',
    `items` = '[{"title":"Panoramic Jungle View Villa","description":"The Panoramic Jungle View Villa is ideal for couples who want a deeper sense of privacy and connection with nature. Its elevated setting and wide jungle views create a peaceful atmosphere for honeymoon mornings, quiet afternoons and relaxed evenings together.","image":"accommodations/cards/panoramic-jungle-view-villa-private-balcony-ubud-bali.webp","image_alt":"Panoramic Jungle View Villa at Nandini Jungle in Ubud","url":"/jungle-villas/panoramic-jungle-view-villa","link_label":"View Details"},{"title":"Private Garden Royal Suite","description":"More space and privacy for a romantic escape.","image":"accommodations/cards/private-garden-royal-suite-living-area-garden-view-ubud-bali.webp","image_alt":"Private Garden Royal Suite at Nandini Jungle","url":"/the-royal-suites/private-garden-royal-suite","link_label":"View Details"},{"title":"Panoramic Corner Jacuzzi Royal Suite","description":"Ideal for honeymoons and special celebrations.","image":"accommodations/cards/panoramic-corner-jacuzzi-royal-suite-balcony-jacuzzi-ubud-bali-2.webp","image_alt":"Panoramic Corner Jacuzzi Royal Suite at Nandini Jungle","url":"/the-royal-suites/panoramic-corner-jacuzzi-royal-suite","link_label":"View Details"}]',
    `button_label` = 'Explore Villas & Royal Suites',
    `button_link_type` = 'manual',
    `button_url` = '/the-royal-suites-and-jungle-villas',
    `button_route` = NULL,
    `is_active` = 1,
    `sort_order` = 40,
    `updated_at` = NOW()
WHERE `page_id` = @honeymoon_page_id AND `section_key` = 'honeymoon_accommodations';

UPDATE `page_sections`
SET
    `items` = '[{"label":"View Honeymoon Package","url":"/honeymoon/honeymoon-packages-4-days-3-nights","style":"solid"},{"label":"Reserve Your Honeymoon","url":"https://nandinijunglebyhanginggardens.reserve-online.net/?nights=3&bkcode=romance","style":"outline"}]',
    `is_active` = 1,
    `sort_order` = 50,
    `updated_at` = NOW()
WHERE `page_id` = @honeymoon_page_id AND `section_key` = 'honeymoon_package';

UPDATE `page_sections`
SET
    `items` = '[{"label":"Explore Dining","url":"https://dining.nandinibali.com/","style":"solid"},{"label":"Romantic Experiences","url":"/experiences/jungle-romance","style":"outline"}]',
    `is_active` = 1,
    `sort_order` = 60,
    `updated_at` = NOW()
WHERE `page_id` = @honeymoon_page_id AND `section_key` = 'honeymoon_dining';

UPDATE `page_sections`
SET
    `items` = '[{"label":"Explore Spa & Wellness","url":"/spa-wellness","style":"solid"},{"label":"Jungle Spa Ubud","url":"/jungle-spa-ubud","style":"outline"},{"label":"Holy River","url":"/holy-river","style":"outline"}]',
    `is_active` = 1,
    `sort_order` = 70,
    `updated_at` = NOW()
WHERE `page_id` = @honeymoon_page_id AND `section_key` = 'honeymoon_spa';

UPDATE `page_sections`
SET
    `is_active` = 1,
    `sort_order` = 80,
    `updated_at` = NOW()
WHERE `page_id` = @honeymoon_page_id AND `section_key` = 'honeymoon_itinerary';

UPDATE `page_sections`
SET
    `button_label` = 'Plan a Romantic Celebration',
    `button_link_type` = 'manual',
    `button_url` = 'https://dining.nandinibali.com/',
    `button_route` = NULL,
    `is_active` = 1,
    `sort_order` = 90,
    `updated_at` = NOW()
WHERE `page_id` = @honeymoon_page_id AND `section_key` = 'honeymoon_celebrations';

UPDATE `page_sections`
SET
    `is_active` = 1,
    `sort_order` = 100,
    `updated_at` = NOW()
WHERE `page_id` = @honeymoon_page_id AND `section_key` = 'honeymoon_faq';

UPDATE `page_sections`
SET
    `items` = '[{"label":"Explore Honeymoon Package","url":"/honeymoon/honeymoon-packages-4-days-3-nights","style":"solid"},{"label":"Reserve Your Stay","url":"https://nandinijunglebyhanginggardens.reserve-online.net/?nights=3&bkcode=romance","style":"white-outline"}]',
    `is_active` = 1,
    `sort_order` = 110,
    `updated_at` = NOW()
WHERE `page_id` = @honeymoon_page_id AND `section_key` = 'honeymoon_final_cta';

-- Create the seven editable section-image records when missing.
INSERT INTO `page_section_images`
    (`id`, `page_section_id`, `image`, `image_alt`, `mobile_image`, `mobile_image_alt`, `caption`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    (@next_page_section_image_id := @next_page_section_image_id + 1), `s`.`id`,
    'pages/hero/1c101d0a-da5a-4f26-9ab6-745ce9820373.webp',
    'Honeymoon at Nandini Jungle by Hanging Gardens in Ubud, Bali',
    NULL, NULL, NULL, 1, 0, NOW(), NOW()
FROM `page_sections` AS `s`
WHERE `s`.`page_id` = @honeymoon_page_id
  AND `s`.`section_key` = 'honeymoon_hero'
  AND NOT EXISTS (
      SELECT 1 FROM `page_section_images` AS `i`
      WHERE `i`.`page_section_id` = `s`.`id` AND `i`.`is_active` = 1
  );

INSERT INTO `page_section_images`
    (`id`, `page_section_id`, `image`, `image_alt`, `mobile_image`, `mobile_image_alt`, `caption`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    (@next_page_section_image_id := @next_page_section_image_id + 1), `s`.`id`,
    'pages/sections/422be0cf-6a86-4d31-b124-a13e1c02880a.webp',
    'Honeymoon experience at Nandini Jungle by Hanging Gardens in Ubud, Bali',
    NULL, NULL, NULL, 1, 0, NOW(), NOW()
FROM `page_sections` AS `s`
WHERE `s`.`page_id` = @honeymoon_page_id
  AND `s`.`section_key` = 'honeymoon_intro'
  AND NOT EXISTS (
      SELECT 1 FROM `page_section_images` AS `i`
      WHERE `i`.`page_section_id` = `s`.`id` AND `i`.`is_active` = 1
  );

INSERT INTO `page_section_images`
    (`id`, `page_section_id`, `image`, `image_alt`, `mobile_image`, `mobile_image_alt`, `caption`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    (@next_page_section_image_id := @next_page_section_image_id + 1), `s`.`id`,
    'pages/sections/422be0cf-6a86-4d31-b124-a13e1c02880a.webp',
    '4 Days 3 Nights honeymoon package at Nandini Jungle by Hanging Gardens',
    NULL, NULL, NULL, 1, 0, NOW(), NOW()
FROM `page_sections` AS `s`
WHERE `s`.`page_id` = @honeymoon_page_id
  AND `s`.`section_key` = 'honeymoon_package'
  AND NOT EXISTS (
      SELECT 1 FROM `page_section_images` AS `i`
      WHERE `i`.`page_section_id` = `s`.`id` AND `i`.`is_active` = 1
  );

INSERT INTO `page_section_images`
    (`id`, `page_section_id`, `image`, `image_alt`, `mobile_image`, `mobile_image_alt`, `caption`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    (@next_page_section_image_id := @next_page_section_image_id + 1), `s`.`id`,
    'experience-categories/c5e0deb3-cd14-4488-ba06-31efb046d0fd.webp',
    'Romantic jungle dining experience for couples at Nandini Jungle by Hanging Gardens',
    NULL, NULL, NULL, 1, 0, NOW(), NOW()
FROM `page_sections` AS `s`
WHERE `s`.`page_id` = @honeymoon_page_id
  AND `s`.`section_key` = 'honeymoon_dining'
  AND NOT EXISTS (
      SELECT 1 FROM `page_section_images` AS `i`
      WHERE `i`.`page_section_id` = `s`.`id` AND `i`.`is_active` = 1
  );

INSERT INTO `page_section_images`
    (`id`, `page_section_id`, `image`, `image_alt`, `mobile_image`, `mobile_image_alt`, `caption`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    (@next_page_section_image_id := @next_page_section_image_id + 1), `s`.`id`,
    'experience-categories/0f670856-8d39-49a1-97ff-96a88f13e2e2.webp',
    'Couples spa and wellness experience at Nandini Jungle by Hanging Gardens',
    NULL, NULL, NULL, 1, 0, NOW(), NOW()
FROM `page_sections` AS `s`
WHERE `s`.`page_id` = @honeymoon_page_id
  AND `s`.`section_key` = 'honeymoon_spa'
  AND NOT EXISTS (
      SELECT 1 FROM `page_section_images` AS `i`
      WHERE `i`.`page_section_id` = `s`.`id` AND `i`.`is_active` = 1
  );

INSERT INTO `page_section_images`
    (`id`, `page_section_id`, `image`, `image_alt`, `mobile_image`, `mobile_image_alt`, `caption`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    (@next_page_section_image_id := @next_page_section_image_id + 1), `s`.`id`,
    'offers/cards/jungle-hideaway-dining-nandini-bali-2.webp',
    'Romantic proposal and anniversary celebration at Nandini Jungle by Hanging Gardens',
    NULL, NULL, NULL, 1, 0, NOW(), NOW()
FROM `page_sections` AS `s`
WHERE `s`.`page_id` = @honeymoon_page_id
  AND `s`.`section_key` = 'honeymoon_celebrations'
  AND NOT EXISTS (
      SELECT 1 FROM `page_section_images` AS `i`
      WHERE `i`.`page_section_id` = `s`.`id` AND `i`.`is_active` = 1
  );

INSERT INTO `page_section_images`
    (`id`, `page_section_id`, `image`, `image_alt`, `mobile_image`, `mobile_image_alt`, `caption`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    (@next_page_section_image_id := @next_page_section_image_id + 1), `s`.`id`,
    'pages/hero/1c101d0a-da5a-4f26-9ab6-745ce9820373.webp',
    'Romantic honeymoon escape at Nandini Jungle by Hanging Gardens in Ubud',
    NULL, NULL, NULL, 1, 0, NOW(), NOW()
FROM `page_sections` AS `s`
WHERE `s`.`page_id` = @honeymoon_page_id
  AND `s`.`section_key` = 'honeymoon_final_cta'
  AND NOT EXISTS (
      SELECT 1 FROM `page_section_images` AS `i`
      WHERE `i`.`page_section_id` = `s`.`id` AND `i`.`is_active` = 1
  );

-- Apply the required alt text even when image records already existed.
UPDATE `page_section_images` AS `i`
INNER JOIN `page_sections` AS `s` ON `s`.`id` = `i`.`page_section_id`
SET `i`.`image_alt` = CASE `s`.`section_key`
    WHEN 'honeymoon_intro' THEN 'Honeymoon experience at Nandini Jungle by Hanging Gardens in Ubud, Bali'
    WHEN 'honeymoon_package' THEN '4 Days 3 Nights honeymoon package at Nandini Jungle by Hanging Gardens'
    WHEN 'honeymoon_dining' THEN 'Romantic jungle dining experience for couples at Nandini Jungle by Hanging Gardens'
    WHEN 'honeymoon_spa' THEN 'Couples spa and wellness experience at Nandini Jungle by Hanging Gardens'
    WHEN 'honeymoon_celebrations' THEN 'Romantic proposal and anniversary celebration at Nandini Jungle by Hanging Gardens'
    WHEN 'honeymoon_final_cta' THEN 'Romantic honeymoon escape at Nandini Jungle by Hanging Gardens in Ubud'
    ELSE `i`.`image_alt`
END,
`i`.`updated_at` = NOW()
WHERE `s`.`page_id` = @honeymoon_page_id
  AND `s`.`section_key` IN (
      'honeymoon_intro',
      'honeymoon_package',
      'honeymoon_dining',
      'honeymoon_spa',
      'honeymoon_celebrations',
      'honeymoon_final_cta'
  )
  AND `i`.`is_active` = 1;

-- Remove the obsolete fixed date from the package record as well.
UPDATE `honeymoons`
SET
    `booking_url_override` = 'https://nandinijunglebyhanginggardens.reserve-online.net/?nights=3&bkcode=romance',
    `updated_at` = NOW()
WHERE `slug` = 'honeymoon-packages-4-days-3-nights';

COMMIT;

-- Verification. Expected: one page, eleven active sections, seven section images,
-- and no booking URL containing checkin=2026-05-27.
SELECT
    `id`,
    `page_name`,
    `slug`,
    `meta_title`,
    `meta_description`,
    `updated_at`
FROM `pages`
WHERE `id` = @honeymoon_page_id;

SELECT
    `id`,
    `section_key`,
    `title`,
    `button_label`,
    `button_url`,
    `is_active`,
    `sort_order`
FROM `page_sections`
WHERE `page_id` = @honeymoon_page_id
  AND `section_key` LIKE 'honeymoon_%'
ORDER BY `sort_order`, `id`;

SELECT
    `s`.`section_key`,
    `i`.`image`,
    `i`.`image_alt`
FROM `page_section_images` AS `i`
INNER JOIN `page_sections` AS `s` ON `s`.`id` = `i`.`page_section_id`
WHERE `s`.`page_id` = @honeymoon_page_id
  AND `s`.`section_key` LIKE 'honeymoon_%'
  AND `i`.`is_active` = 1
ORDER BY `s`.`sort_order`, `i`.`sort_order`, `i`.`id`;

SELECT
    `slug`,
    `booking_url_override`
FROM `honeymoons`
WHERE `slug` = 'honeymoon-packages-4-days-3-nights';
