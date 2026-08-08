<?php

namespace Tests\Feature;

use App\Enums\AffiliateBookingStatus;
use App\Enums\AffiliateCommissionState;
use App\Enums\AffiliateMarketingAssetType;
use App\Enums\AffiliateRegistrationSource;
use App\Enums\AffiliateStatus;
use App\Filament\Resources\AffiliateMarketingAssets\Pages\CreateAffiliateMarketingAsset;
use App\Filament\Resources\AffiliateProgramSettings\Pages\EditAffiliateProgramSetting;
use App\Models\Affiliate;
use App\Models\AffiliateBooking;
use App\Models\AffiliateClickEvent;
use App\Models\AffiliateMarketingAsset;
use App\Models\AffiliateProgramSetting;
use App\Models\BookingSyncLog;
use App\Models\Role;
use App\Models\User;
use App\Services\Affiliate\Operations\AffiliateSystemHealthService;
use App\Services\Affiliate\Reports\AffiliateOperationalReportService;
use App\Services\Affiliate\Reports\AffiliateReportDateRange;
use App\Services\Affiliate\Reports\SafeCsvWriter;
use Carbon\CarbonImmutable;
use Database\Seeders\AffiliateFoundationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

class AffiliateOperationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['domains.affiliate' => 'affiliate.nandinibali.test', 'domains.main' => 'nandinibali.test', 'app.timezone' => 'Asia/Makassar']);
        $this->seed(AffiliateFoundationSeeder::class);
    }

    public function test_sales_can_manage_marketing_assets_but_finance_cannot(): void
    {
        $sales = $this->staff(Role::SALES_MARKETING);
        $finance = $this->staff(Role::FINANCE);

        $this->actingAs($sales);
        Livewire::test(CreateAffiliateMarketingAsset::class)
            ->fillForm([
                'title' => 'Synthetic Campaign Banner', 'asset_type' => AffiliateMarketingAssetType::Banner->value,
                'external_url' => 'https://example.test/banner', 'is_active' => true, 'is_featured' => true, 'sort_order' => 10,
            ])->call('create')->assertHasNoFormErrors();
        $this->assertDatabaseHas('affiliate_marketing_assets', ['title' => 'Synthetic Campaign Banner', 'created_by' => $sales->id]);

        $this->actingAs($finance)->get(route('filament.admin.resources.affiliate-marketing-assets.index'))->assertForbidden();
    }

    public function test_marketing_asset_upload_rejects_executable_files_and_insecure_urls(): void
    {
        Storage::fake('local');
        $sales = $this->staff(Role::SALES_MARKETING);
        $this->actingAs($sales);

        Livewire::test(CreateAffiliateMarketingAsset::class)
            ->fillForm([
                'title' => 'Unsafe Synthetic Upload', 'asset_type' => AffiliateMarketingAssetType::Document->value,
                'file_path' => UploadedFile::fake()->create('payload.php', 2, 'application/x-httpd-php'),
                'external_url' => 'http://example.test/insecure', 'sort_order' => 0,
            ])->call('create')->assertHasFormErrors(['file_path', 'external_url']);

        $this->assertDatabaseMissing('affiliate_marketing_assets', ['title' => 'Unsafe Synthetic Upload']);
    }

    public function test_marketing_asset_model_rejects_missing_sources_and_disallowed_extensions(): void
    {
        try {
            AffiliateMarketingAsset::query()->create([
                'title' => 'Missing Source',
                'asset_type' => AffiliateMarketingAssetType::Document,
            ]);
            $this->fail('A marketing asset source should be required.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('file_path', $exception->errors());
        }

        Storage::fake('local');
        Storage::disk('local')->put('affiliate/marketing-assets/unsafe.php', '<?php echo 1;');

        try {
            AffiliateMarketingAsset::query()->create([
                'title' => 'Unsafe Extension',
                'asset_type' => AffiliateMarketingAssetType::Other,
                'file_path' => 'affiliate/marketing-assets/unsafe.php',
            ]);
            $this->fail('A disallowed extension should be rejected.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('file_path', $exception->errors());
        }
    }

    public function test_marketing_material_visibility_and_protected_download_follow_affiliate_status(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('affiliate/marketing-assets/synthetic.pdf', '%PDF synthetic');
        $asset = AffiliateMarketingAsset::query()->create([
            'title' => 'Active Synthetic Guide', 'asset_type' => AffiliateMarketingAssetType::Document,
            'file_path' => 'affiliate/marketing-assets/synthetic.pdf', 'is_active' => true, 'available_from' => now()->subDay(), 'available_until' => now()->addDay(),
        ]);
        AffiliateMarketingAsset::query()->create([
            'title' => 'Expired Private Guide', 'asset_type' => AffiliateMarketingAssetType::Document,
            'external_url' => 'https://example.test/expired', 'is_active' => true, 'available_until' => now()->subMinute(),
        ]);
        AffiliateMarketingAsset::query()->create([
            'title' => 'Inactive Private Guide', 'asset_type' => AffiliateMarketingAssetType::Document,
            'external_url' => 'https://example.test/inactive', 'is_active' => false,
        ]);
        [$approvedUser] = $this->affiliate('approved-operations@example.test', AffiliateStatus::Approved, 'approved826');
        [$pendingUser] = $this->affiliate('pending-operations@example.test', AffiliateStatus::Pending, 'pending826');
        [$suspendedUser] = $this->affiliate('suspended-operations@example.test', AffiliateStatus::Suspended, 'suspended826');

        $this->actingAs($approvedUser, 'affiliate')->get('http://affiliate.nandinibali.test/marketing-materials')
            ->assertOk()
            ->assertSee('Active Synthetic Guide')
            ->assertSee('Images')
            ->assertSee('Videos')
            ->assertSee('Documents')
            ->assertDontSee('?type=banner', false)
            ->assertDontSee('?type=social_media', false)
            ->assertDontSee('?type=special_offer', false)
            ->assertDontSee('Expired Private Guide')
            ->assertDontSee('Inactive Private Guide');
        $download = $this->actingAs($approvedUser, 'affiliate')->get('http://affiliate.nandinibali.test/marketing-materials/'.$asset->id.'/download');
        $download->assertOk()->assertDownload('synthetic.pdf');
        $this->actingAs($pendingUser, 'affiliate')->get('http://affiliate.nandinibali.test/marketing-materials')->assertOk()->assertSee('Materials are not available yet')->assertDontSee('Active Synthetic Guide');
        $this->actingAs($pendingUser, 'affiliate')->get('http://affiliate.nandinibali.test/marketing-materials/'.$asset->id.'/download')->assertForbidden();
        $this->actingAs($suspendedUser, 'affiliate')->get('http://affiliate.nandinibali.test/marketing-materials')
            ->assertForbidden()->assertSee('This page is unavailable for this account');
    }

    public function test_affiliate_reports_are_own_only_currency_separated_and_privacy_safe(): void
    {
        [$user, $affiliate] = $this->affiliate('report-owner@example.test', AffiliateStatus::Approved, 'owner826');
        [, $other] = $this->affiliate('report-other@example.test', AffiliateStatus::Approved, 'other826');
        $this->click($affiliate, '=Synthetic Country');
        $this->booking($affiliate, 'own-safe', 'IDR', '125000.00');
        $this->booking($affiliate, 'own-usd', 'USD', '20.00');
        $this->booking($other, 'other-private', 'IDR', '999999.00');

        $this->actingAs($user, 'affiliate')->get('http://affiliate.nandinibali.test/reports?range=this_year')
            ->assertOk()
            ->assertSee('IDR')
            ->assertSee('USD')
            ->assertDontSee('Tracked Affiliate conversion indicator')
            ->assertDontSee('CSV exports')
            ->assertDontSee('999999.00')
            ->assertDontSee('PRIVATE-REFERENCE-other-private')
            ->assertDontSee('1000000.00');

        $csv = $this->actingAs($user, 'affiliate')->get('http://affiliate.nandinibali.test/reports/export/bookings?range=this_year');
        $csv->assertOk();
        $content = $csv->streamedContent();
        $this->assertStringContainsString('Room Type', $content);
        $this->assertStringNotContainsString('Booking Reference', $content);
        $this->assertStringNotContainsString('Room Revenue', $content);
        $this->assertStringNotContainsString('PRIVATE-REFERENCE', $content);
    }

    public function test_affiliate_navigation_exposes_only_status_appropriate_destinations(): void
    {
        [$approved] = $this->affiliate('approved-navigation@example.test', AffiliateStatus::Approved, 'approvednav826');
        [$pending] = $this->affiliate('pending-navigation@example.test', AffiliateStatus::Pending, 'pendingnav826');

        $this->actingAs($approved, 'affiliate')->get('http://affiliate.nandinibali.test/dashboard')
            ->assertOk()->assertSee('Dashboard')->assertSee('Reports')->assertSee('Marketing Materials')->assertSee('Payment Details')->assertSee('Logout');
        $this->actingAs($pending, 'affiliate')->get('http://affiliate.nandinibali.test/dashboard')
            ->assertOk()->assertSee('Dashboard')->assertDontSee('Marketing Materials')->assertDontSee('Payment Details');
    }

    public function test_affiliate_export_blocks_pending_accounts_and_csv_formula_values_are_escaped(): void
    {
        [$pending] = $this->affiliate('pending-export@example.test', AffiliateStatus::Pending, 'pendingexp826');
        $this->actingAs($pending, 'affiliate')->get('http://affiliate.nandinibali.test/reports/export/clicks?range=this_month')->assertForbidden();
        $this->assertSame("'=SUM(1,1)", app(SafeCsvWriter::class)->escape('=SUM(1,1)'));
        $this->assertSame("'+123", app(SafeCsvWriter::class)->escape('+123'));
        $this->assertSame("'-123", app(SafeCsvWriter::class)->escape('-123'));
        $this->assertSame("'@SUM(1,1)", app(SafeCsvWriter::class)->escape('@SUM(1,1)'));
        $this->assertSame('Normal', app(SafeCsvWriter::class)->escape('Normal'));
    }

    public function test_internal_performance_filters_currency_affiliate_and_room_quantity_consistently(): void
    {
        [, $affiliate] = $this->affiliate('filtered-report@example.test', AffiliateStatus::Approved, 'filtered826');
        [, $other] = $this->affiliate('filtered-other@example.test', AffiliateStatus::Approved, 'filteredother826');
        $booking = $this->booking($affiliate, 'filtered-idr', 'IDR', '125000.00');
        $booking->rooms()->update(['room_quantity' => 2]);
        $this->booking($affiliate, 'filtered-usd', 'USD', '50.00');
        $this->booking($other, 'filtered-other', 'IDR', '999999.00');
        $range = new AffiliateReportDateRange(CarbonImmutable::now()->startOfYear(), CarbonImmutable::now()->endOfDay(), 'custom');

        $report = app(AffiliateOperationalReportService::class)->dashboard($range, currency: 'IDR', affiliateId: $affiliate->id);

        $this->assertCount(1, $report['performance']);
        $this->assertSame(1, $report['performance']->first()['bookings']);
        $this->assertSame(4, $report['performance']->first()['room_nights']);
        $this->assertSame('IDR', $report['performance']->first()['estimated'][0]['currency']);
    }

    public function test_internal_reports_and_exports_require_operational_permission(): void
    {
        $sales = $this->staff(Role::SALES_MARKETING);
        $finance = $this->staff(Role::FINANCE);
        $unprivileged = User::factory()->create();

        $this->actingAs($sales)->get(route('filament.admin.pages.affiliate-operational-reports'))->assertOk();
        $this->actingAs($finance)->get(route('filament.admin.pages.affiliate-operational-reports'))->assertOk();
        $this->actingAs($unprivileged)->get(route('affiliate.operations.export', ['type' => 'exceptions', 'range' => 'this_month']))->assertForbidden();
        $export = $this->actingAs($sales)->get(route('affiliate.operations.export', ['type' => 'exceptions', 'range' => 'this_month']));
        $export->assertOk();
        $content = $export->streamedContent();
        $this->assertStringContainsString('Exception', $content);
        $this->assertStringNotContainsString('guest_name', $content);
        $this->assertStringNotContainsString('visitor_hash', $content);
    }

    public function test_settings_validation_audit_and_historical_warning_behave_safely(): void
    {
        $admin = $this->staff(Role::ADMINISTRATOR);
        $setting = AffiliateProgramSetting::current();
        [, $affiliate] = $this->affiliate('historical-setting@example.test', AffiliateStatus::Approved, 'history826');
        $historicalBooking = $this->booking($affiliate, 'historical-setting', 'IDR', '100000.00');
        $this->actingAs($admin);

        Livewire::test(EditAffiliateProgramSetting::class, ['record' => $setting->getRouteKey()])
            ->fillForm(['affiliate_commission_percentage' => 101, 'commission_validation_start_day' => 9, 'commission_validation_end_day' => 4])
            ->call('save')->assertHasFormErrors(['affiliate_commission_percentage', 'commission_validation_end_day']);

        Livewire::test(EditAffiliateProgramSetting::class, ['record' => $setting->getRouteKey()])
            ->fillForm(['affiliate_commission_percentage' => 12.5, 'commission_validation_start_day' => 2, 'commission_validation_end_day' => 8])
            ->call('save')->assertHasNoFormErrors();
        $this->assertDatabaseHas('affiliate_audit_events', ['event' => 'affiliate_setting.changed', 'actor_user_id' => $admin->id]);
        $this->assertSame('10.00', $historicalBooking->fresh()->commission_rate_snapshot);
        $this->assertSame('100000.00', $historicalBooking->fresh()->estimated_commission_amount);
    }

    public function test_system_health_is_administrator_only_and_hides_secrets(): void
    {
        $admin = $this->staff(Role::ADMINISTRATOR);
        $finance = $this->staff(Role::FINANCE);
        config(['services.membership_api.token' => 'SECRET-BOOKING-TOKEN']);

        $this->actingAs($admin)->get(route('filament.admin.pages.affiliate-system-health'))
            ->assertOk()->assertSee('Affiliate System Health')->assertSee('Affiliate-specific terms and privacy wording require business or legal approval')
            ->assertDontSee((string) config('app.key'))->assertDontSee('SECRET-BOOKING-TOKEN');
        $this->actingAs($finance)->get(route('filament.admin.pages.affiliate-system-health'))->assertForbidden();

        $domainCheck = collect(app(AffiliateSystemHealthService::class)->checks())->firstWhere('label', 'Affiliate Domain Configuration');
        $this->assertSame('Unknown', $domainCheck['status']);
        $this->assertStringContainsString('external verification', $domainCheck['summary']);
    }

    public function test_system_health_warns_when_the_latest_successful_booking_sync_is_stale(): void
    {
        config(['services.membership_api.booking_sync_max_age_hours' => 25]);
        BookingSyncLog::query()->create([
            'started_at' => now()->subHours(30),
            'finished_at' => now()->subHours(30),
            'status' => BookingSyncLog::STATUS_SUCCESS,
            'bookings_received' => 4,
        ]);

        $bookingCheck = collect(app(AffiliateSystemHealthService::class)->checks())
            ->firstWhere('label', 'Last Booking Sync');

        $this->assertSame('Attention Required', $bookingCheck['status']);
        $this->assertStringContainsString('within 25 hours', $bookingCheck['summary']);
    }

    public function test_scheduler_heartbeat_is_registered_and_records_state(): void
    {
        $this->artisan('affiliate:heartbeat')->assertSuccessful();
        $this->assertDatabaseHas('affiliate_operational_states', ['key' => 'scheduler_heartbeat', 'status' => 'success']);
        $this->artisan('schedule:list')->expectsOutputToContain('affiliate:heartbeat')->assertSuccessful();
    }

    private function staff(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    private function affiliate(string $email, AffiliateStatus $status, string $code): array
    {
        $affiliate = Affiliate::query()->create([
            'name' => 'Synthetic Operations Affiliate', 'email' => $email, 'password' => 'password', 'phone_whatsapp' => '+62 800 0000',
            'status' => $status, 'registration_source' => AffiliateRegistrationSource::CreatedByNandini, 'affiliate_code' => $code,
            'affiliate_code_generated_at' => now(), 'short_link_slug' => $code, 'short_link_activated_at' => $status === AffiliateStatus::Approved ? now() : null,
        ]);
        $affiliate->assignRole(Role::AFFILIATE);

        return [$affiliate, $affiliate];
    }

    private function click(Affiliate $affiliate, string $country): void
    {
        AffiliateClickEvent::query()->create([
            'affiliate_id' => $affiliate->id, 'clicked_at' => now(), 'click_date' => now()->toDateString(), 'country_code' => 'ZZ',
            'country_name' => $country, 'device_type' => 'desktop', 'visitor_hash' => hash('sha256', $affiliate->id.$country), 'is_unique' => true, 'is_bot' => false,
        ]);
    }

    private function booking(Affiliate $affiliate, string $id, string $currency, string $commission): AffiliateBooking
    {
        $booking = AffiliateBooking::query()->create([
            'affiliate_id' => $affiliate->id, 'source_system' => 'part6_test', 'external_booking_id' => $id,
            'external_booking_reference' => 'PRIVATE-REFERENCE-'.$id, 'affiliate_code_snapshot' => $affiliate->affiliate_code,
            'check_in_date' => now()->subDays(3), 'check_out_date' => now()->subDay(), 'stay_nights' => 2,
            'room_revenue_amount' => '1000000.00', 'currency' => $currency, 'booking_status' => AffiliateBookingStatus::Completed,
            'source_status' => 'completed', 'commission_rate_snapshot' => '10.00', 'estimated_commission_amount' => $commission,
            'commission_state' => AffiliateCommissionState::PendingValidation, 'last_synced_at' => now(), 'data_fingerprint' => hash('sha256', $id),
        ]);
        $booking->rooms()->create([
            'external_room_id' => 'room-'.$id, 'room_type_name' => 'Synthetic Villa', 'room_quantity' => 1,
            'stay_nights' => 2, 'room_revenue_amount' => '1000000.00', 'currency' => $currency, 'line_fingerprint' => hash('sha256', 'room-'.$id),
        ]);

        return $booking;
    }
}
