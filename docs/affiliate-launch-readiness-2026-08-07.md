# Nandini Partner Circle launch-readiness review

**Review date:** 7 August 2026  
**Application revision reviewed:** `58db3cc15bcf987ff1862cb810ae2f59083e945a` plus the current uncommitted Parts 1–6 worktree  
**Intended production hosts:** `affiliate.nandinibali.com`, `go.nandinibali.com`  
**Decision:** **NO-GO**  
**Risk:** High until the production routing, TLS, booking integration, workers, mail, backups, browser QA, Finance approval, and legal approval are verified.

This report distinguishes source implementation and local verification from production evidence. A configured value is not treated as proof that an external dependency works. No secret values or booking payloads were printed during this review.

## Production environment inventory

The production server environment was not available for inspection. “Missing evidence” means the value may exist on the server, but no production evidence was supplied or observable; it must not be interpreted as proof that the value itself is absent.

| Requirement | Status | Evidence or required action |
| --- | --- | --- |
| `APP_ENV=production` | Missing evidence | Confirm on the production release without printing secrets. |
| `APP_DEBUG=false` | Missing evidence | Confirm before enabling registration. |
| `APP_URL=https://nandinibali.com` | Missing evidence | Confirm before caching configuration. |
| `APP_TIMEZONE=Asia/Makassar` | Configured in project | Production value and scheduler timestamps remain unverified. |
| `AFFILIATE_DOMAIN=affiliate.nandinibali.com` | Configured in deployment plan | Production Laravel environment is not serving the host. |
| `SHORT_LINK_DOMAIN=go.nandinibali.com` | Configured in deployment plan | Production Laravel environment is not serving the host. |
| `SHORT_LINK_SCHEME=https` | Configured in deployment plan | HTTPS is currently invalid externally. |
| `AFFILIATE_REGISTRATION_ENABLED=false` during cutover | Configured in project | Must be set and config-cached in production. |
| `SESSION_DOMAIN=.nandinibali.com` | Configured in deployment plan | Production cookie behavior remains unverified. |
| `SESSION_SECURE_COOKIE=true` | Configured in deployment plan | Cannot work correctly until valid HTTPS is installed. |
| Stable production `APP_KEY` and secured backup | Missing evidence | Never print, regenerate, or replace it. |
| Production queue connection and supervised worker | Missing evidence | Verify a real queued notification and failed-job visibility. |
| Production mailer and sender identity | Missing evidence | Log/array mailers do not qualify. Verify SPF, DKIM, delivery, and links. |
| Dedicated `AFFILIATE_CLICK_HASH_KEY` | Missing evidence | Confirm only as Configured/Missing; never print it. |
| `AFFILIATE_CLICK_RETENTION_DAYS=395` | Configured in project | Verify production config cache and cleanup execution. |
| Exact `TRUSTED_PROXIES` | Missing evidence | Never use `*`; verify the immediate peer and header overwrite behavior. |
| Trusted country header or readable GeoIP database | Missing evidence | Country analytics must remain Not Configured until a real request succeeds. |
| Booking API URL and token | Configured locally | A safe read-only connection attempt failed; production credentials/runtime remain unverified. |
| Protected booking-sync cron token | Missing evidence | Never print it; verify the actual hosting cron. |

## External verification recorded on 7 August 2026

- DNS A records for both subdomains resolve to `208.109.41.229`, the same A record returned for `nandinibali.com`. Observed TTL: 3600 seconds. No CNAME answer was returned.
- Both HTTP hosts return `200 OK` from Apache and the identical 1,963-byte **Coming Soon** document. They do not redirect to HTTPS and are not routed to this Laravel release.
- Both HTTPS hosts present `CN=*.prod.phx3.secureserver.net`, issued by Starfield and expiring 23 September 2026. That certificate does not match either Nandini hostname, so normal HTTPS verification fails.
- The font exists at `public/fonts/Span-Semibold.otf`. Vite deliberately leaves its root-relative public URL for runtime resolution. The production font URL cannot be verified while TLS and virtual-host routing are invalid.
- The configured booking API URL and token were detected without printing either value. A read-only five-minute query returned the application’s safe `Unable to connect to the booking sync API` category. Live `voucher_code` presence is therefore unverified.
- No attached browser runtime was available. Desktop/mobile screenshots and interactive QA are not complete.

## Confirmed application corrections in this review

1. Added `AFFILIATE_REGISTRATION_ENABLED` as a focused launch switch. Production defaults safely to disabled when the value is omitted; local development remains enabled by default.
2. Disabled registration returns a neutral `503` page for GET and POST before form validation or account creation. Landing, login, existing Affiliate access, short links, and authorized Filament creation remain available.
3. Landing, login, and shared navigation no longer promote public registration while the switch is disabled.
4. Added `BOOKING_SYNC_MAX_AGE_HOURS` with a 25-hour default. System Health now requires a recent successful sync, not merely an old success record.
5. Approved dashboards warn that booking information may be stale when no sufficiently recent successful synchronization exists.
6. Removed guest email, external relay response data, and raw exception messages from booking-sync and Affiliate notification logs.
7. Added an explicit warning that production must not run `composer setup`, `key:generate`, development seeders, `migrate:fresh`, or `db:wipe`.
8. Corrected public copy that previously implied real-time synchronization and Affiliate revenue visibility; payout wording now states Finance validation and threshold conditions.

## Verification summary

| Area | Result |
| --- | --- |
| Parts 1–6 implementation | Implemented and covered by the Affiliate suite; no Affiliate “coming soon” UI remains in source. Historical migration references to `nandini.link` are legitimate data migration logic. Test/example addresses are fixtures or form placeholders. |
| Authentication and authorization | Locally verified through guard, policy, Filament restriction, cross-Affiliate, invitation expiry/single-use, and role tests. Production cookies and server headers remain unverified. |
| Click tracking and privacy | Locally verified: daily uniqueness, bot-safe redirects, keyed hash, no plain IP/full UA/full referrer storage, aggregate Affiliate access. Country source remains production-unverified. |
| Booking projection | Locally verified for case-insensitive/manual codes, privacy-safe projection, stale updates, and idempotency. Live source connectivity and `voucher_code` remain blocked. |
| Commission and payout workflows | Locally verified with synthetic data only. No money was transferred. Finance values and authorities remain unapproved. |
| Payment profiles | Encrypted casts, masking, authorization, audit/export exclusions, and synthetic workflow tests pass. Production `APP_KEY` continuity remains unverified. |
| Marketing assets | Local authorization, availability, upload validation, private delivery, and replacement behavior tests pass. Production storage permissions/backups remain unverified. |
| Reports and exports | Local ownership/permission scoping, privacy exclusions, per-currency totals, UTF-8 BOM, filters, and formula-prefix escaping pass. |
| System Health | Correctly treats external configuration as Unknown and now detects stale syncs. Production heartbeats and external checks are absent. |
| Affiliate automated suite | 105 tests, 704 assertions, all passing. |
| Full project suite | 218 tests, 1,193 assertions; 217 pass and one unrelated Voucher wording assertion fails. Business must decide the approved Voucher wording before changing code or test. |
| Production asset build | Pass. Vite reports the documented root-relative Span font runtime warning; the file is present locally. |
| Composer / Blade / routes | Composer manifest valid, Blade cache succeeds, and 214 routes load. |
| Scheduler registration | Pass locally: heartbeat every five minutes, cleanup 02:20, commission preparation 02:40 in Asia/Makassar. Execution in production is unverified. |
| Migration status | All local migrations through `2026_08_05_000003` report Ran. No production migration evidence exists. |
| Formatting | All files changed for this launch review pass Pint. Repository-wide Pint reports pre-existing drift in unrelated application modules. |
| Browser and accessibility | Source-level labels, headings, alt text, error summaries, responsive classes, and focus styling were reviewed. Real keyboard, modal focus, contrast, overflow, font loading, and 375/768/1440 screenshots are blocked by the missing browser runtime and undeployed hosts. |

## Launch blockers

1. Replace the default Apache Coming Soon virtual hosts with aliases that use the current release’s Laravel `public` directory.
2. Install hostname-valid certificates for both subdomains, configure a complete chain and renewal monitoring, then redirect HTTP to HTTPS.
3. Verify `.env` cannot be served, directory listing is off, source/private storage is inaccessible, and the PHP/document-root configuration matches the main application.
4. Resolve booking API connectivity and prove the live response contains `voucher_code`. Repeat/update/stale/idempotency checks must use controlled approved data.
5. Verify production queue supervision, retries, failed jobs, deployment restart, and all five safe test notifications.
6. Verify the one-per-minute scheduler and recent heartbeat, click cleanup, commission preparation, and existing booking synchronization execution.
7. Verify real mail sender identity, SPF, DKIM, DMARC where available, queued delivery, HTTPS portal/setup links, expiry, and safe notification content.
8. Verify production session/cookie behavior across main, voucher, Affiliate, and short-link hosts.
9. Confirm and securely back up the stable production `APP_KEY`; test synthetic payment encryption and permission boundaries in the target environment.
10. Obtain Finance approval for percentages, validation dates, release days, every currency threshold, payment methods, and finalization/payment authorities.
11. Obtain approved Affiliate-specific terms/privacy/program rules, or written management acceptance of the documented limitation.
12. Create and restore-test database and private marketing-asset backups outside the public directory.
13. Complete a production-like deployment rehearsal and real browser QA with screenshots at approximately 375, 768, and 1440 pixels.
14. Assign named operational owners and escalation contacts rather than role names alone.

## Cutover sequence

1. Approve the maintenance/cutover window and named owners.
2. Set `AFFILIATE_REGISTRATION_ENABLED=false` and confirm `APP_DEBUG=false`.
3. Record the release commit and migration state; back up and restore-test the database, private assets, environment configuration, and stable `APP_KEY`.
4. Deploy the release to the existing Laravel application; do not create another application or database.
5. Run `composer install --no-dev --optimize-autoloader`, `npm ci`, and `npm run build`. Do not run `composer setup` or `key:generate`.
6. Run `php artisan migrate --force` and only `php artisan db:seed --class=AffiliateFoundationSeeder --force`.
7. Run `php artisan optimize:clear`, then cache configuration, routes, and views.
8. Configure both Apache aliases to the same release `public` directory and install valid certificates.
9. Restart supervised queue workers and verify the one-per-minute scheduler.
10. Run the controlled public, authentication, short-link, click, booking, Finance-page, storage, export, mail, queue, and health smoke tests.
11. Complete desktop/mobile browser and accessibility checks and archive screenshots internally.
12. Enable public registration only after every critical check is signed off; rebuild the configuration cache and monitor closely.

## Rollback

- Disable public registration first while keeping existing Affiliate and Finance access available.
- Roll back application code, built assets, compatible environment configuration, and caches together; restart workers on the previous release.
- Revert DNS only if it was changed and the prior destination is known-good. Do not point either hostname at a second application copy.
- Do not automatically reverse financial migrations after production records exist. Preserve encrypted profiles, commission items, payouts, audit history, and the same `APP_KEY`; prefer a forward fix unless a tested data-safe rollback exists.
- Restore the database or private assets only through an approved, restore-tested incident procedure. Never delete financial history to simulate rollback.

## Monitoring and ownership

- **Sales & Marketing:** registration review, approval/rejection/suspension, marketing assets, Affiliate performance, and partner communication.
- **Finance:** validation decisions, holds/exclusions/adjustments, payment-profile review, payout preparation, external payment, and payment-reference recording.
- **IT / Webmaster:** domains, TLS renewal, web server, queues, failed jobs, scheduler heartbeat, booking sync, mail, storage, backups, releases, and technical incidents.
- **Management / Legal:** program, commission, payout, tax, dispute, termination, self-referral, prohibited-traffic, brand, terms, and privacy decisions.

Named people, escalation contacts, alert destinations, priorities, and target response times are still required. Monitor registration/invitation/notification failures, short-link and click errors, booking-sync failures, unknown codes/statuses, missing voucher/revenue fields, commission preparation, failed/overdue payouts, failed jobs, scheduler heartbeat, TLS expiry, and private storage without logging guest or payment-sensitive data.

## Registration-enable gate

Public registration may be enabled only after valid HTTPS/routing, production sessions, queue, scheduler, mail, booking API plus live `voucher_code`, backups, browser QA, Finance approval, and required legal/business approval have written evidence. Until then the decision remains **NO-GO**.
