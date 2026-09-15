# Dining landing page

The Dining homepage uses the existing application layout, navbar, footer and link buttons. It contains the hero, its information bar, Dining Philosophy, Why Dine at Nandini, and Featured Dining Experiences. The main homepage and `/dining` route retain their existing controllers.

## Configuration

Dining content backgrounds are white, with a #f3f4f5 quick information bar and dark text. Why Dine at Nandini also uses a plain #f3f4f5 background without botanical decoration. Missing Philosophy and Featured Experience photos use labeled placehold.co images, replaced automatically when real images are configured. The video hero and shared site chrome retain their existing styling.

Set these environment values on the deployment and rebuild the Laravel configuration cache:

```dotenv
DINING_DOMAIN=dining.nandinibali.com
DINING_HERO_IMAGE=
DINING_RESERVATION_URL=https://wa.me/6281236871170
DINING_MENU_URL=
DINING_HERO_VIDEO_ID=GZav9hOJKts
DINING_PHILOSOPHY_IMAGE=
DINING_SIGNATURE_LOBSTER_IMAGE=
DINING_RESTAURANT_IMAGE=
DINING_BAR_IMAGE=
DINING_TEA_IMAGE=
DINING_WINE_IMAGE=
DINING_ROMANTIC_IMAGE=
```

The header reuses the homepage video component and deferred loader with muted autoplay, looping and hidden controls. Desktop uses a full viewport height, while smaller screens retain space for the Dining content. Tailwind utilities crop the video to cover the header.

`DINING_HERO_IMAGE` optionally supplies a poster as a public URL or root-relative asset path. If unset, the page reuses the active main Dining page's locally available CMS hero (page ID 4), then falls back to the supplied video's YouTube thumbnail.

Set `DINING_MENU_URL` to the published menu URL. Until supplied, View Menu opens WhatsApp with a menu request. Hero content aligns with the site's left page padding; the only hero actions are Reserve a Table and View Menu.

The Philosophy component uses Tailwind utilities and the existing content container and fonts. Our Story links to the main site's About Us page. Set `DINING_PHILOSOPHY_IMAGE` to the approved chef-plating image URL or public asset path; no matching photograph exists in the local assets, so an ivory/gold image placeholder reserves its space until supplied. The decorative phrase uses a system cursive font fallback without adding a global font or package.

## Local development and hosting

Featured experience images accept public URLs or root-relative paths. Tea and Romantic Dining also reuse locally available images from the active CMS experiences `luxe-high-tea` and `romantic-dining-by-the-chapel`. Missing images reserve a consistent 4:3 placeholder. Restaurant, Bar and Wine link to the existing main Dining page; Tea links to the active Luxe High Tea page when available, otherwise Dining. Romantic Dining links to the reservation contact. Desktop uses five columns, tablet two (the final card stays left-aligned), and mobile one. Live browser testing was skipped for this section as requested.

The default local domain is `dining.nandinibali.test`. Point it at the same local Laravel virtual host as `nandinibali.test`, or use `DINING_DOMAIN=localhost` with `php artisan serve` and visit `http://localhost:8000/`. Keep the main domain configured separately. Run the existing Vite development server or `npm run build`.

In production, point Dining DNS and the web server's TLS virtual host at this application's `public` directory. Build assets with `npm run build`. The canonical remains `https://dining.nandinibali.com/`, including during local previews. DNS and web-server configuration are outside this repository.

## Verification

Special Occasions follows Signature Dishes, using the existing light-grey section background, fonts, button and container. It reuses `DINING_ROMANTIC_IMAGE` or the active romantic dining CMS image when locally available, otherwise a placehold.co fallback. The enquiry button and occasion enquiries use the configured reservation contact; Honeymoon Dinner links to the existing honeymoon page. Portrait tablet and mobile stack the image above the content.

Guest Experiences follows Special Occasions. It uses up to three published CMS guest reviews that mention food, dining, the restaurant, or wine. If fewer than three suitable reviews are available, the remaining slides use the approved temporary dining testimonial copy. It reuses the main website's guest-review slider, navigation controls, pagination dots, spacing, and typography while intentionally omitting rating graphics.

Practical Information and FAQ follows Guest Experiences. It presents six essential visit details, links to the configured or CMS food menu and the existing beverage list, and uses a keyboard-accessible Alpine accordion that keeps one answer open at a time. The two panels have a vertical divider on desktop and stack with a horizontal divider below that breakpoint.

The final Dining reservation CTA sits immediately above the shared footer. It reuses the optimized romantic-dining WebP at `public/images/dining/romantic-dining-by-the-chapel.webp`, the production Wild Ginger reservation flow, the configured Dining WhatsApp link, and the established Dining button styles. Its compact image treatment uses a dark readability overlay, side-by-side desktop actions, and full-width stacked mobile actions.

Dining-specific sections follow the main Nandini website design system: `max-w-7xl` content alignment, compact uppercase section headings, `text-xs`/`sm:text-sm` body copy, shared buttons, and the existing Slick carousel controls. Dining Experiences uses the same three/two/one-card responsive carousel, gold square arrows, pagination dots, bordered 4:3 cards, and transition timing as the homepage content carousels.

Signature Dishes follows Featured Experiences. Its data lives in `config/dining.php`; only Signature Grilled Lobster is supplied. It reuses Alpine for optional manual fade navigation, with arrows omitted for a single dish. Set `DINING_SIGNATURE_LOBSTER_IMAGE` to replace the placehold.co fallback. The menu CTA uses the configured menu URL, then the active Dining CMS View Menu link, with the verified current Drive menu as a fallback, and opens in a new tab like the main Dining page. Live browser QA is skipped as requested.

The production asset build and 18 Dining/SPA domain tests pass (74 assertions). Browser checks at 1366×768, 1440×900, 1920×1080, 768×1024, 820×1180, 1024×768, 375×667, 390×844 and 430×932 found no horizontal or heading overflow, with 52px CTA targets. Desktop and mobile layouts and the shared mobile menu were visually inspected. The existing layout's promotional widget is disabled only on Dining to keep this initial page focused on the requested content.

Final menu delivery and production DNS/TLS still require configuration before launch.
