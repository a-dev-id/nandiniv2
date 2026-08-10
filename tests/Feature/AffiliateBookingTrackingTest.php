<?php

namespace Tests\Feature;

use App\Enums\AffiliateBookingStatus;
use App\Enums\AffiliateCommissionState;
use App\Enums\AffiliateRegistrationSource;
use App\Enums\AffiliateStatus;
use App\Filament\Resources\AffiliateBookings\AffiliateBookingResource;
use App\Models\Affiliate;
use App\Models\AffiliateBooking;
use App\Models\AffiliateBookingRoom;
use App\Models\AffiliateProgramSetting;
use App\Models\Member;
use App\Models\Role;
use App\Models\SyncedWebhotelierBooking;
use App\Models\User;
use App\Services\Affiliate\Booking\AffiliateBookingAnalyticsService;
use App\Services\Affiliate\Booking\AffiliateBookingData;
use App\Services\Affiliate\Booking\AffiliateBookingSyncResult;
use App\Services\Affiliate\Booking\SetManualAffiliateBookingStatusService;
use App\Services\Affiliate\Booking\SyncAffiliateBookingService;
use App\Services\Affiliate\AffiliateNotificationService;
use App\Services\BookingSyncService;
use App\Services\MembershipBookingApiService;
use Carbon\CarbonImmutable;
use Database\Seeders\AffiliateFoundationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Tests\TestCase;

class AffiliateBookingTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_sync_cron_does_not_start_or_write_browser_sessions(): void
    {
        $route = collect(app('router')->getRoutes()->getRoutes())
            ->first(fn ($route) => $route->getName() === 'cron.bookings.sync');

        $this->assertNotNull($route);
        $this->assertNotContains(
            \Illuminate\Session\Middleware\StartSession::class,
            app('router')->gatherRouteMiddleware($route),
        );
    }

    protected function setUp(): void
    {
        parent::setUp();
        config(['domains.affiliate' => 'affiliate.nandinibali.test']);
        $this->seed(AffiliateFoundationSeeder::class);
    }

    public function test_valid_case_normalized_manual_voucher_creates_privacy_safe_booking_without_click(): void
    {
        [, $affiliate] = $this->affiliate(AffiliateStatus::Approved, 'partner4826');

        $result = $this->sync($this->data($affiliate, [
            'affiliateCode' => '  PARTNER4826  ',
            'roomItems' => [['external_room_id' => 'villa-1', 'room_type_name' => 'Jungle View Villa', 'room_quantity' => 1]],
            'roomRevenueAmount' => '3686000.00',
        ]));

        $this->assertSame('created', $result->state);
        $this->assertDatabaseCount('affiliate_click_events', 0);
        $booking = AffiliateBooking::query()->with('rooms')->sole();
        $this->assertSame($affiliate->id, $booking->affiliate_id);
        $this->assertSame('partner4826', $booking->affiliate_code_snapshot);
        $this->assertSame(3, $booking->stay_nights);
        $this->assertSame('10.00', $booking->commission_rate_snapshot);
        $this->assertSame('368600.00', $booking->estimated_commission_amount);
        $this->assertSame(AffiliateCommissionState::Estimated, $booking->commission_state);
        $this->assertSame('Jungle View Villa', $booking->rooms->sole()->room_type_name);
    }

    public function test_unknown_missing_and_partial_codes_are_skipped(): void
    {
        [, $affiliate] = $this->affiliate(AffiliateStatus::Approved, 'exact4826');

        $missing = $this->sync($this->data($affiliate, ['externalBookingId' => 'missing', 'affiliateCode' => null]));
        $unknown = $this->sync($this->data($affiliate, ['externalBookingId' => 'unknown', 'affiliateCode' => 'not-known']));
        $partial = $this->sync($this->data($affiliate, ['externalBookingId' => 'partial', 'affiliateCode' => 'exact']));

        $this->assertSame('skipped_no_affiliate_code', $missing->state);
        $this->assertSame('skipped_unknown_affiliate', $unknown->state);
        $this->assertSame('skipped_unknown_affiliate', $partial->state);
        $this->assertDatabaseCount('affiliate_bookings', 0);
    }

    public function test_repeated_update_and_stale_events_are_idempotent_and_audited_safely(): void
    {
        [, $affiliate] = $this->affiliate(AffiliateStatus::Approved, 'sync4826');
        $first = $this->data($affiliate, ['sourceUpdatedAt' => CarbonImmutable::parse('2026-08-04 10:00:00')]);

        $this->assertSame('created', $this->sync($first)->state);
        $auditCount = $affiliate->auditEvents()->count();
        $this->assertSame('unchanged', $this->sync($first)->state);
        $this->assertSame($auditCount, $affiliate->auditEvents()->count());

        $updated = $this->data($affiliate, [
            'checkInDate' => '2026-09-11',
            'checkOutDate' => '2026-09-16',
            'roomRevenueAmount' => '5000.00',
            'roomItems' => [
                ['external_room_id' => 'one', 'room_type_name' => 'Jungle View Villa'],
                ['external_room_id' => 'two', 'room_type_name' => 'Panorama View Villa'],
            ],
            'sourceUpdatedAt' => CarbonImmutable::parse('2026-08-04 11:00:00'),
        ]);
        $this->assertSame('updated', $this->sync($updated)->state);
        $booking = AffiliateBooking::query()->with('rooms')->sole();
        $this->assertSame(5, $booking->stay_nights);
        $this->assertSame('500.00', $booking->estimated_commission_amount);
        $this->assertCount(2, $booking->rooms);

        $stale = $this->data($affiliate, [
            'bookingStatus' => 'cancelled',
            'sourceUpdatedAt' => CarbonImmutable::parse('2026-08-04 09:00:00'),
        ]);
        $this->assertSame('stale_update_ignored', $this->sync($stale)->state);
        $this->assertSame(AffiliateBookingStatus::Confirmed, $booking->fresh()->booking_status);
        $this->assertDatabaseHas('affiliate_audit_events', ['event' => 'affiliate_booking.stale_update_ignored']);
    }

    public function test_room_lines_do_not_duplicate_and_source_nights_use_date_calculation(): void
    {
        [, $affiliate] = $this->affiliate(AffiliateStatus::Approved, 'rooms4826');
        $data = $this->data($affiliate, ['roomItems' => [
            ['external_room_id' => 'a', 'room_type_name' => 'Villa A', 'room_quantity' => 2, 'stay_nights' => 99],
            ['external_room_id' => 'b', 'room_type_name' => 'Villa B', 'room_quantity' => 1],
        ]]);

        $this->sync($data);
        $this->sync($data);
        $booking = AffiliateBooking::query()->with('rooms')->sole();
        $this->assertCount(2, $booking->rooms);
        $this->assertSame(3, $booking->stay_nights);
        $this->assertSame([3, 3], $booking->rooms->pluck('stay_nights')->all());
        $this->assertStringContainsString('date-derived nights', $booking->synchronization_warning);
        $this->assertSame(9, app(AffiliateBookingAnalyticsService::class)->summaryForAffiliate($affiliate)['room_nights']);
    }

    public function test_invalid_dates_and_duplicate_room_identifiers_fail_without_writes(): void
    {
        [, $affiliate] = $this->affiliate(AffiliateStatus::Approved, 'invalid4826');

        $invalidDates = $this->sync($this->data($affiliate, ['checkOutDate' => '2026-09-10']));
        $duplicateRooms = $this->sync($this->data($affiliate, [
            'externalBookingId' => 'duplicate-rooms',
            'roomItems' => [
                ['external_room_id' => 'same', 'room_type_name' => 'A'],
                ['external_room_id' => 'same', 'room_type_name' => 'B'],
            ],
        ]));
        $invalidRoomRevenue = $this->sync($this->data($affiliate, [
            'externalBookingId' => 'invalid-room-revenue',
            'roomItems' => [['external_room_id' => 'room-1', 'room_revenue_amount' => '1e3']],
        ]));

        $this->assertSame('failed_validation', $invalidDates->state);
        $this->assertSame('failed_validation', $duplicateRooms->state);
        $this->assertSame('failed_validation', $invalidRoomRevenue->state);
        $this->assertDatabaseCount('affiliate_bookings', 0);
    }

    public function test_commission_states_and_missing_revenue_follow_part_four_rules(): void
    {
        [, $affiliate] = $this->affiliate(AffiliateStatus::Approved, 'states4826');
        $expected = [
            'confirmed' => [AffiliateCommissionState::Estimated, '100.00'],
            'in_house' => [AffiliateCommissionState::Estimated, '100.00'],
            'completed' => [AffiliateCommissionState::PendingValidation, '100.00'],
            'cancelled' => [AffiliateCommissionState::Ineligible, '0.00'],
            'no_show' => [AffiliateCommissionState::Ineligible, '0.00'],
            'refunded' => [AffiliateCommissionState::Ineligible, '0.00'],
        ];

        foreach ($expected as $status => [$state, $amount]) {
            $this->sync($this->data($affiliate, ['externalBookingId' => 'state-'.$status, 'bookingStatus' => $status]));
            $booking = AffiliateBooking::query()->where('external_booking_id', 'state-'.$status)->sole();
            $this->assertSame($state, $booking->commission_state);
            $this->assertSame($amount, $booking->estimated_commission_amount);
        }

        $this->sync($this->data($affiliate, ['externalBookingId' => 'no-revenue', 'roomRevenueAmount' => null]));
        $this->sync($this->data($affiliate, ['externalBookingId' => 'unknown-status', 'bookingStatus' => 'unmapped-source-state']));
        $this->assertSame(AffiliateCommissionState::CalculationUnavailable, AffiliateBooking::where('external_booking_id', 'no-revenue')->sole()->commission_state);
        $unknown = AffiliateBooking::where('external_booking_id', 'unknown-status')->sole();
        $this->assertSame(AffiliateBookingStatus::Unknown, $unknown->booking_status);
        $this->assertSame('unmapped_source_state', $unknown->source_status);
    }

    public function test_commission_rate_is_snapshotted_and_currency_totals_are_separate(): void
    {
        [, $affiliate] = $this->affiliate(AffiliateStatus::Approved, 'currency4826');
        $this->sync($this->data($affiliate, ['externalBookingId' => 'idr', 'roomRevenueAmount' => '1000.00', 'currency' => 'IDR']));
        AffiliateProgramSetting::current()->update(['affiliate_commission_percentage' => '15.00']);
        $this->sync($this->data($affiliate, ['externalBookingId' => 'usd', 'roomRevenueAmount' => '1000.00', 'currency' => 'USD']));
        $this->sync($this->data($affiliate, ['externalBookingId' => 'idr', 'roomRevenueAmount' => '2000.00', 'currency' => 'IDR', 'sourceUpdatedAt' => CarbonImmutable::now()->addMinute()]));
        $this->sync($this->data($affiliate, ['externalBookingId' => 'completed-idr', 'bookingStatus' => 'checked_out', 'roomRevenueAmount' => '5000.00', 'currency' => 'IDR']));

        $idr = AffiliateBooking::where('external_booking_id', 'idr')->sole();
        $this->assertSame('10.00', $idr->commission_rate_snapshot);
        $this->assertSame('200.00', $idr->estimated_commission_amount);
        $totals = collect(app(AffiliateBookingAnalyticsService::class)->summaryForAffiliate($affiliate)['commission_totals'])->keyBy('currency');
        $this->assertSame(200.0, (float) $totals['IDR']['amount']);
        $this->assertSame(150.0, (float) $totals['USD']['amount']);
    }

    public function test_historical_attribution_remains_after_affiliate_suspension(): void
    {
        [, $affiliate] = $this->affiliate(AffiliateStatus::Approved, 'history4826');
        $this->sync($this->data($affiliate));
        $affiliate->update(['status' => AffiliateStatus::Suspended]);

        $result = $this->sync($this->data($affiliate, [
            'affiliateCode' => null,
            'bookingStatus' => 'completed',
            'sourceUpdatedAt' => CarbonImmutable::now()->addMinute(),
        ]));

        $this->assertSame('updated', $result->state);
        $this->assertSame($affiliate->id, $result->booking->affiliate_id);
        $this->assertNotNull($result->booking->attribution_warning);
    }

    public function test_transaction_rolls_back_when_room_persistence_fails(): void
    {
        [, $affiliate] = $this->affiliate(AffiliateStatus::Approved, 'rollback4826');
        Event::listen('eloquent.created: '.AffiliateBookingRoom::class, function (): void {
            throw new RuntimeException('Synthetic room failure');
        });

        try {
            $this->sync($this->data($affiliate));
            $this->fail('The synthetic room failure should escape the transaction.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Synthetic room failure', $exception->getMessage());
        }

        $this->assertDatabaseCount('affiliate_bookings', 0);
        $this->assertDatabaseCount('affiliate_booking_rooms', 0);
        $this->assertDatabaseCount('affiliate_audit_events', 0);
    }

    public function test_affiliate_schema_and_audits_contain_no_guest_identity_or_raw_payload_fields(): void
    {
        [, $affiliate] = $this->affiliate(AffiliateStatus::Approved, 'privacy4826');
        $this->sync($this->data($affiliate));

        foreach (['guest_name', 'guest_email', 'email', 'phone', 'address', 'ip_address', 'raw_payload', 'payload'] as $forbidden) {
            $this->assertFalse(Schema::hasColumn('affiliate_bookings', $forbidden));
            $this->assertFalse(Schema::hasColumn('affiliate_booking_rooms', $forbidden));
        }

        $serialized = $affiliate->auditEvents()->get()->toJson();
        $this->assertStringNotContainsString('guest@example.com', $serialized);
        $this->assertStringNotContainsString('raw_payload', $serialized);
    }

    public function test_approved_dashboard_is_scoped_private_filtered_and_paginated(): void
    {
        [$user, $affiliate] = $this->affiliate(AffiliateStatus::Approved, 'dashboard4826');
        [, $other] = $this->affiliate(AffiliateStatus::Approved, 'other4826');
        $this->sync($this->data($affiliate, ['externalBookingReference' => 'HIDDEN-REFERENCE', 'roomRevenueAmount' => '987654.00']));
        $this->sync($this->data($affiliate, ['externalBookingId' => 'completed-view', 'bookingStatus' => 'completed']));
        $this->sync($this->data($other, ['externalBookingId' => 'OTHER-BOOKING', 'roomItems' => [['room_type_name' => 'Other Secret Room']]]));
        for ($i = 0; $i < 11; $i++) {
            $this->sync($this->data($affiliate, ['externalBookingId' => 'page-'.$i]));
        }

        $response = $this->actingAs($user, 'affiliate')->get('http://affiliate.nandinibali.test/dashboard?bookings=all&affiliate_id='.$other->id);
        $response->assertOk()
            ->assertSee('Tracked bookings')
            ->assertSee('Room Type')
            ->assertSee('Length of Stay')
            ->assertSee('Check-in')
            ->assertSee('Check-out')
            ->assertSee('Estimated Commission')
            ->assertSee('data-filter-target="#affiliate-click-analytics-section"', false)
            ->assertSee('data-filter-target="#affiliate-bookings-section"', false)
            ->assertDontSee('onchange="this.form.submit()"', false)
            ->assertSee('HIDDEN-REFERENCE')
            ->assertDontSee('987654.00')
            ->assertDontSee('OTHER-BOOKING')
            ->assertDontSee('Other Secret Room');
        $this->assertSame(10, $response->viewData('bookingAnalytics')['bookings']->count());

        $this->actingAs($user, 'affiliate')->get('http://affiliate.nandinibali.test/dashboard?bookings=invalid')->assertSessionHasErrors('bookings');
    }

    public function test_inactive_affiliates_never_receive_booking_dashboard_data(): void
    {
        foreach ([AffiliateStatus::Pending, AffiliateStatus::Rejected, AffiliateStatus::Suspended] as $index => $status) {
            [$user, $affiliate] = $this->affiliate($status, 'inactive'.$index.'4826');
            $this->sync($this->data($affiliate, ['externalBookingId' => 'inactive-'.$index]));
            $this->actingAs($user, 'affiliate')->get('http://affiliate.nandinibali.test/dashboard')
                ->assertOk()
                ->assertDontSee('Tracked bookings');
        }
    }

    public function test_dashboard_summary_does_not_label_ineligible_bookings_as_pending_calculation(): void
    {
        [$user, $affiliate] = $this->affiliate(AffiliateStatus::Approved, 'ineligible4826');
        $this->sync($this->data($affiliate, [
            'externalBookingId' => 'INELIGIBLE-SUMMARY-1',
            'bookingStatus' => 'cancelled',
        ]));

        $this->actingAs($user, 'affiliate')
            ->get('http://affiliate.nandinibali.test/dashboard?bookings=all')
            ->assertOk()
            ->assertSee('Cancelled')
            ->assertSee('Not eligible')
            ->assertDontSee('Pending calculation');
    }

    public function test_staff_can_mark_a_booking_as_no_show_and_the_override_survives_source_sync(): void
    {
        [$affiliateUser, $affiliate] = $this->affiliate(AffiliateStatus::Approved, 'manualnoshow4826');
        $booking = $this->sync($this->data($affiliate, [
            'externalBookingId' => 'MANUAL-NO-SHOW-1',
            'bookingStatus' => 'confirmed',
        ]))->booking;
        $sales = User::factory()->create();
        $sales->assignRole(Role::SALES_MARKETING);

        $booking = app(SetManualAffiliateBookingStatusService::class)->set(
            $booking,
            AffiliateBookingStatus::NoShow,
            'Guest did not arrive at the resort.',
            $sales,
        );

        $this->assertSame(AffiliateBookingStatus::NoShow, $booking->manual_booking_status);
        $this->assertSame(AffiliateCommissionState::Ineligible, $booking->commission_state);
        $this->assertSame('0.00', $booking->estimated_commission_amount);

        $this->sync($this->data($affiliate, [
            'externalBookingId' => 'MANUAL-NO-SHOW-1',
            'bookingStatus' => 'confirmed',
            'sourceUpdatedAt' => CarbonImmutable::parse('2026-08-04 11:00:00'),
        ]));

        $booking = $booking->fresh();
        $this->assertSame(AffiliateBookingStatus::Confirmed, $booking->booking_status);
        $this->assertSame(AffiliateBookingStatus::NoShow, $booking->manual_booking_status);
        $this->assertSame(AffiliateCommissionState::Ineligible, $booking->commission_state);
        $this->assertDatabaseHas('affiliate_audit_events', [
            'affiliate_id' => $affiliate->id,
            'event' => 'affiliate_booking.manual_status_set',
            'actor_user_id' => $sales->id,
        ]);

        $this->actingAs($affiliateUser, 'affiliate')
            ->get('http://affiliate.nandinibali.test/dashboard?bookings=all')
            ->assertOk()
            ->assertSee('No-show')
            ->assertDontSee('Guest did not arrive at the resort.');
    }

    public function test_filament_booking_resource_is_permission_protected_private_and_read_only(): void
    {
        [, $affiliate] = $this->affiliate(AffiliateStatus::Approved, 'filament4826');
        $booking = $this->sync($this->data($affiliate, ['externalBookingReference' => 'OPAQUE-1001']))->booking;
        $sales = User::factory()->create();
        $sales->assignRole(Role::SALES_MARKETING);
        $finance = User::factory()->create();
        $finance->assignRole(Role::FINANCE);
        [$affiliateUser] = $this->affiliate(AffiliateStatus::Approved, 'blocked4826');

        $this->actingAs($sales)->get(route('filament.admin.resources.affiliate-bookings.index'))
            ->assertOk()->assertSee('OPAQUE-1001')->assertDontSee('Guest Name');
        $this->actingAs($finance)->get(route('filament.admin.resources.affiliate-bookings.view', $booking))
            ->assertOk()->assertSee('OPAQUE-1001')->assertDontSee('Guest Email');
        auth('web')->logout();
        $this->actingAs($affiliateUser, 'affiliate')->get(route('filament.admin.resources.affiliate-bookings.index'))->assertRedirect();

        $this->assertFalse(AffiliateBookingResource::canCreate());
        $this->assertFalse(AffiliateBookingResource::canEdit($booking));
        $this->assertFalse(AffiliateBookingResource::canDelete($booking));
    }

    public function test_existing_membership_api_sync_uses_room_subtotal_for_estimated_commission(): void
    {
        [, $affiliate] = $this->affiliate(AffiliateStatus::Approved, 'api4826');
        Member::query()->create(['name' => 'Existing Member', 'email' => 'existing@example.com']);
        $this->mock(AffiliateNotificationService::class, function ($mock) use ($affiliate): void {
            $mock->shouldReceive('afterCommitNewBooking')->once()->withArgs(
                fn (AffiliateBooking $booking): bool => $booking->affiliate_id === $affiliate->id
                    && $booking->estimated_commission_amount === '750000.00'
            );
        });
        $api = $this->mock(MembershipBookingApiService::class, function ($mock) use ($affiliate): void {
            $mock->shouldReceive('fetchBookings')->once()->andReturn([[
                'booking_number' => 'API-AFFILIATE-1',
                'email' => 'existing@example.com',
                'guest_name' => 'Not copied to Affiliate tables',
                'check_in' => '2026-09-10',
                'check_out' => '2026-09-13',
                'room_name' => 'Jungle View Villa',
                'currency' => 'IDR',
                'room_subtotal' => '7500000.00',
                'booking_total' => '9999999.00',
                'status' => 'confirmed',
                'voucher_code' => $affiliate->affiliate_code,
                'remote_updated_at' => '2026-08-04 10:00:00',
            ]]);
            $mock->shouldReceive('debugData')->andReturn([]);
        });

        $summary = app(BookingSyncService::class, ['api' => $api])->sync('2026-08-01 00:00:00');

        $this->assertTrue($summary['success']);
        $this->assertSame(1, $summary['affiliate_bookings']['created']);
        $this->assertArrayNotHasKey('missing_room_revenue', $summary['affiliate_booking_warnings']);
        $booking = AffiliateBooking::query()->sole();
        $this->assertNotNull($booking->synced_webhotelier_booking_id);
        $this->assertSame('7500000.00', $booking->room_revenue_amount);
        $this->assertSame('750000.00', $booking->estimated_commission_amount);
        $this->assertSame(AffiliateCommissionState::Estimated, $booking->commission_state);
        $sourceBooking = SyncedWebhotelierBooking::query()->sole();
        $this->assertSame('7500000.00', $sourceBooking->room_subtotal);
        $this->assertSame('9999999.00', $sourceBooking->booking_total);
    }

    public function test_normal_booking_sync_repairs_an_existing_pending_affiliate_commission(): void
    {
        [, $affiliate] = $this->affiliate(AffiliateStatus::Approved, 'repair4826');
        $member = Member::query()->create(['name' => 'Repair Member', 'email' => 'repair@example.com']);
        $sourceBooking = SyncedWebhotelierBooking::query()->create([
            'member_id' => $member->id,
            'booking_number' => 'REPAIR-1',
            'email' => 'repair@example.com',
            'affiliate_code' => $affiliate->affiliate_code,
            'check_in' => '2026-09-10',
            'check_out' => '2026-09-13',
            'currency' => 'IDR',
            'status' => 'confirmed',
            'last_synced_at' => now(),
        ]);

        app(SyncAffiliateBookingService::class)->sync(
            app(\App\Services\Affiliate\Booking\SyncedWebhotelierAffiliateBookingSource::class)->normalize($sourceBooking)
        );
        $this->assertSame(AffiliateCommissionState::CalculationUnavailable, AffiliateBooking::query()->sole()->commission_state);

        $sourceBooking->forceFill(['room_subtotal' => '2000000.00'])->save();
        $api = $this->mock(MembershipBookingApiService::class, function ($mock): void {
            $mock->shouldReceive('fetchBookings')->once()->andReturn([]);
            $mock->shouldReceive('debugData')->andReturn([]);
        });

        $summary = app(BookingSyncService::class, ['api' => $api])->sync('2026-08-01 00:00:00');

        $this->assertTrue($summary['success']);
        $booking = AffiliateBooking::query()->sole();
        $this->assertSame(AffiliateCommissionState::Estimated, $booking->commission_state);
        $this->assertSame('200000.00', $booking->estimated_commission_amount);
    }

    public function test_existing_booking_backfill_uses_the_same_ingestion_service(): void
    {
        [, $affiliate] = $this->affiliate(AffiliateStatus::Approved, 'backfill4826');
        $member = Member::query()->create(['name' => 'Backfill Member', 'email' => 'backfill@example.com']);
        SyncedWebhotelierBooking::query()->create([
            'member_id' => $member->id,
            'booking_number' => 'BACKFILL-1',
            'email' => 'backfill@example.com',
            'affiliate_code' => $affiliate->affiliate_code,
            'check_in' => '2026-09-10',
            'check_out' => '2026-09-13',
            'status' => 'confirmed',
            'last_synced_at' => now(),
        ]);

        $this->artisan('affiliate-bookings:sync-existing')->assertSuccessful()->expectsOutputToContain('created: 1');
        $this->assertDatabaseCount('affiliate_bookings', 1);
    }

    private function sync(AffiliateBookingData $data): AffiliateBookingSyncResult
    {
        return app(SyncAffiliateBookingService::class)->sync($data);
    }

    /** @param array<string, mixed> $overrides */
    private function data(Affiliate $affiliate, array $overrides = []): AffiliateBookingData
    {
        $values = array_merge([
            'sourceSystem' => 'test_source',
            'externalBookingId' => 'booking-1',
            'externalBookingReference' => 'OPAQUE-1',
            'affiliateCode' => $affiliate->affiliate_code,
            'roomItems' => [['external_room_id' => 'room-1', 'room_type_name' => 'Jungle View Villa', 'room_quantity' => 1]],
            'checkInDate' => '2026-09-10',
            'checkOutDate' => '2026-09-13',
            'roomRevenueAmount' => '1000.00',
            'currency' => 'IDR',
            'bookingStatus' => 'confirmed',
            'sourceCreatedAt' => CarbonImmutable::parse('2026-08-04 09:00:00'),
            'sourceUpdatedAt' => CarbonImmutable::parse('2026-08-04 10:00:00'),
            'syncedWebhotelierBookingId' => null,
        ], $overrides);

        return new AffiliateBookingData(...$values);
    }

    /** @return array{Affiliate, Affiliate} */
    private function affiliate(AffiliateStatus $status, string $code): array
    {
        $affiliate = Affiliate::query()->create([
            'name' => 'Affiliate '.$code,
            'email' => $code.'@example.com',
            'password' => 'password',
            'phone_whatsapp' => '+62 812 0000 0000',
            'status' => $status,
            'registration_source' => AffiliateRegistrationSource::CreatedByNandini,
            'affiliate_code' => $code,
            'affiliate_code_generated_at' => now(),
            'short_link_slug' => $code,
            'short_link_activated_at' => $status === AffiliateStatus::Approved ? now() : null,
            'approved_at' => $status === AffiliateStatus::Approved ? now() : null,
        ]);
        $affiliate->assignRole(Role::AFFILIATE);

        return [$affiliate, $affiliate];
    }
}
