-- Minimal CMS copy corrections. No slugs, prices, policies, images or booking URLs are changed.
-- REPLACE operations are rerunnable and preserve unrelated edits.
SET NAMES utf8mb4;
START TRANSACTION;

UPDATE accommodations
SET excerpt = 'The Panoramic Jungle View Villa offers sweeping river valley views, 48 square metres of accommodation, a canopied king-size bed and a private balcony overlooking the rainforest. Balinese-inspired architecture and teak flooring create a quiet retreat close to nature.',
    updated_at = NOW()
WHERE id = 1 AND slug = 'panoramic-jungle-view-villa'
  AND excerpt LIKE 'Stay in private jungle villas in Ubud and spacious royal suites%';

UPDATE accommodations
SET description = REPLACE(description, 'rainforest center', 'rainforest landscape'), updated_at = NOW()
WHERE id = 1 AND slug = 'panoramic-jungle-view-villa' AND description LIKE '%rainforest center%';

UPDATE accommodations
SET description = REPLACE(REPLACE(description, 'Featuring a breathtaking golden sunshine spilling into the room', 'With golden sunshine spilling into the room'), 'Featuring a wide outdoor living area', 'With a wide outdoor living area'), updated_at = NOW()
WHERE id = 3 AND slug = 'sunrise-view-villa' AND description LIKE '%Featuring a breathtaking golden sunshine%';

UPDATE accommodations
SET description = REPLACE(description, 'The Jungle View Royal Suite are lavishly furnished', 'The Jungle View Royal Suite is lavishly furnished'), updated_at = NOW()
WHERE id = 5 AND slug = 'jungle-view-royal-suite' AND description LIKE '%Suite are lavishly furnished%';

UPDATE accommodations
SET description = REPLACE(REPLACE(REPLACE(REPLACE(description,
    'The Private Garden Royal Suite are professionally illustrated and loaded with lavish furnishing.',
    'The Private Garden Royal Suite is thoughtfully designed and furnished.'),
    'These prosperous suites', 'These spacious suites'),
    'the rampant and green grassland panorama', 'the green garden panorama'),
    'the squirrels of birds tweeting merrily', 'the sound of birds singing'), updated_at = NOW()
WHERE id = 7 AND slug = 'private-garden-royal-suite' AND description LIKE '%The Private Garden Royal Suite are%';

UPDATE accommodations
SET description = REPLACE(description, 'the verdant jungle Ubud, Bali', 'the verdant jungle in Ubud, Bali'), updated_at = NOW()
WHERE id = 6 AND slug = 'panoramic-corner-jacuzzi-royal-suite' AND description LIKE '%the verdant jungle Ubud, Bali%';

UPDATE dining_experiences
SET description = REPLACE(description,
    'Nandini Jungle by Hanging Gardens meticulously curated wine collection',
    'The meticulously curated wine collection at Nandini Jungle by Hanging Gardens'), updated_at = NOW()
WHERE id = 52 AND slug = 'world-class-wine-cellar-selection'
  AND description LIKE '%Nandini Jungle by Hanging Gardens meticulously curated wine collection%';

UPDATE spas
SET description = REPLACE(REPLACE(REPLACE(description,
    'A’Lang Bar', 'Alang Bar'),
    'the lush Ubud jungle and the mystical Ayung River meets', 'the lush Ubud jungle and the mystical Ayung River meet'),
    'bid farewell to Nandini Jungle Resort', 'bid farewell to Nandini Jungle'), updated_at = NOW()
WHERE id = 4 AND slug = 'a-mystical-journey-at-nandini-4d3n-wellness-retreat'
  AND (description LIKE '%A’Lang Bar%' OR description LIKE '%Ayung River meets%' OR description LIKE '%bid farewell to Nandini Jungle Resort%');

UPDATE pages
SET description = REPLACE(description, 'both in the Indonesia and Internationally', 'in Indonesia and internationally'), updated_at = NOW()
WHERE id = 9 AND slug = 'awards' AND description LIKE '%both in the Indonesia and Internationally%';

UPDATE pages
SET description = REPLACE(REPLACE(description, 'Ayung river', 'Ayung River'), 'that you will never forget</p>', 'that you will never forget.</p>'), updated_at = NOW()
WHERE id = 5 AND page_name = 'Experiences Page'
  AND (description LIKE '%Ayung river%' OR description LIKE '%that you will never forget</p>%');

COMMIT;
