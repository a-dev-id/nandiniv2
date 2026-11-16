# Structured data implementation — 10 November 2026

Implemented in the existing Laravel application. Main, Dining and Spa sources are all present. Changes are limited to server-rendered JSON-LD generators, their Blade emitters, tests and this report. No visible content, styles, canonical tags, routes, navigation, CTAs, booking behavior, robots, sitemap or analytics configuration changed. No dependencies or database changes were added.

## Page report

| Page | Before | After | Remarks |
|---|---|---|---|
| Homepage | WebSite, Organization, Hotel | Preserved | Existing configured name, address, telephone, email, image, logo and social profiles retained. Hotel ID is `https://nandinibali.com/#hotel`. No unsupported coordinates, amenities or prices added. |
| Jungle Villas | WebPage | WebPage retained; ItemList and HotelRoom nodes added | Existing page ID preserved. Local database renders three villa categories, with CMS descriptions, images, beds, occupancy and active feature labels where supplied. |
| Royal Suites | Missing schema | CollectionPage, ItemList, Suite, BreadcrumbList | Local database renders three suites. Uses exactly the filtered carousel collection; Presidential Royal Suite is excluded from that collection by existing presentation logic. Each room links to its official detail URL and the same hotel ID. |
| Offers | Missing schema | CollectionPage and ItemList | Names, positions and canonical detail URLs follow the published collection and its displayed order, including the existing 99-item display limit. |
| Offer Details | BreadcrumbList | BreadcrumbList preserved; WebPage and Offer added | CMS description carries real inclusions and conditions. Explicit reservation destinations can produce ReserveAction. Tested multiple offers, missing fields, inactive/future/expired records and both date boundaries. |
| Dining | Restaurant's hotel reference lacked a type, causing the reported error | Restaurant → containedInPlace now has Hotel type, ID and official name | Existing Wild Ginger name, description, address, menu, cuisine and reservation information preserved. External Validator confirmation remains pending. |
| Spa | WebPage and Service | Preserved | Existing Essence Spa service already references the hotel as provider. No additional unsupported treatment entities introduced. |

The supplied audit described some pages as WebPage/CreativeWork. The source explicitly declares WebPage; validators can also display its CreativeWork superclass. No duplicate CreativeWork was added or removed.

## Representation choices

- Reuse `SeoStructuredData`, `config/resort.php`, loaded accommodation relationships, the published offer collection and the existing script-safe `x-seo.json-ld` component.
- Use one consistent hotel identity. Typed hotel references describe that identity; they do not create another hotel with a different ID.
- Suites use Suite; villa categories use HotelRoom. Accommodation images are normalized and deduplicated. Bed descriptions and occupancy are preserved verbatim rather than guessing bed counts or maximum occupancy. Optional properties are omitted when empty.
- Offers are identifiable CMS promotions. Full descriptions preserve the supplied inclusion and condition text with HTML removed and spacing normalized. No arbitrary price, currency, availability, booking-validity date or minimum stay is generated.
- The offer form explicitly says `valid_start_date`/`valid_end_date` determine when an offer appears. These are used for publication eligibility, not relabeled as booking or stay dates. `booking_nights` is a booking URL default, not evidence of a minimum stay.
- ReserveAction uses explicit booking information. It avoids the model's legacy fallback URL and member-specific voucher links; arbitrary custom enquiry/page CTAs are not marked as reservations.
- Homepage and Spa already contain supported entity information and relationships; their working graphs are unchanged.

Property compatibility was reviewed against the official [HotelRoom](https://schema.org/HotelRoom), [Suite](https://schema.org/Suite), [Offer](https://schema.org/Offer), [containedInPlace](https://schema.org/containedInPlace) and [value](https://schema.org/value) definitions. `containedInPlace` expects Place, which Hotel satisfies. QuantitativeValue permits text values; preserving the CMS occupancy wording avoids inventing a numeric maximum.

## Validation performed

- Rendered actual local database pages through Laravel's HTTP kernel: homepage, Jungle Villas, Royal Suites, Offers, Dining, Spa, `/offer/a-journey-within-solo-renewal` and `/offer/last-minute-deal`. All eight returned HTTP 200. Every emitted JSON-LD block parsed with `JSON_THROW_ON_ERROR`, and all top-level node IDs were unique within each page.
- Actual local output: homepage WebSite/Organization/Hotel; villas WebPage/ItemList/three HotelRoom nodes; suites CollectionPage/ItemList/three Suite nodes/BreadcrumbList; offers CollectionPage/ItemList; each offer detail WebPage/Offer/BreadcrumbList; Dining WebPage/Restaurant; Spa WebPage/Service.
- Focused tests: `php artisan test --compact --filter='SeoStructuredDataTest|StructuredDataPagesTest|HomepageSubtitleTest|OfferPublicationServiceTest'` — **11 passed, 166 assertions**.
- Coverage includes typed hotel identity matching the homepage, script-closing-tag escaping, image normalization, gallery images, real feature labels, optional fields, empty collections, hidden accommodation records, exact suite filtering, ordered listing URLs, the offer display limit, two offer details, publication boundaries and redirects for unpublished/future/expired offers.
- Broader regression run (before the additional empty-collection/display-limit test): **56 passed, 4 failed**. The failures are existing expectations in `DiningLandingTest.php:56` and `SpaSiteDomainTest.php:94`, `:193`, `:604`. They concern Dining's video autoplay query and Spa canonical/entity-ID expectations. A separate run with the original HEAD Dining schema generator reproduced all four failures (46 passed, 4 failed); Spa templates/controllers were unchanged. No frontend changes were made to satisfy these unrelated expectations.
- Laravel Pint passed for all six changed/new PHP files. `git diff --check` passed.

## Incomplete checks and remaining issues

- The public Schema.org Validator page was reachable through web retrieval, but interactive browser access failed with **“The browser access host is unavailable.”** No external validation result is claimed. Repeat independent code-mode validation for each affected page when browser access is available, and production URL validation after deployment.
- Local source changes are not deployed. Production behavior and zero external validation errors remain unconfirmed.
- The four pre-existing regression failures above remain unresolved within this structured-data-only scope.
- CMS descriptions remain the authority for promotion conditions. No natural-language date extraction or invented restrictions were introduced; editorial accuracy of CMS content remains necessary.
- Valid schema does not guarantee ChatGPT recommendations, AI-search inclusion or Google rich results.

## Modified files

- `app/Support/SeoStructuredData.php` — shared hotel reference, optional parent breadcrumbs, text normalization and collection graph construction.
- `app/Support/DiningStructuredData.php` — typed containing-hotel reference.
- `app/Support/AccommodationStructuredData.php` — new accommodation collection/room generator.
- `app/Support/OfferStructuredData.php` — new offer listing/detail generator.
- `resources/views/pages/accommodations/jungle-villas.blade.php` — emit enhanced graph.
- `resources/views/pages/accommodations/the-royal-suite.blade.php` — emit suite graph using the visible collection.
- `resources/views/pages/offers/index.blade.php` — emit offers listing graph.
- `resources/views/pages/offers/show.blade.php` — emit offer graph alongside existing breadcrumbs.
- `tests/Unit/SeoStructuredDataTest.php` — verify containing hotel matches homepage identity.
- `tests/Feature/StructuredDataPagesTest.php` — rendered-page regression and edge-case coverage.
- `docs/structured-data-implementation-2026-11-10.md` — this report.
