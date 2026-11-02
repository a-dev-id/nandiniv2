<?php

namespace Tests\Feature;

use App\Filament\Resources\Members\Pages\EditMember;
use App\Models\Accommodation;
use App\Models\Member;
use App\Models\Page;
use App\Models\Reward;
use App\Models\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class MemberDashboardPreviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['features.disable_membership_feature' => false]);

        Page::query()->forceCreate([
            'id' => 37,
            'site' => Page::SITE_MAIN,
            'page_name' => 'Member Dashboard',
            'title' => 'Member Dashboard',
            'slug' => 'member-dashboard',
            'is_active' => true,
        ]);

        Reward::query()->create([
            'title' => 'Preview Reward',
            'points_required' => 10,
            'is_active' => true,
        ]);
    }

    public function test_administrator_can_preview_an_unverified_member_without_changing_the_member_or_signing_in(): void
    {
        $administrator = $this->administrator();
        $member = $this->member([
            'must_change_password' => true,
            'membership_expires_at' => now()->subYears(2),
            'last_login_at' => now()->subMonth(),
        ]);
        $original = $member->refresh()->getAttributes();

        $this->actingAs($administrator, 'web')
            ->get(route('membership.dashboard.preview', $member))
            ->assertOk()
            ->assertViewIs('pages.membership.dashboard')
            ->assertViewHas('member', fn (Member $previewed) => $previewed->is($member))
            ->assertSee($member->email)
            ->assertSee('Administrator preview for '.$member->name)
            ->assertSee('Back to Edit Member')
            ->assertSee('Preview Only')
            ->assertDontSee('data-reward-redeem-button', false)
            ->assertDontSee('redemption-modal-title', false);

        $this->assertAuthenticatedAs($administrator, 'web');
        $this->assertGuest('member');
        $this->assertSame($original, $member->fresh()->getAttributes());
    }

    public function test_preview_uses_the_selected_member_and_preserves_an_existing_member_session(): void
    {
        $administrator = $this->administrator();
        $signedInMember = $this->member(['email' => 'signed-in@example.com']);
        $selectedMember = $this->member(['email' => 'selected@example.com', 'name' => 'Selected Member']);

        $this->actingAs($signedInMember, 'member')
            ->actingAs($administrator, 'web')
            ->get(route('membership.dashboard.preview', $selectedMember))
            ->assertOk()
            ->assertViewHas('member', fn (Member $previewed) => $previewed->is($selectedMember))
            ->assertSee('Administrator preview for Selected Member')
            ->assertSee('selected@example.com');

        $this->assertAuthenticatedAs($signedInMember, 'member');
        $this->assertAuthenticatedAs($administrator, 'web');
    }

    public function test_guests_cannot_preview_a_member_dashboard(): void
    {
        $this->get(route('membership.dashboard.preview', $this->member()))
            ->assertRedirect(route('login'));
    }

    public function test_members_cannot_preview_a_member_dashboard(): void
    {
        $member = $this->member();

        $this->actingAs($member, 'member')
            ->get(route('membership.dashboard.preview', $member))
            ->assertRedirect(route('login'));
    }

    public function test_staff_without_the_administrator_role_cannot_preview_a_member_dashboard(): void
    {
        $role = Role::query()->create(['name' => 'Sales Marketing', 'slug' => Role::SALES_MARKETING]);
        $staff = User::factory()->create()->assignRole($role);

        $this->actingAs($staff, 'web')
            ->get(route('membership.dashboard.preview', $this->member()))
            ->assertForbidden();
    }

    public function test_preview_of_a_missing_member_returns_not_found(): void
    {
        $this->actingAs($this->administrator(), 'web')
            ->get(route('membership.dashboard.preview', ['member' => 999]))
            ->assertNotFound();
    }

    public function test_edit_member_has_a_dashboard_button_linking_to_the_selected_member(): void
    {
        $member = $this->member();
        $this->actingAs($this->administrator(), 'web');
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(EditMember::class, ['record' => $member->getRouteKey()])
            ->assertOk()
            ->assertActionVisible('viewDashboard')
            ->assertActionShouldOpenUrlInNewTab('viewDashboard')
            ->assertActionHasUrl('viewDashboard', route('membership.dashboard.preview', $member));
    }

    public function test_normal_member_dashboard_keeps_profile_editing_and_reward_redemption(): void
    {
        $member = $this->member(['email_verified_at' => now(), 'must_change_password' => false]);

        $this->actingAs($member, 'member')
            ->get(route('membership.dashboard'))
            ->assertOk()
            ->assertSee($member->email)
            ->assertSee(route('membership.profile.edit'), false)
            ->assertSee('data-reward-redeem-button', false)
            ->assertSee('redemption-modal-title', false)
            ->assertDontSee('Administrator preview');
    }

    #[DataProvider('dashboardModes')]
    public function test_completed_bookings_render_with_room_images(bool $preview): void
    {
        $member = $this->member(['email_verified_at' => now(), 'must_change_password' => false]);

        Accommodation::query()->forceCreate([
            'id' => 3,
            'title' => 'Jungle Villa',
            'slug' => 'jungle-villa',
            'card_image' => 'accommodations/jungle-villa.jpg',
            'is_active' => true,
        ]);

        $member->syncedBookings()->create([
            'booking_number' => 'COMPLETED-BOOKING',
            'guest_name' => $member->name,
            'email' => $member->email,
            'room_name' => 'Jungle Villa',
            'check_in' => today()->subDays(7),
            'check_out' => today()->subDays(2),
            'status' => 'confirmed',
        ]);

        if ($preview) {
            $this->actingAs($this->administrator(), 'web');
            $url = route('membership.dashboard.preview', $member);
        } else {
            $this->actingAs($member, 'member');
            $url = route('membership.dashboard');
        }

        $this->get($url)
            ->assertOk()
            ->assertSee('COMPLETED-BOOKING')
            ->assertSee(asset('storage/accommodations/jungle-villa.jpg'), false);
    }

    public static function dashboardModes(): array
    {
        return [
            'administrator preview' => [true],
            'member dashboard' => [false],
        ];
    }

    private function administrator(): User
    {
        $role = Role::query()->create(['name' => 'Administrator', 'slug' => Role::ADMINISTRATOR]);

        return User::factory()->create()->assignRole($role);
    }

    private function member(array $attributes = []): Member
    {
        return Member::query()->create(array_merge([
            'name' => 'Preview Member',
            'email' => 'preview-member@example.com',
            'points' => 500,
            'tier' => Member::TIER_SILVER,
        ], $attributes));
    }
}
