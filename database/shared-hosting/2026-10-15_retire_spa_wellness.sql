-- Back up the database before deployment. This preserves the page and its media.
-- Run only on the MAIN website database; no Spa subdomain settings are changed.
START TRANSACTION;

UPDATE pages
SET is_active = 0, include_in_sitemap = 0
WHERE site = 'main' AND slug = 'spa-wellness';

-- Update only exact retired landing-page targets; individual treatment URLs stay intact.
UPDATE page_sections SET button_url = 'https://spa.nandinibali.com/'
WHERE button_url IN ('/spa-wellness', '/spa-wellness/', 'https://nandinibali.com/spa-wellness', 'https://nandinibali.com/spa-wellness/', 'spa.index')
AND page_id IN (SELECT id FROM pages WHERE site = 'main');

UPDATE offers SET button_url = 'https://spa.nandinibali.com/'
WHERE button_url IN ('/spa-wellness', '/spa-wellness/', 'https://nandinibali.com/spa-wellness', 'https://nandinibali.com/spa-wellness/', 'spa.index');

COMMIT;
