# Content quality and factual consistency audit

## Scope and counts

Targeted review: **32 distinct pages**, consisting of 13 live core pages and 19 additional pages reviewed through their CMS copy. Live review covered public text, headings, selected operational claims and contact details; it was not a complete visual/device audit. **169 active records** were additionally screened for selected terminology/error patterns, not individually audited in full. The initial broad content output was truncated, so it is not counted as a complete editorial review.

Completed: **10 corrected CMS fields across nine records**. Unresolved: **10 management-confirmation issue groups**, listed below. No Blade templates, styles, layouts, slugs, prices, policies, images, testimonials, quotations, booking URLs, tables, fields or migrations were changed in this task.

Live pages reviewed (all returned HTTP 200):

- https://nandinibali.com/
- https://dining.nandinibali.com/
- https://spa.nandinibali.com/
- https://voucher.nandinibali.com/
- https://affiliate.nandinibali.com/
- https://nandinibali.com/faq
- https://nandinibali.com/about-us
- https://nandinibali.com/contact-us
- https://nandinibali.com/weddings
- https://nandinibali.com/honeymoon
- https://nandinibali.com/offers
- https://nandinibali.com/experiences
- https://nandinibali.com/blog-news

Additional CMS-based page reviews: all seven active accommodation details (Jungle View Villa, Sunrise View Villa, Panoramic Jungle View Villa, Private Garden Royal Suite, Jungle View Royal Suite, Panoramic Corner Jacuzzi Royal Suite, Presidential Royal Suite); all five active dining experience details (Wild Ginger Restaurant, Alang Bar & Lounge, Afternoon Tea in the Heart of the Jungle, World-Class Wine Cellar Selection, Romantic Experiences in the Heart of the Jungle); all four spa package details; the honeymoon package detail; legacy main-site dining page (`pages.id=4`) and Awards (`pages.id=9`). These 19 reviews concern stored text, not a claim that each live detail page was independently fetched. The Wine Cellar and Afternoon Tea live detail pages were also opened as corroborating sources and are not counted twice.

## Corrections completed locally

Relative URLs below are on https://nandinibali.com unless another host is shown. Changes await application of the SQL on shared hosting.

| Page URL | Issue | Original text/value | Corrected text/value | Action | Status |
| --- | --- | --- | --- | --- | --- |
| / and /jungle-villas/panoramic-jungle-view-villa | Generic excerpt duplicated the accommodation overview | “Stay in private jungle villas in Ubud and spacious royal suites…” | Villa-specific river valley views, 48 sqm, canopy bed, private balcony, Balinese architecture and teak flooring | `accommodations.id=1.excerpt` | Corrected locally |
| /jungle-villas/panoramic-jungle-view-villa | Awkward landscape phrase | “rainforest center” | “rainforest landscape” | `accommodations.id=1.description` | Corrected locally |
| /jungle-villas/sunrise-view-villa | Article error and repetitive opening | “Featuring a breathtaking golden sunshine…” / “Featuring a wide…” | “With golden sunshine…” / “With a wide…” | `accommodations.id=3.description` | Corrected locally |
| /the-royal-suites/jungle-view-royal-suite | Subject/verb agreement | “The Jungle View Royal Suite are…” | “The Jungle View Royal Suite is…” | `accommodations.id=5.description` | Corrected locally |
| /the-royal-suites/private-garden-royal-suite | Several clear mistranslations | “Suite are professionally illustrated…”, “prosperous suites”, “rampant…grassland panorama”, “squirrels of birds…” | “Suite is thoughtfully designed and furnished”, “spacious suites”, “green garden panorama”, “sound of birds singing” | `accommodations.id=7.description` | Corrected locally |
| /the-royal-suites/panoramic-corner-jacuzzi-royal-suite | Missing preposition | “verdant jungle Ubud, Bali” | “verdant jungle in Ubud, Bali” | `accommodations.id=6.description` | Corrected locally |
| https://dining.nandinibali.com/experiences/world-class-wine-cellar-selection | Missing possessive/connection in opening | “Nandini Jungle by Hanging Gardens meticulously curated wine collection” | “The meticulously curated wine collection at Nandini Jungle by Hanging Gardens” | `dining_experiences.id=52.description` | Corrected locally |
| https://spa.nandinibali.com/spa-wellness/a-mystical-journey-at-nandini-4d3n-wellness-retreat | Venue naming and agreement | “A’Lang Bar”, “jungle and…River meets”, farewell to “Nandini Jungle Resort” | “Alang Bar”, “jungle and…River meet”, farewell to “Nandini Jungle” | `spas.id=4.description` | Corrected locally |
| /awards | Grammar | “both in the Indonesia and Internationally” | “in Indonesia and internationally” | `pages.id=9.description` | Corrected locally; awards retained |
| /experiences | River capitalization and missing final punctuation | “Ayung river”; sentence ending without a full stop | “Ayung River”; full stop added | `pages.id=5.description` | Corrected locally |

The Panoramic excerpt facts are already present in the accommodation description and active feature records. Its updated shared excerpt also improves any existing cards that consume that record; the page-specific Jungle Villas listing copy remains unchanged.

## Factual issues requiring management confirmation — unchanged

| Page URL | Issue | Original text/value | Corrected text/value | Action | Status |
| --- | --- | --- | --- | --- | --- |
| https://dining.nandinibali.com/ | General hours vs dinner service | General 07:00–22:00; dinner 18:30–22:30 | Pending venue/department clarification | Confirm whether closing times refer to different services | Unresolved |
| / and dining Afternoon Tea | Guest access may differ by product | Homepage “offered exclusively to our cherished guests”; dining allows outside guests generally | Pending tea-specific policy | Confirm Afternoon Tea access separately from restaurant access | Unresolved |
| Dining Afternoon Tea detail/card | Image labels identify a different product | “Luxe High Tea in Ubud…” on Afternoon Tea image alt fields | Pending asset-subject confirmation | Verify actual image/product assignment before changing labels or assets | Unresolved |
| https://spa.nandinibali.com/ | Outside-guest policy not verified | No authoritative published access policy found | No new policy published | Confirm spa-specific access; do not copy dining policy | Unresolved |
| Contact/booking engine | Postal-code discrepancy | Verified resort address 80571; booking-engine About page previously showed 80572 | Retain user-verified 80571 in the website | Management to reconcile booking-engine address | Unresolved |
| Accommodation overview | Inventory counts need current confirmation | `pages.id=3`: 18 Jungle Villas and 16 Royal Suites | Unchanged | Confirm current physical inventory, not number of room categories | Unresolved |
| Signature Spa on the River across channels | Different amounts may be channel-specific | Holy River detail previously 4,000,000++; live voucher listing 4,400,000++ per person | Unchanged | Confirm whether voucher and direct experience amounts/inclusions intentionally differ | Unresolved |
| /contact-us | Reservations availability claim | “Reservations team…operating 24/7” | Unchanged | Confirm reservations staffing; 24-hour front desk does not prove 24-hour reservations service | Unresolved |
| Presidential Royal Suite | Comparative accommodation-size claim | “One of the largest suites in Bali” | Unchanged | Obtain evidence before retaining/changing comparative claim | Unresolved |
| /offers and local offer records | Stay Longer Save More inclusions differ | Live copy mentions complimentary 60-minute massage for two; local copy emphasises minibar beyond five nights | Unchanged | Confirm current live rate inclusions and reconcile CMS/environment versions | Unresolved |

Unknown extra-bed fees, child age bands and rate-specific cancellation deadlines were not guessed. They remain outside verified scope. Superseded blog offer references were screened but not rewritten without publication context and approved current terms.

## Existing content verified or preserved

| Page URL | Issue | Original text/value | Corrected text/value | Action | Status |
| --- | --- | --- | --- | --- | --- |
| /faq, /contact-us, shared footer | Resort location/address | Banjar Susut, Desa Buahan, Payangan, Bali 80571, Indonesia; greater Ubud | Unchanged | Cross-checked against supplied official identity | Consistent in reviewed sources |
| Shared footer, dining/spa public pages | Contact channels | Resort +62 361 89 83 111; reservations +62 812 3687 1170; reservation@nandinibali.com | Unchanged | Preserved distinct departmental phone/email roles | Consistent in reviewed sources |
| /faq | Check-in/out | 14:00 / 12:00 | Unchanged | Matches official booking-engine About policy reviewed in FAQ task | Verified source retained |
| Three Jungle Villa details | Core amenities | 48 sqm, king-size bed, balcony, outdoor bathtub/rain shower; two adults and one child listed in features | Unchanged | Checked accommodation feature records | Verified CMS facts; no private-pool claim added |
| https://dining.nandinibali.com/ | Outside dining guests | Welcome; advance reservation recommended | Unchanged | Verified live published restaurant policy | Retained; does not establish spa or tea-specific access |
| https://spa.nandinibali.com/ | Spa hours/booking | 09:00–22:00; advance booking recommended | Unchanged | Read current live spa page | Verified as published, not an operational management attestation |
| Honeymoon package/voucher catalogue | Distinct tea products | One-time Luxe High Tea and daily Afternoon Tea are separately described | Unchanged | Kept separate product terminology | Distinction preserved |
| /weddings and /honeymoon | Occasion offerings | Chapel/riverside ceremonies and honeymoon packages | Unchanged | Read published content | No dates, prices or capacities invented |
| https://affiliate.nandinibali.com/ | Program content | Public Partner Circle page accessible | Unchanged | Read public landing-page copy | Language reviewed; financial terms not independently certified |

## Not fully audited / limitations

- Blog/news detail bodies, all individual experience details, offer details, voucher product details and authentication/private account pages were not exhaustively reviewed.
- A targeted database pattern scan covered active rows in pages (33), accommodations (7), experiences (31), spas (4), dining experiences (5), offers (5), blog/news (38), honeymoons (1) and vouchers (45): 169 total. It cannot prove absence of other spelling or factual errors.
- Private affiliate/member dashboards, checkout/payment screens and account data were not accessed. Testimonials, reviews and direct quotations were not edited.
- Afternoon Tea image subject was not reliably verified; its labels were deliberately not changed.
- Live content and local CMS differ in several places. Guarded SQL replaces only matching original phrases; it does not overwrite different live content or policies.
- No audited public core URL was inaccessible in the final direct-fetch pass. The web reader initially could not fetch the affiliate site; a direct public HTTP request succeeded.

## Files / records and validation

Created `database/shared-hosting/2026-10-14_content_quality_corrections.sql` and this report. Updated local `accommodations` records 1, 3, 5, 6, 7; `dining_experiences` record 52; `spas` record 4; `pages` records 5 and 9. Only description/excerpt fields and update timestamps changed. Protected-field comparison confirmed that metadata, slugs, images, booking URLs, room codes, pricing and other fields remained unchanged.

Deployment is pending. Run the SQL against the existing shared-hosting database after a normal database backup; it is rerunnable and does not create or delete records.
