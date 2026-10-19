# Shared inquiry tracking

## Audit and coverage

One shared `x-inquiry-modal` / Alpine `inquiryModal` handles Dining homepage,
experience details, Holy River experience details, and weddings. Private Dining
and celebrations use the Dining trigger. Romantic, wellness and dining experiences
are classified by their existing experience-category slugs, not headings. No
separate inquiry popup exists on the standalone spa or honeymoon templates; their
external contact/booking links are not converted into new forms. Redemption,
membership, contact forms, mini-popup and other non-inquiry interactions are excluded.

`inquiry_popup_open` is emitted on a closed-to-open transition. `inquiry_success`
is emitted only after HTTP success plus explicit `success: true`, `lead_type:
inquiry`, a positive saved inquiry ID and an allowlisted category. IDs deduplicate
within the modal instance and are not sent to analytics. Category/source maps:

| Category | Source |
|---|---|
| private_dining, dining | dining |
| spa | spa |
| experience, romantic_experience | experiences |
| wedding | wedding |
| general | website |

Events include category, source, a structured experience identifier (or category
fallback), and pathname only, with no URL query/hash or form data. Success adds
`lead_type: inquiry`. The category hidden input is validated then discarded before
database/email processing; no schema change. Legacy `general_dining` and
`dining_experience` inputs normalize to `dining`.

## GTM migration — required before claiming GA4 completion

Repository dependencies on `dining_inquiry_success` were its frontend emitter,
mocked tests and the earlier Dining setup document. External container dependencies
cannot be inspected here. The shared event replaces it; both events are never emitted.

1. Inspect container `GTM-TW272WHM` for the old Dining success trigger/tag and any
   button/form-submit lead tags. Migrate the existing lead tag to Custom Event
   `inquiry_success`, remove its Dining-only hostname/lead-type restrictions,
   and send GA4 `generate_lead` to `G-3HTZW0EWMJ` with `lead_type`,
   `inquiry_category`, `inquiry_source`, `experience`, `page_path` Data Layer variables.
   Do not add a second lead tag while retaining another matching lead trigger.
2. Add Custom Event `inquiry_popup_open` -> GA4 `inquiry_popup_open`, with the
   category/source/experience/path parameters. Popup open is not a lead conversion.
3. Exclude inquiry popup triggers from each WhatsApp click trigger. Use a Custom
   JavaScript variable returning
   `!!({{Click Element}} && {{Click Element}}.closest && {{Click Element}}.closest('[data-inquiry-button]'))`
   and require it to equal false. Handle nested elements using `closest`, not
   only Click URL. Apply to All Elements or Just Links triggers as appropriate.
   The shared initialization marks legacy inquiry-labelled links/buttons, too.
4. Genuine unmarked WhatsApp links retain their navigation and existing tracking.
   Private Dining's existing WhatsApp href remains as a no-JavaScript fallback;
   application code cannot guarantee that an external GTM click tag excludes it.
5. Preserve consent requirements on both GA4 tags. No CMP/Consent Mode code was
   found in the inspected application; consent may be supplied by GTM. The helper
   only queues data-layer events, never calls GA4 directly or grants consent.
   Verify denied/granted consent in Preview rather than assuming compliance.
6. Verify each category, open/close/reopen, failed submission, single confirmed
   lead, genuine WhatsApp, and no old Dining event/duplicate lead. Confirm DebugView
   and key-event configuration before publishing.

The earlier `dining-inquiry-tracking.md` describes the superseded Dining-only event;
this document is the current migration/configuration reference. No external GTM
container or production inquiry was changed during implementation.
