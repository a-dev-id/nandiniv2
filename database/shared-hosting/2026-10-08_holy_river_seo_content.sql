-- Holy River SEO/content update for shared hosting.
-- Idempotent: prices, slugs, URLs, and booking records are not changed.
START TRANSACTION;

SET @page_id := (
    SELECT id FROM pages
    WHERE page_name = 'Holy River Page' OR slug = 'holy-river'
    ORDER BY CASE WHEN page_name = 'Holy River Page' THEN 0 ELSE 1 END
    LIMIT 1
);

UPDATE pages SET
    title = 'Sacred Holy River & Balinese Purification in Ubud',
    subtitle = 'A SPIRITUAL JOURNEY BY THE AYUNG RIVER',
    excerpt = CONCAT(
        'Along the Ayung River below Nandini Jungle by Hanging Gardens, guests can discover a quieter side of Balinese spirituality through sacred water, traditional blessings and moments of reflection surrounded by the jungle.',
        CHAR(10), CHAR(10),
        'The Holy River experience is centred on Balinese purification traditions, including Melukat, a ritual associated with spiritual cleansing and renewal. Guided experiences by the river offer guests an opportunity to connect with Balinese culture and the natural setting of Nandini in Payangan, within the greater Ubud area.'
    ),
    description = '<p>Along the Ayung River below Nandini Jungle by Hanging Gardens, guests can discover a quieter side of Balinese spirituality through sacred water, traditional blessings and moments of reflection surrounded by the jungle.</p><p>The Holy River experience is centred on Balinese purification traditions, including Melukat, a ritual associated with spiritual cleansing and renewal. Guided experiences by the river offer guests an opportunity to connect with Balinese culture and the natural setting of Nandini in Payangan, within the greater Ubud area.</p>',
    hero_image_alt = 'Balinese priest preparing a blessing ritual at Nandini Jungle',
    hero_mobile_image_alt = 'Balinese priest preparing a blessing ritual at Nandini Jungle',
    meta_title = 'Balinese Purification & Holy River in Ubud | Nandini Jungle',
    meta_description = 'Experience a Balinese purification ritual by the sacred Ayung River at Nandini Jungle in Ubud, with Melukat, traditional blessings and riverside experiences.',
    updated_at = NOW()
WHERE id = @page_id;

UPDATE page_sections SET
    title = 'THE MEANING OF MELUKAT', subtitle = 'BALINESE PURIFICATION', excerpt = NULL,
    description = '<p>Melukat is a Balinese purification ritual associated with cleansing, renewal and spiritual reflection. At Nandini, the Holy River setting offers guests the opportunity to experience this tradition beside the Ayung River in a peaceful jungle environment.</p>',
    button_label = NULL, button_url = NULL, button_route = NULL,
    sort_order = 1, updated_at = NOW()
WHERE id = 8 AND page_id = @page_id;

UPDATE page_sections SET
    title = 'A SACRED SETTING BY THE AYUNG RIVER', subtitle = 'THE AYUNG RIVER', excerpt = NULL,
    description = '<p>Descend through Nandini''s tropical landscape to the banks of the Ayung River, where the sound of flowing water and surrounding jungle create a peaceful setting for reflection, Balinese rituals and meaningful moments in nature.</p>',
    button_label = NULL, button_url = NULL, button_route = NULL,
    sort_order = 2, updated_at = NOW()
WHERE id = 9 AND page_id = @page_id;

UPDATE page_sections SET
    title = 'HOLY RIVER EXPERIENCES', subtitle = 'SACRED RITUALS BY THE RIVER', excerpt = NULL,
    description = '<p>Discover experiences that bring together the Ayung River setting and Balinese tradition, from Melukat purification and blessing rituals to a half-day journey that pairs the ceremony with a riverside spa treatment.</p>',
    button_label = NULL, button_url = NULL, button_route = NULL,
    sort_order = 3, updated_at = NOW()
WHERE id = 11 AND page_id = @page_id;

UPDATE page_sections SET
    title = 'BALINESE BLESSING & PURIFICATION', subtitle = 'BALINESE TRADITION', excerpt = NULL,
    description = '<p>The Balinese Blessing Purification at the Holy River is led by a Pemangku beside the Ayung River. The experience includes a traditional Balinese sarong, towels and a welcome drink, with advance reservation recommended.</p>',
    button_label = 'MORE DETAILS', button_link_type = 'manual',
    button_url = '/holy-river/balinese-blessing-purification-at-the-holy-river', button_route = NULL,
    sort_order = 4, updated_at = NOW()
WHERE id = 10 AND page_id = @page_id;

UPDATE page_sections SET
    title = 'SPA ON THE RIVER', subtitle = 'RIVERSIDE WELLNESS', excerpt = NULL,
    description = '<p>Nandini''s 180-minute Spa on the River experience begins with a soothing foot bath before a signature treatment beside the Ayung River. Explore the dedicated Essence Spa site for treatments and spa enquiries.</p>',
    button_label = 'EXPLORE ESSENCE SPA', button_link_type = 'manual',
    button_url = 'https://spa.nandinibali.com/', button_route = NULL,
    sort_order = 5, updated_at = NOW()
WHERE id = 12 AND page_id = @page_id;

UPDATE page_section_images SET
    image_alt = 'Ayung River flowing through the tropical jungle at Nandini Jungle',
    mobile_image_alt = 'Ayung River flowing through the tropical jungle at Nandini Jungle', updated_at = NOW()
WHERE page_section_id = 8 AND is_active = 1;

UPDATE page_section_images SET
    image_alt = 'Sacred river setting surrounded by tropical jungle',
    mobile_image_alt = 'Sacred river setting surrounded by tropical jungle', updated_at = NOW()
WHERE page_section_id = 9 AND is_active = 1;

UPDATE page_section_images SET
    image_alt = 'Balinese blessing experience in Nandini Jungle''s riverside setting',
    mobile_image_alt = 'Balinese blessing experience in Nandini Jungle''s riverside setting', updated_at = NOW()
WHERE page_section_id = 10 AND is_active = 1;

UPDATE page_section_images SET
    image_alt = 'Spa on the River setting beside the Ayung River at Nandini Jungle',
    mobile_image_alt = 'Spa on the River setting beside the Ayung River at Nandini Jungle', updated_at = NOW()
WHERE page_section_id = 12 AND is_active = 1;

UPDATE experiences SET
    title = 'Sacred Waters: Half-Day Ubud Healing Retreat',
    excerpt = 'A half-day riverside experience combining Nandini''s signature Spa on the River treatment with Melukat purification and a Balinese blessing led by a traditional priest.',
    description = '<p>A half-day experience beside the Ayung River combining Nandini''s signature Spa on the River treatment with Melukat purification and a Balinese blessing led by a traditional priest.</p><p><strong>Inclusions:</strong><br>Traditional Balinese sarong and towels<br>Melukat purification and blessing ceremony by the river<br>Signature Spa on the River treatment for two<br>60-minute Exotic Balinese massage<br>30-minute body mask<br>30-minute body scrub treatment<br>Tea for two at the spa reception</p>',
    image_alt = 'Sacred Waters purification and blessing experience beside the Ayung River',
    card_image_alt = 'Sacred Waters Holy River experience at Nandini Jungle',
    meta_title = 'Sacred Waters & Melukat Experience in Ubud | Nandini Jungle',
    meta_description = 'Discover a half-day Holy River experience at Nandini Jungle combining Melukat purification, a Balinese blessing and a Spa on the River treatment.',
    updated_at = NOW()
WHERE slug = 'sacred-waters-half-day-ubud-healing-retreat';

UPDATE experiences SET
    title = 'Balinese Blessing Purification at the Holy River',
    excerpt = 'A Balinese purification and blessing experience beside the Ayung River, led by a Pemangku and accompanied by traditional sarong, towels and a welcome drink.',
    description = '<p>Experience a Balinese purification and blessing ritual beside the Ayung River, led by a Pemangku in Nandini''s peaceful jungle setting.</p><p><strong>Inclusions:</strong><br>Healthy welcome drink on arrival<br>Traditional Balinese sarong and towels<br>Balinese purification and blessing ritual led by a Pemangku</p>',
    image_alt = 'Balinese blessing and purification ritual beside the Ayung River',
    card_image_alt = 'Balinese Blessing Purification at the Holy River',
    meta_title = 'Balinese Blessing & Purification in Ubud | Nandini Jungle',
    meta_description = 'Experience a Balinese purification and blessing ritual led by a Pemangku beside the Ayung River at Nandini Jungle in Ubud.',
    updated_at = NOW()
WHERE slug = 'balinese-blessing-purification-at-the-holy-river';

UPDATE experiences SET
    title = 'Nandini Signature: Spa on the River',
    excerpt = 'A 180-minute Spa on the River experience beside the Ayung River, beginning with a soothing foot bath and continuing with Nandini''s signature riverside treatment.',
    description = '<p>A 180-minute Spa on the River experience beside the Ayung River, beginning with a soothing foot bath and continuing with Nandini''s signature riverside treatment.</p><p><strong>Inclusions:</strong><br>Soothing foot bath<br>60-minute Exotic Balinese massage<br>30-minute body mask<br>30-minute body scrub treatment<br>60-minute facial</p>',
    image_alt = 'Spa on the River treatment beside the Ayung River at Nandini Jungle',
    card_image_alt = 'Nandini Signature Spa on the River experience',
    meta_title = 'Nandini Signature Spa on the River | Nandini Jungle',
    meta_description = 'Discover Nandini''s 180-minute Spa on the River experience in a peaceful riverside setting beside the Ayung River.',
    updated_at = NOW()
WHERE slug = 'nandini-signature-spa-on-the-river';

INSERT INTO migrations (id, migration, batch)
SELECT COALESCE((SELECT MAX(m.id) FROM migrations AS m), 0) + 1,
       '2026_10_08_000004_reposition_holy_river_page',
       COALESCE((SELECT MAX(m.batch) FROM migrations AS m), 0) + 1
WHERE NOT EXISTS (
    SELECT 1 FROM migrations
    WHERE migration = '2026_10_08_000004_reposition_holy_river_page'
);

COMMIT;

-- Verification queries.
SELECT id, slug, title, subtitle, meta_title, meta_description
FROM pages WHERE id = @page_id;

SELECT id, sort_order, section_key, subtitle, title, button_label, button_url
FROM page_sections WHERE page_id = @page_id ORDER BY sort_order, id;

SELECT id, slug, title, duration, meta_title
FROM experiences
WHERE slug IN (
    'sacred-waters-half-day-ubud-healing-retreat',
    'balinese-blessing-purification-at-the-holy-river',
    'nandini-signature-spa-on-the-river'
)
ORDER BY sort_order, id;

SELECT ep.experience_id, e.slug, ep.label, ep.price, ep.currency,
       ep.price_type, ep.unit_type, ep.is_active
FROM experience_prices AS ep
INNER JOIN experiences AS e ON e.id = ep.experience_id
WHERE e.slug IN (
    'sacred-waters-half-day-ubud-healing-retreat',
    'balinese-blessing-purification-at-the-holy-river',
    'nandini-signature-spa-on-the-river'
)
ORDER BY ep.experience_id, ep.sort_order, ep.id;
