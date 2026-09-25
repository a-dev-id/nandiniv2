<?php

namespace Tests\Feature;

use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MembershipEmailLookupApiTest extends TestCase
{
    use RefreshDatabase;

    private const TOKEN = 'guestletter-test-token';

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.guestletter_membership_api.token' => self::TOKEN]);
    }

    public function test_it_rejects_a_missing_bearer_token(): void
    {
        $this->postJson('/api/membership/check-email', [
            'email' => 'guest@example.com',
        ])->assertForbidden()
            ->assertExactJson([
                'ok' => false,
                'message' => 'Unauthorized.',
            ]);
    }

    public function test_it_rejects_an_incorrect_bearer_token(): void
    {
        $this->withToken('incorrect-token')
            ->postJson('/api/membership/check-email', [
                'email' => 'guest@example.com',
            ])
            ->assertForbidden()
            ->assertExactJson([
                'ok' => false,
                'message' => 'Unauthorized.',
            ]);
    }

    public function test_it_rejects_requests_when_the_configured_api_token_is_empty(): void
    {
        config(['services.guestletter_membership_api.token' => '']);

        $this->withToken(self::TOKEN)
            ->postJson('/api/membership/check-email', [
                'email' => 'guest@example.com',
            ])
            ->assertForbidden()
            ->assertExactJson([
                'ok' => false,
                'message' => 'Unauthorized.',
            ]);
    }

    public function test_it_requires_an_email_address(): void
    {
        $this->withToken(self::TOKEN)
            ->postJson('/api/membership/check-email')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    public function test_it_validates_the_email_address(): void
    {
        $this->withToken(self::TOKEN)
            ->postJson('/api/membership/check-email', [
                'email' => 'not-an-email',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    public function test_it_rejects_an_email_longer_than_255_characters(): void
    {
        $email = str_repeat('a', 244).'@example.com';

        $this->withToken(self::TOKEN)
            ->postJson('/api/membership/check-email', [
                'email' => $email,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    public function test_it_returns_member_status_for_a_registered_email_case_insensitively(): void
    {
        Member::create([
            'name' => 'Guest Member',
            'email' => 'Guest.Member@Example.com',
            'password' => 'temporary-password',
            'tier' => Member::TIER_GOLD,
            'points' => 900,
        ]);

        $this->withToken(self::TOKEN)
            ->postJson('/api/membership/check-email', [
                'email' => '  guest.member@example.com  ',
            ])
            ->assertOk()
            ->assertExactJson([
                'ok' => true,
                'is_member' => true,
                'member' => [
                    'tier' => Member::TIER_GOLD,
                    'tier_label' => 'Dhyana',
                ],
                'links' => $this->membershipLinks(),
            ]);
    }

    public function test_it_returns_non_member_status_without_exposing_account_data(): void
    {
        $this->withToken(self::TOKEN)
            ->postJson('/api/membership/check-email', [
                'email' => 'new.guest@example.com',
            ])
            ->assertOk()
            ->assertExactJson([
                'ok' => true,
                'is_member' => false,
                'member' => null,
                'links' => $this->membershipLinks(),
            ]);
    }

    public function test_it_is_rate_limited_to_sixty_requests_per_minute(): void
    {
        for ($request = 1; $request <= 60; $request++) {
            $this->withToken(self::TOKEN)
                ->postJson('/api/membership/check-email', [
                    'email' => 'guest@example.com',
                ])
                ->assertOk();
        }

        $this->withToken(self::TOKEN)
            ->postJson('/api/membership/check-email', [
                'email' => 'guest@example.com',
            ])
            ->assertTooManyRequests();
    }

    private function membershipLinks(): array
    {
        return [
            'membership' => route('membership.index'),
            'join' => route('membership.register'),
            'sign_in' => route('membership.login'),
        ];
    }
}
