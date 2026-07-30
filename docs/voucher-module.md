# Nandini Voucher Module

## Local WAMP domains

Add these Windows hosts entries manually:

```text
127.0.0.1 nandinibali.test
127.0.0.1 voucher.nandinibali.test
```

Both hostnames must point to the same Laravel `public` directory. You can use one Apache virtual host with `ServerAlias voucher.nandinibali.test` or two virtual hosts with the same `DocumentRoot`.

Required local environment:

```env
MAIN_DOMAIN=nandinibali.test
MEMBERSHIP_DOMAIN=nandinibali.test
VOUCHER_DOMAIN=voucher.nandinibali.test
SESSION_DOMAIN=.nandinibali.test
SESSION_SECURE_COOKIE=false
```

## Flywire environments

Set Flywire credentials only in `.env`. Demo and production use different signing-secret variables so credentials cannot be mixed accidentally:

```env
# Demo
FLYWIRE_ENVIRONMENT=demo
FLYWIRE_DEMO_SHARED_SECRET=replace-with-demo-secret

# Production
FLYWIRE_ENVIRONMENT=production
FLYWIRE_RECIPIENT_CODE=NAND3
FLYWIRE_SHARED_SECRET=replace-with-production-secret
FLYWIRE_BILLING_CURRENCY=IDR
FLYWIRE_ISSUE_ON_STATUSES=guaranteed
```

Do not put the production secret in `FLYWIRE_DEMO_SHARED_SECRET`. The Checkout integration uses the portal code and shared secret. `FLYWIRE_API_KEY` is optional and is only needed for server-side payment reconciliation (the **Check now** action).

For local notifications, expose the voucher domain through an HTTPS tunnel and set `FLYWIRE_NOTIFICATION_URL` to that tunnel URL. Do not hardcode tunnel URLs.

For the current shared-hosting staging domains, use:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://staging.nandinibali.com
MAIN_DOMAIN=staging.nandinibali.com
MEMBERSHIP_DOMAIN=staging.nandinibali.com
VOUCHER_DOMAIN=voucher.staging.nandinibali.com
SESSION_DOMAIN=.staging.nandinibali.com
SESSION_SECURE_COOKIE=true
FLYWIRE_NOTIFICATION_URL=https://voucher.staging.nandinibali.com/api/flywire/notifications
FLYWIRE_RETURN_URL=https://voucher.staging.nandinibali.com/payment/return
FLYWIRE_CANCEL_URL=https://voucher.staging.nandinibali.com/cart
```

Change the staging hostnames to `nandinibali.com` and `voucher.nandinibali.com` when deploying to the live domains. Both domains must have valid SSL before enabling production payments.

## Production checklist

1. Back up the database.
2. Deploy code.
3. Set production domains and `SESSION_SECURE_COOKIE=true`.
4. Confirm DNS and SSL for `voucher.nandinibali.com`.
5. Point Apache to the existing Laravel `public` directory.
6. Run migrations.
7. Confirm protected storage for generated voucher PDFs.
8. Configure cron jobs for the scheduler and queued jobs; do not rely on long-running daemons on shared hosting.
9. Add the Flywire production portal code, shared secret, notification URL, return URL, and cancel URL. Add an API key only if server-side reconciliation is required.
10. Confirm the Flywire status used for voucher issuance and set `FLYWIRE_ISSUE_ON_STATUSES` (currently `guaranteed`).
11. Test email, PDF generation, GTM events, route generation, and a sandbox purchase before enabling production payments.

## Commands

```bash
npm run build
composer install --no-dev --optimize-autoloader
php artisan optimize:clear
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Run these from shared-hosting cron once per minute (adjust the PHP binary and project path for the host):

```cron
* * * * * cd /path/to/project && php artisan schedule:run >/dev/null 2>&1
* * * * * cd /path/to/project && php artisan queue:work --stop-when-empty --tries=3 >/dev/null 2>&1
```
