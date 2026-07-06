-- Add persistent file-name fields for Blog & News images.
-- Run this once if you are applying the database changes manually instead of using:
-- php artisan migrate

ALTER TABLE blog_news
    ADD COLUMN hero_image_file_name VARCHAR(255) NULL AFTER hero_image,
    ADD COLUMN hero_mobile_image_file_name VARCHAR(255) NULL AFTER hero_mobile_image,
    ADD COLUMN card_image_file_name VARCHAR(255) NULL AFTER card_image;

ALTER TABLE blog_news_section_images
    ADD COLUMN image_file_name VARCHAR(255) NULL AFTER image,
    ADD COLUMN mobile_image_file_name VARCHAR(255) NULL AFTER mobile_image;

UPDATE blog_news
SET hero_image_file_name = SUBSTRING_INDEX(SUBSTRING_INDEX(hero_image, '/', -1), '.', 1)
WHERE hero_image IS NOT NULL
  AND hero_image_file_name IS NULL;

UPDATE blog_news
SET hero_mobile_image_file_name = SUBSTRING_INDEX(SUBSTRING_INDEX(hero_mobile_image, '/', -1), '.', 1)
WHERE hero_mobile_image IS NOT NULL
  AND hero_mobile_image_file_name IS NULL;

UPDATE blog_news
SET card_image_file_name = SUBSTRING_INDEX(SUBSTRING_INDEX(card_image, '/', -1), '.', 1)
WHERE card_image IS NOT NULL
  AND card_image_file_name IS NULL;

UPDATE blog_news_section_images
SET image_file_name = SUBSTRING_INDEX(SUBSTRING_INDEX(image, '/', -1), '.', 1)
WHERE image IS NOT NULL
  AND image_file_name IS NULL;

UPDATE blog_news_section_images
SET mobile_image_file_name = SUBSTRING_INDEX(SUBSTRING_INDEX(mobile_image, '/', -1), '.', 1)
WHERE mobile_image IS NOT NULL
  AND mobile_image_file_name IS NULL;
