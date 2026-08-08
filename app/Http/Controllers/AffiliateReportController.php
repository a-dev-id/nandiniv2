<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Services\Affiliate\Booking\AffiliateMoneyFormatter;
use App\Services\Affiliate\Reports\AffiliateReportDateRange;
use App\Services\Affiliate\Reports\AffiliateReportService;
use App\Services\Affiliate\Reports\SafeCsvWriter;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class AffiliateReportController extends Controller
{
    public function index(Request $request, AffiliateReportService $reports, AffiliateMoneyFormatter $money): View
    {
        $affiliate = $request->user('affiliate');
        abort_unless($affiliate->isApproved() && $affiliate->hasPermissionTo(Permission::AFFILIATE_REPORT_VIEW_OWN), 403);
        $range = AffiliateReportDateRange::fromRequest($request);

        return view('pages.affiliate.reports', ['affiliate' => $affiliate, 'range' => $range, 'summary' => $reports->summary($affiliate, $range), 'money' => $money]);
    }

    public function export(Request $request, string $type, AffiliateReportService $reports, SafeCsvWriter $csv): StreamedResponse|Response
    {
        $affiliate = $request->user('affiliate');
        abort_unless($affiliate->isApproved() && $affiliate->hasPermissionTo(Permission::AFFILIATE_REPORT_VIEW_OWN), 403);
        abort_unless(in_array($type, ['clicks', 'bookings', 'payouts'], true), 404);
        $range = AffiliateReportDateRange::fromRequest($request);
        try {
            [$headers, $rows] = match ($type) {
                'clicks' => [['Date', 'Total Clicks', 'Unique Clicks', 'Top Country', 'Top Device'], $reports->clickRows($affiliate, $range)],
                'bookings' => [['Room Type', 'Check-in', 'Check-out', 'Stay Nights', 'Booking Status', 'Commission Status', 'Commission Amount', 'Currency'], $reports->bookingRows($affiliate, $range)],
                default => [['Payout Number', 'Amount', 'Currency', 'Payment Method', 'Status', 'Payment Date'], $reports->payoutRows($affiliate, $range)],
            };
        } catch (Throwable $exception) {
            report($exception);

            return response()->view('pages.affiliate.portal-unavailable', [
                'title' => 'Export temporarily unavailable',
                'message' => 'The report export could not be prepared. Please try again later.',
            ], 500);
        }

        return $csv->download("affiliate-{$type}-{$range->from->toDateString()}-to-{$range->to->toDateString()}.csv", $headers, $rows);
    }
}
