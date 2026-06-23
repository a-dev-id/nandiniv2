<?php

use App\Models\Member;
use App\Services\MembershipLifecycleService;
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
            $command->line($member->email . ' => ' . $booking->booking_number);
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

Schedule::command('membership:process-lifecycle')->dailyAt('08:00');
