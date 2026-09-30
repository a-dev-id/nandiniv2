-- Nandini public sitemap database update
-- Target: MariaDB 10.11+
-- Safe to run more than once through phpMyAdmin or the MariaDB command line.

START TRANSACTION;

ALTER TABLE `pages`
    ADD COLUMN IF NOT EXISTS `include_in_sitemap` TINYINT(1) NOT NULL DEFAULT 1 AFTER `is_active`;

ALTER TABLE `pages`
    ADD INDEX IF NOT EXISTS `pages_sitemap_visibility_index`
        (`site`, `is_active`, `include_in_sitemap`);

-- Existing CMS records include pages that are rendered through dedicated routes
-- and private/member slugs. Those routes are managed directly by the sitemap
-- generator, so generic CMS inclusion starts disabled.
UPDATE `pages`
SET `include_in_sitemap` = 0;

-- These are standalone public SEO pages handled by the generic page route.
UPDATE `pages`
SET `include_in_sitemap` = 1
WHERE `site` = 'main'
  AND `slug` IN (
      'ubud-jungle-resort-in-bali',
      'ubud-wellness-retreat',
      'jungle-spa-ubud'
  );

COMMIT;

-- Verification query: only intended standalone public CMS pages should return.
SELECT
    `id`,
    `site`,
    `page_name`,
    `slug`,
    `is_active`,
    `include_in_sitemap`
FROM `pages`
WHERE `include_in_sitemap` = 1
ORDER BY `site`, `sort_order`, `id`;
