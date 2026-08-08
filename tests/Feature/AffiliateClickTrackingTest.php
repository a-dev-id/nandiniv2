<?php

namespace Tests\Feature;

use App\Enums\AffiliateRegistrationSource;
use App\Enums\AffiliateStatus;
use App\Models\Affiliate;
use App\Models\AffiliateClickEvent;
use App\Models\AffiliateUniqueClick;
use App\Models\Role;
use App\Models\User;
use App\Services\Affiliate\Click\AffiliateClickAnalyticsService;
use App\Services\Affiliate\Click\CountryResolver;
use App\Services\Affiliate\Click\RecordAffiliateClickService;
use Carbon\Carbon;
use Database\Seeders\AffiliateFoundationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class AffiliateClickTrackingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.timezone' => 'Asia/Makassar',
            'domains.affiliate' => 'affiliate.nandinibali.test',
            'domains.short_link' => 'go.nandinibali.test',
            'domains.short_link_scheme' => 'http',
            'affiliate-clicks.visitor_hash_key' => 'test-only-keyed-secret',
            'affiliate-clicks.country_header' => 'CF-IPCountry',
            'affiliate-clicks.trusted_proxies' => ['198.51.100.10'],
            'affiliate-clicks.geoip_database' => null,
            'trustedproxy.proxies' => ['198.51.100.10'],
        ]);

        date_default_timezone_set('Asia/Makassar');
        $this->seed(AffiliateFoundationSeeder::class);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_approved_link_records_privacy_safe_click_and_redirects_with_unchanged_voucher(): void
    {
        Carbon::setTestNow('2026-08-04 14:30:00');
        [, $affiliate] = $this->affiliate(AffiliateStatus::Approved, 'privacy4826');

        $response = $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.10'])
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) Mobile Safari/604.1',
                'CF-IPCountry' => 'id',
                'X-Forwarded-For' => '203.0.113.50',
                'Referer' => 'https://www.instagram.com/nandini/?utm_source=private',
            ])->get('http://go.nandinibali.test/'.$affiliate->affiliate_code);

        $response->assertRedirect('https://nandinijunglebyhanginggardens.reserve-online.net/?voucher=privacy4826&checkin=today');
        $event = AffiliateClickEvent::query()->sole();

        $this->assertSame($affiliate->id, $event->affiliate_id);
        $this->assertSame('2026-08-04 14:30:00', $event->clicked_at->format('Y-m-d H:i:s'));
        $this->assertSame('2026-08-04', $event->click_date->toDateString());
        $this->assertSame('ID', $event->country_code);
        $this->assertSame('Indonesia', $event->country_name);
        $this->assertSame('mobile', $event->device_type);
        $this->assertSame('instagram.com', $event->referrer_domain);
        $this->assertTrue($event->is_unique);
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $event->getRawOriginal('visitor_hash'));
        $this->assertNotSame(hash('sha256', '198.51.100.10'), $event->getRawOriginal('visitor_hash'));
        $this->assertFalse(\Schema::hasColumn('affiliate_click_events', 'ip_address'));
        $this->assertFalse(\Schema::hasColumn('affiliate_click_events', 'user_agent'));
        $this->assertStringNotContainsString('utm_source', (string) $event->referrer_domain);
    }

    public function test_untrusted_country_header_and_invalid_country_are_unknown(): void
    {
        [, $affiliate] = $this->affiliate(AffiliateStatus::Approved, 'unknowncountry4826');

        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.9'])
            ->withHeaders(['CF-IPCountry' => 'US', 'User-Agent' => 'Mozilla/5.0'])
            ->get('http://go.nandinibali.test/'.$affiliate->affiliate_code)
            ->assertRedirect();

        $event = AffiliateClickEvent::query()->sole();
        $this->assertNull($event->country_code);
        $this->assertNull($event->country_name);
    }

    public function test_daily_unique_clicks_are_affiliate_scoped_repeat_safe_and_reset_next_day(): void
    {
        [, $firstAffiliate] = $this->affiliate(AffiliateStatus::Approved, 'firstunique4826');
        [, $secondAffiliate] = $this->affiliate(AffiliateStatus::Approved, 'secondunique4826');
        $headers = ['User-Agent' => 'Mozilla/5.0 Same Visitor'];
        $server = ['REMOTE_ADDR' => '198.51.100.10'];

        Carbon::setTestNow('2026-08-04 09:00:00');
        $this->withServerVariables($server)->withHeaders($headers)->get('http://go.nandinibali.test/'.$firstAffiliate->affiliate_code);
        $this->withServerVariables($server)->withHeaders($headers)->get('http://go.nandinibali.test/'.$firstAffiliate->affiliate_code);
        $this->withServerVariables($server)->withHeaders($headers)->get('http://go.nandinibali.test/'.$secondAffiliate->affiliate_code);

        Carbon::setTestNow('2026-08-05 09:00:00');
        $this->withServerVariables($server)->withHeaders($headers)->get('http://go.nandinibali.test/'.$firstAffiliate->affiliate_code);

        $this->assertSame([true, false, true, true], AffiliateClickEvent::query()->orderBy('id')->pluck('is_unique')->all());
        $this->assertDatabaseCount('affiliate_unique_clicks', 3);
    }

    public function test_bot_and_social_preview_are_recorded_but_not_unique_or_public(): void
    {
        [, $affiliate] = $this->affiliate(AffiliateStatus::Approved, 'preview4826');

        $this->withHeaders(['User-Agent' => 'facebookexternalhit/1.1'])
            ->get('http://go.nandinibali.test/'.$affiliate->affiliate_code)
            ->assertRedirect();

        $event = AffiliateClickEvent::query()->sole();
        $this->assertTrue($event->is_bot);
        $this->assertSame('facebook', $event->bot_name);
        $this->assertFalse($event->is_unique);
        $this->assertDatabaseCount('affiliate_unique_clicks', 0);
        $analytics = app(AffiliateClickAnalyticsService::class)->forAffiliate($affiliate, '30');
        $this->assertSame(0, $analytics['summary']['total']);
        $this->assertSame(1, $analytics['summary']['bots']);
    }

    public function test_inactive_and_unknown_links_neither_redirect_nor_record(): void
    {
        foreach ([AffiliateStatus::Pending, AffiliateStatus::Rejected, AffiliateStatus::Suspended] as $index => $status) {
            [, $affiliate] = $this->affiliate($status, 'inactive'.$index.'4826');
            $this->get('http://go.nandinibali.test/'.$affiliate->affiliate_code)->assertNotFound();
        }

        $this->get('http://go.nandinibali.test/missing4826')->assertNotFound();
        $this->assertDatabaseCount('affiliate_click_events', 0);
    }

    public function test_recording_failure_is_safely_logged_and_never_blocks_valid_redirect(): void
    {
        [, $affiliate] = $this->affiliate(AffiliateStatus::Approved, 'failure4826');
        $service = Mockery::mock(RecordAffiliateClickService::class);
        $service->shouldReceive('record')->once()->andThrow(new RuntimeException('private 203.0.113.88 sensitive-agent'));
        $this->app->instance(RecordAffiliateClickService::class, $service);
        Log::spy();

        $this->get('http://go.nandinibali.test/'.$affiliate->affiliate_code)
            ->assertRedirect('https://nandinijunglebyhanginggardens.reserve-online.net/?voucher=failure4826&checkin=today');

        Log::shouldHaveReceived('warning')->once()->withArgs(function (string $message, array $context): bool {
            $serialized = json_encode([$message, $context]);

            return $message === 'Affiliate click analytics recording failed.'
                && $context['exception'] === RuntimeException::class
                && ! str_contains($serialized, '203.0.113.88')
                && ! str_contains($serialized, 'sensitive-agent');
        });
    }

    public function test_country_resolver_failure_also_preserves_redirect(): void
    {
        [, $affiliate] = $this->affiliate(AffiliateStatus::Approved, 'countryfailure4826');
        $resolver = Mockery::mock(CountryResolver::class);
        $resolver->shouldReceive('resolve')->andThrow(new RuntimeException('GeoIP unavailable'));
        $this->app->instance(CountryResolver::class, $resolver);

        $this->get('http://go.nandinibali.test/'.$affiliate->affiliate_code)
            ->assertRedirect('https://nandinijunglebyhanginggardens.reserve-online.net/?voucher=countryfailure4826&checkin=today');
        $this->assertDatabaseCount('affiliate_click_events', 0);
        $this->assertDatabaseCount('affiliate_unique_clicks', 0);
    }

    public function test_dashboard_uses_authenticated_affiliate_aggregates_and_ignores_other_id(): void
    {
        [$user, $affiliate] = $this->affiliate(AffiliateStatus::Approved, 'dashboard4826');
        [, $other] = $this->affiliate(AffiliateStatus::Approved, 'other4826');
        $this->event($affiliate, ['country_code' => 'ID', 'country_name' => 'Indonesia', 'device_type' => 'mobile', 'is_unique' => true]);
        $this->event($other, ['country_code' => 'AU', 'country_name' => 'Australia', 'device_type' => 'desktop', 'is_unique' => true]);

        $this->actingAs($user, 'affiliate')
            ->get('http://affiliate.nandinibali.test/dashboard?range=30&affiliate_id='.$other->id)
            ->assertOk()
            ->assertSee('Click analytics')
            ->assertSee('Indonesia')
            ->assertDontSee('Australia');
    }

    public function test_dashboard_empty_pending_and_invalid_range_states_are_safe(): void
    {
        [$approvedUser] = $this->affiliate(AffiliateStatus::Approved, 'empty4826');
        $this->actingAs($approvedUser, 'affiliate')
            ->get('http://affiliate.nandinibali.test/dashboard')
            ->assertOk()
            ->assertSee('No link activity yet.');
        $this->actingAs($approvedUser, 'affiliate')
            ->get('http://affiliate.nandinibali.test/dashboard?range=365')
            ->assertSessionHasErrors('range');

        [$pendingUser] = $this->affiliate(AffiliateStatus::Pending, 'pending4826');
        $this->actingAs($pendingUser, 'affiliate')
            ->get('http://affiliate.nandinibali.test/dashboard')
            ->assertOk()
            ->assertDontSee('Click analytics');
    }

    public function test_internal_analytics_authorization_and_top_affiliate_aggregation(): void
    {
        [, $affiliate] = $this->affiliate(AffiliateStatus::Approved, 'top4826');
        $this->event($affiliate, ['country_code' => 'ID', 'country_name' => 'Indonesia', 'is_unique' => true]);
        $this->event($affiliate, ['country_code' => 'ID', 'country_name' => 'Indonesia', 'is_unique' => false]);

        $sales = User::factory()->create();
        $sales->assignRole(Role::SALES_MARKETING);
        $this->actingAs($sales)
            ->get(route('filament.admin.pages.affiliate-click-analytics'))
            ->assertOk()
            ->assertSee('Affiliate Click Analytics')
            ->assertSee($affiliate->affiliate_code);

        $overview = app(AffiliateClickAnalyticsService::class)->overview('30');
        $this->assertSame(2, $overview['top_affiliates'][0]['total']);
        $this->assertSame(1, $overview['top_affiliates'][0]['unique']);

        $finance = User::factory()->create();
        $finance->assignRole(Role::FINANCE);
        $this->actingAs($finance)->get(route('filament.admin.pages.affiliate-click-analytics'))->assertForbidden();

        [$affiliateUser] = $this->affiliate(AffiliateStatus::Approved, 'role4826');
        auth('web')->logout();
        $this->actingAs($affiliateUser, 'affiliate')->get(route('filament.admin.pages.affiliate-click-analytics'))->assertRedirect();
    }

    public function test_filament_detail_never_renders_visitor_hash_or_ip_fields(): void
    {
        [, $affiliate] = $this->affiliate(AffiliateStatus::Approved, 'detail4826');
        $event = $this->event($affiliate, ['is_unique' => true]);
        $hash = $event->getRawOriginal('visitor_hash');
        $administrator = User::factory()->create();
        $administrator->assignRole(Role::ADMINISTRATOR);

        $this->actingAs($administrator, 'web')
            ->get(route('filament.admin.resources.affiliates.view', $affiliate))
            ->assertOk()
            ->assertSee('Click analytics')
            ->assertSee('Recent click events')
            ->assertDontSee($hash)
            ->assertDontSee('IP address')
            ->assertDontSee('Visitor hash');
    }

    public function test_cleanup_requires_positive_retention_and_removes_only_expired_click_data(): void
    {
        [, $affiliate] = $this->affiliate(AffiliateStatus::Approved, 'cleanup4826');
        $old = $this->event($affiliate, ['clicked_at' => now()->subDays(400), 'click_date' => now()->subDays(400)->toDateString(), 'is_unique' => true]);
        $recent = $this->event($affiliate, ['clicked_at' => now()->subDay(), 'click_date' => now()->subDay()->toDateString(), 'is_unique' => true]);
        AffiliateUniqueClick::query()->create(['affiliate_id' => $affiliate->id, 'visitor_hash' => $old->getRawOriginal('visitor_hash'), 'click_date' => $old->click_date]);
        AffiliateUniqueClick::query()->create(['affiliate_id' => $affiliate->id, 'visitor_hash' => $recent->getRawOriginal('visitor_hash'), 'click_date' => $recent->click_date]);

        $this->artisan('affiliate-clicks:cleanup', ['--retention' => 395])->assertSuccessful();
        $this->assertDatabaseMissing('affiliate_click_events', ['id' => $old->id]);
        $this->assertDatabaseHas('affiliate_click_events', ['id' => $recent->id]);
        $this->assertDatabaseHas('affiliates', ['id' => $affiliate->id]);
        $this->artisan('affiliate-clicks:cleanup', ['--retention' => 0])->assertFailed();
    }

    /** @return array{Affiliate, Affiliate} */
    private function affiliate(AffiliateStatus $status, string $code): array
    {
        $approved = $status === AffiliateStatus::Approved;
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
            'short_link_activated_at' => $approved ? now() : null,
            'approved_at' => $approved ? now() : null,
        ]);
        $affiliate->assignRole(Role::AFFILIATE);

        return [$affiliate, $affiliate];
    }

    /** @param array<string, mixed> $overrides */
    private function event(Affiliate $affiliate, array $overrides = []): AffiliateClickEvent
    {
        return AffiliateClickEvent::query()->create(array_merge([
            'affiliate_id' => $affiliate->id,
            'clicked_at' => now(),
            'click_date' => now()->toDateString(),
            'country_code' => null,
            'country_name' => null,
            'device_type' => 'unknown',
            'referrer_domain' => null,
            'visitor_hash' => hash_hmac('sha256', (string) fake()->uuid(), 'test-secret'),
            'is_unique' => false,
            'is_bot' => false,
            'bot_name' => null,
        ], $overrides));
    }
}
