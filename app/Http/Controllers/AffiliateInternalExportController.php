<?php

namespace App\Http\Controllers;

use App\Enums\AffiliateCommissionItemStatus;
use App\Enums\AffiliatePayoutStatus;
use App\Enums\AffiliateRegistrationSource;
use App\Enums\AffiliateStatus;
use App\Models\Permission;
use App\Services\Affiliate\Reports\AffiliateOperationalReportService;
use App\Services\Affiliate\Reports\AffiliateReportDateRange;
use App\Services\Affiliate\Reports\SafeCsvWriter;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AffiliateInternalExportController extends Controller
{
    public function __invoke(Request $request, string $type, AffiliateOperationalReportService $reports, SafeCsvWriter $csv): StreamedResponse
    {
        abort_unless($request->user()?->hasPermissionTo(Permission::AFFILIATE_REPORT_VIEW), 403);
        abort_unless(in_array($type, ['affiliates', 'performance', 'bookings', 'commission-items', 'payouts', 'exceptions'], true), 404);
        $range = AffiliateReportDateRange::fromRequest($request);
        $filters = $request->validate([
            'status' => ['nullable', Rule::enum(AffiliateStatus::class)],
            'currency' => ['nullable', 'regex:/^[A-Z]{3,10}$/'],
            'affiliate_id' => ['nullable', 'integer', 'min:1'],
            'registration_source' => ['nullable', Rule::enum(AffiliateRegistrationSource::class)],
            'approver_id' => ['nullable', 'integer', 'min:1'],
            'commission_status' => ['nullable', Rule::enum(AffiliateCommissionItemStatus::class)],
            'payout_status' => ['nullable', Rule::enum(AffiliatePayoutStatus::class)],
            'reviewer_id' => ['nullable', 'integer', 'min:1'],
        ]);
        $status = $filters['status'] ?? null;
        $currency = $filters['currency'] ?? null;
        $affiliateId = isset($filters['affiliate_id']) ? (int) $filters['affiliate_id'] : null;
        $registrationSource = $filters['registration_source'] ?? null;
        $approverId = isset($filters['approver_id']) ? (int) $filters['approver_id'] : null;
        $commissionStatus = $filters['commission_status'] ?? null;
        $payoutStatus = $filters['payout_status'] ?? null;
        $reviewerId = isset($filters['reviewer_id']) ? (int) $filters['reviewer_id'] : null;
        [$headers, $rows] = match ($type) {
            'affiliates' => [['Name', 'Email', 'Phone / WhatsApp', 'Affiliate Code', 'Status', 'Registration Source', 'Registration Date', 'Created By', 'Approved By'], $reports->affiliateRows($range, $status, $registrationSource, $approverId)],
            'performance' => [['Affiliate', 'Affiliate Code', 'Affiliate Status', 'Total Clicks', 'Unique Clicks', 'Tracked Bookings', 'Room Nights', 'Estimated Commission', 'Approved Commission', 'Paid Commission', 'Currency', 'Tracked Conversion Indicator', 'Last Activity'], $reports->performanceRows($range, $status, $currency, $affiliateId, $registrationSource)],
            'bookings' => [['Affiliate', 'Affiliate Code', 'Room Type', 'Check-in', 'Check-out', 'Stay Nights', 'Booking Status', 'Commission Status', 'Commission Amount', 'Currency'], $reports->bookingRows($range, $currency, $affiliateId)],
            'commission-items' => [['Period', 'Affiliate', 'Affiliate Code', 'Check-out', 'Status', 'Original Commission', 'Approved Commission', 'Currency', 'Source Changed'], $reports->commissionRows($range, $currency, $affiliateId, $commissionStatus, $reviewerId)],
            'payouts' => [['Payout Number', 'Affiliate', 'Gross Amount', 'Adjustment Amount', 'Net Amount', 'Currency', 'Payment Method', 'Status', 'Due Date', 'Payment Date'], $reports->payoutRows($range, $currency, $affiliateId, $payoutStatus)],
            default => [['Exception', 'Count'], collect($reports->exceptions())->map(fn ($count, $label): array => [$label, $count])],
        };

        return $csv->download("affiliate-operations-{$type}-{$range->from->toDateString()}-to-{$range->to->toDateString()}.csv", $headers, $rows);
    }
}
