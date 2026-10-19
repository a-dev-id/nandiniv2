# FAQ content review

Updated CMS records: `pages.id=25` (FAQ Page, slug `frequently-asked-questions`) and `page_sections.id=61`. The dedicated `/faq` route selects page 25 independently of its stored slug. No new records, fields, components, or schema markup were added. Hero fields and H1 were preserved. Deploy `database/shared-hosting/2026-10-14_faq_content.sql` to apply the same content on shared hosting.

## Review of the original 20 questions

| Original question | Decision |
| --- | --- |
| 1. Resort identity and location | Retained; precise address and greater Ubud location |
| 2. Why choose Nandini over other resorts? | Removed promotional comparison |
| 3. Ubud town centre and transport | Retained; approximate 30–40 minutes, traffic dependent |
| 4. Airport journey | Retained; approximate 90 minutes, transfer question separated |
| 5. Best time of year | Removed generic travel advice |
| 6. Room types and outdoor spaces | Retained; corrected links and removed unsupported size record |
| 7. Spa, yoga and meditation | Retained; removed unverified daily frequency |
| 8. Cooking classes | Merged into resort activities |
| 9. Families and couples | Retained children question; honeymoon covered separately |
| 10. Sustainability | Removed from FAQ to prioritise practical stay information; sustainability page unchanged |
| 11. Limited mobility | Retained hillside/stairs warning |
| 12. Current packages and newsletters | Removed; current terms belong to selected offers; newsletter claim not repeated |
| 13. Transfers across Bali | Merged into transport guidance without blanket service promise |
| 14. Weddings and events | Retained weddings/intimate ceremonies question |
| 15. Electrical adapters | Removed generic travel advice |
| 16. Personalised itinerary | Merged into arranging activities |
| 17. Things to see in Bali | Removed generic travel advice |
| 18. Travel safety | Removed broad safety assurance |
| 19. Special dining | Incorporated into dining options/reservations |
| 20. Combining Bali destinations | Removed repetitive itinerary promotion |

New or separated questions cover airport transfers, scheduled shuttle, check-in/out, private-pool clarification, direct reservations, cancellation/modification terms, restaurant access for outside guests, advance dining/spa reservations, activity reservations, and honeymoon experiences. The final page has 21 questions in four groups.

## Factual sources

- Official booking engine: https://nandinijunglebyhanginggardens.reserve-online.net/about — check-in 14:00, check-out 12:00, airport/port transfers, accommodation categories. The displayed postal code is 80572, conflicting with the user-verified resort address (80571); the FAQ uses the explicitly verified address. Management should reconcile the booking engine separately.
- Existing FAQ: https://nandinibali.com/faq — travel-time estimates and mobility information retained with qualifications.
- Local accommodation features — size, bed, occupancy, balcony and outdoor bathtub/rain shower; no private-pool claim for the three Jungle Villas.
- Active CMS offers — scheduled Ubud shuttle and non-refundable terms on selected offers; inclusions and terms are not generalised to all bookings.
- Dining site: https://dining.nandinibali.com/ — outside guests welcome, advance reservations recommended, dining products. Luxe High Tea and Afternoon Tea remain distinct.
- Spa information bar component: `resources/views/components/spa-landing/information-bar.blade.php` — advance booking recommended. Existing spa/experience content confirms treatment categories, yoga and meditation.
- Weddings, honeymoon, experiences and contact routes and pages confirm their respective purposes.

## Management confirmation still required

- Spa access for outside guests: no verified policy found; question excluded.
- Extra-bed permission, additional-guest limits, child age bands and associated charges: no verified general policy; extra-bed question excluded and children answer gives no invented ages or fees.
- Current shuttle timetable, cost and eligibility by rate; do not promise universal complimentary access.
- Transfer pricing and current traffic-based journey estimates; published times remain approximate existing guidance.
- Cancellation/modification deadlines for each rate and channel; FAQ refers to the selected rate and booking confirmation.
- Accessibility of particular rooms and routes, including access to the river.
- Reconcile the postal-code discrepancy above.

## SEO and presentation

Updated requested meta title and description. Retained `/faq`, existing canonical generation, static HTML, original typography and hero. Four H2 groups with H3 questions replace the flat H2 question sequence. `/faq` already appears in the sitemap via `SitemapService`'s dedicated route entry; `include_in_sitemap=false` on its CMS record prevents a duplicate generic slug entry. No FAQPage or QAPage markup was present or added.

Verification: local HTTP 200, one H1, four groups and 21 HTML questions; protected hero/H1/slug fields unchanged. No obsolete villa URLs or unsupported largest-room claim remains.
