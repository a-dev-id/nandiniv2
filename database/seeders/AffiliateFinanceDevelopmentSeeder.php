<?php

namespace Database\Seeders;

use App\Enums\AffiliateBookingStatus;
use App\Enums\AffiliateCommissionItemStatus;
use App\Enums\AffiliateCommissionPeriodStatus;
use App\Enums\AffiliateCommissionState;
use App\Enums\AffiliatePaymentMethod;
use App\Enums\AffiliatePayoutStatus;
use App\Enums\AffiliateRegistrationSource;
use App\Enums\AffiliateStatus;
use App\Models\Affiliate;
use App\Models\AffiliateBooking;
use App\Models\AffiliateCommissionItem;
use App\Models\AffiliateCommissionPeriod;
use App\Models\AffiliatePaymentProfile;
use App\Models\AffiliatePayout;
use App\Models\Role;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AffiliateFinanceDevelopmentSeeder extends Seeder
{
    public function run(): void
    {
        $wise = $this->affiliate('Local Finance Wise Affiliate', 'finance.wise.local@nandinibali.test', 'localwisefinance4826');
        $bank = $this->affiliate('Local Finance Bank Affiliate', 'finance.bank.local@nandinibali.test', 'localbankfinance4826');
        $missing = $this->affiliate('Local Missing Profile Affiliate', 'finance.missing.local@nandinibali.test', 'localmissingfinance4826');
        $currency = $this->affiliate('Local Missing Threshold Affiliate', 'finance.currency.local@nandinibali.test', 'localcurrencyfinance4826');

        AffiliatePaymentProfile::query()->updateOrCreate(['affiliate_id' => $wise->id], [
            'payment_method' => AffiliatePaymentMethod::Wise,
            'account_holder_name' => 'Synthetic Wise Affiliate',
            'wise_email' => 'synthetic.wise@example.test',
            'is_complete' => true,
            'verified_at' => now(),
        ]);
        AffiliatePaymentProfile::query()->updateOrCreate(['affiliate_id' => $bank->id], [
            'payment_method' => AffiliatePaymentMethod::BankTransfer,
            'account_holder_name' => 'Synthetic Bank Affiliate',
            'bank_name' => 'Synthetic International Bank',
            'bank_account_name' => 'Synthetic Bank Affiliate',
            'bank_account_number' => '0000111122223333',
            'bank_country' => 'Singapore',
            'swift_bic' => 'SYNTSG22',
            'is_complete' => true,
        ]);

        $draft = $this->period(now()->year, now()->month, AffiliateCommissionPeriodStatus::Draft);
        $reviewDate = now()->subMonthNoOverflow();
        $review = $this->period($reviewDate->year, $reviewDate->month, AffiliateCommissionPeriodStatus::UnderReview);
        $finalDate = now()->subMonthsNoOverflow(2);
        $finalized = $this->period($finalDate->year, $finalDate->month, AffiliateCommissionPeriodStatus::Finalized);

        $this->item($review, $wise, 'pending', AffiliateCommissionItemStatus::PendingReview, '150000.00');
        $this->item($review, $wise, 'held', AffiliateCommissionItemStatus::Held, '125000.00', ['hold_reason' => 'Synthetic reconciliation hold.']);
        $this->item($review, $wise, 'excluded', AffiliateCommissionItemStatus::Excluded, '0.00', ['exclusion_reason' => 'Synthetic duplicate booking.']);
        $this->item($finalized, $missing, 'missing-profile', AffiliateCommissionItemStatus::Approved, '550000.00');
        $this->item($finalized, $currency, 'missing-threshold', AffiliateCommissionItemStatus::Approved, '900.00', [], 'USD');
        $this->item($finalized, $bank, 'below-minimum', AffiliateCommissionItemStatus::Approved, '300000.00');
        $this->item($finalized, $bank, 'carry-forward', AffiliateCommissionItemStatus::Approved, '250000.00');
        $this->item($finalized, $wise, 'adjusted', AffiliateCommissionItemStatus::Approved, '120000.00', [
            'approved_commission_amount' => '135000.00',
            'adjustment_reason' => 'Synthetic documented Finance adjustment.',
        ]);

        foreach (AffiliatePayoutStatus::cases() as $index => $status) {
            $itemStatus = $status === AffiliatePayoutStatus::Paid
                ? AffiliateCommissionItemStatus::Paid
                : ($status === AffiliatePayoutStatus::Cancelled ? AffiliateCommissionItemStatus::Approved : AffiliateCommissionItemStatus::IncludedInPayout);
            $item = $this->item($finalized, $wise, 'payout-'.$status->value, $itemStatus, '600000.00');
            $payout = AffiliatePayout::query()->updateOrCreate(['payout_number' => sprintf('AFF-PAY-%d-%05d', now()->year, 90000 + $index)], [
                'affiliate_id' => $wise->id,
                'currency' => 'IDR',
                'gross_commission_amount' => '600000.00',
                'adjustment_amount' => '0.00',
                'net_payout_amount' => '600000.00',
                'payment_method_snapshot' => AffiliatePaymentMethod::Wise->value,
                'payment_details_masked_snapshot' => 'Wise · s***@example.test',
                'status' => $status,
                'due_at' => now()->addDays(30),
                'prepared_at' => now(),
                'processing_at' => in_array($status, [AffiliatePayoutStatus::Processing, AffiliatePayoutStatus::Paid], true) ? now() : null,
                'paid_at' => $status === AffiliatePayoutStatus::Paid ? now() : null,
                'payment_reference' => $status === AffiliatePayoutStatus::Paid ? 'SYNTHETIC-PAID-REFERENCE' : null,
                'failure_reason' => $status === AffiliatePayoutStatus::Failed ? 'Synthetic external processing failure.' : null,
                'cancelled_at' => $status === AffiliatePayoutStatus::Cancelled ? now() : null,
                'cancellation_reason' => $status === AffiliatePayoutStatus::Cancelled ? 'Synthetic cancellation.' : null,
            ]);

            if ($status !== AffiliatePayoutStatus::Cancelled) {
                $payout->items()->updateOrCreate(['affiliate_commission_item_id' => $item->id], ['amount' => '600000.00']);
            }
        }

        DB::table('affiliate_payout_number_sequences')->updateOrInsert(['sequence_year' => now()->year], ['next_number' => 91000, 'created_at' => now(), 'updated_at' => now()]);
        $draft->touch();
    }

    private function affiliate(string $name, string $email, string $code): Affiliate
    {
        $affiliate = Affiliate::query()->updateOrCreate(['email' => $email], [
            'name' => $name, 'password' => Hash::make('LocalAffiliate!2026'), 'email_verified_at' => now(), 'phone_whatsapp' => '+62 000 0000 0000',
            'status' => AffiliateStatus::Approved, 'registration_source' => AffiliateRegistrationSource::CreatedByNandini,
            'affiliate_code' => $code, 'affiliate_code_generated_at' => now(), 'short_link_slug' => $code, 'short_link_activated_at' => now(),
        ]);
        $affiliate->assignRole(Role::AFFILIATE);

        return $affiliate;
    }

    private function period(int $year, int $month, AffiliateCommissionPeriodStatus $status): AffiliateCommissionPeriod
    {
        $start = CarbonImmutable::create($year, $month, 1, 0, 0, 0, config('app.timezone'));

        return AffiliateCommissionPeriod::query()->updateOrCreate(['period_year' => $year, 'period_month' => $month], [
            'period_start_date' => $start->startOfMonth(), 'period_end_date' => $start->endOfMonth(), 'status' => $status,
            'prepared_at' => $status === AffiliateCommissionPeriodStatus::Draft ? null : now(),
            'finalized_at' => $status === AffiliateCommissionPeriodStatus::Finalized ? now() : null,
            'notes' => 'Synthetic local Part 5 fixture.',
        ]);
    }

    private function item(AffiliateCommissionPeriod $period, Affiliate $affiliate, string $key, AffiliateCommissionItemStatus $status, string $amount, array $extra = [], string $currency = 'IDR'): AffiliateCommissionItem
    {
        $booking = AffiliateBooking::query()->updateOrCreate(['source_system' => 'local_finance_fixture', 'external_booking_id' => 'local-finance-'.$key.'-'.$affiliate->id], [
            'affiliate_id' => $affiliate->id, 'external_booking_reference' => 'LOCAL-FINANCE-'.mb_strtoupper($key), 'affiliate_code_snapshot' => $affiliate->affiliate_code,
            'check_in_date' => $period->period_end_date->copy()->subDays(2), 'check_out_date' => $period->period_end_date, 'stay_nights' => 2,
            'room_revenue_amount' => '6000000.00', 'currency' => $currency, 'booking_status' => AffiliateBookingStatus::Completed,
            'source_status' => 'completed', 'commission_rate_snapshot' => '10.00', 'estimated_commission_amount' => $amount,
            'commission_state' => AffiliateCommissionState::PendingValidation, 'last_synced_at' => now(), 'data_fingerprint' => hash('sha256', $key.$affiliate->id),
        ]);
        $booking->rooms()->updateOrCreate(['external_room_id' => 'local-room'], [
            'room_type_name' => 'Synthetic Jungle Villa', 'room_quantity' => 1, 'stay_nights' => 2, 'room_revenue_amount' => '6000000.00', 'currency' => $currency, 'line_fingerprint' => hash('sha256', 'room'.$key.$affiliate->id),
        ]);

        return AffiliateCommissionItem::query()->updateOrCreate(['affiliate_booking_id' => $booking->id], [
            'commission_period_id' => $period->id, 'affiliate_id' => $affiliate->id, 'currency' => $currency,
            'room_revenue_snapshot' => '6000000.00', 'commission_rate_snapshot' => '10.00', 'original_commission_amount' => $amount,
            'approved_commission_amount' => in_array($status, [AffiliateCommissionItemStatus::Approved, AffiliateCommissionItemStatus::IncludedInPayout, AffiliateCommissionItemStatus::Paid], true) ? $amount : null,
            'status' => $status, 'reviewed_at' => $status === AffiliateCommissionItemStatus::PendingReview ? null : now(),
            'approved_at' => in_array($status, [AffiliateCommissionItemStatus::Approved, AffiliateCommissionItemStatus::IncludedInPayout, AffiliateCommissionItemStatus::Paid], true) ? now() : null,
            ...$extra,
        ]);
    }
}
