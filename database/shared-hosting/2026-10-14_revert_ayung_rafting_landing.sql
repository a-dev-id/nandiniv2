-- Restore the standard Ayung rafting experience content.
-- Safe to run more than once. Pricing rows are not changed.

START TRANSACTION;

UPDATE `experiences`
SET
    `title` = 'Ayung White Water Rafting',
    `subtitle` = NULL,
    `excerpt` = 'Elevate your Bali adventure with the ultimate river rafting experience on the Ayung River. This thrilling journey combines adrenaline, breathtaking nature, and pure excitement as you navigate through exhilarating rapids, glide past cascading waterfalls, and immerse yourself in the lush tropical jungle.',
    `description` = '<p>Elevate your Bali adventure with the ultimate river rafting experience on the Ayung River. This thrilling journey combines adrenaline, breathtaking nature, and pure excitement as you navigate through exhilarating rapids, glide past cascading waterfalls, and immerse yourself in the lush tropical jungle.<br><br>Perfect for both beginners and adventure enthusiasts, expert guides ensure a safe and unforgettable ride. Get ready for splashes, laughter, and stunning scenery at every twist and turn of Bali’s most iconic river.</p>',
    `image_alt` = 'Ayung White Water Rafting | Bali River Adventure Experience',
    `card_image_alt` = 'Ayung White Water Rafting | Bali River Adventure Experience',
    `meta_title` = 'Ayung White Water Rafting | Bali River Adventure Experience',
    `meta_description` = 'Experience an exhilarating white water rafting adventure on one of Bali’s most iconic rivers. Navigate thrilling rapids, pass waterfalls, and immerse yourself in lush tropical.',
    `updated_at` = NOW()
WHERE `slug` = 'ayung-white-water-rafting';

COMMIT;

SELECT
    `id`, `slug`, `title`, `subtitle`, `meta_title`, `meta_description`, `updated_at`
FROM `experiences`
WHERE `slug` = 'ayung-white-water-rafting';
