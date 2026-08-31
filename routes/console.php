<?php

use App\Models\AffiliateProgramSetting;
use App\Models\Member;
use App\Models\SyncedWebhotelierBooking;
use App\Services\Affiliate\Booking\SyncAffiliateBookingService;
use App\Services\Affiliate\Booking\SyncedWebhotelierAffiliateBookingSource;
use App\Services\Affiliate\Click\CleanupAffiliateClickEventsService;
use App\Services\Affiliate\Finance\PrepareAffiliateCommissionPeriodService;
use App\Services\Affiliate\Operations\AffiliateOperationalStateService;
use App\Services\BlogNewsPublicationService;
use App\Services\MemberCheckoutNotificationService;
use App\Services\MembershipLifecycleService;
use App\Services\MemberStayDateBackfillService;
use App\Services\OfferPublicationService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('membership:set-booking-passwords {--all-synced : Update every member with synced bookings, including manually registered members}', function () {
    $query = Member::query()
        ->whereHas('syncedBookings')
        ->with(['syncedBookings' => fn ($query) => $query->orderBy('check_in')->orderBy('id')]);

    if (! $this->option('all-synced')) {
        $query->where(function ($query) {
            $query
                ->where('member_source', Member::SOURCE_AUTO_JOIN)
                ->orWhere('must_change_password', true);
        });
    }

    $updated = 0;
    $command = $this;

    $query->chunkById(100, function ($members) use (&$updated, $command) {
        foreach ($members as $member) {
            $booking = $member->syncedBookings->first();

            if (! $booking || blank($booking->booking_number)) {
                continue;
            }

            $member->forceFill([
                'password' => (string) $booking->booking_number,
                'must_change_password' => true,
            ])->save();

            $updated++;
            $command->line($member->email.' => '.$booking->booking_number);
        }
    });

    $this->info("Updated {$updated} member password(s).");
})->purpose('Set synced member passwords to their first synced booking number.');

Artisan::command('membership:process-lifecycle {--skip-reminders : Do not send 90-day expiry reminders} {--skip-expired : Do not process expired memberships}', function (MembershipLifecycleService $service) {
    $remindersSent = 0;
    $expiredSummary = [
        'renewed' => 0,
        'downgraded' => 0,
        'skipped' => 0,
    ];

    if (! $this->option('skip-reminders')) {
        $remindersSent = $service->sendExpiryReminders(90);
    }

    if (! $this->option('skip-expired')) {
        $expiredSummary = $service->processExpiredMemberships();
    }

    $this->info("Membership expiry reminders sent: {$remindersSent}");
    $this->info("Expired memberships renewed: {$expiredSummary['renewed']}");
    $this->info("Expired memberships downgraded: {$expiredSummary['downgraded']}");
    $this->info("Expired memberships skipped: {$expiredSummary['skipped']}");
})->purpose('Send membership expiry reminders and process yearly tier renewals/downgrades.');

Artisan::command('membership:send-checkout-notifications {--date= : Checkout date to process in YYYY-MM-DD format}', function (MemberCheckoutNotificationService $service) {
    $summary = $service->sendTodayNotifications($this->option('date'));

    $this->info("Checkout date: {$summary['date']}");
    $this->info("Checkout notifications sent: {$summary['sent']}");
    $this->info("Checkout notifications failed: {$summary['failed']}");
    $this->info("Checkout notifications already sent today: {$summary['skipped']}");
})->purpose('Send reservation notifications for members checking out today.');

Artisan::command('membership:backfill-stay-dates {--dry-run : Count records without saving changes}', function (MemberStayDateBackfillService $service) {
    $summary = $service->backfill((bool) $this->option('dry-run'));

    $this->info("Members checked: {$summary['checked']}");
    $this->info("Members updated: {$summary['updated']}");
    $this->info("Members skipped: {$summary['skipped']}");
})->purpose('Fill blank member check-in/check-out dates from synced WebHotelier bookings.');

Artisan::command('offers:sync-publication', function (OfferPublicationService $service) {
    $summary = $service->sync();

    $this->info("Offers activated: {$summary['activated']}");
    $this->info("Future offers deactivated: {$summary['deactivated_scheduled']}");
    $this->info("Expired offers deactivated: {$summary['deactivated_expired']}");
})->purpose('Activate and deactivate offers based on their valid date range.');

Artisan::command('blog-news:sync-publication', function (BlogNewsPublicationService $service) {
    $summary = $service->sync();

    $this->info("Blog & News activated: {$summary['activated']}");
    $this->info("Future Blog & News deactivated: {$summary['deactivated_scheduled']}");
})->purpose('Activate and deactivate Blog & News based on their published date.');

Artisan::command('affiliate-clicks:cleanup {--retention= : Override the configured retention in days}', function (CleanupAffiliateClickEventsService $service) {
    $state = app(AffiliateOperationalStateService::class);
    $state->attempted('click_cleanup', 'Affiliate click retention cleanup started.');
    $configured = AffiliateProgramSetting::current()->click_event_retention_days
        ?: config('affiliate-clicks.retention_days', 395);
    $retention = filled($this->option('retention')) ? (int) $this->option('retention') : (int) $configured;

    if ($retention <= 0) {
        $state->failed('click_cleanup', 'Affiliate click retention was not executed because the retention value was invalid.');
        $this->error('Affiliate click retention must be a positive number of days.');

        return self::FAILURE;
    }

    $summary = $service->cleanup($retention);
    $state->succeeded('click_cleanup', "Deleted {$summary['events']} click events and {$summary['unique_markers']} unique markers.", ['retention_days' => $summary['retention_days']]);

    logger()->info('Affiliate click retention cleanup executed.', $summary);
    $this->info("Raw click events deleted: {$summary['events']}");
    $this->info("Daily unique markers deleted: {$summary['unique_markers']}");
    $this->info("Retention: {$summary['retention_days']} days");

    return self::SUCCESS;
})->purpose('Delete expired affiliate click events and daily unique markers.');

Artisan::command('affiliate-bookings:sync-existing {--id= : Process one synced booking ID}', function (
    SyncedWebhotelierAffiliateBookingSource $source,
    SyncAffiliateBookingService $service,
) {
    $query = SyncedWebhotelierBooking::query()->orderBy('id');

    if (filled($this->option('id'))) {
        $query->whereKey((int) $this->option('id'));
    }

    $summary = [];
    $query->chunkById(100, function ($bookings) use ($source, $service, &$summary): void {
        foreach ($bookings as $booking) {
            $result = $service->sync($source->normalize($booking));
            $summary[$result->state] = ($summary[$result->state] ?? 0) + 1;
        }
    });

    if ($summary === []) {
        $this->info('No synced bookings were found.');

        return self::SUCCESS;
    }

    foreach ($summary as $state => $count) {
        $this->line("{$state}: {$count}");
    }

    return self::SUCCESS;
})->purpose('Build privacy-safe Affiliate booking projections from existing synced bookings.');

Artisan::command('affiliate:prepare-commissions {--year= : Completion year} {--month= : Completion month}', function (PrepareAffiliateCommissionPeriodService $service) {
    $state = app(AffiliateOperationalStateService::class);
    $state->attempted('commission_preparation', 'Affiliate commission preparation started.');
    $now = now()->timezone(config('app.timezone'));
    $hasYear = filled($this->option('year'));
    $hasMonth = filled($this->option('month'));

    if ($hasYear !== $hasMonth) {
        $state->failed('commission_preparation', 'Commission preparation was not executed because year and month must be supplied together.');
        $this->error('Use --year and --month together.');

        return self::FAILURE;
    }

    $period = $hasYear
        ? $now->setDate((int) $this->option('year'), (int) $this->option('month'), 1)
        : $now->subMonthNoOverflow()->startOfMonth();
    $summary = $service->prepare((int) $period->year, (int) $period->month);
    $state->succeeded('commission_preparation', 'Prepared '.$summary['period']->label()."; {$summary['created']} created, {$summary['unchanged']} unchanged.", ['period' => $summary['period']->label(), 'created' => $summary['created'], 'unchanged' => $summary['unchanged'], 'skipped' => $summary['skipped'], 'unavailable' => $summary['unavailable']]);

    $this->info('Commission period: '.$summary['period']->label());
    $this->line('Created: '.$summary['created']);
    $this->line('Unchanged: '.$summary['unchanged']);
    $this->line('Skipped: '.$summary['skipped']);
    $this->line('Unavailable: '.$summary['unavailable']);

    return self::SUCCESS;
})->purpose('Prepare privacy-safe Affiliate commission items for a completion month.');

Artisan::command('affiliate:heartbeat', function (AffiliateOperationalStateService $state) {
    $state->succeeded('scheduler_heartbeat', 'Laravel scheduler heartbeat recorded.');
    $this->info('Affiliate scheduler heartbeat recorded.');

    return self::SUCCESS;
})->purpose('Record an Affiliate scheduler heartbeat for operational monitoring.');

Schedule::command('membership:process-lifecycle')->dailyAt('08:00');
Schedule::command('membership:send-checkout-notifications')->dailyAt('07:00');
Schedule::command('offers:sync-publication')->dailyAt('00:05');
Schedule::command('blog-news:sync-publication')->dailyAt('00:10');
Schedule::command('affiliate-clicks:cleanup')->dailyAt('02:20')->timezone(config('app.timezone'))->withoutOverlapping();
Schedule::command('affiliate:heartbeat')->everyFiveMinutes()->timezone(config('app.timezone'))->withoutOverlapping();
Schedule::command('affiliate:prepare-commissions')
    ->dailyAt('02:40')
    ->timezone(config('app.timezone'))
    ->when(function (): bool {
        $settings = AffiliateProgramSetting::current();
        $day = (int) now()->timezone(config('app.timezone'))->day;

        return $day >= $settings->commission_validation_start_day
            && $day <= $settings->commission_validation_end_day;
    })
    ->withoutOverlapping();
