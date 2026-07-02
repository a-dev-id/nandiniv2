<?php

namespace Tests\Feature;

use App\Services\MemberCheckoutNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberCheckoutNotificationCronTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_notification_cron_rejects_invalid_token(): void
    {
        config(['services.membership.lifecycle_cron_token' => 'valid-token']);

        $this->getJson('/cron/members/checkout-notifications/wrong-token')
            ->assertForbidden()
            ->assertJson([
                'success' => false,
                'message' => 'Invalid cron token.',
            ]);
    }

    public function test_checkout_notification_cron_runs_with_valid_token(): void
    {
        config(['services.membership.lifecycle_cron_token' => 'valid-token']);

        $this->mock(MemberCheckoutNotificationService::class, function ($mock): void {
            $mock->shouldReceive('sendTodayNotifications')
                ->once()
                ->with('2026-07-02')
                ->andReturn([
                    'date' => '2026-07-02',
                    'sent' => 2,
                    'failed' => 0,
                    'skipped' => 1,
                ]);
        });

        $this->getJson('/cron/members/checkout-notifications/valid-token?date=2026-07-02')
            ->assertOk()
            ->assertJson([
                'success' => true,
                'checkout_date' => '2026-07-02',
                'notifications_sent' => 2,
                'notifications_failed' => 0,
                'notifications_already_sent' => 1,
            ]);
    }
}
