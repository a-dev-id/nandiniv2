<?php

namespace Tests\Feature;

use App\Services\EventScheduleService;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class EventScheduleCronTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.events.schedule_cron_token' => 'test-event-token',
            'cache.default' => 'array',
        ]);
    }

    public function test_get_route_calls_the_processor_and_returns_its_summary(): void
    {
        $this->mock(EventScheduleService::class)
            ->shouldReceive('sync')->once()->andReturn(['checked' => 3, 'updated' => 2]);

        $this->getJson('http://nandinibali.test/cron/events/schedule/test-event-token')
            ->assertOk()
            ->assertJson([
                'success' => true,
                'checked' => 3,
                'updated' => 2,
            ]);

        $lock = Cache::lock('event-schedule-cron', 300);
        $this->assertTrue($lock->get());
        $lock->release();

        $route = Route::getRoutes()->getByName('cron.events.schedule');
        $this->assertNotContains(
            StartSession::class,
            app('router')->gatherRouteMiddleware($route),
        );
    }

    public function test_invalid_and_unconfigured_tokens_do_not_run_the_processor(): void
    {
        $this->mock(EventScheduleService::class)->shouldNotReceive('sync');

        $this->getJson('http://nandinibali.test/cron/events/schedule/wrong-token')
            ->assertForbidden()->assertJson(['success' => false]);

        config(['services.events.schedule_cron_token' => null]);

        $this->getJson('http://nandinibali.test/cron/events/schedule/test-event-token')
            ->assertForbidden()->assertJson(['success' => false]);
    }

    public function test_overlapping_requests_do_not_run_the_processor(): void
    {
        $this->mock(EventScheduleService::class)->shouldNotReceive('sync');
        $lock = Cache::lock('event-schedule-cron', 300);
        $this->assertTrue($lock->get());

        try {
            $this->getJson('http://nandinibali.test/cron/events/schedule/test-event-token')
                ->assertStatus(429)->assertJson(['success' => false]);
        } finally {
            $lock->release();
        }
    }

    public function test_processor_failure_releases_the_lock_and_is_not_reported_as_success(): void
    {
        $this->mock(EventScheduleService::class)->shouldReceive('sync')->once()
            ->andThrow(new \RuntimeException('Processing failed.'));

        $this->getJson('http://nandinibali.test/cron/events/schedule/test-event-token')
            ->assertStatus(500);

        $lock = Cache::lock('event-schedule-cron', 300);
        $this->assertTrue($lock->get());
        $lock->release();
    }
}
