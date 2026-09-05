# Events Schedule cron restoration

Commit `9294948` removed this endpoint, controller, service, token configuration,
and recurrence types on August 31, 2026. This repair restores the original HTTP
processor and recurrence algorithm. One-time (`regular`) records remain untouched.
No database migration, record conversion, or second scheduled trigger is included.

## Upload

Upload these application files into the existing Laravel installation:

- `routes/web.php`
- `config/services.php`
- `app/Enums/EventType.php`
- `app/Http/Controllers/Cron/EventScheduleController.php`
- `app/Services/EventScheduleService.php`
- `app/Filament/Resources/Events/Schemas/EventForm.php`

Keep the existing production `.env`. Confirm privately that
`EVENT_SCHEDULE_CRON_TOKEN` contains the token used by the existing hosting cron,
`MAIN_DOMAIN=nandinibali.com`, and `APP_TIMEZONE=Asia/Makassar`. Do not put the
token in frontend code, shared logs, or this document.

From the Laravel project directory, using the host's PHP 8.3+ binary, run:

```sh
php artisan route:clear
php artisan config:clear
php artisan route:list --path=cron/events/schedule -vv
```

Expect exactly one `GET|HEAD` route on `nandinibali.com`, named
`cron.events.schedule`, handled by `Cron\EventScheduleController`. It uses the
same session exclusions as the existing booking cron. Token validation remains
mandatory. Rebuild caches only if the deployment normally uses them:

```sh
php artisan config:cache
php artisan route:cache
```

Do not run migrations for this repair. In particular, the earlier
`2026_08_31_000001_make_events_one_time` migration converts recurring types to
`regular`. If already applied, previous recurrence types cannot be recovered by
guessing. Leave those records unchanged; the owner must identify any events that
should recur. Check migration status before any separate future migration work.

## Shared hosting

The domain must point to the Laravel `public` directory, or the host's existing
equivalent `public_html` front-controller setup. The deployed `index.php` must
load this installation's `vendor/autoload.php` and `bootstrap/app.php`.

The checked-in `public/.htaccess` already rewrites nonexistent paths to
`index.php`. Inspect the actual document root and any `public_html/cron`
directory, nested `.htaccess`, Alias, or hosting-panel overrides if Laravel's
registered route still returns 404. A physical parent `cron` directory alone
does not prove interference: the `!-d`/`!-f` checks concern the requested path.
Do not delete directories or alter global rewriting without confirming the
server behavior. No rewrite change is part of this repair.

The existing configured cache store must support Laravel atomic locks and be
accessible to PHP. The restored controller retains its original 300-second lock
and always releases it after processing. No daemon or queue worker is required.
Keep the existing hosting HTTP cron; do not add a duplicate `schedule:run` task
for events. The former Laravel event trigger ran daily at 00:15 Asia/Makassar.

## Production verification using existing records

Before invoking the endpoint, inspect the existing rows privately in the host's
database tool and retain their dates for comparison:

```sql
SELECT id, status, event_type, event_start_at, event_end_at
FROM events
ORDER BY id;
```

Send GET to the existing secret URL. Successful processing returns:

```json
{"success":true,"message":"Event schedule cron completed.","checked":3,"updated":3}
```

Counts above illustrate the response shape only; actual counts depend on existing
records. `checked: 0, updated: 0` means no eligible records, not proof of a date
advancement. Missing or invalid token returns 403; an overlapping execution
returns 429; processing failures must not be treated as success.

Re-run the SELECT and compare. Only published weekly/monthly/yearly events whose
start is before today's midnight in the application timezone are eligible. Their
start advances by the original recurrence rule to today or later, retaining the
time of day and duration (or null end). Draft, one-time, undated, and future
events must remain unchanged. A second GET on the same day should produce no
further updates. Do not insert test records or change an event's type just to
obtain nonzero counts.

Local automated checks mock the processor for HTTP behavior and exercise the
original calendar calculation without database records. Production deployment,
token configuration, filesystem routing, and actual row changes require separate
verification on the hosting account.
