# Retired Spa & Wellness landing page

The named `spa.index` GET/HEAD route on the main domain now returns a direct
301 to `https://spa.nandinibali.com/`, preserving the raw query string. Laravel
matches the trailing-slash form as well. This route is before the generic CMS
route and does not query the page or depend on `SPA_ENABLED`.

The sitemap excludes the main-site `spa-wellness` slug even if a CMS administrator
reactivates it. Individual treatment redirects and the Spa website are unchanged.
No analytics scripts or HTML page views run on the redirect response.

Main navigation already uses `spa-landing.index`, pointing directly at the Spa
subdomain. The footer has no retired Spa & Wellness target; its separate
`jungle-spa-ubud` editorial link is intentionally preserved. No hardcoded public
landing links to the old URL were found. Treatment paths and CSS identifiers
containing `spa-wellness` are not retired-page links and are unchanged.

## Hosting steps

1. Back up the database and review/run
   `database/shared-hosting/2026-10-15_retire_spa_wellness.sql`. It deactivates the
   main CMS page without deleting content and updates exact old landing targets
   in main-page section buttons and offer buttons. No migration is needed.
2. Inspect Filament's homepage section links, rich text, action arrays, menus and
   other CMS-managed URLs for additional old landing references, including ones
   with query strings. Point those directly to the Spa homepage, retaining any
   tracking parameters. Preserve treatment-detail and unrelated booking links.
3. Deploy code and refresh the host's Laravel route cache using its usual
   deployment process. Purge any CDN/HTML cache for the retired path/sitemap.
4. Verify `/spa-wellness`, `/spa-wellness/`, and UTM links return one 301 with
   the correct Location header; check destination HTTP 200, sitemap and links.

The local MySQL server refused connections during implementation, so CMS record
deactivation and stored-link inspection were not performed. The SQL is supplied
for deployment, not executed. The redirect and sitemap protection work regardless
of that pending CMS step. No production database or media was changed.

Automated redirect/sitemap checks use isolated SQLite data. The broader Spa suite
also has three canonical-host assertion failures: current Spa views use production
canonical hosts while those tests expect `.test` hosts. Those destination metadata
templates were not modified as part of this retirement task.
