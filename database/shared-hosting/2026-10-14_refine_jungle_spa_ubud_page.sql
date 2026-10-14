-- Refine the existing /jungle-spa-ubud page below its unchanged hero.
-- This script is rerunnable and does not modify hero or metadata fields.

SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;
START TRANSACTION;

SET @page_id := (
    SELECT `id`
    FROM `pages`
    WHERE `site` = 'main' AND `slug` = 'jungle-spa-ubud'
    LIMIT 1
);

UPDATE `pages`
SET
    `excerpt` = 'Discover a refined jungle spa in Ubud, where Balinese treatments and riverside rituals unfold within Nandini’s rainforest setting.',
    `description` = '<p>At Nandini Jungle by Hanging Gardens, wellness unfolds between Bali’s rainforest canopy and the Ayung River landscape. Our jungle spa in Ubud offers a quiet setting for Balinese treatments, restorative rituals and time to slow down.</p><p>Choose an intimate treatment at Essence Spa or journey deeper into the landscape for a riverside spa experience in Ubud, each shaped by attentive care and the natural rhythm of the surroundings.</p>',
    `updated_at` = NOW()
WHERE `id` = @page_id;

UPDATE `page_sections`
SET `is_active` = 0, `updated_at` = NOW()
WHERE `page_id` = @page_id
  AND `title` IN (
      'A Spa Experience Shaped by the Jungle',
      'From Jungle Canopy to Riverside Calm',
      'Make Wellness Part of Your Ubud Escape',
      'Why Experience a Jungle Spa at Nandini?',
      'Frequently Asked Questions'
  );

UPDATE `page_sections`
SET
    `title` = 'Discover Our Spa Experiences',
    `description` = '<p>Discover four distinctive ways to experience wellness at Nandini, from quiet treatments among the trees to a signature ritual beside the Ayung River.</p>',
    `text_align` = 'center',
    `background_color` = 'soft_gray',
    `is_active` = 1,
    `sort_order` = 1,
    `updated_at` = NOW()
WHERE `page_id` = @page_id
  AND `title` IN ('Discover Our Jungle Spa Experiences', 'Discover Our Spa Experiences');

UPDATE `page_sections`
SET
    `description` = '<p>Essence Spa brings Balinese wellness traditions into an intimate setting surrounded by tropical greenery. Skilled therapies, aromatic oils and thoughtfully chosen ingredients create a restorative pause within your stay.</p><p>For guests seeking a jungle spa in Bali, it is a tranquil place to release tension and return to the day feeling renewed.</p>',
    `button_label` = 'Explore Essence Spa',
    `button_link_type` = 'manual',
    `button_url` = 'https://spa.nandinibali.com/',
    `background_color` = 'white',
    `is_active` = 1,
    `sort_order` = 2,
    `updated_at` = NOW()
WHERE `page_id` = @page_id AND `title` = 'Essence Spa';

UPDATE `page_sections`
SET
    `title` = 'Nandini Signature Spa on the River',
    `description` = '<p>Set beside the Ayung River, Nandini’s signature 180-minute ritual begins with a soothing foot bath before an Exotic Balinese Massage, body mask, body scrub and relaxing facial.</p><p>Flowing water and the forested riverbank shape an immersive riverside spa in Ubud, created for guests who want to linger in the landscape.</p>',
    `button_label` = 'View Spa on the River',
    `button_link_type` = 'manual',
    `button_url` = '/holy-river/nandini-signature-spa-on-the-river',
    `background_color` = 'soft_gray',
    `is_active` = 1,
    `sort_order` = 3,
    `updated_at` = NOW()
WHERE `page_id` = @page_id
  AND `title` IN ('Signature Spa on the River', 'Nandini Signature Spa on the River');

UPDATE `page_sections`
SET
    `description` = '<p>This 2.5-hour ritual pairs a Traditional Balinese Massage with a wine body scrub, body mask and wine bath. Antioxidant-rich ingredients and an unhurried pace make it an indulgent spa experience in Ubud.</p>',
    `button_label` = 'View Wine Spa',
    `button_link_type` = 'manual',
    `button_url` = 'https://voucher.nandinibali.com/voucher/luxury-spa-package-wine-spa',
    `background_color` = 'white',
    `is_active` = 1,
    `sort_order` = 4,
    `updated_at` = NOW()
WHERE `page_id` = @page_id AND `title` = 'Wine Spa';

UPDATE `page_sections`
SET
    `description` = '<p>Pause in the Spa Jacuzzi while tropical greenery frames the view. The warm water and quiet setting offer a gentle interlude before or after a treatment.</p>',
    `button_label` = 'Book Spa Jacuzzi',
    `button_link_type` = 'manual',
    `button_url` = 'https://wa.me/6281236871170?text=Hello%2C%20I%20would%20like%20to%20make%20an%20inquiry%20about%20the%20Spa%20Jacuzzi%20at%20Nandini%20Jungle.',
    `background_color` = 'soft_gray',
    `is_active` = 1,
    `sort_order` = 5,
    `updated_at` = NOW()
WHERE `page_id` = @page_id AND `title` = 'Spa Jacuzzi';

UPDATE `page_sections`
SET
    `title` = 'A Romantic Spa Escape',
    `description` = '<p>Share unhurried time together with a couples’ spa ritual shaped by the stillness of the rainforest and river.</p><p>Pair your treatment with a <a href="/honeymoon"><strong>honeymoon experience</strong></a>, or discover a longer <a href="/ubud-wellness-retreat"><strong>Ubud wellness retreat</strong></a> centred on rest, nature and reconnection.</p>',
    `button_label` = 'Explore Honeymoon Experiences',
    `button_link_type` = 'manual',
    `button_url` = '/honeymoon',
    `background_color` = 'white',
    `is_active` = 1,
    `sort_order` = 6,
    `updated_at` = NOW()
WHERE `page_id` = @page_id
  AND `title` IN ('A Jungle Spa Experience for Couples', 'A Romantic Spa Escape');

UPDATE `page_sections`
SET
    `title` = 'Plan Your Spa Experience',
    `description` = '<p>Explore the <a href="https://spa.nandinibali.com/"><strong>Essence Spa menu</strong></a>, then contact our team to reserve the treatment and time that suit your stay.</p>',
    `button_label` = 'Book a Spa Experience',
    `button_link_type` = 'manual',
    `button_url` = 'https://wa.me/6281236871170?text=Hello%2C%20I%20would%20like%20to%20make%20an%20inquiry%20about%20a%20spa%20experience%20at%20Nandini%20Jungle.',
    `background_color` = 'soft_gray',
    `is_active` = 1,
    `sort_order` = 7,
    `updated_at` = NOW()
WHERE `page_id` = @page_id
  AND `title` IN ('Find Your Moment of Calm in the Bali Jungle', 'Plan Your Spa Experience');

UPDATE `page_section_images` AS `images`
INNER JOIN `page_sections` AS `sections` ON `sections`.`id` = `images`.`page_section_id`
SET
    `images`.`image_alt` = CASE `sections`.`title`
        WHEN 'Essence Spa' THEN 'Balinese spa treatment at Essence Spa in Ubud, Bali'
        WHEN 'Nandini Signature Spa on the River' THEN 'Riverside spa treatment beside the Ayung River at Nandini Jungle'
        WHEN 'Wine Spa' THEN 'Wine Spa treatment in the jungle at Nandini Jungle, Bali'
        WHEN 'Spa Jacuzzi' THEN 'Spa Jacuzzi overlooking tropical greenery at Nandini Jungle'
        WHEN 'A Romantic Spa Escape' THEN 'Couples spa experience at Nandini Jungle in Ubud, Bali'
        ELSE `images`.`image_alt`
    END,
    `images`.`mobile_image_alt` = CASE `sections`.`title`
        WHEN 'Essence Spa' THEN 'Balinese spa treatment at Essence Spa in Ubud, Bali'
        WHEN 'Nandini Signature Spa on the River' THEN 'Riverside spa treatment beside the Ayung River at Nandini Jungle'
        WHEN 'Wine Spa' THEN 'Wine Spa treatment in the jungle at Nandini Jungle, Bali'
        WHEN 'Spa Jacuzzi' THEN 'Spa Jacuzzi overlooking tropical greenery at Nandini Jungle'
        WHEN 'A Romantic Spa Escape' THEN 'Couples spa experience at Nandini Jungle in Ubud, Bali'
        ELSE `images`.`mobile_image_alt`
    END,
    `images`.`updated_at` = NOW()
WHERE `sections`.`page_id` = @page_id
  AND `sections`.`title` IN (
      'Essence Spa',
      'Nandini Signature Spa on the River',
      'Wine Spa',
      'Spa Jacuzzi',
      'A Romantic Spa Escape'
  );

COMMIT;

SELECT
    `id`, `title`, `slug`, `meta_title`, `meta_description`,
    `hero_image`, `hero_mobile_image`, `updated_at`
FROM `pages`
WHERE `id` = @page_id;

SELECT `section_key`, `title`, `button_label`, `button_url`, `sort_order`
FROM `page_sections`
WHERE `page_id` = @page_id AND `is_active` = 1
ORDER BY `sort_order`;
