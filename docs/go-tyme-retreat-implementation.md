# Go Tyme Group retreat article

Implemented in the existing Laravel BlogNews system. No packages, admin framework,
commits, pushes or deployments were added. The local article was imported on
2 November 2026; publication dates are set to the actual import date on each
installation, separately from the 4–9 October retreat dates.

Preview: http://nandinibali.test/blog/go-tyme-group-retreat-bali-2026

Canonical: https://nandinibali.com/blog/go-tyme-group-retreat-bali-2026

## Shared-hosting SQL import

Import `database/shared-hosting/2026-11-02_go_tyme_group_retreat.sql` through
phpMyAdmin with the site's database selected. Upload the updated Laravel files,
the image dimensions manifest, the built assets and original/optimized images
first. Header rendering is a code change, not a database setting.

The SQL matches the content migration: it creates an active article, eight
sections and 89 gallery photos if the slug is missing, using the actual import
date in Bali. An existing article is left unchanged, including its publication
permissions. The final query reports the imported counts. It was verified on
connection-local temporary copies of the local MySQL tables for correct content,
repeated imports, preservation of existing edits/drafts and compatibility with
`NO_BACKSLASH_ESCAPES`. No permanent tables were changed during verification.

The former `/blog-news/go-tyme-group-retreat-bali-2026` URL redirects permanently
to the canonical path for a published record. Inactive and scheduled records stay
out of public article responses, listings and sitemaps. Existing signed previews
work and carry `noindex, nofollow`. The migration does not overwrite an existing
record or its publication settings; rollback intentionally retains editorial data.

## Files created

- `database/content/go-tyme-retreat.php`: chronological editorial copy, captions,
  subject-based selection, descriptive alt text and section featured selections.
- `database/content/go-tyme-image-dimensions.php`: generated original dimensions.
- `database/migrations/2026_11_02_000001_create_go_tyme_retreat_article.php`:
  imports the article, eight sections and 89 section photographs transactionally.
- `resources/views/components/blog/retreat-story.blade.php`: editorial layout
  inside the existing article template, breadcrumbs and gallery markup.
- `resources/views/components/blog/retreat-photo.blade.php`: responsive images
  with intrinsic dimensions and direct original-image fallback links.
- `resources/css/retreat-article.css`: scoped responsive galleries and modal.
- `resources/js/retreat-lightbox.js`: dependency-free modal navigation.
- `scripts/optimize-go-tyme.php`: reproducible local GD optimizer.
- `public/images/go-tyme-images/optimized/`: 188 new WebP derivatives.
- `tests/Feature/GoTymeRetreatArticleTest.php`: route, SEO, publication, discovery,
  asset integrity and compatibility checks.
- `tests/Frontend/retreat-lightbox.test.mjs`: interaction-logic checks.
- `docs/go-tyme-retreat-implementation.md`: this report.

## Files modified

- `app/Models/BlogNews.php`: article path and URL helpers.
- `app/Http/Controllers/BlogController.php`: canonical redirect after publication
  checks, preserving the existing signed preview and publication flow.
- `app/Services/SitemapService.php`: uses the article's canonical path.
- `routes/web.php`: requested `/blog/` route using the existing controller.
- `resources/views/pages/blog-news/show.blade.php`: scoped editorial body,
  canonical/social image handling and preview robots metadata.
- `resources/views/components/sections/item-list.blade.php`: article link, local
  card photograph and publication date in the existing blog listing.
- `resources/views/components/sections/item-carousel.blade.php`: local article
  image and canonical link in existing related-article carousels.
- `vite.config.js`: page-specific CSS and JavaScript entry points.
- `tests/Feature/BlogNewsPublicationServiceTest.php`: isolates publication-count
  fixtures from articles installed by content migrations.

The existing navbar, footer, global typography rules and other page templates were
not modified. The article uses Span headings, Inter body text, slate text, gold
accents, the existing body type scale and shared buttons.

The retreat now uses the existing blog detail image hero, title, subtitle,
publication/author and sharing section directly. The custom journal heading and
dark header spacer were removed. The hero remains part of the 90-photo lightbox;
the shared hero component controls its responsive framing, as on other articles.

## Photography and performance

90 distinct photographs were visually selected from 92 local WebP originals.
`dsc08537` and `dsc08619` were omitted as near-duplicates. The hero is `dsc08615`;
89 other photographs are organized by their visible subjects. Captions distinguish
subject grouping from verified shooting dates. Undocumented itinerary steps are
described as planned, with no invented testimonials or personal outcomes.

All originals remain unchanged. Each selected image has 480px and 960px versions;
the hero and seven section features also have 1600px versions. All preserve aspect
ratio. `srcset`, `sizes`, width and height are supplied. Only the hero is eager;
the other 89 article images are lazy. Original-resolution photos are requested
when selected in the lightbox, or through direct links when JavaScript is absent.

The selected originals total 35,665,690 bytes. All 188 derivatives together total
20,734,602 bytes; the browser requests the appropriate size, not every variant.
Galleries use two columns on phones, three on tablets and four on wide screens.
Native modal dialog supplies focus containment and Escape dismissal. Buttons,
arrow keys and horizontal touch swipes navigate; closing returns focus to the
selected photo. These browser behaviors still need visual confirmation below.

## SEO

The supplied SEO title and meta description are installed, along with a production
canonical, Open Graph and Twitter metadata, a featured image, one H1, nested H2s,
BlogPosting JSON-LD, breadcrumb JSON-LD and internal links to existing dining,
Holy River, experiences and contact pages. Public articles remain indexable.

The brief description of canang as a daily Balinese Hindu offering expressing
gratitude was checked against [SAKA Museum](https://www.sakamuseum.org/en/subaktranslation/interactive-canang-sari)
and [Indonesia Travel](https://www.indonesia.travel/gb/en/travel-ideas/heritage/discover-the-cultural-heritage-of-bali).
No external photographs were used.

## Verification

- Article HTTP response: 200 on the configured local domain.
- All 278 selected original/derivative image URLs: successful HEAD responses with
  `image/webp` content type.
- Laravel feature and SEO tests: passed, including public/private behavior,
  preview signatures, canonical redirect, discovery, existing article rendering,
  intrinsic proportions and migration idempotence.
- Node interaction tests: 3 passed, covering deferred loading, previous/next wrap,
  keyboard navigation, close/focus restoration, swipes and fallback links.
- `npm run build`: passed. Existing Span font paths produce Vite resolution
  warnings, but both font files exist in `public/fonts` for runtime serving.
- `php artisan view:cache`: compiled successfully.
- `git diff --check`: passed.

Manual review remains: desktop/mobile appearance, actual hero and gallery display,
native Escape/focus trapping and touch browsing in a real browser. Computer Use
was unable to verify Chrome's current URL and stopped; no browser visual checks
are claimed. No production content or hosting was changed.
