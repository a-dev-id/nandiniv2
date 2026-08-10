<?php

namespace Tests\Feature;

use App\Enums\AffiliateBookingStatus;
use App\Enums\AffiliateCommissionItemStatus;
use App\Enums\AffiliateCommissionPeriodStatus;
use App\Enums\AffiliateCommissionState;
use App\Enums\AffiliatePaymentMethod;
use App\Enums\AffiliatePayoutStatus;
use App\Enums\AffiliateRegistrationSource;
use App\Enums\AffiliateStatus;
use App\Models\Affiliate;
use App\Models\AffiliateExchangeRate;
use App\Models\AffiliateAuditEvent;
use App\Models\AffiliateBooking;
use App\Models\AffiliateCommissionItem;
use App\Models\AffiliateCommissionPeriod;
use App\Models\AffiliatePaymentProfile;
use App\Models\AffiliatePayout;
use App\Models\Role;
use App\Models\User;
use App\Services\MembershipEmailRelayService;
use App\Services\Affiliate\Finance\AffiliateCommissionReviewService;
use App\Services\Affiliate\Finance\AffiliatePaymentProfileService;
use App\Services\Affiliate\Finance\AffiliatePayoutWorkflowService;
use App\Services\Affiliate\Finance\PayAffiliateCommissionService;
use App\Services\Affiliate\Finance\PrepareAffiliateCommissionPeriodService;
use App\Services\Affiliate\Finance\PrepareAffiliatePayoutsService;
use App\Services\Affiliate\Finance\SynchronizeAffiliateCommissionItemService;
use Carbon\CarbonImmutable;
use Database\Seeders\AffiliateFoundationSeeder;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class AffiliateFinanceWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['domains.affiliate' => 'affiliate.nandinibali.test', 'app.timezone' => 'Asia/Makassar']);
        $this->seed(AffiliateFoundationSeeder::class);
        $this->mock(MembershipEmailRelayService::class, function ($mock): void {
            $mock->shouldReceive('sendView')->zeroOrMoreTimes()->andReturn([
                'success' => true,
                'status' => 200,
                'response' => ['ok' => true],
                'error' => null,
            ]);
        });
    }

    public function test_monthly_period_preparation_uses_checkout_month_filters_and_is_idempotent(): void
    {
        [, $affiliate] = $this->affiliate();
        $eligible = $this->booking($affiliate, 'eligible', '2026-07-31', AffiliateBookingStatus::Completed, AffiliateCommissionState::PendingValidation, '300000.00');
        $this->booking($affiliate, 'created-in-july-checkout-august', '2026-08-01', AffiliateBookingStatus::Completed, AffiliateCommissionState::PendingValidation);
        $this->booking($affiliate, 'cancelled', '2026-07-20', AffiliateBookingStatus::Cancelled, AffiliateCommissionState::Ineligible, '0.00');
        $this->booking($affiliate, 'no-show', '2026-07-21', AffiliateBookingStatus::NoShow, AffiliateCommissionState::Ineligible, '0.00');
        $this->booking($affiliate, 'refunded', '2026-07-22', AffiliateBookingStatus::Refunded, AffiliateCommissionState::Ineligible, '0.00');

        $first = app(PrepareAffiliateCommissionPeriodService::class)->prepare(2026, 7);
        $second = app(PrepareAffiliateCommissionPeriodService::class)->prepare(2026, 7);

        $this->assertSame(1, $first['created']);
        $this->assertSame(0, $first['unavailable']);
        $this->assertSame(0, $second['created']);
        $this->assertSame(1, $second['unchanged']);
        $this->assertDatabaseCount('affiliate_commission_periods', 1);
        $this->assertDatabaseCount('affiliate_commission_items', 1);
        $item = AffiliateCommissionItem::sole();
        $this->assertSame($eligible->id, $item->affiliate_booking_id);
        $this->assertSame('300000.00', $item->original_commission_amount);
        $this->assertSame(AffiliateCommissionItemStatus::PendingReview, $item->status);
        $this->assertSame(AffiliateCommissionPeriodStatus::UnderReview, $item->period->status);
    }

    public function test_finance_review_requires_permissions_reasons_and_preserves_original_amount(): void
    {
        [, $affiliate] = $this->affiliate();
        $item = $this->preparedItem($affiliate, '300000.00');
        $sales = $this->staff(Role::SALES_MARKETING);

        try {
            app(AffiliateCommissionReviewService::class)->approve($item, $sales, '300000.00');
            $this->fail('Sales should not approve commission.');
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }

        $finance = $this->staff(Role::FINANCE);
        try {
            app(AffiliateCommissionReviewService::class)->approve($item, $finance, '350000.00');
            $this->fail('Adjusted approval should require a reason.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('adjustment_reason', $exception->errors());
        }

        $approved = app(AffiliateCommissionReviewService::class)->approve($item, $finance, '350000.00', 'Approved service recovery exception.');
        $this->assertSame('300000.00', $approved->original_commission_amount);
        $this->assertSame('350000.00', $approved->approved_commission_amount);
        $this->assertSame(AffiliateCommissionItemStatus::Approved, $approved->status);
        $this->assertSame($finance->id, $approved->approved_by);
        $this->assertDatabaseHas('affiliate_audit_events', ['event' => 'affiliate_commission.adjusted_and_approved']);

        $held = $this->preparedItem($affiliate, '100000.00', 'held');
        $excluded = $this->preparedItem($affiliate, '100000.00', 'excluded');
        $this->expectException(ValidationException::class);
        app(AffiliateCommissionReviewService::class)->hold($held, $finance, '');
    }

    public function test_hold_exclusion_finalization_and_reopening_are_controlled(): void
    {
        [, $affiliate] = $this->affiliate();
        $finance = $this->staff(Role::FINANCE);
        $pending = $this->preparedItem($affiliate, '300000.00', 'pending');
        $held = $this->preparedItem($affiliate, '100000.00', 'held');
        $excluded = $this->preparedItem($affiliate, '100000.00', 'excluded');
        $period = $pending->period;

        app(AffiliateCommissionReviewService::class)->hold($held, $finance, 'Awaiting reconciliation.');
        app(AffiliateCommissionReviewService::class)->exclude($excluded, $finance, 'Duplicate booking.');

        try {
            app(AffiliateCommissionReviewService::class)->finalize($period, $finance);
            $this->fail('Pending review must block finalization.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('period', $exception->errors());
        }

        app(AffiliateCommissionReviewService::class)->approve($pending, $finance, '300000.00');
        $finalized = app(AffiliateCommissionReviewService::class)->finalize($period, $finance);
        $this->assertSame(AffiliateCommissionPeriodStatus::Finalized, $finalized->status);
        $this->assertSame($finance->id, $finalized->finalized_by);

        try {
            app(AffiliateCommissionReviewService::class)->approve($held->fresh(), $finance, '100000.00');
            $this->fail('Finalized period review should be locked.');
        } catch (DomainException) {
            $this->assertTrue(true);
        }

        $reopened = app(AffiliateCommissionReviewService::class)->reopen($finalized, $finance, 'Documented reconciliation correction.');
        $this->assertSame(AffiliateCommissionPeriodStatus::Reopened, $reopened->status);
        $this->assertStringContainsString('Documented reconciliation correction', $reopened->notes);
    }

    public function test_payment_profile_is_own_only_validated_encrypted_and_masked(): void
    {
        [$user, $affiliate] = $this->affiliate();
        [$otherUser, $otherAffiliate] = $this->affiliate('other@example.com', 'other4826');

        $this->actingAs($user, 'affiliate')->put('http://affiliate.nandinibali.test/payment-details', [
            'payment_method' => 'wise',
            'preferred_currency' => 'USD',
            'account_holder_name' => 'Synthetic Partner',
            'wise_email' => 'synthetic@example.test',
        ])->assertRedirect(route('affiliate.payment-details.edit'));

        $profile = $affiliate->paymentProfile()->sole();
        $raw = DB::table('affiliate_payment_profiles')->where('id', $profile->id)->first();
        $this->assertSame('synthetic@example.test', $profile->wise_email);
        $this->assertSame('USD', $profile->preferred_currency->value);
        $this->assertNotSame('synthetic@example.test', $raw->wise_email);
        $this->assertNotSame('Synthetic Partner', $raw->account_holder_name);
        $this->assertSame('Wise · s***@example.test', $profile->maskedDetails());
        $this->assertNull($otherAffiliate->paymentProfile);

        $this->actingAs($otherUser, 'affiliate')->get('http://affiliate.nandinibali.test/payment-details')
            ->assertOk()
            ->assertSee('Preferred currency')
            ->assertDontSee('Back to dashboard')
            ->assertDontSee('Internal review does not mean the account was verified through Wise.')
            ->assertDontSee('synthetic@example.test');
        $pending = $this->affiliate('pending@example.com', 'pending4826', AffiliateStatus::Pending)[0];
        $this->actingAs($pending, 'affiliate')->get('http://affiliate.nandinibali.test/payment-details')->assertForbidden();
    }

    public function test_bank_profile_validation_and_finance_review_are_permission_protected(): void
    {
        [$user, $affiliate] = $this->affiliate();
        $this->actingAs($user, 'affiliate')->put('http://affiliate.nandinibali.test/payment-details', [
            'payment_method' => 'bank_transfer', 'account_holder_name' => 'Synthetic Partner', 'bank_name' => '',
        ])->assertSessionHasErrors(['bank_name', 'bank_account_name', 'bank_account_number', 'bank_country']);

        $profile = app(AffiliatePaymentProfileService::class)->updateOwn($affiliate, [
            'payment_method' => 'bank_transfer', 'account_holder_name' => 'Synthetic Partner', 'wise_email' => null,
            'bank_name' => 'Test Bank', 'bank_account_name' => 'Synthetic Partner', 'bank_account_number' => '0000 1111 2222', 'bank_country' => 'Singapore', 'swift_bic' => 'TESTSG22',
        ]);
        $this->assertNotNull($profile->verified_at);
        $this->assertNull($profile->verified_by);
        $sales = $this->staff(Role::SALES_MARKETING);
        $finance = $this->staff(Role::FINANCE);
        $this->actingAs($sales, 'web')->get(route('filament.admin.resources.affiliate-payment-profiles.view', $profile))->assertForbidden();
        $this->actingAs($finance, 'web')->get(route('filament.admin.resources.affiliate-payment-profiles.view', $profile))->assertOk()->assertSee('000011112222');
        $reviewed = app(AffiliatePaymentProfileService::class)->markReviewed($profile, $finance);
        $this->assertNotNull($reviewed->verified_at);
        $this->assertSame($finance->id, $reviewed->verified_by);
    }

    public function test_minimums_carry_forward_currencies_profiles_and_suspension_gate_payouts(): void
    {
        [, $affiliate] = $this->affiliate();
        $finance = $this->staff(Role::FINANCE);
        $this->wiseProfile($affiliate);
        $july = $this->approvedFinalizedItem($affiliate, '300000.00', 'IDR', 'july');

        $first = app(PrepareAffiliatePayoutsService::class)->prepare($finance);
        $this->assertSame(1, $first['carried']);
        $this->assertDatabaseCount('affiliate_payouts', 0);
        $this->assertSame(AffiliateCommissionItemStatus::Approved, $july->fresh()->status);

        $august = $this->approvedFinalizedItem($affiliate, '250000.00', 'IDR', 'august');
        $usd = $this->approvedFinalizedItem($affiliate, '1000.00', 'USD', 'usd');
        $second = app(PrepareAffiliatePayoutsService::class)->prepare($finance);
        $this->assertSame(1, $second['created']);
        $this->assertSame(1, $second['missing_threshold']);
        $payout = AffiliatePayout::sole();
        $this->assertSame('550000.00', $payout->gross_commission_amount);
        $this->assertSame('IDR', $payout->currency);
        $this->assertCount(2, $payout->items);
        $this->assertSame(AffiliateCommissionItemStatus::Approved, $usd->fresh()->status);

        $affiliate->update(['status' => AffiliateStatus::Suspended]);
        $this->approvedFinalizedItem($affiliate, '500000.00', 'IDR', 'suspended');
        $third = app(PrepareAffiliatePayoutsService::class)->prepare($finance);
        $this->assertGreaterThan(0, $third['account_review']);
    }

    public function test_payout_lifecycle_adjustment_paid_notification_and_cancellation_preserve_history(): void
    {
        [, $affiliate] = $this->affiliate();
        $finance = $this->staff(Role::FINANCE);
        $this->wiseProfile($affiliate);
        $item = $this->approvedFinalizedItem($affiliate, '600000.00');
        app(PrepareAffiliatePayoutsService::class)->prepare($finance);
        $payout = AffiliatePayout::sole();
        $workflow = app(AffiliatePayoutWorkflowService::class);

        try {
            $workflow->adjust($payout, $finance, '-10000.00', null);
            $this->fail('Non-zero adjustment should require a reason.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('adjustment_reason', $exception->errors());
        }

        $payout = $workflow->adjust($payout, $finance, '-10000.00', 'External transfer fee correction.');
        $this->assertSame('590000.00', $payout->net_payout_amount);
        $payout = $workflow->markReady($payout, $finance);
        $payout = $workflow->startProcessing($payout, $finance);

        try {
            $workflow->markPaid($payout, $finance, '2026-08-04', '');
            $this->fail('Payment reference is required.');
        } catch (ValidationException) {
            $this->assertTrue(true);
        }

        $paid = $workflow->markPaid($payout, $finance, '2026-08-04', 'SYNTHETIC-REFERENCE');
        $this->assertSame(AffiliatePayoutStatus::Paid, $paid->status);
        $this->assertSame(AffiliateCommissionItemStatus::Paid, $item->fresh()->status);
        $this->assertDatabaseHas('affiliate_audit_events', [
            'affiliate_id' => $affiliate->id,
            'event' => 'affiliate_payout.paid_notification_dispatched',
        ]);
        $this->expectException(DomainException::class);
        $workflow->cancel($paid, $finance, 'Should not be possible.');
    }

    public function test_source_changes_refresh_pending_reopen_reviewed_and_warn_locked_items(): void
    {
        [, $affiliate] = $this->affiliate();
        $finance = $this->staff(Role::FINANCE);
        $item = $this->preparedItem($affiliate, '100000.00', 'source-change');
        $booking = $item->booking;
        $booking->update(['room_revenue_amount' => '2000000.00', 'estimated_commission_amount' => '200000.00']);
        app(SynchronizeAffiliateCommissionItemService::class)->synchronize($booking->fresh());
        $this->assertSame('200000.00', $item->fresh()->original_commission_amount);

        $item = app(AffiliateCommissionReviewService::class)->approve($item->fresh(), $finance, '200000.00');
        $booking->update(['room_revenue_amount' => '2500000.00', 'estimated_commission_amount' => '250000.00']);
        app(SynchronizeAffiliateCommissionItemService::class)->synchronize($booking->fresh());
        $this->assertSame(AffiliateCommissionItemStatus::PendingReview, $item->fresh()->status);
        $this->assertTrue($item->fresh()->source_changed_after_review);
        $this->assertNull($item->fresh()->approved_commission_amount);

        $item->update(['status' => AffiliateCommissionItemStatus::Paid]);
        $item->period->update(['status' => AffiliateCommissionPeriodStatus::Finalized]);
        $booking->update(['estimated_commission_amount' => '300000.00']);
        app(SynchronizeAffiliateCommissionItemService::class)->synchronize($booking->fresh());
        $this->assertSame(AffiliateCommissionItemStatus::Paid, $item->fresh()->status);
        $this->assertSame('250000.00', $item->fresh()->original_commission_amount);
        $this->assertNotNull($item->fresh()->discrepancy_warning);
    }

    public function test_missing_profile_and_duplicate_preparation_are_safe_and_payout_numbers_are_unique(): void
    {
        [, $affiliate] = $this->affiliate();
        $finance = $this->staff(Role::FINANCE);
        $this->approvedFinalizedItem($affiliate, '600000.00', 'IDR', 'missing-profile');
        $missing = app(PrepareAffiliatePayoutsService::class)->prepare($finance);
        $this->assertSame(1, $missing['missing_profile']);
        $this->assertDatabaseCount('affiliate_payouts', 0);

        $this->wiseProfile($affiliate);
        $first = app(PrepareAffiliatePayoutsService::class)->prepare($finance);
        $second = app(PrepareAffiliatePayoutsService::class)->prepare($finance);
        $this->assertSame(1, $first['created']);
        $this->assertSame(0, $second['created']);
        $this->assertDatabaseCount('affiliate_payouts', 1);
        $this->assertMatchesRegularExpression('/^AFF-PAY-\d{4}-\d{5}$/', AffiliatePayout::sole()->payout_number);

        $this->approvedFinalizedItem($affiliate, '700000.00', 'IDR', 'second-number');
        app(PrepareAffiliatePayoutsService::class)->prepare($finance);
        $this->assertCount(2, AffiliatePayout::query()->distinct()->pluck('payout_number'));
    }

    public function test_finance_audits_and_serialization_never_contain_plain_payment_values(): void
    {
        [, $affiliate] = $this->affiliate();
        $profile = $this->wiseProfile($affiliate);
        $serializedProfile = $profile->toJson();
        $serializedAudits = AffiliateAuditEvent::query()->get()->toJson();

        $this->assertStringNotContainsString('synthetic@example.test', $serializedProfile);
        $this->assertStringNotContainsString('Synthetic Affiliate', $serializedProfile);
        $this->assertStringNotContainsString('synthetic@example.test', $serializedAudits);
        $this->assertStringNotContainsString((string) $profile->getRawOriginal('wise_email'), $serializedAudits);
    }

    public function test_cancelled_unpaid_payout_releases_items_without_deleting_payout(): void
    {
        [, $affiliate] = $this->affiliate();
        $finance = $this->staff(Role::FINANCE);
        $this->wiseProfile($affiliate);
        $item = $this->approvedFinalizedItem($affiliate, '600000.00');
        app(PrepareAffiliatePayoutsService::class)->prepare($finance);
        $payout = AffiliatePayout::sole();
        $cancelled = app(AffiliatePayoutWorkflowService::class)->cancel($payout, $finance, 'Payment profile must be corrected.');

        $this->assertSame(AffiliatePayoutStatus::Cancelled, $cancelled->status);
        $this->assertSame(AffiliateCommissionItemStatus::Approved, $item->fresh()->status);
        $this->assertDatabaseHas('affiliate_payouts', ['id' => $payout->id, 'status' => 'cancelled']);
        $this->assertDatabaseMissing('affiliate_payout_items', ['affiliate_payout_id' => $payout->id]);
        $this->assertDatabaseHas('affiliate_audit_events', ['event' => 'affiliate_commission.released_from_cancelled_payout']);
    }

    public function test_dashboard_is_private_currency_separated_and_hides_finance_and_booking_secrets(): void
    {
        [$user, $affiliate] = $this->affiliate();
        [, $other] = $this->affiliate('other-dashboard@example.com', 'otherdash4826');
        $this->approvedFinalizedItem($affiliate, '600000.00', 'IDR', 'own');
        $otherItem = $this->approvedFinalizedItem($other, '999999.00', 'USD', 'other');
        $otherItem->update(['adjustment_reason' => 'PRIVATE INTERNAL REASON']);

        $this->actingAs($user, 'affiliate')->get('http://affiliate.nandinibali.test/dashboard')
            ->assertOk()
            ->assertSee('Commission summary')
            ->assertSee('Pending')
            ->assertSee('IDR')
            ->assertDontSee('999999.00')
            ->assertDontSee('PRIVATE INTERNAL REASON')
            ->assertDontSee('Room Revenue Snapshot')
            ->assertDontSee('external_booking_reference');
    }

    public function test_command_scheduler_and_finance_filament_access_follow_part_five_rules(): void
    {
        CarbonImmutable::setTestNow('2026-08-04 02:40:00');
        [, $affiliate] = $this->affiliate();
        $this->booking($affiliate, 'previous-month', '2026-07-20', AffiliateBookingStatus::Completed, AffiliateCommissionState::PendingValidation);
        $this->assertSame(0, Artisan::call('affiliate:prepare-commissions'));
        $this->assertSame(0, Artisan::call('affiliate:prepare-commissions'));
        $this->assertDatabaseCount('affiliate_commission_items', 1);
        $this->assertStringContainsString('Commission period: July 2026', Artisan::output());
        $events = collect(Schedule::events());
        $scheduled = $events->first(fn ($event): bool => str_contains($event->command ?? '', 'affiliate:prepare-commissions'));
        $this->assertNotNull($scheduled);
        $this->assertSame('40 2 * * *', $scheduled->expression);

        $finance = $this->staff(Role::FINANCE);
        $sales = $this->staff(Role::SALES_MARKETING);
        $this->actingAs($finance, 'web')->get(route('filament.admin.resources.affiliate-commission-periods.index'))->assertOk();
        $this->actingAs($finance, 'web')->get(route('filament.admin.resources.affiliate-payouts.index'))->assertOk();
        $this->actingAs($sales, 'web')->get(route('filament.admin.resources.affiliate-payouts.index'))->assertForbidden();
    }

    public function test_pending_commission_can_be_marked_paid_in_one_step(): void
    {
        [$affiliateUser, $affiliate] = $this->affiliate();
        $this->wiseProfile($affiliate);
        $item = $this->approvedFinalizedItem($affiliate, '347687.80');
        $finance = $this->staff(Role::FINANCE);

        $payout = app(PayAffiliateCommissionService::class)->pay(
            $item,
            $finance,
            '2026-08-10',
            'TEST-PAYMENT-001',
            'Simplified payment workflow test.',
        );

        $this->assertSame(AffiliatePayoutStatus::Paid, $payout->status);
        $this->assertSame('347687.80', $payout->net_payout_amount);
        $this->assertSame('TEST-PAYMENT-001', $payout->payment_reference);
        $this->assertSame(AffiliateCommissionItemStatus::Paid, $item->fresh()->status);
        $this->assertDatabaseHas('affiliate_payout_items', [
            'affiliate_payout_id' => $payout->id,
            'affiliate_commission_item_id' => $item->id,
            'amount' => '347687.80',
        ]);
        $this->assertDatabaseHas('affiliate_audit_events', ['event' => 'affiliate_commission.paid_directly']);
        $this->actingAs($affiliateUser, 'affiliate')
            ->get('http://affiliate.nandinibali.test/dashboard')
            ->assertOk()
            ->assertSee('Payment Reference')
            ->assertSee('TEST-PAYMENT-001');

        $this->expectException(DomainException::class);
        app(PayAffiliateCommissionService::class)->pay($item->fresh(), $finance, '2026-08-10', 'DUPLICATE');
    }

    public function test_manual_exchange_rate_converts_and_locks_usd_payout(): void
    {
        [$affiliateUser, $affiliate] = $this->affiliate('usd-affiliate@example.com', 'usdfinance4826');
        $this->wiseProfile($affiliate)->update(['preferred_currency' => 'USD']);
        AffiliateExchangeRate::query()->create([
            'base_currency' => 'IDR',
            'quote_currency' => 'USD',
            'base_units_per_quote' => '16478.100000',
            'is_active' => true,
            'effective_at' => now(),
        ]);
        $item = $this->approvedFinalizedItem($affiliate, '347687.80', 'IDR', 'usd');
        $finance = $this->staff(Role::FINANCE);

        $this->actingAs($affiliateUser, 'affiliate')
            ->get('http://affiliate.nandinibali.test/dashboard')
            ->assertOk()
            ->assertSee('USD 21.10')
            ->assertSee('Estimated using Nandini');

        $payout = app(PayAffiliateCommissionService::class)->pay(
            $item,
            $finance,
            '2026-08-10',
            'USD-PAYMENT-001',
        );

        $this->assertSame('USD', $payout->currency);
        $this->assertSame('21.10', $payout->net_payout_amount);
        $this->assertSame('IDR', $payout->source_currency);
        $this->assertSame('347687.80', $payout->source_amount);
        $this->assertSame('16478.100000', $payout->exchange_rate_snapshot);

        AffiliateExchangeRate::query()->where('quote_currency', 'USD')->update(['base_units_per_quote' => '17000.000000']);

        $this->assertSame('21.10', $payout->fresh()->net_payout_amount);
        $this->assertSame('16478.100000', $payout->fresh()->exchange_rate_snapshot);
    }

    private function affiliate(string $email = 'affiliate@example.com', string $code = 'finance4826', AffiliateStatus $status = AffiliateStatus::Approved): array
    {
        $affiliate = Affiliate::query()->create([
            'name' => 'Synthetic Affiliate', 'email' => $email, 'password' => 'password', 'phone_whatsapp' => '+62 800 0000',
            'status' => $status, 'registration_source' => AffiliateRegistrationSource::CreatedByNandini,
            'affiliate_code' => $code, 'affiliate_code_generated_at' => now(), 'short_link_slug' => $code,
            'short_link_activated_at' => $status === AffiliateStatus::Approved ? now() : null,
        ]);
        $affiliate->assignRole(Role::AFFILIATE);

        return [$affiliate, $affiliate];
    }

    private function staff(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    private function booking(Affiliate $affiliate, string $externalId, string $checkOut, AffiliateBookingStatus $status, AffiliateCommissionState $state, string $commission = '100000.00', string $currency = 'IDR'): AffiliateBooking
    {
        $booking = AffiliateBooking::query()->create([
            'affiliate_id' => $affiliate->id, 'source_system' => 'part5_test', 'external_booking_id' => $externalId.'-'.$affiliate->id,
            'external_booking_reference' => 'PRIVATE-REFERENCE', 'affiliate_code_snapshot' => $affiliate->affiliate_code,
            'check_in_date' => CarbonImmutable::parse($checkOut)->subDays(2), 'check_out_date' => $checkOut, 'stay_nights' => 2,
            'room_revenue_amount' => $status->isIneligible() ? '0.00' : '1000000.00', 'currency' => $currency, 'booking_status' => $status,
            'source_status' => $status->value, 'commission_rate_snapshot' => '10.00', 'estimated_commission_amount' => $commission,
            'commission_state' => $state, 'last_synced_at' => now(), 'data_fingerprint' => hash('sha256', $externalId.$affiliate->id),
        ]);
        $booking->rooms()->create(['external_room_id' => 'room-'.$booking->id, 'room_type_name' => 'Synthetic Jungle Villa', 'room_quantity' => 1, 'stay_nights' => 2, 'room_revenue_amount' => '1000000.00', 'currency' => $currency, 'line_fingerprint' => hash('sha256', 'room'.$booking->id)]);

        return $booking;
    }

    private function preparedItem(Affiliate $affiliate, string $amount, string $suffix = 'one'): AffiliateCommissionItem
    {
        $booking = $this->booking($affiliate, 'prepared-'.$suffix, '2026-07-20', AffiliateBookingStatus::Completed, AffiliateCommissionState::PendingValidation, $amount);
        app(PrepareAffiliateCommissionPeriodService::class)->prepare(2026, 7);

        return $booking->commissionItem()->sole();
    }

    private function approvedFinalizedItem(Affiliate $affiliate, string $amount, string $currency = 'IDR', string $suffix = 'one'): AffiliateCommissionItem
    {
        $month = match ($suffix) {
            'august' => 8, 'usd' => 9, 'suspended' => 10, 'missing-profile' => 11, 'second-number' => 12, default => 7
        };
        $booking = $this->booking($affiliate, 'approved-'.$suffix, "2026-{$month}-20", AffiliateBookingStatus::Completed, AffiliateCommissionState::PendingValidation, $amount, $currency);
        $period = AffiliateCommissionPeriod::query()->firstOrCreate(['period_year' => 2026, 'period_month' => $month], ['period_start_date' => "2026-{$month}-01", 'period_end_date' => CarbonImmutable::create(2026, $month)->endOfMonth(), 'status' => AffiliateCommissionPeriodStatus::Finalized, 'finalized_at' => now()]);

        return AffiliateCommissionItem::query()->create([
            'commission_period_id' => $period->id, 'affiliate_booking_id' => $booking->id, 'affiliate_id' => $affiliate->id,
            'currency' => $currency, 'room_revenue_snapshot' => '1000000.00', 'commission_rate_snapshot' => '10.00',
            'original_commission_amount' => $amount, 'approved_commission_amount' => $amount, 'status' => AffiliateCommissionItemStatus::Approved,
            'reviewed_at' => now(), 'approved_at' => now(),
        ]);
    }

    private function wiseProfile(Affiliate $affiliate): AffiliatePaymentProfile
    {
        return app(AffiliatePaymentProfileService::class)->updateOwn($affiliate, [
            'payment_method' => AffiliatePaymentMethod::Wise->value, 'account_holder_name' => 'Synthetic Affiliate', 'wise_email' => 'synthetic@example.test',
            'bank_name' => null, 'bank_account_name' => null, 'bank_account_number' => null, 'bank_country' => null, 'swift_bic' => null,
        ]);
    }
}
