-- Update only the Jungle Villas listing introduction and meta description.
-- Hero, villa records, room codes and booking URLs remain unchanged.
SET NAMES utf8mb4;
START TRANSACTION;
UPDATE `pages`
SET `description` = '<p>Set within the lush rainforest landscape of Payangan, in the greater Ubud area of Bali, Nandini Jungle by Hanging Gardens offers a collection of private jungle villas inspired by traditional Balinese architecture. Each villa features a private balcony, refined interiors and views of the surrounding tropical landscape, creating a tranquil retreat close to nature.</p>',
    `excerpt` = 'Set within the lush rainforest landscape of Payangan, in the greater Ubud area of Bali, Nandini Jungle by Hanging Gardens offers a collection of private jungle villas inspired by traditional Balinese architecture.',
    `meta_description` = 'Discover private jungle villas in Ubud, Bali, with Balinese-inspired interiors, rainforest views and private balconies. Book directly with Nandini Jungle.',
    `updated_at` = NOW()
WHERE `site` = 'main' AND `slug` = 'jungle-villas';
COMMIT;
