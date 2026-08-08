<?php

namespace Tests\Feature;

use App\Enums\AffiliateRegistrationSource;
use App\Enums\AffiliateStatus;
use App\Models\Affiliate;
use App\Models\AffiliateProgramSetting;
use App\Models\Member;
use App\Models\MiniPopup;
use App\Models\Page;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\AffiliateFoundationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AffiliateLandingPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'domains.main' => 'nandinibali.test',
            'domains.affiliate' => 'affiliate.nandinibali.test',
            'domains.voucher' => 'voucher.nandinibali.test',
            'features.disable_affiliate_feature' => false,
            'features.disable_voucher_feature' => false,
        ]);

        $this->seed(AffiliateFoundationSeeder::class);
    }

    public function test_guest_landing_page_uses_approved_copy_and_working_named_ctas(): void
    {
        $response = $this->get('http://affiliate.nandinibali.test/');

        $response->assertOk()
            ->assertSee('data-navbar-mode="transparent"', false)
            ->assertSee('id="navAffiliateLoginBtn"', false)
            ->assertDontSee('id="navGiftVoucherBtn"', false)
            ->assertSee('Join the Nandini Partner Circle')
            ->assertSee('Share the beauty of Nandini Jungle by Hanging Gardens with your audience and earn rewards for every successful referral.')
            ->assertSee('The Nandini Partner Circle is our official affiliate program designed for travel creators, bloggers, publishers, travel advisors, wellness professionals, and anyone passionate about inspiring unforgettable travel experiences.')
            ->assertSee('Why Join?')
            ->assertSee("As a Nandini Partner, you'll enjoy:", false)
            ->assertSee('Dedicated Affiliate dashboard to review tracked bookings, room nights, and commission status after booking synchronization.')
            ->assertSee('Monthly payout processing for qualified completed stays, subject to Finance validation and the applicable payout threshold.')
            ->assertDontSee('Real-time affiliate dashboard')
            ->assertDontSee('room nights, revenue')
            ->assertSee("No joining fee or sales target—it's free to join and you earn based on your performance.")
            ->assertSee("Become part of our growing community of trusted partners and introduce travelers to one of Bali's most tranquil jungle retreats. It's a simple way to monetize your audience while helping guests enjoy exclusive savings when they book directly with Nandini Jungle by Hanging Gardens.", false)
            ->assertSee('href="'.route('affiliate.register').'"', false)
            ->assertSee('href="'.route('affiliate.login').'"', false)
            ->assertSee('<a href="'.route('affiliate.landing').'" class="hover:underline">Affiliate Program</a>', false)
            ->assertSee('border-slate-950 bg-transparent px-3 py-2 text-[10px] font-medium uppercase tracking-[0.08em] text-slate-950', false)
            ->assertSee('Join Affiliate')
            ->assertSee('Login')
            ->assertDontSee('disabled', false)
            ->assertDontSee('Coming soon')
            ->assertDontSee('nandini.link')
            ->assertDontSee('localpendingaffiliate4826')
            ->assertDontSee('How It Works')
            ->assertDontSee('Who Can Join')
            ->assertDontSee('Commission applies to qualified completed stays.')
            ->assertDontSee('future program module', false);
    }

    public function test_financial_values_are_rendered_from_central_program_settings(): void
    {
        AffiliateProgramSetting::current()->update([
            'affiliate_commission_percentage' => '12.50',
            'guest_discount_percentage' => '4.25',
        ]);

        $this->get('http://affiliate.nandinibali.test/')
            ->assertOk()
            ->assertSee('up to 12.5% commission')
            ->assertSee('up to 4.25%');
    }

    public function test_landing_page_title_and_description_are_rendered_from_page_44(): void
    {
        config(['filesystems.disks.public.url' => 'https://nandinibali.test/storage']);

        Page::query()->forceCreate([
            'id' => 44,
            'page_name' => 'Affiliate',
            'title' => 'Affiliate CMS Title',
            'slug' => 'affiliate',
            'excerpt' => 'Affiliate CMS meta fallback.',
            'description' => '<p>Affiliate CMS description.</p><p><strong>Managed from Pages.</strong></p>',
            'hero_image' => 'pages/hero/affiliate-desktop.webp',
            'hero_image_alt' => 'Affiliate desktop hero',
            'hero_mobile_image' => 'pages/hero-mobile/affiliate-mobile.webp',
            'hero_mobile_image_alt' => 'Affiliate mobile hero',
            'is_active' => true,
            'sort_order' => 44,
        ]);

        $this->get('http://affiliate.nandinibali.test/')
            ->assertOk()
            ->assertSee('<title>Affiliate CMS Title</title>', false)
            ->assertSee('<meta name="description" content="Affiliate CMS meta fallback.">', false)
            ->assertSee('Affiliate CMS Title')
            ->assertSee('<p>Affiliate CMS description.</p><p><strong>Managed from Pages.</strong></p>', false)
            ->assertSee('src="https://nandinibali.test/storage/pages/hero/affiliate-desktop.webp"', false)
            ->assertSee('srcset="https://nandinibali.test/storage/pages/hero-mobile/affiliate-mobile.webp"', false)
            ->assertSee('alt="Affiliate desktop hero"', false)
            ->assertDontSee('Join the Nandini Partner Circle');
    }

    public function test_mini_popup_images_use_the_configured_public_media_url_on_the_affiliate_domain(): void
    {
        config(['filesystems.disks.public.url' => 'https://nandinibali.test/storage']);
        MiniPopup::query()->create([
            'title' => 'Affiliate Popup Image Test',
            'image' => 'mini-popups/affiliate-popup.webp',
            'image_alt' => 'Affiliate popup image',
            'is_active' => true,
        ]);

        $response = $this->get('http://affiliate.nandinibali.test/');

        $response->assertOk();
        $rendered = str_replace('\/', '/', stripslashes(html_entity_decode($response->getContent())));

        $this->assertStringContainsString(
            'https://nandinibali.test/storage/mini-popups/affiliate-popup.webp',
            $rendered,
        );
        $this->assertStringNotContainsString(
            'http://affiliate.nandinibali.test/storage/mini-popups/affiliate-popup.webp',
            $response->getContent(),
        );
    }

    public function test_disabled_affiliate_feature_redirects_public_portal_and_short_links(): void
    {
        config(['features.disable_affiliate_feature' => true]);

        foreach ([
            'http://affiliate.nandinibali.test/',
            'http://affiliate.nandinibali.test/login',
            'http://affiliate.nandinibali.test/register',
            'http://affiliate.nandinibali.test/dashboard',
            'http://go.nandinibali.test/landingpage4826',
        ] as $url) {
            $this->get($url)->assertRedirect(route('home'));
        }
    }

    public function test_disabled_affiliate_feature_is_hidden_from_main_navigation_and_footer(): void
    {
        config(['features.disable_affiliate_feature' => true]);
        Page::query()->forceCreate([
            'id' => 1,
            'title' => 'Home',
            'slug' => 'home',
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $this->get('http://nandinibali.test/')
            ->assertOk()
            ->assertDontSee('id="navAffiliateSidebarBtn"', false)
            ->assertDontSee('href="'.route('affiliate.landing').'"', false)
            ->assertDontSee('Affiliate Program');
    }

    public function test_authenticated_affiliate_receives_dashboard_ctas_instead_of_registration(): void
    {
        [$user] = $this->affiliate();

        $this->actingAs($user, 'affiliate')
            ->get('http://affiliate.nandinibali.test/')
            ->assertOk()
            ->assertSee('Go to Dashboard')
            ->assertSee('Landing Page Affiliate')
            ->assertSee('href="'.route('affiliate.profile').'"', false)
            ->assertSee('action="'.route('affiliate.logout').'"', false)
            ->assertSee('Profile')
            ->assertSee('href="'.route('affiliate.dashboard').'"', false)
            ->assertDontSee('href="'.route('affiliate.register').'"', false)
            ->assertDontSee('id="navAffiliateLoginBtn"', false)
            ->assertDontSee('id="navGiftVoucherBtn"', false)
            ->assertDontSee('Join Affiliate');
    }

    public function test_authenticated_affiliate_can_open_the_profile_from_the_navigation(): void
    {
        [$user] = $this->affiliate();

        $this->actingAs($user, 'affiliate')
            ->get('http://affiliate.nandinibali.test/profile')
            ->assertOk()
            ->assertSee('Profile')
            ->assertSee('Landing Page Affiliate')
            ->assertSee($user->email);
    }

    public function test_authenticated_internal_user_is_not_given_an_affiliate_dashboard_cta(): void
    {
        $internal = User::factory()->create();
        $internal->assignRole(Role::SALES_MARKETING);

        $this->actingAs($internal)
            ->get('http://affiliate.nandinibali.test/')
            ->assertOk()
            ->assertSee('Join Affiliate')
            ->assertDontSee('Go to Dashboard')
            ->assertDontSee('href="'.route('affiliate.dashboard').'"', false);
    }

    public function test_internal_and_member_sessions_can_open_affiliate_registration_and_login(): void
    {
        $internal = User::factory()->create();
        $internal->assignRole(Role::SALES_MARKETING);

        $this->actingAs($internal)
            ->get('http://affiliate.nandinibali.test/register')
            ->assertOk();
        $this->get('http://affiliate.nandinibali.test/login')->assertOk();

        auth('web')->logout();

        $member = Member::query()->create([
            'name' => 'Independent Member',
            'email' => 'independent-member@example.com',
            'password' => 'password',
        ]);

        $this->actingAs($member, 'member')
            ->get('http://affiliate.nandinibali.test/register')
            ->assertOk();
        $this->get('http://affiliate.nandinibali.test/login')->assertOk();
    }

    public function test_internal_session_can_switch_to_an_affiliate_login(): void
    {
        [$affiliateUser] = $this->affiliate();
        $internal = User::factory()->create();
        $internal->assignRole(Role::SALES_MARKETING);

        $this->actingAs($internal)
            ->post('http://affiliate.nandinibali.test/login', [
                'email' => $affiliateUser->email,
                'password' => 'password',
            ])
            ->assertRedirect(route('affiliate.dashboard'));

        $this->assertAuthenticatedAs($affiliateUser, 'affiliate');
        $this->assertAuthenticatedAs($internal, 'web');
    }

    public function test_landing_page_has_affiliate_scoped_seo_metadata_and_approved_image(): void
    {
        $this->get('http://affiliate.nandinibali.test/')
            ->assertOk()
            ->assertSee('<title>Nandini Partner Circle | Affiliate Program</title>', false)
            ->assertSee('<meta name="description" content="Share the beauty of Nandini Jungle by Hanging Gardens with your audience and earn rewards for every successful referral.">', false)
            ->assertSee('<link rel="canonical" href="http://affiliate.nandinibali.test">', false)
            ->assertSee('<meta property="og:title" content="Nandini Partner Circle | Affiliate Program">', false)
            ->assertSee('<meta name="twitter:card" content="summary_large_image">', false)
            ->assertSee('images/membership/join-today.webp', false)
            ->assertDontSee('noindex', false);
    }

    public function test_member_authentication_remains_separate_and_registration_and_voucher_routes_still_work(): void
    {
        $member = Member::query()->create([
            'name' => 'Separate Member',
            'email' => 'separate-member@example.com',
            'password' => 'password',
        ]);

        $this->actingAs($member, 'member')
            ->get('http://affiliate.nandinibali.test/')
            ->assertOk()
            ->assertSee('Join Affiliate')
            ->assertDontSee('Go to Dashboard');

        auth('member')->logout();
        $this->get('http://affiliate.nandinibali.test/register')->assertOk();
        $this->get('http://voucher.nandinibali.test/')->assertOk();
    }

    public function test_successful_member_login_ends_an_existing_affiliate_session(): void
    {
        [$affiliate] = $this->affiliate();
        $member = Member::query()->create([
            'name' => 'Guest Member Account',
            'email' => $affiliate->email,
            'password' => 'member-password',
            'member_source' => Member::SOURCE_AUTO_JOIN,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($affiliate, 'affiliate')
            ->post('http://nandinibali.test/membership/sign-in', [
                'email' => $member->email,
                'password' => 'member-password',
            ])
            ->assertRedirect(route('membership.dashboard'));

        $this->assertAuthenticatedAs($member, 'member');
        $this->assertGuest('affiliate');
    }

    public function test_successful_affiliate_login_ends_an_existing_member_session(): void
    {
        [$affiliate] = $this->affiliate();
        $member = Member::query()->create([
            'name' => 'Guest Member Account',
            'email' => $affiliate->email,
            'password' => 'member-password',
            'member_source' => Member::SOURCE_AUTO_JOIN,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($member, 'member')
            ->post('http://affiliate.nandinibali.test/login', [
                'email' => $affiliate->email,
                'password' => 'password',
            ])
            ->assertRedirect(route('affiliate.dashboard'));

        $this->assertAuthenticatedAs($affiliate, 'affiliate');
        $this->assertGuest('member');
    }

    public function test_stale_parallel_session_is_resolved_as_a_member_on_the_affiliate_site(): void
    {
        [$affiliate] = $this->affiliate();
        $member = Member::query()->create([
            'name' => 'Guest Member Account',
            'email' => $affiliate->email,
            'password' => 'member-password',
        ]);

        $this->actingAs($affiliate, 'affiliate');
        $this->actingAs($member, 'member');

        $this->get('http://affiliate.nandinibali.test/')
            ->assertOk()
            ->assertSee('Join Affiliate')
            ->assertDontSee('Go to Dashboard');

        $this->assertAuthenticatedAs($member, 'member');
        $this->assertGuest('affiliate');
    }

    public function test_stale_parallel_session_preserves_affiliate_on_protected_affiliate_pages(): void
    {
        [$affiliate] = $this->affiliate();
        $member = Member::query()->create([
            'name' => 'Guest Member Account',
            'email' => $affiliate->email,
            'password' => 'member-password',
        ]);

        $this->actingAs($affiliate, 'affiliate');
        $this->actingAs($member, 'member');

        $this->get('http://affiliate.nandinibali.test/dashboard')
            ->assertOk()
            ->assertSee('Affiliate Dashboard');

        $this->assertAuthenticatedAs($affiliate, 'affiliate');
        $this->assertGuest('member');
    }

    public function test_landing_page_remains_available_when_registration_is_disabled(): void
    {
        config(['features.affiliate_registration_enabled' => false]);

        $this->get('http://affiliate.nandinibali.test/')
            ->assertOk()
            ->assertSee('Registration Temporarily Unavailable')
            ->assertSee('Login')
            ->assertDontSee('href="'.route('affiliate.register').'"', false);
    }

    /** @return array{Affiliate, Affiliate} */
    private function affiliate(): array
    {
        $affiliate = Affiliate::query()->create([
            'name' => 'Landing Page Affiliate',
            'email' => 'landing-affiliate@example.com',
            'password' => 'password',
            'phone_whatsapp' => '+62 812 0000 0000',
            'status' => AffiliateStatus::Approved,
            'registration_source' => AffiliateRegistrationSource::CreatedByNandini,
            'affiliate_code' => 'landingpage4826',
            'affiliate_code_generated_at' => now(),
            'short_link_slug' => 'landingpage4826',
            'short_link_activated_at' => now(),
            'approved_at' => now(),
        ]);
        $affiliate->assignRole(Role::AFFILIATE);

        return [$affiliate, $affiliate];
    }
}
