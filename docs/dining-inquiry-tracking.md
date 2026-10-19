# Dining inquiry tracking

Superseded by [site-wide-inquiry-tracking.md](site-wide-inquiry-tracking.md).
The current emitter is `inquiry_success`; migrate the old GTM trigger as documented
there. The Dining-only configuration below is historical, not the current setup.

The application queues `dining_inquiry_success` only after an HTTP-successful
response with `success: true`, a Dining lead type, an allowlisted category and
a positive inquiry ID. IDs are used for local popup-instance deduplication only
and are never included in the analytics event. The event contains only
`event`, `lead_type: dining_inquiry`, and `inquiry_category`.

Categories: `private_dining`, `general_dining`, `dining_experience`. The Private
Dining trigger explicitly declares its category. Other triggers can declare
`data-inquiry-category`, or modal-opening events can supply `inquiryCategory`.
Undeclared triggers default to `general_dining`; titles are never classified.

Success means saved to the database, not guaranteed email delivery. Existing
email and reCAPTCHA behavior is unchanged. No direct GA4 request or consent
override is introduced; GTM must enforce the site's existing consent policy.

## Required GTM changes (not implemented in the codebase)

1. Create a Custom Event trigger with event name `dining_inquiry_success`.
   Restrict to the Dining hostname (and `lead_type` equals `dining_inquiry`).
2. Create Data Layer Variables for `lead_type` and `inquiry_category`.
3. Configure a GA4 event tag targeting `G-3HTZW0EWMJ`. Use event name
   `generate_lead`, with those two parameters. Use the Custom Event trigger,
   not a form-submit or button-click trigger. Preserve configured consent checks.
4. For the existing WhatsApp link-click trigger, add an exclusion for inquiry
   popup triggers. A Custom JavaScript variable using Click Element:

   ```javascript
   function () {
     var element = {{Click Element}};
     return !!(element && element.closest && element.closest('[data-inquiry-button]'));
   }
   ```

   Require this variable to equal `false` on the WhatsApp trigger. Alternatively,
   attach an exception with the same click event type as the WhatsApp trigger,
   where the variable equals `true`. If that tag uses All Elements clicks, apply
   the exclusion to that trigger too. Do not suppress genuine floating WhatsApp
   or other unmarked contact links.
5. In Preview verify popup open/Submit/failure produce no lead, confirmed save
   produces exactly one lead, and genuine WhatsApp still fires separately.
   Verify granted/denied consent behavior and GA4 DebugView before publishing.
   If lead conversions are desired, mark `generate_lead` as a GA4 key event.

The Private Dining CTA's `wa.me` href is retained as its existing no-JavaScript
fallback. No documentation established whether that fallback was deliberate,
so it was preserved. JavaScript prevents navigation and opens the popup, but
GTM link-click listeners may still classify the underlying href as WhatsApp.
The existing `data-inquiry-button` marker supports the exclusion above; its
new category attribute also distinguishes the Private Dining interaction.

The data-layer implementation alone does not establish completed GA4 tracking.
Container configuration, consent behavior and actual GA4 receipt need verification.
