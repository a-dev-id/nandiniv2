<?php

namespace Tests\Feature;

use App\Filament\Resources\FestiveEvents\Pages\EditFestiveEvent;
use App\Http\Controllers\FestiveEventController;
use App\Models\FestiveEvent;
use App\Models\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Tests\TestCase;

class FestiveEventPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_christmas_detail_page_uses_shared_layout_and_seeded_content(): void
    {
        $url = 'https://'.config('domains.main').'/festive-season/christmas-dinner';
        $route = Route::getRoutes()->match(Request::create($url));
        $this->assertSame(FestiveEventController::class, $route->getActionName());

        $this->get($url)
            ->assertOk()
            ->assertViewIs('pages.festive.show')
            ->assertSee('id="mainNavbar"', false)
            ->assertSee('Copyright')
            ->assertDontSee('aria-label="Event details"', false)
            ->assertSee('A Christmas<br />', false)
            ->assertSee('Modern Surf and Turf')
            ->assertSee('Chawanmusi Egg')
            ->assertSee('/images/festive/2026/Dish/CHAWANMUSI-EGG.jpg', false)
            ->assertSee('Octopus Dumpling')
            ->assertSee('Programme of the Evening')
            ->assertSee('/images/festive/2026/Dish/OCTOPUS%20DUMPLING.jpg', false)
            ->assertSee('aspect-[4/3]', false)
            ->assertSee('lg:h-[70vh]', false)
            ->assertSee('justify-center', false)
            ->assertSee('lg:w-[calc(25%_-_1.875rem)]', false)
            ->assertSee('bg-[#A88444]', false);
    }

    public function test_new_year_detail_page_has_intermezzo_and_new_year_rundown(): void
    {
        $this->get('https://'.config('domains.main').'/festive-season/new-year-dinner')
            ->assertOk()
            ->assertSee('A Night to<br />', false)
            ->assertSee('Dinner Menu')
            ->assertDontSee('Chawanmusi Egg')
            ->assertSee('Blood Orange Fish Carpaccio')
            ->assertSee('Water Melon and Lemon Basil Sorbet')
            ->assertSee('Tape Ketan Panna Cotta')
            ->assertSee('/images/festive/2026/Dish/Tape%20ketan%20panna%20cotta.jpg', false)
            ->assertSee('Programme of the Evening<br />', false)
            ->assertSee('31 December 2026')
            ->assertSee('Dinner with Balinese Dance Performance')
            ->assertSee('Countdown to 2027');
    }

    public function test_detail_content_order_copy_and_visibility_come_from_filament(): void
    {
        $event = FestiveEvent::query()->where('slug', 'christmas-dinner')->firstOrFail();
        $event->update([
            'hero_heading' => "CMS Event Heading\n<script>alert(1)</script>",
            'menu_items' => [
                ['type' => 'dish', 'title' => 'First CMS Course', 'description' => 'First description'],
                ['type' => 'intermezzo', 'label' => 'PAUSE', 'title' => 'Second CMS Course'],
            ],
            'programme_visible' => false,
        ]);

        $response = $this->get('https://'.config('domains.main').'/festive-season/christmas-dinner')
            ->assertOk()
            ->assertSee('CMS Event Heading<br />', false)
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('First CMS Course')
            ->assertSee('Second CMS Course')
            ->assertDontSee('Programme of the Evening');

        $content = $response->getContent();
        $this->assertLessThan(strpos($content, 'Second CMS Course'), strpos($content, 'First CMS Course'));
    }

    public function test_inactive_detail_page_returns_not_found(): void
    {
        FestiveEvent::query()->where('slug', 'new-year-dinner')->firstOrFail()->update(['is_active' => false]);

        $this->get('https://'.config('domains.main').'/festive-season/new-year-dinner')->assertNotFound();
    }

    public function test_filament_resource_updates_nested_detail_content(): void
    {
        $role = Role::query()->create(['name' => 'Administrator', 'slug' => Role::ADMINISTRATOR]);
        $user = User::factory()->create()->assignRole($role);
        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $event = FestiveEvent::query()->where('slug', 'christmas-dinner')->firstOrFail();

        Livewire::test(EditFestiveEvent::class, ['record' => $event->getRouteKey()])
            ->assertSuccessful()
            ->fillForm([
                'hero_heading' => 'Updated Christmas Detail',
                'menu_items' => [
                    [
                        'type' => 'dish',
                        'title' => 'Updated CMS Course',
                        'description' => 'Updated course description',
                    ],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $event->refresh();
        $this->assertSame('Updated Christmas Detail', $event->hero_heading);
        $this->assertSame('Updated CMS Course', $event->menu_items[0]['title']);
    }
}
