<?php

namespace Tests\Feature;

use App\Enums\AffiliateRegistrationSource;
use App\Enums\AffiliateStatus;
use App\Filament\Resources\Affiliates\Pages\CreateAffiliate;
use App\Filament\Resources\Affiliates\Pages\EditAffiliate;
use App\Models\Affiliate;
use App\Models\AffiliateProgramSetting;
use App\Models\Member;
use App\Models\Role;
use App\Models\User;
use App\Services\Affiliate\AffiliateLinkService;
use App\Services\Affiliate\AffiliateWorkflowService;
use App\Services\Affiliate\CreateAffiliateService;
use App\Services\MembershipEmailRelayService;
use Carbon\CarbonImmutable;
use Database\Seeders\AffiliateFoundationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Tests\TestCase;

class AffiliateWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'domains.main' => 'nandinibali.test',
            'domains.affiliate' => 'affiliate.nandinibali.test',
            'domains.short_link' => 'go.nandinibali.test',
            'domains.short_link_scheme' => 'http',
            'domains.voucher' => 'voucher.nandinibali.test',
        ]);

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

    public function test_registration_page_and_required_social_validation(): void
    {
        $this->get('http://affiliate.nandinibali.test/register')
            ->assertOk()
            ->assertSee('Become a Nandini affiliate')
            ->assertSee('Phone / WhatsApp')
            ->assertSee('Threads');

        $this->post('http://affiliate.nandinibali.test/register', [
            'name' => 'Angga Rista',
            'email' => 'angga@example.com',
            'phone_whatsapp' => '+62 812 3456 7890',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
        ])->assertSessionHasErrors('social_profiles');
    }

    public function test_affiliate_email_templates_use_the_public_brand_logo(): void
    {
        $affiliate = new Affiliate(['name' => 'Email Template Partner']);
        $logo = 'https://nandinibali.com/images/logo-njhg.png';

        $notificationHtml = view('emails.affiliate.notification', [
            'affiliate' => $affiliate,
            'subject' => 'Template Test',
            'eyebrow' => 'Nandini Partner Circle',
            'heading' => 'Template Test',
        ])->render();
        $verificationHtml = view('emails.affiliate.verify-email', [
            'affiliate' => $affiliate,
            'verificationUrl' => 'https://affiliate.nandinibali.test/verify-email/test',
        ])->render();

        $this->assertStringContainsString('src="'.$logo.'"', $notificationHtml);
        $this->assertStringContainsString('src="'.$logo.'"', $verificationHtml);
    }

    public function test_public_registration_can_be_disabled_without_disabling_login_or_internal_creation(): void
    {
        config(['features.affiliate_registration_enabled' => false]);

        $this->get('http://affiliate.nandinibali.test/register')
            ->assertStatus(503)
            ->assertSee('Registration Temporarily Unavailable')
            ->assertSee('Existing partners can continue to sign in.');

        $this->post('http://affiliate.nandinibali.test/register', $this->registrationData([
            'email' => 'disabled-registration@example.com',
        ]))->assertStatus(503);

        $this->assertDatabaseMissing('affiliates', ['email' => 'disabled-registration@example.com']);
        $this->get('http://affiliate.nandinibali.test/login')->assertOk();

        $this->actingAs($this->sales())
            ->get(route('filament.admin.resources.affiliates.create'))
            ->assertOk();
    }

    public function test_login_registration_prompt_is_grouped_with_the_intro_copy(): void
    {
        $this->get('http://affiliate.nandinibali.test/login')
            ->assertOk()
            ->assertSeeInOrder([
                'Sign in to the Nandini Partner Circle affiliate portal.',
                'Not registered yet?',
                '<form method="POST"',
            ], false);
    }

    public function test_self_registration_creates_a_dedicated_pending_affiliate_account(): void
    {
        CarbonImmutable::setTestNow('2026-08-04 10:00:00');
        $this->mock(MembershipEmailRelayService::class, function ($mock): void {
            $mock->shouldReceive('sendView')
                ->once()
                ->with(
                    'emails.affiliate.verify-email',
                    \Mockery::on(fn (array $data): bool => str_contains($data['verificationUrl'], '/verify-email/')),
                    \Mockery::on(fn (array $payload): bool => $payload['to'] === 'angga@example.com'),
                )
                ->andReturn(['success' => true, 'status' => 200, 'response' => ['ok' => true], 'error' => null]);
            $mock->shouldReceive('sendView')
                ->once()
                ->with(
                    'emails.affiliate.notification',
                    \Mockery::on(fn (array $data): bool => $data['heading'] === 'Registration Received'),
                    \Mockery::on(fn (array $payload): bool => $payload['to'] === 'angga@example.com'),
                )
                ->andReturn(['success' => true, 'status' => 200, 'response' => ['ok' => true], 'error' => null]);
            $mock->shouldReceive('sendView')
                ->once()
                ->with(
                    'emails.affiliate.notification',
                    \Mockery::on(fn (array $data): bool => $data['heading'] === 'New Affiliate Request'
                        && $data['greeting'] === 'Reservations Team'
                        && $data['details']['Email'] === 'angga@example.com'),
                    \Mockery::on(fn (array $payload): bool => $payload['to'] === 'reservation@nandinibali.com'
                        && $payload['cc'] === ['news@nandinibali.com']
                        && $payload['reply_to'] === 'angga@example.com'),
                )
                ->andReturn(['success' => true, 'status' => 200, 'response' => ['ok' => true], 'error' => null]);
        });

        $response = $this->post('http://affiliate.nandinibali.test/register', [
            'name' => ' Angga  Rista ',
            'email' => 'ANGGA@example.com',
            'phone_whatsapp' => '+62   812 3456 7890',
            'instagram' => '@angga.rista',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
        ]);

        $affiliate = Affiliate::query()->where('email', 'angga@example.com')->firstOrFail();

        $response->assertRedirect(route('affiliate.dashboard'));
        $this->assertAuthenticatedAs($affiliate, 'affiliate');
        $this->assertTrue($affiliate->hasRole(Role::AFFILIATE));
        $this->assertDatabaseMissing('users', ['email' => 'angga@example.com']);
        $this->assertSame(AffiliateStatus::Pending, $affiliate->status);
        $this->assertSame(AffiliateRegistrationSource::SelfRegistration, $affiliate->registration_source);
        $this->assertSame('anggarista4826', $affiliate->affiliate_code);
        $this->assertSame($affiliate->affiliate_code, $affiliate->short_link_slug);
        $this->assertNull($affiliate->short_link_activated_at);
        $this->assertSame('https://www.instagram.com/angga.rista', $affiliate->instagram);
        $this->assertDatabaseHas('affiliate_audit_events', ['affiliate_id' => $affiliate->id, 'event' => 'self_registration']);
        $this->assertDatabaseHas('affiliate_audit_events', [
            'affiliate_id' => $affiliate->id,
            'event' => 'registration_notification_dispatched',
        ]);
        $this->assertDatabaseHas('affiliate_audit_events', [
            'affiliate_id' => $affiliate->id,
            'event' => 'affiliate_registration.staff_notification_dispatched',
        ]);

        $this->get('http://affiliate.nandinibali.test/dashboard')
            ->assertOk()
            ->assertSee('href="https://nandinibali.com"', false)
            ->assertSee('Account under review')
            ->assertDontSee('anggarista4826');
    }

    public function test_unverified_affiliate_can_use_dashboard_resend_and_verify_email(): void
    {
        $affiliate = app(CreateAffiliateService::class)->create(
            $this->profileData(['email' => 'verify-affiliate@example.com']),
            AffiliateRegistrationSource::CreatedByNandini,
            actor: $this->sales(),
        );

        $this->actingAs($affiliate, 'affiliate')
            ->get('http://affiliate.nandinibali.test/dashboard')
            ->assertOk()
            ->assertSee('Verify your email address')
            ->assertSee('You can continue using your account while you verify your email.')
            ->assertSee('action="'.route('affiliate.verification.send').'"', false);

        $this->mock(MembershipEmailRelayService::class, function ($mock): void {
            $mock->shouldReceive('sendView')
                ->once()
                ->with(
                    'emails.affiliate.verify-email',
                    \Mockery::type('array'),
                    \Mockery::on(fn (array $payload): bool => $payload['to'] === 'verify-affiliate@example.com'),
                )
                ->andReturn(['success' => true, 'status' => 200, 'response' => ['ok' => true], 'error' => null]);
        });

        $this->post('http://affiliate.nandinibali.test/email/verification-notification')
            ->assertRedirect()
            ->assertSessionHas('status', 'A new verification link has been sent to your email address.');

        $verificationUrl = URL::temporarySignedRoute(
            'affiliate.verification.verify',
            now()->addHours(24),
            ['affiliate' => $affiliate->id, 'hash' => sha1($affiliate->email)],
        );

        $this->get($verificationUrl)
            ->assertRedirect(route('affiliate.dashboard'))
            ->assertSessionHas('status', 'Your email address has been verified.');

        $this->assertNotNull($affiliate->fresh()->email_verified_at);

        $this->get('http://affiliate.nandinibali.test/dashboard')
            ->assertOk()
            ->assertDontSee('data-email-verification-notice', false);
    }

    public function test_affiliate_can_log_in_after_email_verification(): void
    {
        $affiliate = app(CreateAffiliateService::class)->create(
            $this->profileData(['email' => 'verified-login@example.com']),
            AffiliateRegistrationSource::SelfRegistration,
            password: 'secure-password',
        );

        $verificationUrl = URL::temporarySignedRoute(
            'affiliate.verification.verify',
            now()->addHours(24),
            ['affiliate' => $affiliate->id, 'hash' => sha1($affiliate->email)],
        );

        $this->get($verificationUrl)
            ->assertRedirect(route('affiliate.login'));

        $this->withSession(['url.intended' => route('affiliate.login')])
            ->post('http://affiliate.nandinibali.test/login', [
            'email' => 'verified-login@example.com',
            'password' => 'secure-password',
        ])->assertRedirect(route('affiliate.dashboard'));

        $this->assertAuthenticatedAs($affiliate, 'affiliate');
        $this->get('http://affiliate.nandinibali.test/dashboard')->assertOk();
    }

    public function test_affiliate_registration_ends_an_existing_member_session(): void
    {
        $member = Member::query()->create([
            'name' => 'Guest Member Account',
            'email' => 'guest@example.com',
            'password' => 'member-password',
        ]);

        $this->actingAs($member, 'member')
            ->post('http://affiliate.nandinibali.test/register', $this->registrationData([
                'email' => 'influencer@example.com',
            ]))
            ->assertRedirect(route('affiliate.dashboard'));

        $affiliate = Affiliate::query()->where('email', 'influencer@example.com')->firstOrFail();

        $this->assertGuest('member');
        $this->assertAuthenticatedAs($affiliate, 'affiliate');
    }

    public function test_registration_recreates_missing_program_settings_instead_of_returning_not_found(): void
    {
        AffiliateProgramSetting::query()->delete();

        $this->post('http://affiliate.nandinibali.test/register', $this->registrationData([
            'email' => 'missing-settings@example.com',
        ]))
            ->assertRedirect(route('affiliate.dashboard'));

        $this->assertDatabaseHas('affiliate_program_settings', [
            'id' => AffiliateProgramSetting::SINGLETON_ID,
            'program_name' => 'Nandini Partner Circle',
        ]);
    }

    public function test_duplicate_email_is_rejected_safely(): void
    {
        Affiliate::query()->create([
            'name' => 'Existing Affiliate',
            'email' => 'used@example.com',
            'password' => 'password',
            'status' => AffiliateStatus::Pending,
            'registration_source' => AffiliateRegistrationSource::SelfRegistration,
        ]);

        $this->post('http://affiliate.nandinibali.test/register', $this->registrationData(['email' => 'USED@example.com']))
            ->assertSessionHasErrors('email');

        $this->assertDatabaseCount('affiliates', 1);
    }

    public function test_duplicate_codes_receive_zero_padded_sequence(): void
    {
        CarbonImmutable::setTestNow('2026-08-04 10:00:00');
        $service = app(CreateAffiliateService::class);

        $first = $service->create($this->profileData(['email' => 'first@example.com']), AffiliateRegistrationSource::CreatedByNandini, actor: $this->sales());
        $second = $service->create($this->profileData(['email' => 'second@example.com']), AffiliateRegistrationSource::CreatedByNandini, actor: $this->sales());

        $this->assertSame('anggarista4826', $first->affiliate_code);
        $this->assertSame('anggarista482602', $second->affiliate_code);
        $this->assertSame($second->affiliate_code, $second->short_link_slug);
    }

    public function test_filament_can_create_pending_and_approved_affiliates_without_password_field(): void
    {
        $sales = $this->sales();
        $this->actingAs($sales);

        Livewire::test(CreateAffiliate::class)
            ->fillForm($this->profileData(['email' => 'pending-internal@example.com']))
            ->call('create')
            ->assertHasNoFormErrors();

        $pending = Affiliate::query()->where('email', 'pending-internal@example.com')->firstOrFail();
        $this->assertTrue($pending->isPending());
        $this->assertSame($sales->id, $pending->created_by);
        $this->assertNull($pending->short_link_activated_at);
        $this->assertDatabaseHas('affiliate_audit_events', [
            'affiliate_id' => $pending->id,
            'event' => 'invitation_sent',
        ]);
        $this->get(route('filament.admin.resources.affiliates.view', $pending))
            ->assertOk()
            ->assertSee($pending->affiliate_code)
            ->assertSee('Hidden from affiliate until approval');

        Livewire::test(CreateAffiliate::class)
            ->fillForm($this->profileData(['email' => 'approved-internal@example.com']))
            ->call('createApproved')
            ->assertHasNoFormErrors();

        $approved = Affiliate::query()->where('email', 'approved-internal@example.com')->firstOrFail();
        $this->assertTrue($approved->isApproved());
        $this->assertSame($sales->id, $approved->approved_by);
        $this->assertNotNull($approved->short_link_activated_at);
        $this->assertFalse(Password::broker('affiliates')->getRepository()->exists($approved, 'not-a-real-token'));
    }

    public function test_pending_affiliate_edit_updates_dedicated_email_without_changing_code(): void
    {
        $sales = $this->sales();
        $affiliate = app(CreateAffiliateService::class)->create(
            $this->profileData(['email' => 'before-edit@example.com']),
            AffiliateRegistrationSource::CreatedByNandini,
            actor: $sales,
        );
        $code = $affiliate->affiliate_code;
        $this->actingAs($sales);

        Livewire::test(EditAffiliate::class, ['record' => $affiliate->getRouteKey()])
            ->fillForm($this->profileData([
                'name' => 'Updated Partner Name',
                'email' => 'after-edit@example.com',
                'phone_whatsapp' => '+65 6123 4567',
            ]))
            ->call('save')
            ->assertHasNoFormErrors();

        $affiliate->refresh();
        $this->assertSame('after-edit@example.com', $affiliate->email);
        $this->assertSame($code, $affiliate->affiliate_code);
        $this->assertDatabaseHas('affiliate_audit_events', ['affiliate_id' => $affiliate->id, 'event' => 'email_changed']);
    }

    public function test_invitation_token_sets_password_once_and_expires_after_use(): void
    {
        $affiliate = app(CreateAffiliateService::class)->create(
            $this->profileData(['email' => 'invited@example.com']),
            AffiliateRegistrationSource::CreatedByNandini,
            actor: $this->sales(),
        );
        $token = Password::broker('affiliates')->createToken($affiliate);

        $this->get('http://affiliate.nandinibali.test/set-password/'.$token.'?email='.urlencode($affiliate->email))
            ->assertOk()
            ->assertSee('Set your password');

        $payload = [
            'token' => $token,
            'email' => $affiliate->email,
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ];

        $this->post('http://affiliate.nandinibali.test/set-password', $payload)
            ->assertRedirect(route('affiliate.login'));
        $this->post('http://affiliate.nandinibali.test/set-password', $payload)
            ->assertSessionHasErrors('email');
    }

    public function test_authenticated_internal_user_can_open_affiliate_registration(): void
    {
        $this->actingAs($this->sales())
            ->get('http://affiliate.nandinibali.test/register')
            ->assertOk();
    }

    public function test_unauthorized_user_cannot_open_filament_creation(): void
    {
        $affiliateUser = Affiliate::query()->create([
            'name' => 'Portal Only Affiliate',
            'email' => 'portal-only@example.com',
            'password' => 'password',
            'status' => AffiliateStatus::Approved,
            'registration_source' => AffiliateRegistrationSource::CreatedByNandini,
        ]);
        $affiliateUser->assignRole(Role::AFFILIATE);

        $this->actingAs($affiliateUser, 'affiliate')
            ->get(route('filament.admin.resources.affiliates.create'))
            ->assertRedirect();
    }

    public function test_approval_activates_tools_without_regenerating_code_and_double_approval_is_blocked(): void
    {
        $sales = $this->sales();
        $affiliate = app(CreateAffiliateService::class)->create(
            $this->profileData(['email' => 'approve@example.com']),
            AffiliateRegistrationSource::CreatedByNandini,
            actor: $sales,
        );
        $originalCode = $affiliate->affiliate_code;

        $approved = app(AffiliateWorkflowService::class)->approve($affiliate, $sales);

        $this->assertTrue($approved->isApproved());
        $this->assertSame($originalCode, $approved->affiliate_code);
        $this->assertNotNull($approved->short_link_activated_at);
        $this->assertDatabaseHas('affiliate_audit_events', [
            'affiliate_id' => $approved->id,
            'event' => 'approval_notification_dispatched',
        ]);

        $this->actingAs($approved, 'affiliate')
            ->get('http://affiliate.nandinibali.test/dashboard')
            ->assertOk()
            ->assertSee('Booking information is currently being refreshed')
            ->assertSee('http://go.nandinibali.test/'.$approved->affiliate_code);
        $this->expectException(\DomainException::class);
        app(AffiliateWorkflowService::class)->approve($approved, $sales);
    }

    public function test_approved_affiliate_can_permanently_dismiss_the_dashboard_welcome_message(): void
    {
        $approved = app(CreateAffiliateService::class)->create(
            $this->profileData(['email' => 'welcome-message@example.com']),
            AffiliateRegistrationSource::CreatedByNandini,
            AffiliateStatus::Approved,
            $this->sales(),
        );

        $this->actingAs($approved, 'affiliate')
            ->get('http://affiliate.nandinibali.test/dashboard')
            ->assertOk()
            ->assertSee('data-approved-welcome-modal', false)
            ->assertSee('Welcome to the Nandini Partner Circle Affiliate Dashboard.')
            ->assertSee('What You Can Track')
            ->assertSee('Commission Payment Terms')
            ->assertSee('Terms &amp; Conditions', false)
            ->assertSee('Do not show this message again');

        $this->post('http://affiliate.nandinibali.test/dashboard/welcome/dismiss', [
            'do_not_show_again' => '1',
        ])->assertRedirect(route('affiliate.dashboard'));

        $this->assertNotNull($approved->fresh()->dashboard_welcome_dismissed_at);

        $this->app['session']->flush();
        $this->actingAs($approved->fresh(), 'affiliate')
            ->get('http://affiliate.nandinibali.test/dashboard')
            ->assertOk()
            ->assertSee('approvedWelcomeOpen: false', false)
            ->assertSee('data-approved-welcome-modal', false)
            ->assertSee('Terms &amp; Conditions', false);
    }

    public function test_dashboard_welcome_message_is_not_available_to_pending_affiliates(): void
    {
        $pending = app(CreateAffiliateService::class)->create(
            $this->profileData(['email' => 'pending-welcome-message@example.com']),
            AffiliateRegistrationSource::CreatedByNandini,
            actor: $this->sales(),
        );

        $this->actingAs($pending, 'affiliate')
            ->get('http://affiliate.nandinibali.test/dashboard')
            ->assertOk()
            ->assertDontSee('data-approved-welcome-modal', false);

        $this->post('http://affiliate.nandinibali.test/dashboard/welcome/dismiss', [
            'do_not_show_again' => '1',
        ])->assertForbidden();
    }

    public function test_rejection_records_visible_reason_hides_tools_and_cannot_then_be_approved(): void
    {
        $sales = $this->sales();
        $affiliate = app(CreateAffiliateService::class)->create(
            $this->profileData(['email' => 'reject@example.com']),
            AffiliateRegistrationSource::CreatedByNandini,
            actor: $sales,
        );

        $rejected = app(AffiliateWorkflowService::class)->reject($affiliate, $sales, 'The profile does not meet the current program criteria.');

        $this->assertTrue($rejected->isRejected());
        $this->assertNull($rejected->short_link_activated_at);
        $this->assertSame($sales->id, $rejected->rejected_by);
        $this->assertDatabaseHas('affiliate_audit_events', [
            'affiliate_id' => $rejected->id,
            'event' => 'rejection_notification_dispatched',
        ]);

        $this->actingAs($rejected, 'affiliate')
            ->get('http://affiliate.nandinibali.test/dashboard')
            ->assertOk()
            ->assertSee('The profile does not meet the current program criteria.')
            ->assertDontSee($rejected->affiliate_code);

        $this->expectException(\DomainException::class);
        app(AffiliateWorkflowService::class)->approve($rejected, $sales);
    }

    public function test_short_link_redirects_only_for_approved_active_affiliate(): void
    {
        $sales = $this->sales();
        $pending = app(CreateAffiliateService::class)->create($this->profileData(['email' => 'short-pending@example.com']), AffiliateRegistrationSource::CreatedByNandini, actor: $sales);

        $this->get('http://go.nandinibali.test/'.$pending->affiliate_code)
            ->assertNotFound()
            ->assertSee('This partner link is unavailable');

        $approved = app(AffiliateWorkflowService::class)->approve($pending, $sales);
        $response = $this->get('http://go.nandinibali.test/'.$approved->affiliate_code);

        $response->assertRedirect();
        $target = $response->headers->get('Location');
        $this->assertSame($approved->affiliate_code, data_get($this->query($target), 'voucher'));
        $this->assertSame('today', data_get($this->query($target), 'checkin'));
        $this->assertStringStartsWith('https://nandinijunglebyhanginggardens.reserve-online.net/', $target);
        $rejected = app(CreateAffiliateService::class)->create($this->profileData(['email' => 'short-rejected@example.com']), AffiliateRegistrationSource::CreatedByNandini, actor: $sales);
        $rejected = app(AffiliateWorkflowService::class)->reject($rejected, $sales, 'Not approved for this test.');
        $this->get('http://go.nandinibali.test/'.$rejected->affiliate_code)->assertNotFound();

        $suspended = app(CreateAffiliateService::class)->create($this->profileData(['email' => 'short-suspended@example.com']), AffiliateRegistrationSource::CreatedByNandini, AffiliateStatus::Approved, $sales);
        $suspended->update(['status' => AffiliateStatus::Suspended]);
        $this->get('http://go.nandinibali.test/'.$suspended->affiliate_code)->assertNotFound();

        $this->get('http://go.nandinibali.test/unknown4826')->assertNotFound();
    }

    public function test_generated_short_links_use_the_correct_environment_scheme_and_domain(): void
    {
        $links = app(AffiliateLinkService::class);

        $this->assertSame('http://go.nandinibali.test/example4826', $links->shortLink('example4826'));

        config(['domains.short_link' => 'go.nandinibali.com', 'domains.short_link_scheme' => 'https']);

        $this->assertSame('https://go.nandinibali.com/example4826', $links->shortLink('example4826'));
    }

    private function sales(): User
    {
        $sales = User::factory()->create();
        $sales->assignRole(Role::SALES_MARKETING);

        return $sales;
    }

    private function profileData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Angga Rista',
            'email' => 'angga@example.com',
            'phone_whatsapp' => '+62 812 3456 7890',
            'instagram' => '@angga',
            'facebook' => null,
            'tiktok' => null,
            'x' => null,
            'threads' => null,
        ], $overrides);
    }

    private function registrationData(array $overrides = []): array
    {
        return array_merge($this->profileData(), [
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
        ], $overrides);
    }

    private function query(string $url): array
    {
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        return $query;
    }
}
