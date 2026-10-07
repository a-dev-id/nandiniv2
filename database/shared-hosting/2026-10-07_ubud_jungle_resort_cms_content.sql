-- Nandini Jungle by Hanging Gardens
-- Update the existing /ubud-jungle-resort-in-bali CMS page.
--
-- Scope:
--   pages.id = 31
--   page_sections.id IN (63, 64, 65, 66, 67, 68)
--
-- This script intentionally does not create a page, change the slug, replace
-- images, or alter section component types. Take a database backup before use.

USE `nandini_membership`;

SET NAMES utf8mb4;

START TRANSACTION;

UPDATE `pages`
SET
    `title` = 'Luxury Jungle Resort in Ubud, Bali',
    `subtitle` = 'Nandini Jungle by Hanging Gardens',
    `excerpt` = 'A secluded luxury jungle resort in Payangan, Ubud, surrounded by tropical rainforest and the Ayung River valley, with private jungle villas, Royal Suites, spa, dining and nature-led experiences.',
    `description` = '<p style="text-align: start;">Nandini Jungle by Hanging Gardens is a luxury jungle resort in Banjar Susut, Desa Buahan, Payangan, within the greater Ubud area of Bali. Built into a tropical hillside overlooking the Ayung River valley, the resort combines private jungle villas and Royal Suites with spa, dining, wellness and nature-led experiences.</p><p style="text-align: start;">Designed for couples, honeymooners, wellness travelers, families and guests seeking a quieter side of Bali, Nandini offers an immersive rainforest stay shaped by Balinese hospitality and the natural landscape.</p>',
    `meta_title` = 'Luxury Jungle Resort in Ubud, Bali | Nandini Jungle',
    `meta_description` = 'Stay at Nandini Jungle by Hanging Gardens, a luxury jungle resort in Ubud, Bali with private villas, Royal Suites, spa, dining and Ayung River views.',
    `is_active` = 1,
    `include_in_sitemap` = 1,
    `updated_at` = NOW()
WHERE `id` = 31
  AND `slug` = 'ubud-jungle-resort-in-bali';

UPDATE `page_sections`
SET
    `title` = 'What Makes Nandini a Unique Jungle Resort in Ubud',
    `subtitle` = NULL,
    `excerpt` = '<p></p>',
    `description` = '<p style="text-align: start;">Nandini Jungle by Hanging Gardens is defined by its hillside rainforest setting in Payangan, within the greater Ubud area. Rather than separating the resort from nature, villas, pathways and guest spaces follow the contours of the jungle landscape.</p><h3 style="text-align: start;"><strong>Rainforest and Ayung River Valley Setting</strong></h3><p style="text-align: start;">The resort is surrounded by tropical greenery with views across the river valley, creating a quiet setting away from Bali’s busier coastal areas.</p><h3 style="text-align: start;"><strong>Jungle Villas and Royal Suites</strong></h3><p style="text-align: start;">Guests can choose from private jungle villas and spacious Royal Suites, with accommodation designed around privacy, natural views and Balinese-inspired details.</p><h3 style="text-align: start;"><strong>Wellness and Riverside Experiences</strong></h3><p style="text-align: start;">Spa rituals, yoga, Holy River experiences and other nature-led activities allow guests to experience the jungle beyond their accommodation.</p>',
    `button_label` = 'Explore Jungle View Villa',
    `button_link_type` = 'manual',
    `button_url` = '/jungle-villas/jungle-view-villa',
    `button_route` = NULL,
    `is_active` = 1,
    `sort_order` = 10,
    `updated_at` = NOW()
WHERE `id` = 63
  AND `page_id` = 31;

UPDATE `page_sections`
SET
    `title` = 'Private Jungle Villas & Royal Suites',
    `subtitle` = NULL,
    `excerpt` = '<p></p>',
    `description` = '<p style="text-align: start;">Accommodation at Nandini is designed to keep guests close to the surrounding rainforest while providing privacy and refined comfort.</p><p style="text-align: start;">Choose from the <a href="/jungle-villas/jungle-view-villa"><strong>Jungle View Villa</strong></a>, <a href="/jungle-villas/sunrise-view-villa"><strong>Sunrise View Villa</strong></a> or <a href="/jungle-villas/panoramic-jungle-view-villa"><strong>Panoramic Jungle View Villa</strong></a>, each offering its own perspective of the tropical landscape.</p><p style="text-align: start;">Guests looking for more space can also explore Nandini’s <a href="/the-royal-suites"><strong>Royal Suites</strong></a>, which combine generous living areas with tranquil jungle or garden surroundings.</p><p style="text-align: start;">Whether you are planning a romantic escape, honeymoon, family stay or nature-focused retreat, the accommodation is designed as a calm base for discovering Ubud and the resort’s experiences.</p>',
    `button_label` = 'Explore Jungle Villas',
    `button_link_type` = 'manual',
    `button_url` = '/jungle-villas',
    `button_route` = NULL,
    `is_active` = 1,
    `sort_order` = 20,
    `updated_at` = NOW()
WHERE `id` = 64
  AND `page_id` = 31;

UPDATE `page_sections`
SET
    `title` = 'Spa, Wellness & Riverside Experiences',
    `subtitle` = NULL,
    `excerpt` = '<p></p>',
    `description` = '<p style="text-align: start;">Wellness at Nandini is closely connected to the resort’s natural setting. Guests can combine their stay with spa treatments, yoga and riverside experiences inspired by Balinese traditions.</p><ul><li><p style="text-align: start;"><a href="/spa-wellness"><strong>Spa &amp; Wellness</strong></a> — discover treatments and wellness experiences available at the resort.</p></li><li><p style="text-align: start;"><a href="/jungle-spa-ubud"><strong>Jungle Spa in Ubud</strong></a> — explore spa experiences shaped by the tropical surroundings.</p></li><li><p style="text-align: start;"><a href="/holy-river"><strong>Holy River Experiences</strong></a> — reconnect with nature through peaceful riverside and Balinese-inspired rituals.</p></li></ul><p style="text-align: start;">These experiences give guests different ways to slow down, restore balance and experience the relationship between wellness, nature and Balinese culture during their stay.</p>',
    `button_label` = 'Explore Spa & Wellness',
    `button_link_type` = 'manual',
    `button_url` = '/spa-wellness',
    `button_route` = NULL,
    `is_active` = 1,
    `sort_order` = 30,
    `updated_at` = NOW()
WHERE `id` = 65
  AND `page_id` = 31;

UPDATE `page_sections`
SET
    `title` = NULL,
    `subtitle` = NULL,
    `excerpt` = NULL,
    `description` = '<h2 style="text-align: start;">Nandini Jungle Resort at a Glance</h2><ul><li><p style="text-align: start;"><strong>Location:</strong> Banjar Susut, Desa Buahan, Payangan, within the greater Ubud area of Bali.</p></li><li><p style="text-align: start;"><strong>Setting:</strong> Tropical hillside rainforest overlooking the Ayung River valley.</p></li><li><p style="text-align: start;"><strong>Accommodation:</strong> Jungle View Villa, Sunrise View Villa, Panoramic Jungle View Villa and Royal Suites.</p></li><li><p style="text-align: start;"><strong>Wellness:</strong> Spa treatments, jungle spa experiences, yoga and riverside wellness rituals.</p></li><li><p style="text-align: start;"><strong>Dining:</strong> Resort dining and curated culinary experiences surrounded by the jungle setting.</p></li><li><p style="text-align: start;"><strong>Best suited for:</strong> Couples, honeymooners, wellness travelers, families and nature-focused escapes.</p></li></ul><p style="text-align: start;">Nandini combines accommodation, wellness, dining and experiences in one rainforest setting, making it a strong choice for travelers looking for a luxury jungle resort near Ubud.</p>',
    `button_label` = NULL,
    `button_url` = NULL,
    `button_route` = NULL,
    `is_active` = 1,
    `sort_order` = 40,
    `updated_at` = NOW()
WHERE `id` = 66
  AND `page_id` = 31;

UPDATE `page_sections`
SET
    `title` = NULL,
    `subtitle` = NULL,
    `excerpt` = NULL,
    `description` = '<h2 style="text-align: start;">Plan Your Jungle Stay in Ubud</h2><p style="text-align: start;">Build your stay around the experiences that matter most to you. Explore our <a href="/jungle-villas"><strong>Jungle Villas</strong></a> and <a href="/the-royal-suites"><strong>Royal Suites</strong></a>, discover <a href="/spa-wellness"><strong>Spa &amp; Wellness</strong></a>, browse <a href="/experiences"><strong>Nandini Experiences</strong></a>, or explore dining at <a href="https://dining.nandinibali.com/"><strong>Nandini Dining</strong></a>.</p><p style="text-align: start;">For couples planning a romantic escape, our <a href="/honeymoon"><strong>Honeymoon</strong></a> page brings together accommodation and experiences created for time together in the jungle.</p><p style="text-align: start;">Ready to plan your stay? <a href="https://nandinijunglebyhanginggardens.reserve-online.net/"><strong>Check availability and book directly with Nandini Jungle by Hanging Gardens.</strong></a></p>',
    `button_label` = NULL,
    `button_url` = NULL,
    `button_route` = NULL,
    `is_active` = 1,
    `sort_order` = 50,
    `updated_at` = NOW()
WHERE `id` = 67
  AND `page_id` = 31;

UPDATE `page_sections`
SET
    `title` = NULL,
    `subtitle` = NULL,
    `excerpt` = NULL,
    `description` = '<h2 style="text-align: start;"><strong>Frequently Asked Questions</strong></h2><h3 style="text-align: start;"><strong>Where is Nandini Jungle by Hanging Gardens located?</strong></h3><p style="text-align: start;">Nandini Jungle by Hanging Gardens is located in Banjar Susut, Desa Buahan, Payangan, Bali, within the greater Ubud area. The resort is set on a tropical hillside overlooking the Ayung River valley.</p><h3 style="text-align: start;"><strong>Is Nandini Jungle by Hanging Gardens in Ubud?</strong></h3><p style="text-align: start;">Nandini is located in Payangan within the greater Ubud area, offering a secluded rainforest setting while remaining connected to Ubud’s cultural and natural attractions.</p><h3 style="text-align: start;"><strong>What accommodation is available at Nandini?</strong></h3><p style="text-align: start;">Guests can choose from Jungle View Villa, Sunrise View Villa and Panoramic Jungle View Villa, as well as a collection of spacious Royal Suites.</p><h3 style="text-align: start;"><strong>Does Nandini have a spa and wellness experiences?</strong></h3><p style="text-align: start;">Yes. Guests can discover spa treatments, jungle spa experiences, yoga and selected riverside wellness rituals inspired by Bali’s natural setting and traditions.</p><h3 style="text-align: start;"><strong>Is Nandini suitable for a honeymoon or romantic escape?</strong></h3><p style="text-align: start;">Yes. The secluded jungle setting, private accommodation, spa experiences and romantic dining options make Nandini a natural choice for honeymoons, anniversaries and couples’ escapes.</p><h3 style="text-align: start;"><strong>What can guests do during a stay?</strong></h3><p style="text-align: start;">Guests can combine their stay with wellness, dining, cultural and nature-led experiences, including selected activities connected to the jungle and Ayung River setting.</p><h3 style="text-align: start;"><strong>How can I book a stay at Nandini?</strong></h3><p style="text-align: start;">You can check current availability and book directly through the official Nandini booking engine, or contact the resort for help planning your stay.</p>',
    `button_label` = NULL,
    `button_url` = NULL,
    `button_route` = NULL,
    `is_active` = 1,
    `sort_order` = 60,
    `updated_at` = NOW()
WHERE `id` = 68
  AND `page_id` = 31;

COMMIT;

-- Verification: these queries should return one page and six active sections.
SELECT
    `id`,
    `title`,
    `subtitle`,
    `slug`,
    `meta_title`,
    `meta_description`,
    `is_active`,
    `updated_at`
FROM `pages`
WHERE `id` = 31
  AND `slug` = 'ubud-jungle-resort-in-bali';

SELECT
    `id`,
    `page_id`,
    `section_key`,
    `title`,
    `button_label`,
    `button_url`,
    `is_active`,
    `sort_order`,
    `updated_at`
FROM `page_sections`
WHERE `page_id` = 31
  AND `id` IN (63, 64, 65, 66, 67, 68)
ORDER BY `sort_order`, `id`;
