# Structured-data audit and implementation

## Audit findings before changes

Source audit found JSON-LD in six templates: home (WebSite, Organization, Hotel), blog detail (BlogPosting), spa landing (WebPage and Service), Holy River landing (WebPage), wedding (WebPage), and honeymoon landing (FAQPage corresponding to visible FAQs). No Microdata or RDFa was found in application templates. No duplicate BreadcrumbList emitters were found.

The homepage already uses stable production entity identifiers and the official name, address, contact details, logo and social URLs from `config/resort.php`. These profiles match the footer. Logo URL returned HTTP 200. No reliable geographic coordinates were found.

The shared blog template incorrectly described every article as about the Ayung River. Article image URL concatenation mishandled absolute URLs and storage-prefixed paths. Authors/publishers referenced an organization without sufficient local identification. Dates already came from CMS fields and were not overwritten with the current date.

Dining lacked restaurant markup. The dining landing page includes several distinct experiences and venues; they must not all be represented as the same restaurant. General opening hours and meal-service hours conflict, so hours were not inferred.

Accommodation, experience, offer, honeymoon-package, spa-treatment and dining-detail templates lacked BreadcrumbList despite genuine parent pages. Jungle Villas and FAQ lacked basic WebPage markup. Spa canonical construction relied on environment host configuration and could produce non-production references outside production.

## Changes

- Preserved homepage WebSite/Organization/Hotel graph; added Hotel → parentOrganization reference to the existing Organization ID.
- Preserved BlogPosting; restricted Ayung River subject to `ayung-river-ubud-guide`; normalized stored/absolute image URLs; added concise author/publisher organization identification with the same stable ID. Original publication date and modified timestamp remain sourced from CMS.
- Added Dining WebPage and a single Wild Ginger Restaurant entity only when its published CMS record exists. Uses resort address, contact details, CMS description/image/menu/reservation URLs and cuisine. Restaurant relates to Hotel and Organization. Bar, Afternoon Tea, Luxe High Tea and other experiences are not merged into this Restaurant.
- Added WebPage for Jungle Villas and FAQ. No FAQPage or QAPage was added to FAQ.
- Preserved spa WebPage/Service and wedding/Holy River WebPage. Production spa canonical constants are now independent of local host configuration.
- Retained existing honeymoon FAQPage: it describes visible answers; no rich-result promise is made.
- Added JSON-LD breadcrumbs to villa/suite details, Presidential Royal Suite, general experiences, Holy River experiences, offers, honeymoon packages, spa treatment details, blog articles and dining experience details. No homepage breadcrumbs or visible breadcrumb UI was added.
- No duplicate schema needed removal. New emitters are scoped to individual templates; no site-wide business graph was added.
- No Product, Offer, Event, Review, AggregateRating, fabricated prices, opening hours or coordinates were introduced.

## Validation

Local rendered HTML: homepage, Jungle Villas, Jungle View Villa, Ayung rafting experience, dining landing, spa landing, blog detail and FAQ returned HTTP 200. JSON-LD parsed correctly; representative main-page entity IDs were checked for duplicates. Unit tests cover breadcrumb positions, image handling, omitted unverified restaurant properties and script-safe encoding. Editorial feature tests and dining regression tests passed.

Schema.org Validator was run in code mode using markup collected from all eight representative local pages together. It reported no errors and no warnings. This is a combined semantic test, not eight independent URL-fetch tests.

Google Rich Results Test was run in code mode on the villa breadcrumb separately (one valid breadcrumb) and the combined representative markup (10 valid detected items, including three breadcrumbs and one article). Combining documents can cause Google to report multiple business/organization items; this does not mean those entities are repeated on each individual page.

The article has two non-critical Google warnings: datePublished is date-only and lacks a timezone. `blog_news.published_at` is a date field; inventing a publication time solely to suppress a warning would reduce accuracy. Keep the known date until an authoritative timestamp is available. Valid markup is not a guarantee that a search feature will appear.

WebPage and Service provide semantic information rather than dedicated Google rich-result eligibility. Google supports Breadcrumb and Article markup and LocalBusiness information subject to its policies. FAQ schema is not implemented as a rich-results tactic.

Legacy article image check: `/images/blog/01J2JHJ3CV1FV0WHBMZ5D696JS.jpg` returned 404 while `/storage/images/blog/01J2JHJ3CV1FV0WHBMZ5D696JS.jpg` returned 200. The normalizer preserves storage paths, including legacy `images/blog/...` records.

Production URL-mode validation must be repeated after deployment because local changes are not yet published.

## Confirmation needed

- Restaurant-specific daily opening hours: conflicting published values; omitted.
- Verified geographic coordinates and price ranges: omitted.
- Exact article publication time/timezone: unavailable for date-only publication records.
- Menu/reservation URLs remain CMS-driven; verify whenever those documents or booking channels change.

## Files modified for this task

- `app/Support/SeoStructuredData.php` (new)
- `app/Support/DiningStructuredData.php` (new)
- `app/Support/HomepageStructuredData.php`
- `app/Http/Controllers/DiningLandingController.php`
- `resources/views/components/seo/json-ld.blade.php` (new)
- `resources/views/pages/blog-news/show.blade.php`
- `resources/views/pages/dining-landing/index.blade.php`
- `resources/views/pages/dining-landing/experience.blade.php`
- `resources/views/pages/accommodations/jungle-villas.blade.php`
- `resources/views/pages/accommodations/show.blade.php`
- `resources/views/pages/accommodations/presidential-royal-suite.blade.php`
- `resources/views/pages/experiences/show.blade.php`
- `resources/views/pages/holy-river/show.blade.php`
- `resources/views/pages/offers/show.blade.php`
- `resources/views/pages/honeymoon/show.blade.php`
- `resources/views/pages/spa/show.blade.php`
- `resources/views/pages/spa-landing/index.blade.php`
- `resources/views/pages/spa-landing/show.blade.php`
- `resources/views/pages/faq.blade.php`
- `tests/Unit/SeoStructuredDataTest.php` (new)
- This audit document.

No database changes or migrations are needed. Visible page content, layouts and booking behavior were not changed by this task.
