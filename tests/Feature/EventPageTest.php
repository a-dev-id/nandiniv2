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
use Illuminate\Support\Facades\Route;
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
        config(['app.timezone' => 'Asia/Makassar']);

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
            'is_dish_of_month',
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
            'event_type' => EventType::Regular,
            'schedule_text' => null,
            'event_start_at' => '2026-08-05 19:00:00',
            'event_end_at' => '2026-08-05 21:00:00',
        ]);

        $upcoming = $this->event([
            'title' => 'Jungle Acoustic Night',
            'event_type' => EventType::Regular,
            'event_start_at' => '2026-08-06 12:00:00',
            'event_end_at' => '2026-08-06 14:00:00',
        ]);

        $dishOfTheMonth = $this->event([
            'title' => 'Jungle Harvest Tasting Plate',
            'subtitle' => 'The chef’s featured creation',
            'excerpt' => 'A monthly presentation inspired by local ingredients.',
            'description' => '<p>Available this month at Wild Ginger Restaurant.</p>',
            'image' => 'events/jungle-harvest.webp',
            'image_name' => 'jungle-harvest',
            'event_start_at' => null,
            'event_end_at' => null,
            'is_dish_of_month' => true,
        ]);

        $regular = $this->event([
            'title' => 'Solo Acoustic',
            'event_start_at' => null,
            'event_end_at' => null,
            'schedule_text' => 'Start from 19:00 - 21:00',
        ]);

        $this->event([
            'title' => 'Second Upcoming Event',
            'event_type' => EventType::Regular,
            'event_start_at' => '2026-08-07 18:00:00',
            'event_end_at' => '2026-08-07 20:00:00',
        ]);

        $this->event([
            'title' => 'Third Upcoming Event',
            'event_type' => EventType::Regular,
            'event_start_at' => '2026-08-08 18:00:00',
            'event_end_at' => '2026-08-08 20:00:00',
        ]);

        $fourthUpcoming = $this->event([
            'title' => 'Fourth Upcoming Event',
            'event_type' => EventType::Regular,
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
                'Dish of the Month',
                $dishOfTheMonth->title,
                'Upcoming Events',
                $upcoming->title,
                'Regular Events',
                $regular->title,
            ])
            ->assertSee('Our Upcoming Dining Events at Wild Ginger Restaurant offer exclusive dining experiences available on selected dates throughout the year.')
            ->assertSee('At Wild Ginger Restaurant, every dining experience is enriched with contemporary Balinese culture through regular evening events featuring traditional dance performances and live music.')
            ->assertSee('events/balinese-dance.webp', false)
            ->assertSee('data-today-event-layout="split"', false)
            ->assertSee('data-dish-of-the-month-layout="split"', false)
            ->assertSee('events/jungle-harvest.webp', false)
            ->assertSee('width="600"', false)
            ->assertSee('height="850"', false)
            ->assertSee('Start from 7:00 PM - 9:00 PM')
            ->assertSee('August 6th, 2026. Start from 12:00 PM - 2:00 PM')
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

        $this->assertSame(1, substr_count($response->getContent(), $dishOfTheMonth->title));

    }

    public function test_expired_events_are_hidden_without_changing_or_deleting_the_cms_record(): void
    {
        CarbonImmutable::setTestNow('2026-08-05 14:01:00', 'Asia/Makassar');

        $expired = $this->event([
            'title' => 'Expired Pool Event',
            'event_start_at' => '2026-08-05 12:00:00',
            'event_end_at' => '2026-08-05 14:00:00',
        ]);

        $current = $this->event([
            'title' => 'Current Pool Event',
            'event_start_at' => '2026-08-05 14:00:00',
            'event_end_at' => '2026-08-05 16:00:00',
        ]);

        $this->get('http://nandinibali.test/events-entertainments')
            ->assertOk()
            ->assertDontSee($expired->title)
            ->assertSee($current->title);

        $this->assertDatabaseHas('events', [
            'id' => $expired->id,
            'status' => EventStatus::Published->value,
            'event_start_at' => '2026-08-05 12:00:00',
            'event_end_at' => '2026-08-05 14:00:00',
        ]);
    }

    public function test_only_one_event_can_be_the_dish_of_the_month(): void
    {
        $previousDish = $this->event([
            'title' => 'Previous Monthly Dish',
            'is_dish_of_month' => true,
        ]);

        $currentDish = $this->event([
            'title' => 'Current Monthly Dish',
            'is_dish_of_month' => true,
        ]);

        $this->assertFalse($previousDish->refresh()->is_dish_of_month);
        $this->assertTrue($currentDish->refresh()->is_dish_of_month);
        $this->assertSame(1, Event::query()->where('is_dish_of_month', true)->count());
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
                'is_dish_of_month' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $event = Event::query()->where('title', 'Moonlight Dinner')->firstOrFail();

        $this->assertSame(EventStatus::Published, $event->status);
        $this->assertSame(EventType::Regular, $event->event_type);
        $this->assertNull($event->event_start_at);
        $this->assertNull($event->event_end_at);
        $this->assertSame('Start from 19:00 - 21:00', $event->schedule_text);
        $this->assertTrue($event->is_dish_of_month);
        $this->assertSame('moonlight-dinner', $event->image_name);
        $this->assertStringEndsWith('.webp', $event->image);
        Storage::disk('public')->assertExists($event->image);
        $this->assertSame([600, 850], array_slice(getimagesize(Storage::disk('public')->path($event->image)), 0, 2));

        Livewire::test(ListEvents::class)
            ->assertTableColumnExists('status', fn (ToggleColumn $column): bool => true)
            ->assertTableColumnExists('is_dish_of_month', fn (ToggleColumn $column): bool => true)
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
            'is_dish_of_month' => false,
        ], $overrides));
    }
}
