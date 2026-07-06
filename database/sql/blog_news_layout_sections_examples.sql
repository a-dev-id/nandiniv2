-- Blog & News layout section examples.
-- Replace @blog_slug with the target blog/news slug before running.
-- These inserts create inactive example sections so you can edit, upload images,
-- reorder, and activate them from Filament.

SET @blog_slug = 'replace-with-blog-slug';

INSERT INTO blog_news_sections (
    blog_news_id,
    section_key,
    title,
    subtitle,
    excerpt,
    description,
    text_align,
    background_color,
    is_active,
    sort_order,
    created_at,
    updated_at
)
SELECT
    id,
    'intro_text_section',
    'Intro Text Section',
    NULL,
    NULL,
    '<p>Add your intro text here.</p>',
    'left',
    'white',
    0,
    10,
    NOW(),
    NOW()
FROM blog_news
WHERE slug = @blog_slug;

INSERT INTO blog_news_sections (
    blog_news_id,
    section_key,
    title,
    subtitle,
    excerpt,
    description,
    text_align,
    background_color,
    is_active,
    sort_order,
    created_at,
    updated_at
)
SELECT
    id,
    'image_overlay_section',
    'Image Overlay Section',
    NULL,
    'Add overlay text here.',
    NULL,
    'center',
    'white',
    0,
    20,
    NOW(),
    NOW()
FROM blog_news
WHERE slug = @blog_slug;

INSERT INTO blog_news_sections (
    blog_news_id,
    section_key,
    title,
    subtitle,
    excerpt,
    description,
    text_align,
    background_color,
    is_active,
    sort_order,
    created_at,
    updated_at
)
SELECT
    id,
    'split_media_section',
    'Split Media Section',
    NULL,
    'Add split media text here.',
    NULL,
    'center',
    'white',
    0,
    30,
    NOW(),
    NOW()
FROM blog_news
WHERE slug = @blog_slug;

INSERT INTO blog_news_sections (
    blog_news_id,
    section_key,
    title,
    subtitle,
    excerpt,
    description,
    text_align,
    background_color,
    is_active,
    sort_order,
    created_at,
    updated_at
)
SELECT
    id,
    'split_media_reverse',
    'Split Media Reverse Section',
    NULL,
    'Add reverse split media text here.',
    NULL,
    'center',
    'white',
    0,
    40,
    NOW(),
    NOW()
FROM blog_news
WHERE slug = @blog_slug;

INSERT INTO blog_news_sections (
    blog_news_id,
    section_key,
    title,
    subtitle,
    excerpt,
    description,
    text_align,
    background_color,
    is_active,
    sort_order,
    created_at,
    updated_at
)
SELECT
    id,
    'two_images_section',
    'Two Images Section',
    NULL,
    NULL,
    '<p>Add two images section text here.</p>',
    'center',
    'white',
    0,
    50,
    NOW(),
    NOW()
FROM blog_news
WHERE slug = @blog_slug;

INSERT INTO blog_news_sections (
    blog_news_id,
    section_key,
    title,
    subtitle,
    excerpt,
    description,
    text_align,
    background_color,
    is_active,
    sort_order,
    created_at,
    updated_at
)
SELECT
    id,
    'two_images_reverse',
    'Two Images Reverse Section',
    NULL,
    NULL,
    '<p>Add reverse two images section text here.</p>',
    'center',
    'white',
    0,
    60,
    NOW(),
    NOW()
FROM blog_news
WHERE slug = @blog_slug;
