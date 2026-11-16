-- Import into the existing website database using phpMyAdmin.
-- Deploy routes/web.php as well to enable the HTTP 301 redirect.
START TRANSACTION;

UPDATE blog_news
SET button_url = '/jungle-villas'
WHERE button_url IN ('/the-royal-suites-and-jungle-villas', 'https://nandinibali.com/the-royal-suites-and-jungle-villas');

UPDATE blog_news_sections
SET button_url = '/jungle-villas'
WHERE button_url IN ('/the-royal-suites-and-jungle-villas', 'https://nandinibali.com/the-royal-suites-and-jungle-villas');

UPDATE page_sections
SET button_url = '/jungle-villas'
WHERE button_url IN ('/the-royal-suites-and-jungle-villas', 'https://nandinibali.com/the-royal-suites-and-jungle-villas');

UPDATE blog_news_sections
SET button_route = 'accommodations.villas'
WHERE button_route = 'accommodations.index';

UPDATE page_sections
SET button_route = 'accommodations.villas'
WHERE button_route = 'accommodations.index';

COMMIT;
