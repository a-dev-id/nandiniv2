<?php

namespace Tests\Feature;

use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberLastLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_member_login_updates_last_login_at(): void
    {
        $member = Member::create([
            'name' => 'Active Member',
            'email' => 'active-member@example.com',
            'password' => 'secret-password',
            'must_change_password' => false,
            'email_verified_at' => now(),
        ]);

        $this->assertNull($member->last_login_at);

        $this->post(route('membership.login.submit'), [
            'email' => 'active-member@example.com',
            'password' => 'secret-password',
        ])->assertRedirect(route('membership.dashboard'));

        $this->assertNotNull($member->fresh()->last_login_at);
    }

    public function test_member_login_returns_to_intended_voucher_checkout(): void
    {
        $member = Member::create([
            'name' => 'Checkout Member',
            'email' => 'checkout-member@example.com',
            'password' => 'secret-password',
            'must_change_password' => false,
            'email_verified_at' => now(),
        ]);

        $this
            ->withSession(['url.intended' => route('voucher.checkout.index')])
            ->post(route('membership.login.submit'), [
                'email' => $member->email,
                'password' => 'secret-password',
            ])
            ->assertRedirect(route('voucher.checkout.index'));
    }
}
