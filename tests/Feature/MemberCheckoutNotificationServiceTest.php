<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Services\MemberCheckoutNotificationService;
use App\Services\MembershipEmailRelayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Mockery;
use Tests\TestCase;

class MemberCheckoutNotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_it_sends_checkout_notification_to_reservation_with_env_bcc(): void
    {
        Carbon::setTestNow('2026-07-02 08:00:00');
        config(['mail.guest_bcc' => 'news@nandinibali.com,manager@nandinibali.com']);

        $member = Member::create([
            'name' => 'Checkout Guest',
            'email' => 'checkout@example.com',
            'phone_number' => '+628123456789',
            'booking_check_in' => '2026-06-30',
            'booking_check_out' => '2026-07-02',
            'tier' => Member::TIER_BRONZE,
            'points' => 125,
        ]);

        Member::create([
            'name' => 'Future Guest',
            'email' => 'future@example.com',
            'booking_check_out' => '2026-07-03',
        ]);

        Member::create([
            'name' => 'Already Sent Guest',
            'email' => 'already@example.com',
            'booking_check_out' => '2026-07-02',
            'checkout_notification_sent_at' => now(),
        ]);

        $this->mock(MembershipEmailRelayService::class, function ($mock) use ($member): void {
            $mock->shouldReceive('sendView')
                ->once()
                ->with(
                    'emails.membership.member-checkout-today',
                    Mockery::on(fn (array $data): bool => $data['member']->is($member)
                        && $data['checkoutDate']->toDateString() === '2026-07-02'),
                    Mockery::on(fn (array $payload): bool => $payload['to'] === 'reservation@nandinibali.com'
                        && ! array_key_exists('cc', $payload)
                        && $payload['bcc'] === ['news@nandinibali.com', 'manager@nandinibali.com']
                        && $payload['subject'] === 'Member Checkout Today: Checkout Guest')
                )
                ->andReturn([
                    'success' => true,
                    'status' => 200,
                    'response' => null,
                    'error' => null,
                ]);
        });

        $summary = app(MemberCheckoutNotificationService::class)->sendTodayNotifications();

        $this->assertSame([
            'date' => '2026-07-02',
            'sent' => 1,
            'failed' => 0,
            'skipped' => 1,
        ], $summary);

        $this->assertNotNull($member->fresh()->checkout_notification_sent_at);
    }

    public function test_it_does_not_mark_checkout_notification_sent_when_relay_fails(): void
    {
        Carbon::setTestNow('2026-07-02 08:00:00');

        $member = Member::create([
            'name' => 'Failed Guest',
            'email' => 'failed@example.com',
            'booking_check_out' => '2026-07-02',
        ]);

        $this->mock(MembershipEmailRelayService::class, function ($mock): void {
            $mock->shouldReceive('sendView')
                ->once()
                ->andReturn([
                    'success' => false,
                    'status' => null,
                    'response' => null,
                    'error' => 'Relay unavailable.',
                ]);
        });

        $summary = app(MemberCheckoutNotificationService::class)->sendTodayNotifications();

        $this->assertSame(0, $summary['sent']);
        $this->assertSame(1, $summary['failed']);
        $this->assertNull($member->fresh()->checkout_notification_sent_at);
    }
}
