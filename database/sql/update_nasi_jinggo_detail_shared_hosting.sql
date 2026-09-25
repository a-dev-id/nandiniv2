-- Nandini Jungle by Hanging Gardens
-- Nasi Jinggo detail page for shared hosting (MySQL / MariaDB)
--
-- IMPORTANT:
-- 1. Back up the database before running this script.
-- 2. Deploy the matching application code before opening the detail page.
-- 3. Upload the Nasi Jinggo images to:
--      storage/app/public/dining/signature-dishes/nasi-jinggo/
--    and make sure the public/storage symlink exists.
-- 4. Required image filenames:
--      hero.jpg
--      pork-rib-bakar.jpg
--      nasi-putih.jpg
--      sate.jpg
--      lobster-sambal.jpg
--      pepes-ikan-kedonganan.jpg
--      ayam-bakar-madu.jpg
--      tuna-sambal-matah.jpg
--      tempe-bacem.jpg
-- 5. This script can be rerun. It changes only the `nasi-jinggo` record.

SET NAMES utf8mb4;

-- Shared-hosting compatibility for databases that predate these fields.
SET @has_signature_subtitle := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'signature_dishes'
      AND COLUMN_NAME = 'subtitle'
);

SET @schema_sql := IF(
    @has_signature_subtitle = 0,
    'ALTER TABLE `signature_dishes` ADD COLUMN `subtitle` VARCHAR(255) NULL AFTER `eyebrow`',
    'SELECT 1'
);

PREPARE schema_statement FROM @schema_sql;
EXECUTE schema_statement;
DEALLOCATE PREPARE schema_statement;

SET @has_signature_cta_url := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'signature_dishes'
      AND COLUMN_NAME = 'cta_url'
);

SET @schema_sql := IF(
    @has_signature_cta_url = 0,
    'ALTER TABLE `signature_dishes` ADD COLUMN `cta_url` TEXT NULL AFTER `cta_label`',
    'SELECT 1'
);

PREPARE schema_statement FROM @schema_sql;
EXECUTE schema_statement;
DEALLOCATE PREPARE schema_statement;

SET @has_signature_detail_content := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'signature_dishes'
      AND COLUMN_NAME = 'detail_content'
);

SET @schema_sql := IF(
    @has_signature_detail_content = 0,
    'ALTER TABLE `signature_dishes` ADD COLUMN `detail_content` JSON NULL AFTER `content`',
    'SELECT 1'
);

PREPARE schema_statement FROM @schema_sql;
EXECUTE schema_statement;
DEALLOCATE PREPARE schema_statement;

START TRANSACTION;

SET @nasi_jinggo_detail := '[{
  "hero_title": "Nasi Jinggo $100",
  "hero_description": "A premium sharing experience that transforms one of Bali’s most humble traditional meals into a memorable culinary journey — authentic in spirit, extraordinary in every detail.",
  "hero_image": "dining/signature-dishes/nasi-jinggo/hero.jpg",
  "hero_image_alt": "Nasi Jinggo sharing experience at Nandini Jungle by Hanging Gardens",
  "story_visible": true,
  "story_eyebrow": "Our Story",
  "story_title": "The Story of Nasi Jinggo",
  "story_description": "<p>Nasi Jinggo is a beloved Balinese traditional meal known for its humble everyday character and its connection with banana-leaf presentation. Its story is commonly linked to the 1970s and Ni Ketut Ngasti, affectionately known as Men Jinggo.</p><p>At Nandini, the idea is not to replace that tradition, but to celebrate it differently. Our Nasi Jinggo $100 keeps the spirit of the original while transforming the experience through refined presentation, premium ingredients, traditional cooking techniques and warm Balinese hospitality.</p>",
  "story_image": "dining/signature-dishes/nasi-jinggo/hero.jpg",
  "story_image_alt": "Nasi Jinggo presented as a refined Balinese sharing experience",
  "story_quote": "Same roots. A higher table.",
  "highlights_visible": true,
  "highlights_eyebrow": "The Experience",
  "highlights_title": "From Humble Roots to a Signature Experience",
  "highlights": [
    {"title": "Traditional Technique", "description": "Selected components are grilled over coconut charcoal for a distinctive smoky character."},
    {"title": "Local Sourcing", "description": "Most ingredients are sourced locally from nearby farms and local suppliers."},
    {"title": "Premium Ingredients", "description": "Australian beef tenderloin, bamboo lobster, yellowfin tuna and organic rice."},
    {"title": "Refined Presentation", "description": "A familiar local favorite reimagined as an elegant sharing experience."},
    {"title": "A Memorable Moment", "description": "A generous dining experience for two, created to be shared slowly and remembered."}
  ],
  "components_visible": true,
  "components_eyebrow": "The Menu",
  "components_title": "Eight Iconic Components",
  "components_description": "A journey through Balinese flavors, from charcoal-grilled meats and fresh seafood to sambal, banana-leaf preparations and organic rice.",
  "components": [
    {"title": "Pork Rib Bakar", "description": "Baby back pork ribs cooked until tender, finished over coconut charcoal and glazed with soy.", "image": "dining/signature-dishes/nasi-jinggo/pork-rib-bakar.jpg", "image_alt": "Pork Rib Bakar with soy glaze"},
    {"title": "Nasi Putih", "description": "Organic steamed rice presented in banana-leaf parcels with a traditional visual character.", "image": "dining/signature-dishes/nasi-jinggo/nasi-putih.jpg", "image_alt": "Nasi Putih in banana-leaf parcels"},
    {"title": "Saté", "description": "Grilled calamari and beef tenderloin satay served with red sambal and peanut sauce.", "image": "dining/signature-dishes/nasi-jinggo/sate.jpg", "image_alt": "Calamari and beef tenderloin satay"},
    {"title": "Lobster Sambal", "description": "Grilled bamboo lobster marinated with a rich red spicy sambal for freshness, depth and umami.", "image": "dining/signature-dishes/nasi-jinggo/lobster-sambal.jpg", "image_alt": "Grilled bamboo lobster with red sambal"},
    {"title": "Pepes Ikan Kedonganan", "description": "Red snapper with Balinese spices, wrapped in banana leaf, steamed and then gently grilled.", "image": "dining/signature-dishes/nasi-jinggo/pepes-ikan-kedonganan.jpg", "image_alt": "Pepes Ikan Kedonganan wrapped in banana leaf"},
    {"title": "Ayam Bakar Madu", "description": "Tender chicken leg grilled with honey and red spicy sambal for a juicy, aromatic finish.", "image": "dining/signature-dishes/nasi-jinggo/ayam-bakar-madu.jpg", "image_alt": "Ayam Bakar Madu with honey and sambal"},
    {"title": "Tuna Sambal Matah", "description": "Yellowfin tuna paired with fresh sambal matah, shallot and kaffir lime for brightness and texture.", "image": "dining/signature-dishes/nasi-jinggo/tuna-sambal-matah.jpg", "image_alt": "Yellowfin tuna with sambal matah"},
    {"title": "Tempe Bacem", "description": "Tempe cooked with bacem seasoning and tamarind chili paste, balancing sweetness, savoriness and umami.", "image": "dining/signature-dishes/nasi-jinggo/tempe-bacem.jpg", "image_alt": "Tempe Bacem with tamarind chili paste"}
  ],
  "premium_visible": true,
  "premium_eyebrow": "Crafted at Nandini",
  "premium_title": "Tradition Elevated. Flavors Unforgettable.",
  "premium_description": "<p>The experience brings together traditional tools, coconut-charcoal grilling, banana-leaf presentation and distinctive sambals with premium seafood, meat and carefully sourced ingredients. It is not intended to hide the humble origins of Nasi Jinggo — it celebrates them through a more generous and refined expression.</p>",
  "premium_image": "dining/signature-dishes/nasi-jinggo/hero.jpg",
  "premium_image_alt": "The complete Nasi Jinggo sharing menu at Nandini Jungle",
  "reservation_visible": true,
  "reservation_eyebrow": "Your Table Awaits",
  "reservation_title": "Reserve Nasi Jinggo $100 at Nandini",
  "reservation_description": "Discover a Balinese tradition through a signature sharing experience created for two. Available for lunch or dinner; advance reservation is recommended.",
  "reservation_button_label": "Reserve a Table",
  "reservation_button_url": "https://wa.me/6281236871170?text=Hello%2C%20I%20would%20like%20to%20reserve%20the%20Nasi%20Jinggo%20%24100%20experience.",
  "reservation_image": "dining/signature-dishes/nasi-jinggo/hero.jpg",
  "reservation_image_alt": ""
}]';

INSERT INTO `signature_dishes` (
    `name`,
    `slug`,
    `eyebrow`,
    `subtitle`,
    `price`,
    `short_description`,
    `cta_label`,
    `cta_url`,
    `image`,
    `image_alt`,
    `content`,
    `detail_content`,
    `is_published`,
    `sort_order`,
    `meta_title`,
    `meta_description`,
    `created_at`,
    `updated_at`
) VALUES (
    'Nasi Jinggo $100',
    'nasi-jinggo',
    'Signature Dish',
    'A Signature Taste of Bali',
    NULL,
    'A beloved Balinese street food, reimagined with premium ingredients and refined presentation, offering an authentic taste of Indonesia in the extraordinary setting of Nandini Jungle.',
    'View Signature Dish',
    '/signature-dishes/nasi-jinggo',
    'dining/signature-dishes/nasi-jinggo/hero.jpg',
    'Nasi Jinggo signature sharing experience at Nandini Jungle',
    NULL,
    @nasi_jinggo_detail,
    1,
    1,
    'Nasi Jinggo $100 | Nandini Jungle Dining',
    'Discover Nasi Jinggo $100, a refined Balinese sharing experience for two at Nandini Jungle by Hanging Gardens in Ubud.',
    NOW(),
    NOW()
)
ON DUPLICATE KEY UPDATE
    `name` = VALUES(`name`),
    `eyebrow` = VALUES(`eyebrow`),
    `subtitle` = VALUES(`subtitle`),
    `price` = VALUES(`price`),
    `short_description` = VALUES(`short_description`),
    `cta_label` = VALUES(`cta_label`),
    `cta_url` = VALUES(`cta_url`),
    `image` = VALUES(`image`),
    `image_alt` = VALUES(`image_alt`),
    `detail_content` = VALUES(`detail_content`),
    `is_published` = VALUES(`is_published`),
    `sort_order` = VALUES(`sort_order`),
    `meta_title` = VALUES(`meta_title`),
    `meta_description` = VALUES(`meta_description`),
    `updated_at` = NOW();

-- Prevent Laravel from trying to add `detail_content` again later.
INSERT INTO `migrations` (`migration`, `batch`)
SELECT
    '2026_09_22_000001_add_structured_detail_content_to_signature_dishes',
    COALESCE((SELECT MAX(`batch`) + 1 FROM `migrations`), 1)
WHERE NOT EXISTS (
    SELECT 1
    FROM `migrations`
    WHERE `migration` = '2026_09_22_000001_add_structured_detail_content_to_signature_dishes'
);

COMMIT;

-- Verification result: one row, valid JSON, and the expected hero title.
SELECT
    `id`,
    `slug`,
    `is_published`,
    JSON_VALID(`detail_content`) AS `detail_json_valid`,
    JSON_UNQUOTE(JSON_EXTRACT(`detail_content`, '$[0].hero_title')) AS `hero_title`,
    `updated_at`
FROM `signature_dishes`
WHERE `slug` = 'nasi-jinggo';
