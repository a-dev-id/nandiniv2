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

## Flywire sandbox

Set sandbox credentials only in `.env`. Keep `FLYWIRE_ISSUE_ON_STATUSES` empty until the merchant account's approved success status is confirmed by Flywire Solutions.

For local notifications, expose the voucher domain through an HTTPS tunnel and set `FLYWIRE_NOTIFICATION_URL` to that tunnel URL. Do not hardcode tunnel URLs.

## Production checklist

1. Back up the database.
2. Deploy code.
3. Set production domains and `SESSION_SECURE_COOKIE=true`.
4. Confirm DNS and SSL for `voucher.nandinibali.com`.
5. Point Apache to the existing Laravel `public` directory.
6. Run migrations.
7. Confirm protected storage for generated voucher PDFs.
8. Start queue workers and scheduler.
9. Add Flywire production API key, shared secret, recipient ID, notification URL, return URL, and cancel URL.
10. Confirm the exact Flywire status for voucher issuance and set `FLYWIRE_ISSUE_ON_STATUSES`.
11. Test email, PDF generation, GTM events, route generation, and a sandbox purchase before enabling production payments.

## Commands

```bash
php artisan optimize:clear
php artisan migrate
php artisan db:seed --class=VoucherSeeder
php artisan queue:work
php artisan schedule:work
php artisan route:list
php artisan test
npm run build
```
