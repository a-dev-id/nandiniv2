<?php

namespace Tests\Feature;

use App\Enums\EventStatus;
use App\Enums\EventType;
use App\Filament\Resources\Events\Pages\CreateEvent;
use App\Filament\Resources\Events\Pages\ListEvents;
use App\Models\Event;
use App\Models\Page;
use App\Models\Role;
use App\Models\User;
use Carbon\CarbonImmutable;
use Database\Seeders\AffiliateFoundationSeeder;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class EventPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['domains.main' => 'nandinibali.test']);

        Page::query()->forceCreate([
            'id' => 43,
            'page_name' => 'Events',
            'title' => 'Events & Entertainment',
            'slug' => 'events-entertainments',
            'subtitle' => 'Moments in the jungle',
            'description' => '<p>Discover dining experiences and live entertainment.</p>',
            'hero_image' => 'pages/hero/events.webp',
            'hero_image_alt' => 'Guests enjoying a Nandini event',
            'is_active' => true,
            'sort_order' => 43,
        ]);
    }

    public function test_event_schema_contains_the_requested_content_and_schedule_fields(): void
    {
        $this->assertTrue(Schema::hasColumns('events', [
            'title',
            'subtitle',
            'excerpt',
            'description',
            'image',
            'image_name',
            'alt_text',
            'status',
            'event_start_at',
            'event_end_at',
            'event_type',
            'schedule_text',
        ]));
    }

    public function test_event_page_uses_page_43_and_groups_today_upcoming_and_regular_events(): void
    {
        CarbonImmutable::setTestNow('2026-08-05 10:00:00');

        $today = $this->event([
            'title' => 'Balinese Dance Dinner',
            'subtitle' => 'Culture and dining in the jungle',
            'excerpt' => 'A memorable evening at Wild Ginger Restaurant.',
            'description' => '<p>Enjoy an evening of Balinese culture and cuisine.</p>',
            'image' => 'events/balinese-dance.webp',
            'image_name' => 'balinese-dance',
            'event_type' => EventType::Weekly,
            'schedule_text' => null,
            'event_start_at' => '2026-08-05 19:00:00',
            'event_end_at' => '2026-08-05 21:00:00',
        ]);

        $upcoming = $this->event([
            'title' => 'Jungle Acoustic Night',
            'event_type' => EventType::Monthly,
            'event_start_at' => '2026-08-06 12:00:00',
            'event_end_at' => '2026-08-06 14:00:00',
        ]);

        $regular = $this->event([
            'title' => 'Solo Acoustic',
            'event_start_at' => null,
            'event_end_at' => null,
            'schedule_text' => 'Start from 19:00 - 21:00',
        ]);

        $weekly = $this->event([
            'title' => 'Weekly Flute Performance',
            'event_type' => EventType::Weekly,
            'event_start_at' => '2026-07-29 22:00:00',
            'event_end_at' => '2026-07-29 23:00:00',
        ]);

        $this->event([
            'title' => 'Second Upcoming Event',
            'event_type' => EventType::Monthly,
            'event_start_at' => '2026-08-07 18:00:00',
            'event_end_at' => '2026-08-07 20:00:00',
        ]);

        $this->event([
            'title' => 'Third Upcoming Event',
            'event_type' => EventType::Yearly,
            'event_start_at' => '2026-08-08 18:00:00',
            'event_end_at' => '2026-08-08 20:00:00',
        ]);

        $fourthUpcoming = $this->event([
            'title' => 'Fourth Upcoming Event',
            'event_type' => EventType::Yearly,
            'event_start_at' => '2026-08-20 18:00:00',
            'event_end_at' => '2026-08-20 20:00:00',
        ]);

        $this->event([
            'title' => 'Hidden Draft Event',
            'status' => EventStatus::Draft,
            'event_start_at' => '2026-08-05 09:00:00',
            'event_end_at' => '2026-08-05 11:00:00',
        ]);

        $response = $this->get('http://nandinibali.test/events-entertainments');

        $response->assertOk()
            ->assertSee('<link rel="canonical" href="'.route('events.index').'">', false)
            ->assertSee('Events &amp; Entertainment', false)
            ->assertSee('Moments in the jungle')
            ->assertSee('Discover dining experiences and live entertainment.', false)
            ->assertSeeInOrder([
                'Happening Today',
                'Start from 7:00 PM – 9:00 PM',
                $today->title,
                'Reserve a table',
                'Upcoming Events',
                $upcoming->title,
                'Regular Events',
                $regular->title,
                $weekly->title,
            ])
            ->assertSee('Our Upcoming Dining Events at Wild Ginger Restaurant offer exclusive dining experiences available on selected dates throughout the year.')
            ->assertSee('At Wild Ginger Restaurant, every dining experience is enriched with contemporary Balinese culture through regular evening events featuring traditional dance performances and live music.')
            ->assertSee('events/balinese-dance.webp', false)
            ->assertSee('data-today-event-layout="split"', false)
            ->assertSee('width="600"', false)
            ->assertSee('height="850"', false)
            ->assertSee('Start from 7:00 PM - 9:00 PM')
            ->assertSee('August 6th, 2026. Start from 12:00 PM - 2:00 PM')
            ->assertSee('Every Wednesday · 10:00 PM – 11:00 PM')
            ->assertSee('href="https://cho.pe/wildginger.web"', false)
            ->assertSee('Reserve a table')
            ->assertSee('aria-label="Previous upcoming events"', false)
            ->assertSee('aria-label="Next regular events"', false)
            ->assertDontSee('Event details')
            ->assertDontSee('Event excerpt.')
            ->assertDontSee('Event description.')
            ->assertDontSee('Discover weekly, monthly, and yearly experiences held throughout the Nandini calendar.')
            ->assertDontSee($fourthUpcoming->title)
            ->assertDontSee('Hidden Draft Event');

        $this->assertTrue($weekly->occursOn(today()));
        $this->assertFalse($weekly->occursOn(today()->addDay()));
    }

    public function test_daily_event_schedule_sync_advances_recurring_events_and_preserves_their_duration(): void
    {
        CarbonImmutable::setTestNow('2026-08-05 00:15:00');

        $weekly = $this->event([
            'event_type' => EventType::Weekly,
            'event_start_at' => '2026-07-29 19:00:00',
            'event_end_at' => '2026-07-29 21:00:00',
        ]);

        $regular = $this->event([
            'event_type' => EventType::Regular,
            'event_start_at' => null,
            'event_end_at' => null,
        ]);

        $this->assertSame(0, Artisan::call('events:sync-schedule'));

        $this->assertSame('2026-08-05 19:00:00', $weekly->refresh()->event_start_at->format('Y-m-d H:i:s'));
        $this->assertSame('2026-08-05 21:00:00', $weekly->event_end_at->format('Y-m-d H:i:s'));
        $this->assertNull($regular->refresh()->event_start_at);

        $scheduled = collect(Schedule::events())
            ->first(fn ($event): bool => str_contains($event->command ?? '', 'events:sync-schedule'));

        $this->assertNotNull($scheduled);
        $this->assertSame('15 0 * * *', $scheduled->expression);
    }

    public function test_event_schedule_cron_url_runs_with_a_valid_token(): void
    {
        CarbonImmutable::setTestNow('2026-08-05 00:15:00');
        config(['services.events.schedule_cron_token' => 'test-event-token']);

        $weekly = $this->event([
            'event_type' => EventType::Weekly,
            'event_start_at' => '2026-07-29 19:00:00',
            'event_end_at' => '2026-07-29 21:00:00',
        ]);

        $this->getJson('http://nandinibali.test/cron/events/schedule/test-event-token')
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Event schedule cron completed.',
                'checked' => 1,
                'updated' => 1,
            ]);

        $this->assertSame('2026-08-05 19:00:00', $weekly->refresh()->event_start_at->format('Y-m-d H:i:s'));
    }

    public function test_event_schedule_cron_url_rejects_an_invalid_token(): void
    {
        config(['services.events.schedule_cron_token' => 'test-event-token']);

        $this->getJson('http://nandinibali.test/cron/events/schedule/wrong-token')
            ->assertForbidden()
            ->assertJson([
                'success' => false,
                'message' => 'Invalid cron token.',
            ]);
    }

    public function test_short_events_url_redirects_to_the_canonical_page_and_filament_routes_are_registered(): void
    {
        $this->get('http://nandinibali.test/events')
            ->assertRedirect('/events-entertainments');

        $this->get('http://nandinibali.test/events-entertainment')
            ->assertRedirect('/events-entertainments');

        $this->assertTrue(Route::has('filament.admin.resources.events.index'));
        $this->assertTrue(Route::has('filament.admin.resources.events.create'));
        $this->assertTrue(Route::has('filament.admin.resources.events.edit'));
    }

    public function test_administrator_can_create_an_event_from_filament(): void
    {
        $this->seed(AffiliateFoundationSeeder::class);
        Storage::fake('public');

        $administrator = User::factory()->create();
        $administrator->assignRole(Role::ADMINISTRATOR);
        $this->actingAs($administrator);

        Livewire::test(CreateEvent::class)
            ->fillForm([
                'title' => 'Moonlight Dinner',
                'subtitle' => 'Dinner beneath the jungle sky',
                'excerpt' => 'A special evening in the valley.',
                'description' => '<p>Enjoy a curated dinner and live music.</p>',
                'image' => UploadedFile::fake()->image('moonlight-dinner.jpg', 1600, 1000),
                'image_name' => 'moonlight-dinner',
                'alt_text' => 'Moonlight dinner at Nandini Jungle',
                'status' => true,
                'event_start_at' => null,
                'event_end_at' => null,
                'event_type' => EventType::Regular->value,
                'schedule_text' => 'Start from 19:00 - 21:00',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $event = Event::query()->where('title', 'Moonlight Dinner')->firstOrFail();

        $this->assertSame(EventStatus::Published, $event->status);
        $this->assertSame(EventType::Regular, $event->event_type);
        $this->assertNull($event->event_start_at);
        $this->assertNull($event->event_end_at);
        $this->assertSame('Start from 19:00 - 21:00', $event->schedule_text);
        $this->assertSame('moonlight-dinner', $event->image_name);
        $this->assertStringEndsWith('.webp', $event->image);
        Storage::disk('public')->assertExists($event->image);
        $this->assertSame([600, 850], array_slice(getimagesize(Storage::disk('public')->path($event->image)), 0, 2));

        Livewire::test(ListEvents::class)
            ->assertTableColumnExists('status', fn (ToggleColumn $column): bool => true)
            ->call('updateTableColumnState', 'status', (string) $event->getKey(), false);

        $this->assertSame(EventStatus::Draft, $event->refresh()->status);

        Livewire::test(ListEvents::class)
            ->call('updateTableColumnState', 'status', (string) $event->getKey(), true);

        $this->assertSame(EventStatus::Published, $event->refresh()->status);
    }

    private function event(array $overrides = []): Event
    {
        return Event::query()->create(array_merge([
            'title' => 'Nandini Event',
            'subtitle' => null,
            'excerpt' => 'Event excerpt.',
            'description' => '<p>Event description.</p>',
            'image' => 'events/default-event.webp',
            'image_name' => 'default-event',
            'alt_text' => 'Nandini event in the jungle',
            'status' => EventStatus::Published,
            'event_start_at' => '2026-08-10 18:00:00',
            'event_end_at' => '2026-08-10 20:00:00',
            'event_type' => EventType::Regular,
        ], $overrides));
    }
}
