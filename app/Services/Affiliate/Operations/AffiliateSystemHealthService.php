<?php

namespace App\Services\Affiliate\Operations;

use App\Models\AffiliateAuditEvent;
use App\Models\AffiliateOperationalState;
use App\Models\AffiliateProgramSetting;
use App\Models\BookingSyncLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class AffiliateSystemHealthService
{
    public function checks(): array
    {
        $settings = AffiliateProgramSetting::current();
        $states = AffiliateOperationalState::query()->get()->keyBy('key');
        $lastSync = BookingSyncLog::query()->latest('started_at')->first();
        $lastSuccessfulSync = BookingSyncLog::query()
            ->where('status', BookingSyncLog::STATUS_SUCCESS)
            ->latest('finished_at')
            ->first();
        $bookingSyncMaximumAgeHours = max(1, (int) config('services.membership_api.booking_sync_max_age_hours', 25));
        $bookingSyncRecent = $lastSuccessfulSync?->finished_at?->gte(now()->subHours($bookingSyncMaximumAgeHours)) === true;
        $bookingSyncHealthy = $lastSync?->status === BookingSyncLog::STATUS_SUCCESS && $bookingSyncRecent;

        try {
            DB::select('select 1');
            $database = $this->check('Database Status', 'Healthy', 'Database connection succeeded.');
        } catch (Throwable) {
            $database = $this->check('Database Status', 'Attention Required', 'Database connection check failed.');
        }

        $queueConnection = (string) config('queue.default');
        $failedJobs = Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->where('payload', 'like', '%Affiliate%')->count() : 0;
        $recentAffiliateNotificationDispatches = AffiliateAuditEvent::query()->whereIn('event', [
            'registration_notification_dispatched', 'invitation_sent', 'approval_notification_dispatched',
            'rejection_notification_dispatched', 'affiliate_payout.paid_notification_dispatched',
        ])->where('created_at', '>=', now()->subDay())->count();
        $queueStatus = $queueConnection === 'sync' ? 'Attention Required' : ($failedJobs > 0 ? 'Attention Required' : 'Unknown');
        $queueSummary = "Configured connection: {$queueConnection}. Failed Affiliate jobs: {$failedJobs}.";
        $emailRelayConfigured = filled(config('services.email_relay.url')) && filled(config('services.email_relay.token'));
        $emailRelaySummary = ($emailRelayConfigured ? 'Affiliate email relay is configured.' : 'Affiliate email relay URL or token is missing.')
            ." Successful Affiliate email dispatches recorded in the last 24 hours: {$recentAffiliateNotificationDispatches}.";
        $scheduler = $states->get('scheduler_heartbeat');
        $schedulerHealthy = $scheduler?->last_successful_at?->gte(now()->subMinutes(10));
        $bookingState = $states->get('booking_sync');
        $storageRoot = (string) config('filesystems.disks.local.root');
        $storageAvailable = $storageRoot !== '' && is_dir($storageRoot) && is_writable($storageRoot);
        $geoipPath = config('affiliate-clicks.geoip_database');
        $countryHeader = config('affiliate-clicks.country_header');

        return [
            $this->check('Affiliate Domain Configuration', filled(config('domains.affiliate')) ? 'Unknown' : 'Not Configured', filled(config('domains.affiliate')) ? 'Environment-controlled route: '.config('domains.affiliate').'. DNS, routing, and SSL require external verification.' : 'No Affiliate domain is configured.'),
            $this->check('Short-Link Domain Configuration', filled(config('domains.short_link')) && in_array(config('domains.short_link_scheme'), ['http', 'https'], true) ? 'Unknown' : 'Not Configured', filled(config('domains.short_link')) ? 'Environment-controlled route: '.config('domains.short_link_scheme').'://'.config('domains.short_link').'. DNS, routing, SSL, and redirects require external verification.' : 'No short-link domain is configured.'),
            $database,
            $this->check('Queue Status', $queueStatus, $queueSummary),
            $this->check('Scheduler Status', $schedulerHealthy ? 'Healthy' : ($scheduler ? 'Attention Required' : 'Unknown'), $scheduler?->last_successful_at ? 'Last heartbeat: '.$scheduler->last_successful_at->format('d M Y H:i:s T') : 'No scheduler heartbeat has been recorded.'),
            $this->check(
                'Last Booking Sync',
                $bookingSyncHealthy ? 'Healthy' : ($lastSync ? 'Attention Required' : 'Unknown'),
                $lastSync
                    ? (($lastSync->status === BookingSyncLog::STATUS_SUCCESS ? 'Successful' : 'Failed').' at '.$lastSync->started_at?->format('d M Y H:i:s T').'. Received '.number_format($lastSync->bookings_received).' bookings. A successful sync must be within '.$bookingSyncMaximumAgeHours.' hours.')
                    : 'No booking synchronization has been recorded.',
            ),
            $this->stateCheck('Last Click Cleanup', $states->get('click_cleanup'), 26),
            $this->stateCheck('Last Commission Preparation', $states->get('commission_preparation'), 35 * 24),
            $this->check('Failed Queue Jobs', $failedJobs > 0 ? 'Attention Required' : 'Healthy', number_format($failedJobs).' failed Affiliate-related queue job(s).'),
            $this->check('Storage Availability', $storageAvailable ? 'Healthy' : 'Attention Required', $storageAvailable ? 'Private Affiliate storage is writable.' : 'Private Affiliate storage is not currently writable.'),
            $this->check('Affiliate Email Relay', $emailRelayConfigured ? 'Unknown' : 'Not Configured', $emailRelaySummary),
            $this->check('GeoIP / Country Detection', $geoipPath && is_file($geoipPath) ? 'Healthy' : ($countryHeader ? 'Unknown' : 'Not Configured'), $geoipPath && is_file($geoipPath) ? 'A local GeoIP database is available.' : ($countryHeader ? 'Trusted country header configured as '.$countryHeader.'; production request behavior is not verified.' : 'No country header or GeoIP database is configured.')),
            $this->check('Voucher Field Availability', $bookingState?->metadata['voucher_field_detected'] ?? false ? 'Healthy' : ($lastSync ? 'Attention Required' : 'Unknown'), $bookingState?->summary ?: 'Awaiting a booking synchronization result.'),
            $this->check('Affiliate Terms and Privacy', 'Attention Required', 'Affiliate-specific terms and privacy wording require business or legal approval before public launch.'),
        ];
    }

    private function stateCheck(string $label, ?AffiliateOperationalState $state, int $maximumAgeHours): array
    {
        if (! $state) {
            return $this->check($label, 'Unknown', 'No execution has been recorded.');
        }

        $recent = $state->last_successful_at?->gte(now()->subHours($maximumAgeHours)) === true;
        $healthy = $state->status === 'success' && $recent;

        return $this->check($label, $healthy ? 'Healthy' : 'Attention Required', ($state->summary ?: 'No safe result summary.').' Last attempt: '.$state->last_attempted_at?->format('d M Y H:i:s T').'.');
    }

    private function check(string $label, string $status, string $summary): array
    {
        return compact('label', 'status', 'summary');
    }
}
