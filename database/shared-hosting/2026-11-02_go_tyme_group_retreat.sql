-- Go Tyme Group retreat article for Nandini shared hosting (MySQL / MariaDB).
-- Target: https://nandinibali.com/blog/go-tyme-group-retreat-bali-2026
-- Generated from database/content/go-tyme-retreat.php.
--
-- Import this file in phpMyAdmin with the Nandini database selected.
-- Deploy the corresponding Laravel route/model/controller/Blade changes,
-- database/content/go-tyme-image-dimensions.php, public/build, and the complete
-- public/images/go-tyme-images directory (including optimized/) first.
-- SQL installs article data; the project header and galleries are Blade/code.
-- Requires the existing blog_news, blog_news_sections and
-- blog_news_section_images tables, including the sections.items column.
-- No schema changes, admin packages or changes to unrelated articles are made.
--
-- Publication matches the Laravel content migration: a NEW article is active,
-- with its publication date set to the import date in Bali/Singapore (UTC+8).
-- Existing records with this slug, including drafts, are left unchanged.
-- Safe to rerun: if the slug already exists, the entire content import is skipped.
-- Explicit IDs also support shared-hosting schemas without AUTO_INCREMENT.
-- Take a database backup before importing. Stop the import on any SQL error;
-- if an error occurs before COMMIT, roll back the same connection.

SET NAMES utf8mb4;
START TRANSACTION;

SET @go_tyme_now := UTC_TIMESTAMP() + INTERVAL 8 HOUR;
SET @go_tyme_existing_id := (
    SELECT `id` FROM `blog_news`
    WHERE `slug` = 'go-tyme-group-retreat-bali-2026' LIMIT 1
);
SET @go_tyme_is_new := (@go_tyme_existing_id IS NULL);
SET @go_tyme_blog_id := COALESCE(@go_tyme_existing_id,
    (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news`));
INSERT INTO `blog_news` (`id`, `type`, `slug`, `title`, `subtitle`, `excerpt`, `description`, `hero_image`, `hero_image_alt`, `card_image`, `card_image_alt`, `author_name`, `published_at`, `meta_title`, `meta_description`, `is_active`, `is_featured`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_blog_id,
    'blog',
    'go-tyme-group-retreat-bali-2026',
    'Six Days of Connection in the Jungle: Go Tyme Group at Nandini',
    'A Shared Journey of Reflection, Nature, and Balinese Traditions',
    'Seventeen guests, six days and five nights in Payangan: a photographic story of reflection, jungle surroundings, Balinese traditions and time shared at Nandini.',
    '<h2>Six Days of Connection in the Jungle</h2><p>Between 4 and 9 October 2026, Go Tyme Group gathered at Nandini Jungle by Hanging Gardens in Payangan, Bali. Seventeen guests came together for six days and five nights, with the jungle offering a quiet setting for time shared beyond the familiar rhythms of everyday life.</p><p>The retreat programme brought reflection, nature, cultural discovery and shared dining into a single journey. Open-air spaces invited conversation; paths through the gardens connected one gathering place to the next. These photographs offer glimpses of the group together, while the itinerary traces the intentions behind those moments.</p>',
    '/images/go-tyme-images/optimized/08615-1600.webp',
    'Go Tyme Group gathered on the river deck, surrounded by the jungle at Nandini',
    '/images/go-tyme-images/optimized/08615-960.webp',
    'Go Tyme Group together on Nandini’s jungle-framed river deck',
    'Nandini Jungle by Hanging Gardens',
    DATE(@go_tyme_now),
    'Go Tyme Group Retreat in Bali | Nandini Jungle',
    'Explore the Go Tyme Group''s six-day retreat at Nandini Jungle by Hanging Gardens, featuring jungle meditation, canang making and shared moments in Bali.',
    1,
    0,
    0,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

-- Section 1: A Warm Arrival and First Moments of Connection
SET @go_tyme_section_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_sections`);

INSERT INTO `blog_news_sections` (`id`, `blog_news_id`, `section_key`, `title`, `subtitle`, `description`, `items`, `text_align`, `background_color`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_section_id,
    @go_tyme_blog_id,
    'retreat_gallery_section',
    'A Warm Arrival and First Moments of Connection',
    '4 October 2026',
    '<p>Arrival in Payangan introduces a different pace. Above the river valley, tropical foliage surrounds the resort’s timber buildings, bringing the landscape close even when guests are indoors. For Go Tyme Group, this was the setting for the opening of a shared retreat: a chance to settle in, become familiar with the surroundings and begin spending time together.</p><p>The first evening’s itinerary centred on dinner at <a href="/dining">Wild Ginger</a>. Gathering around a table gave the welcome a personal scale, with room for conversation alongside the simple pleasures of a shared meal. Hospitality here was part of the beginning: a place prepared, a table to return to, and time for the group’s journey to unfold.</p>',
    '{"caption":"Shared tables and evening dining at Wild Ginger. Photographs are grouped by subject, rather than assigned to a specific evening.","featured":"08902"}',
    'left',
    'white',
    1,
    1,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08902-go-tyme-group.webp',
    'Guests seated around a flower-decorated dinner table at Wild Ginger',
    1,
    0,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08908-go-tyme-group.webp',
    'Evening conversation along the shared dining table at Wild Ginger',
    1,
    1,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08912-go-tyme-group.webp',
    'Guests talking across a candlelit table in the open-air restaurant',
    1,
    2,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08915-go-tyme-group.webp',
    'An evening meal beneath the restaurant’s timber roof',
    1,
    3,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08921-go-tyme-group.webp',
    'Go Tyme Group gathering for a photograph beside the dinner table',
    1,
    4,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

-- Section 2: Finding a Shared Rhythm at Djiwa Shala
SET @go_tyme_section_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_sections`);

INSERT INTO `blog_news_sections` (`id`, `blog_news_id`, `section_key`, `title`, `subtitle`, `description`, `items`, `text_align`, `background_color`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_section_id,
    @go_tyme_blog_id,
    'retreat_gallery_section',
    'Finding a Shared Rhythm at Djiwa Shala',
    '5 October 2026',
    '<p>The programme’s first full day was set to begin at 9:30 AM at Djiwa Shala. Beneath its high thatched roof, the pavilion opens towards greenery, allowing the surrounding landscape to remain present throughout a session. The photographs show a space arranged for gathering, with chairs, discussion materials and yoga mats used in different parts of the retreat.</p><p>Group discussions and reflective wellness sessions were woven into the day’s plan. Sitting together in an open-air pavilion creates an invitation to listen with fewer everyday distractions. There was no need to hurry the conversation: the setting allowed both an exchange of ideas and quieter intervals, with the jungle providing a gentle backdrop.</p>',
    '{"caption":"Discussion and mat-based sessions in the open-air Djiwa Shala pavilion.","featured":"08497"}',
    'left',
    'white',
    1,
    2,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08497-go-tyme-group.webp',
    'A group discussion beneath Djiwa Shala’s high thatched roof',
    1,
    0,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08499-go-tyme-group.webp',
    'Guests seated facing the front of the open-air pavilion',
    1,
    1,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08502-go-tyme-group.webp',
    'A speaker addressing guests beside discussion boards at Djiwa Shala',
    1,
    2,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08503-go-tyme-group.webp',
    'Guests listening from chairs arranged inside the pavilion',
    1,
    3,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08508-go-tyme-group.webp',
    'A seated group session with the pavilion open to the gardens',
    1,
    4,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08511-go-tyme-group.webp',
    'A speaker standing before the group beneath the bamboo roof',
    1,
    5,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08514-go-tyme-group.webp',
    'A conversation between seated participants in Djiwa Shala',
    1,
    6,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08520-go-tyme-group.webp',
    'The pavilion framed by broad tropical leaves and its thatched eaves',
    1,
    7,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08528-go-tyme-group.webp',
    'Participants seated on purple mats in the open-air shala',
    1,
    8,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08533-go-tyme-group.webp',
    'A mat-based group session viewed through tropical foliage',
    1,
    9,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08547-go-tyme-group.webp',
    'Guests sitting together beneath the shala’s deep thatched roof',
    1,
    10,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

-- Section 3: A Journey of Trust, Gratitude, and Connection with Nature
SET @go_tyme_section_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_sections`);

INSERT INTO `blog_news_sections` (`id`, `blog_news_id`, `section_key`, `title`, `subtitle`, `description`, `items`, `text_align`, `background_color`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_section_id,
    @go_tyme_blog_id,
    'retreat_gallery_section',
    'A Journey of Trust, Gratitude, and Connection with Nature',
    '6 October 2026',
    '<p>For the morning of 6 October, the itinerary imagined a journey through several of Nandini’s gathering spaces. It was to begin at Djiwa Shala with a paired blindfolded trust exercise: one person guiding, the other practising attentive reliance on a companion. A leaf-writing activity followed in the plan, inviting each guest to put gratitude and wishes into a few considered words.</p><p>From the pavilion, the planned walk led towards the chapel for morning meditation and reflection. Along the resort’s garden paths, broad leaves, palms and flowering plants give the journey its own visual rhythm. Moving between spaces was part of the intention, allowing a pause between the conversations of the shala and the stillness of the next gathering.</p><p>The route then continued towards the river deck. The photographs show purple mats set against the red railing, with rock faces and dense greenery enclosing the space. Here, meditation beside the flowing water was planned before a symbolic release of the written wishes into the river. The gesture belonged to this retreat’s itinerary; the photographs do not document every step of the planned ritual.</p><p>The morning’s programme closed with a return to Wild Ginger for breakfast. After the walk and time set aside for reflection, the shared table offered a natural way to gather again. Readers can explore more of this landscape through Nandini’s <a href="/holy-river">Holy River experiences</a>.</p>',
    '{"caption":"Garden paths and the riverside deck provide the visual setting for the planned morning journey. Not every itinerary activity is documented in the photographs.","featured":"08579"}',
    'left',
    'white',
    1,
    3,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08550-go-tyme-group.webp',
    'Guests walking down a narrow garden path beneath tropical leaves',
    1,
    0,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08556-go-tyme-group.webp',
    'The group following a garden walkway bordered by red tropical flowers',
    1,
    1,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08558-go-tyme-group.webp',
    'Guests descending a sunlit path through the resort gardens',
    1,
    2,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08561-go-tyme-group.webp',
    'Guests gathering on an outdoor terrace beside a frangipani tree',
    1,
    3,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08563-go-tyme-group.webp',
    'The group standing near a wooden terrace in the resort gardens',
    1,
    4,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08565-go-tyme-group.webp',
    'Guests gathered between tropical planting and a thatched-roof building',
    1,
    5,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08579-go-tyme-group.webp',
    'An elevated view of purple mats on the jungle-framed river deck',
    1,
    6,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08583-go-tyme-group.webp',
    'Guests standing on mats beside the river deck’s red railing',
    1,
    7,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08593-go-tyme-group.webp',
    'Participants lying on purple mats on the riverside deck',
    1,
    8,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08598-go-tyme-group.webp',
    'The group gathering at the edge of the deck beneath dense jungle foliage',
    1,
    9,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08600-go-tyme-group.webp',
    'Go Tyme Group posing together on the river deck among purple mats',
    1,
    10,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

-- Section 4: Creating Space for Conversation, Movement, and Togetherness
SET @go_tyme_section_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_sections`);

INSERT INTO `blog_news_sections` (`id`, `blog_news_id`, `section_key`, `title`, `subtitle`, `description`, `items`, `text_align`, `background_color`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_section_id,
    @go_tyme_blog_id,
    'retreat_gallery_section',
    'Creating Space for Conversation, Movement, and Togetherness',
    'Across the retreat',
    '<p>Across the six-day programme, shared sessions offered a thread of continuity. Djiwa Shala could hold a seated discussion, then make room for movement and mindful exercises. The photographs capture guests standing together on mats, working in pairs and listening in a circle, showing different ways of occupying the same welcoming space.</p><p>These gatherings did not need a single prescribed outcome to have a place in the retreat. A conversation, a shared exercise or a pause beside someone else could each be approached in its own way. The jungle remained close throughout, beyond the pavilion’s open sides and along the paths connecting it to the rest of the resort.</p><p>There were also the less formal moments around meals. In the dining photographs, tables become another place to listen and exchange ideas. Together, these scenes describe the retreat’s atmosphere without assuming what any individual participant felt or took away.</p>',
    '{"caption":"Paired exercises, group circles and conversations around shared tables.","featured":"08627"}',
    'left',
    'white',
    1,
    4,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08622-go-tyme-group.webp',
    'Guests working in pairs on purple mats inside Djiwa Shala',
    1,
    0,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08624-go-tyme-group.webp',
    'Paired movement exercises beneath the pavilion’s open roof',
    1,
    1,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08626-go-tyme-group.webp',
    'Participants raising their arms during a paired group exercise',
    1,
    2,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08627-go-tyme-group.webp',
    'A standing circle of guests across the pavilion floor',
    1,
    3,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08631-go-tyme-group.webp',
    'Guests gathered in a circle with purple mats beneath their feet',
    1,
    4,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08632-go-tyme-group.webp',
    'Participants standing together at Djiwa Shala’s garden-facing side',
    1,
    5,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08448-go-tyme-group.webp',
    'Guests sharing tables in the garden-facing dining pavilion',
    1,
    6,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08449-go-tyme-group.webp',
    'A dining table beside the restaurant’s open tropical garden edge',
    1,
    7,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08451-go-tyme-group.webp',
    'Guests seated together beneath the restaurant’s timber beams',
    1,
    8,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08452-go-tyme-group.webp',
    'A dining conversation framed by a wooden pillar and garden leaves',
    1,
    9,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08455-go-tyme-group.webp',
    'Guests in conversation at a table overlooking tropical foliage',
    1,
    10,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08457-go-tyme-group.webp',
    'A shared meal with the restaurant open to the green landscape',
    1,
    11,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08459-go-tyme-group.webp',
    'A long view along the group’s dining table',
    1,
    12,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08475-go-tyme-group.webp',
    'Guests talking beneath the restaurant roof beside the gardens',
    1,
    13,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08481-go-tyme-group.webp',
    'A dining gathering viewed past red flowers and tropical leaves',
    1,
    14,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08485-go-tyme-group.webp',
    'Guests seated around a table at the open edge of the restaurant',
    1,
    15,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08489-go-tyme-group.webp',
    'A wide view of the group at shared restaurant tables',
    1,
    16,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

-- Section 5: Discovering Bali Through the Art of Canang
SET @go_tyme_section_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_sections`);

INSERT INTO `blog_news_sections` (`id`, `blog_news_id`, `section_key`, `title`, `subtitle`, `description`, `items`, `text_align`, `background_color`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_section_id,
    @go_tyme_blog_id,
    'retreat_gallery_section',
    'Discovering Bali Through the Art of Canang',
    '7 October 2026',
    '<p>The cultural programme for 7 October turned towards canang, the small offerings that form a familiar part of Balinese Hindu daily life. Prepared with care using leaves and flowers, these offerings express devotion and gratitude. Their significance extends beyond their beauty: they belong to a living spiritual practice, encountered with respect for the people who sustain it.</p><p>At Djiwa Shala, local instructors guided the hands-on activity. The photographs bring the details into focus: green leaf bases, flowers in vivid colours, materials gathered on trays and guests seated close enough to watch each demonstration. Learning began with attention to the hands, observing how a form was prepared before trying the arrangement for oneself.</p><p>Some images linger on the materials, while others show the exchange between instructor and participant. Together, they give the activity a human scale. The invitation was to learn a little about a tradition through patient participation, with its meaning held alongside the making.</p>',
    '{"caption":"Local instruction, leaf bases and colourful flowers during canang making at Djiwa Shala.","featured":"08668"}',
    'left',
    'white',
    1,
    5,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08636-go-tyme-group.webp',
    'An instructor demonstrating with offering materials on a low table',
    1,
    0,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08638-go-tyme-group.webp',
    'Guests sitting around a local instructor for canang making',
    1,
    1,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08640-go-tyme-group.webp',
    'Participants folding leaf materials while seated on the pavilion floor',
    1,
    2,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08642-go-tyme-group.webp',
    'An instructor helping a participant arrange a small leaf offering',
    1,
    3,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08646-go-tyme-group.webp',
    'Guests watching offering preparation beside the open edge of the shala',
    1,
    4,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08649-go-tyme-group.webp',
    'A circle of guests working with flowers and leaves on shared trays',
    1,
    5,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08650-go-tyme-group.webp',
    'Participants following a canang demonstration on the timber floor',
    1,
    6,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08651-go-tyme-group.webp',
    'A guest shaping leaf materials during the canang workshop',
    1,
    7,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08653-go-tyme-group.webp',
    'Participants concentrating on small leaf bases in their hands',
    1,
    8,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08655-go-tyme-group.webp',
    'Hands arranging offering materials during the workshop',
    1,
    9,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08662-go-tyme-group.webp',
    'Guests seated together around colourful canang materials',
    1,
    10,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08663-go-tyme-group.webp',
    'A local instructor demonstrating leaf preparation to seated guests',
    1,
    11,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08668-go-tyme-group.webp',
    'Participants watching an instructor beside trays of flowers',
    1,
    12,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08671-go-tyme-group.webp',
    'Guests preparing leaf offerings beside a tray of yellow and red flowers',
    1,
    13,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08673-go-tyme-group.webp',
    'A shared tray of flowers at the centre of a canang-making group',
    1,
    14,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08677-go-tyme-group.webp',
    'An instructor reaching across the materials to help participants',
    1,
    15,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08680-go-tyme-group.webp',
    'Guests leaning towards a shared arrangement of offering flowers',
    1,
    16,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08682-go-tyme-group.webp',
    'Participants working with flowers and leaf bases on the floor',
    1,
    17,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08684-go-tyme-group.webp',
    'A small group learning canang making beside the pavilion gardens',
    1,
    18,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08688-go-tyme-group.webp',
    'A wide view of the workshop with flower trays between seated groups',
    1,
    19,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08690-go-tyme-group.webp',
    'Guests and instructors arranging offerings in the sunlit pavilion',
    1,
    20,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08691-go-tyme-group.webp',
    'Participants seated among flower trays with mats arranged behind them',
    1,
    21,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08692-go-tyme-group.webp',
    'An instructor demonstrating leaf work beside colourful petals',
    1,
    22,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08696-go-tyme-group.webp',
    'The canang workshop beneath the pavilion’s thatched eaves',
    1,
    23,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08697-go-tyme-group.webp',
    'Guests gathered closely around a seated instructor',
    1,
    24,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08701-go-tyme-group.webp',
    'A local instructor speaking to guests seated with their offering materials',
    1,
    25,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08704-go-tyme-group.webp',
    'Guests watching a demonstration beside trays of flowers and leaves',
    1,
    26,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08706-go-tyme-group.webp',
    'Participants examining small offerings as an instructor assists',
    1,
    27,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08708-go-tyme-group.webp',
    'A close view of hands, leaf bases and bright offering flowers',
    1,
    28,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08711-go-tyme-group.webp',
    'A group preparing canang together at the garden-facing edge of the shala',
    1,
    29,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

-- Section 6: A Respectful Moment at the Resort Shrine
SET @go_tyme_section_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_sections`);

INSERT INTO `blog_news_sections` (`id`, `blog_news_id`, `section_key`, `title`, `subtitle`, `description`, `items`, `text_align`, `background_color`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_section_id,
    @go_tyme_blog_id,
    'retreat_gallery_section',
    'A Respectful Moment at the Resort Shrine',
    '7 October 2026 · following canang making',
    '<p>After canang making, the itinerary included a visit to the shrine near the lobby. The photographs show guests seated outside with local guidance, bringing the cultural activity into the context of a place of devotion. Flowers and prepared offerings appear alongside moments of attentive participation.</p><p>Such an encounter is approached through courtesy and listening. Following local guidance allows visitors to appreciate a tradition while recognising that its meaning belongs to the community practising it. For this retreat, the scheduled visit offered an opportunity to move from learning how an offering is made towards a respectful appreciation of the spiritual life around it.</p>',
    '{"caption":"Guests at the resort shrine with local guidance after the canang-making activity.","featured":"08743"}',
    'left',
    'white',
    1,
    6,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08726-go-tyme-group.webp',
    'A local guide speaking to guests beside offerings outside a thatched building',
    1,
    0,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08739-go-tyme-group.webp',
    'Guests seated on mats with small offerings beside tropical planting',
    1,
    1,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08741-go-tyme-group.webp',
    'Participants looking down at offerings arranged in front of them',
    1,
    2,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08743-go-tyme-group.webp',
    'Guests seated facing the resort’s decorated outdoor shrine',
    1,
    3,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08745-go-tyme-group.webp',
    'An instructor guiding seated guests holding flowers in their hands',
    1,
    4,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08751-go-tyme-group.webp',
    'Guests raising flowers towards their foreheads during the guided shrine visit',
    1,
    5,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08762-go-tyme-group.webp',
    'Seated guests following local guidance with offerings before them',
    1,
    6,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08763-go-tyme-group.webp',
    'Participants attending to flowers and offerings at the outdoor shrine',
    1,
    7,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08771-go-tyme-group.webp',
    'Guests holding flowers close to their foreheads beside a local instructor',
    1,
    8,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08772-go-tyme-group.webp',
    'A rear view of the group seated before the decorated shrine',
    1,
    9,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08777-go-tyme-group.webp',
    'A row of guests with raised hands during the guided shrine visit',
    1,
    10,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08781-go-tyme-group.webp',
    'A local guide standing beside the decorated resort shrine',
    1,
    11,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08785-go-tyme-group.webp',
    'A local instructor attending to seated guests during the shrine visit',
    1,
    12,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08790-go-tyme-group.webp',
    'Guests receiving local guidance beside their prepared offerings',
    1,
    13,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

-- Section 7: A Final Day of Reflection and a Warm Farewell
SET @go_tyme_section_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_sections`);

INSERT INTO `blog_news_sections` (`id`, `blog_news_id`, `section_key`, `title`, `subtitle`, `description`, `items`, `text_align`, `background_color`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_section_id,
    @go_tyme_blog_id,
    'retreat_gallery_section',
    'A Final Day of Reflection and a Warm Farewell',
    '8–9 October 2026',
    '<p>As the retreat approached its close, the programme left space for final group sessions and reflection. After several days of gathering in the same open-air spaces, the closing sessions offered a way to revisit the journey: the conversations, the shared exercises and the encounters with the landscape and culture of Bali.</p><p>For 9 October, the itinerary included a final wishing-note activity, a shared lunch and a farewell from the Nandini team. It was a gentle shape for a departure day, balancing a personal moment of reflection with one more opportunity to sit together at a table.</p><p>The photographs do not record every closing activity, but the wider collection holds the spirit of time spent together. A retreat can be remembered through small details as much as its programme: the view from a pavilion, the care involved in making an offering, or the familiar presence of companions gathered nearby.</p>',
    '{"caption":"A group portrait at Djiwa Shala, included here as a closing reflection rather than a photograph dated to departure day.","featured":"08716"}',
    'left',
    'white',
    1,
    7,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

SET @go_tyme_image_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_section_images`);

INSERT INTO `blog_news_section_images` (`id`, `blog_news_section_id`, `image`, `image_alt`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_image_id,
    @go_tyme_section_id,
    '/images/go-tyme-images/dsc08716-go-tyme-group.webp',
    'Go Tyme Group standing together for a portrait inside Djiwa Shala',
    1,
    0,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

-- Section 8: A Shared Story to Carry Forward
SET @go_tyme_section_id := (SELECT COALESCE(MAX(`id`), 0) + 1 FROM `blog_news_sections`);

INSERT INTO `blog_news_sections` (`id`, `blog_news_id`, `section_key`, `title`, `subtitle`, `description`, `items`, `text_align`, `background_color`, `is_active`, `sort_order`, `created_at`, `updated_at`)
SELECT
    @go_tyme_section_id,
    @go_tyme_blog_id,
    'retreat_gallery_section',
    'A Shared Story to Carry Forward',
    'Nature, culture and connection',
    '<p>The Go Tyme Group retreat brought together different ways of experiencing Bali: through the surrounding jungle, through moments set aside for reflection, through cultural learning and through time shared with other people. The itinerary offered a framework; the photographs preserve particular scenes within that shared story.</p><p>At Nandini Jungle by Hanging Gardens, intimate group retreats and wellness experiences take their character from both the setting and the people who gather here. Readers imagining a journey of their own can explore the resort’s <a href="/experiences">experiences</a> and <a href="/contact-us">speak with the Nandini team about a group retreat</a>, shaped around their interests and the arrangements available for their stay.</p>',
    '{"caption":null,"featured":null}',
    'left',
    'white',
    1,
    8,
    @go_tyme_now,
    @go_tyme_now
WHERE @go_tyme_is_new = 1;

COMMIT;

-- Verification: a fresh import should report 8 sections and 89 section photos.
-- Together with the hero image, the article contains 90 distinct photographs.
SELECT
    IF(@go_tyme_is_new, 'Article created', 'Existing article preserved; no changes made') AS `import_result`,
    b.`id`, b.`slug`, b.`published_at`, b.`is_active`,
    COUNT(DISTINCT s.`id`) AS `sections`,
    COUNT(i.`id`) AS `section_photographs`
FROM `blog_news` b
LEFT JOIN `blog_news_sections` s ON s.`blog_news_id` = b.`id`
LEFT JOIN `blog_news_section_images` i ON i.`blog_news_section_id` = s.`id`
WHERE b.`id` = @go_tyme_blog_id
GROUP BY b.`id`, b.`slug`, b.`published_at`, b.`is_active`;
