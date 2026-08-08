<?php

namespace Tests\Feature;

use App\Enums\AffiliateRegistrationSource;
use App\Enums\AffiliateStatus;
use App\Models\Affiliate;
use App\Models\AffiliateProgramSetting;
use App\Models\Member;
use App\Models\Page;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\AffiliateFoundationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AffiliateFoundationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'domains.main' => 'nandinibali.test',
            'domains.affiliate' => 'affiliate.nandinibali.test',
            'domains.voucher' => 'voucher.nandinibali.test',
            'features.disable_voucher_feature' => false,
        ]);

        $this->seed(AffiliateFoundationSeeder::class);
    }

    public function test_affiliate_landing_page_loads_on_the_affiliate_domain(): void
    {
        $this->get('http://affiliate.nandinibali.test/')
            ->assertOk()
            ->assertSee('Join the Nandini Partner Circle')
            ->assertSee('Login')
            ->assertSee('Join Affiliate')
            ->assertSee('Gift Voucher')
            ->assertSee('id="navAffiliateSidebarBtn"', false)
            ->assertSee('id="mainNavbar"', false);

        $this->assertSame('http://affiliate.nandinibali.test', route('affiliate.landing'));
    }

    public function test_existing_main_and_voucher_domains_remain_functional(): void
    {
        Page::query()->forceCreate([
            'id' => 1,
            'title' => 'Home',
            'slug' => 'home',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $this->get('http://nandinibali.test/')
            ->assertOk()
            ->assertSee('Gift Voucher')
            ->assertSee('id="navAffiliateSidebarBtn"', false)
            ->assertSee('href="'.route('affiliate.landing').'"', false);
        $this->get('http://voucher.nandinibali.test/')->assertOk();
    }

    public function test_guest_is_redirected_to_the_affiliate_login(): void
    {
        $this->get('http://affiliate.nandinibali.test/dashboard')
            ->assertRedirect(route('affiliate.login'));
    }

    public function test_non_affiliate_user_cannot_access_the_dashboard(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('http://affiliate.nandinibali.test/dashboard')
            ->assertRedirect(route('affiliate.login'));
    }

    public function test_affiliate_login_uses_its_dedicated_guard_and_table(): void
    {
        [$user] = $this->affiliate(AffiliateStatus::Pending);

        $this->post('http://affiliate.nandinibali.test/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('affiliate.dashboard'));

        $this->assertAuthenticatedAs($user, 'affiliate');
        $this->assertArrayHasKey('affiliate', config('auth.guards'));
        $this->assertTrue(Schema::hasColumn('affiliates', 'password'));
        $this->assertDatabaseMissing('users', ['email' => $user->email]);
    }

    public function test_inner_circle_member_authentication_remains_separate(): void
    {
        $member = Member::create([
            'name' => 'Local Inner Circle Member',
            'email' => 'member-only@example.com',
            'password' => 'password',
        ]);

        $this->actingAs($member, 'member')
            ->get('http://affiliate.nandinibali.test/dashboard')
            ->assertRedirect(route('affiliate.login'));

        $this->assertDatabaseMissing('users', ['email' => $member->email]);
        $this->assertDatabaseMissing('affiliates', ['email' => $member->email]);
    }

    public function test_affiliate_can_view_only_their_own_profile(): void
    {
        [$user, $affiliate] = $this->affiliate(AffiliateStatus::Approved);
        [, $otherAffiliate] = $this->affiliate(AffiliateStatus::Approved);

        $this->assertTrue($user->can('view', $affiliate));
        $this->assertFalse($user->can('view', $otherAffiliate));

        $this->actingAs($user, 'affiliate')
            ->get('http://affiliate.nandinibali.test/dashboard')
            ->assertOk()
            ->assertSee($affiliate->name);
    }

    public function test_pending_affiliate_sees_review_popup_once_per_session_and_no_tools(): void
    {
        [$user] = $this->affiliate(AffiliateStatus::Pending);
        $this->actingAs($user, 'affiliate');

        $this->get('http://affiliate.nandinibali.test/dashboard')
            ->assertOk()
            ->assertSee('Account under review')
            ->assertSee('data-pending-review-modal', false)
            ->assertDontSee('Your account is approved')
            ->assertDontSee('Your affiliate code')
            ->assertDontSee('Click analytics')
            ->assertDontSee('Payout information');

        $this->get('http://affiliate.nandinibali.test/dashboard')
            ->assertOk()
            ->assertSee('Account under review')
            ->assertDontSee('data-pending-review-modal', false);
    }

    public function test_approved_affiliate_reaches_the_approved_placeholder(): void
    {
        [$user] = $this->affiliate(AffiliateStatus::Approved);

        $this->actingAs($user, 'affiliate')
            ->get('http://affiliate.nandinibali.test/dashboard')
            ->assertOk()
            ->assertSee('Your account is approved')
            ->assertDontSee('Account under review')
            ->assertDontSee('data-pending-review-modal', false);
    }

    public function test_rejected_affiliate_sees_a_neutral_inactive_message(): void
    {
        [$user] = $this->affiliate(AffiliateStatus::Rejected);

        $this->actingAs($user, 'affiliate')
            ->get('http://affiliate.nandinibali.test/dashboard')
            ->assertOk()
            ->assertSee('Account not active')
            ->assertDontSee('Internal rejection note');
    }

    public function test_suspended_affiliate_sees_the_suspended_message_without_tools(): void
    {
        [$user] = $this->affiliate(AffiliateStatus::Suspended);

        $this->actingAs($user, 'affiliate')
            ->get('http://affiliate.nandinibali.test/dashboard')
            ->assertOk()
            ->assertSee('Account suspended')
            ->assertSee('Active affiliate tools are unavailable')
            ->assertDontSee('Your affiliate code');
    }

    public function test_affiliate_cannot_access_filament_affiliate_management(): void
    {
        [$user] = $this->affiliate(AffiliateStatus::Approved);

        $this->actingAs($user, 'affiliate')
            ->get(route('filament.admin.resources.affiliates.index'))
            ->assertRedirect();
    }

    public function test_sales_and_marketing_can_access_the_read_only_filament_affiliate_list(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole(Role::SALES_MARKETING);

        $this->actingAs($sales)
            ->get(route('filament.admin.resources.affiliates.index'))
            ->assertOk()
            ->assertSee('Affiliates')
            ->assertDontSee('New affiliate', false);
    }

    public function test_sales_and_marketing_are_blocked_from_unrelated_filament_resources(): void
    {
        $sales = User::factory()->create();
        $sales->assignRole(Role::SALES_MARKETING);

        $this->actingAs($sales)
            ->get(route('filament.admin.resources.members.index'))
            ->assertForbidden();
    }

    public function test_program_settings_are_seeded_with_centralized_defaults(): void
    {
        $settings = AffiliateProgramSetting::current();

        $this->assertSame('Nandini Partner Circle', $settings->program_name);
        $this->assertSame('10.00', $settings->affiliate_commission_percentage);
        $this->assertSame('3.00', $settings->guest_discount_percentage);
        $this->assertSame('monthly', $settings->payment_cycle);
        $this->assertSame('500000.00', $settings->minimum_payout_amount);
        $this->assertSame('IDR', $settings->currency);
        $this->assertStringContainsString('48 hours', $settings->review_time_message);
        $this->assertSame('https://nandinijunglebyhanginggardens.reserve-online.net/', $settings->booking_engine_base_url);
        $this->assertSame(config('domains.affiliate'), $settings->affiliate_domain);
        $this->assertSame(config('domains.short_link'), $settings->short_link_domain);
        $this->assertTrue($settings->minimum_payout_requires_finance_confirmation);
    }

    public function test_roles_and_permissions_are_seeded_and_assigned_logically(): void
    {
        $this->assertDatabaseCount('roles', 4);
        $this->assertDatabaseCount('permissions', count(Permission::affiliatePermissionNames()));

        $administrator = Role::query()->where('slug', Role::ADMINISTRATOR)->firstOrFail();
        $sales = Role::query()->where('slug', Role::SALES_MARKETING)->firstOrFail();
        $finance = Role::query()->where('slug', Role::FINANCE)->firstOrFail();
        $affiliate = Role::query()->where('slug', Role::AFFILIATE)->firstOrFail();

        $this->assertCount(count(Permission::affiliatePermissionNames()), $administrator->permissions);
        $this->assertTrue($sales->permissions->contains('name', Permission::AFFILIATE_APPROVE));
        $this->assertTrue($finance->permissions->contains('name', Permission::AFFILIATE_COMMISSION_APPROVE));
        $this->assertTrue($affiliate->permissions->contains('name', Permission::AFFILIATE_DASHBOARD_VIEW_OWN));
        $this->assertFalse($affiliate->permissions->contains('name', Permission::AFFILIATE_VIEW));
    }

    /**
     * @return array{Affiliate, Affiliate}
     */
    private function affiliate(AffiliateStatus $status): array
    {
        $affiliate = Affiliate::create([
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'phone_whatsapp' => fake()->phoneNumber(),
            'status' => $status,
            'registration_source' => AffiliateRegistrationSource::CreatedByNandini,
            'status_note' => 'Internal rejection note',
        ]);
        $affiliate->assignRole(Role::AFFILIATE);

        return [$affiliate, $affiliate];
    }
}
