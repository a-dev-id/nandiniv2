<?php

namespace Tests\Feature;

use App\Filament\Pages\Festive\FestiveSettings;
use App\Http\Controllers\FestiveLandingController;
use App\Models\FestiveSetting;
use App\Models\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Tests\TestCase;

class FestiveLandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_festive_route_uses_shared_site_layout_and_seeded_content(): void
    {
        $route = Route::getRoutes()->match(Request::create('https://'.config('domains.main').'/festive-season'));
        $this->assertSame(FestiveLandingController::class, $route->getActionName());

        $this->get('https://'.config('domains.main').'/festive-season')
            ->assertOk()
            ->assertViewIs('pages.festive.index')
            ->assertSee('id="mainNavbar"', false)
            ->assertSee('Copyright')
            ->assertSee('A Festive Season<br />', false)
            ->assertSee('Festive Celebrations in the Heart of Bali')
            ->assertSee('Christmas at Nandini Jungle')
            ->assertSee('A Night to Begin Anew')
            ->assertSee('Festive Programme at Nandini Jungle')
            ->assertSee('31 December 2026')
            ->assertSee('Cocktails &amp; Social Party', false)
            ->assertSee('Countdown to 2027')
            ->assertSee('/images/festive/2026/header-landing.jpg', false)
            ->assertSee('/images/festive/2026/christmas-dining.jpg', false)
            ->assertSee('/images/festive/2026/new-year-dining.jpg', false)
            ->assertSee('/festive-season/christmas-dinner', false)
            ->assertSee('/festive-season/new-year-dinner', false)
            ->assertSee('rgba(0,0,0,.4)', false)
            ->assertDontSee('rgba(0,0,0,.78)', false)
            ->assertDontSee('from-black/35', false)
            ->assertSee('bg-[#A88444]', false);
    }

    public function test_festive_content_order_and_copy_come_from_filament_settings(): void
    {
        FestiveSetting::query()->firstOrFail()->update([
            'hero_heading' => "CMS Festive Heading\n<script>alert(1)</script>",
            'celebrations' => [
                [
                    'anchor' => 'first-event',
                    'heading' => 'First CMS Celebration',
                    'description' => 'First celebration description.',
                    'button_label' => 'Book first',
                    'button_url' => '#reserve',
                ],
                [
                    'anchor' => 'second-event',
                    'heading' => 'Second CMS Celebration',
                    'description' => 'Second celebration description.',
                ],
            ],
            'programme_days' => [[
                'date' => 'CMS Programme Date',
                'items' => [['time' => '06:00 PM', 'activity' => 'CMS Programme Activity']],
            ]],
        ]);

        $response = $this->get('https://'.config('domains.main').'/festive-season')
            ->assertOk()
            ->assertSee('CMS Festive Heading<br />', false)
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('CMS Programme Date')
            ->assertSee('CMS Programme Activity');

        $content = $response->getContent();
        $this->assertLessThan(strpos($content, 'Second CMS Celebration'), strpos($content, 'First CMS Celebration'));
    }

    public function test_sections_can_be_hidden_from_filament(): void
    {
        FestiveSetting::query()->firstOrFail()->update([
            'introduction_visible' => false,
            'programme_visible' => false,
            'booking_cta_visible' => false,
        ]);

        $this->get('https://'.config('domains.main').'/festive-season')
            ->assertOk()
            ->assertDontSee('Festive Celebrations in the Heart of Bali')
            ->assertDontSee('Festive Programme at Nandini Jungle')
            ->assertDontSee('Celebrate Together');
    }

    public function test_filament_page_loads_and_updates_nested_festive_content(): void
    {
        $role = Role::query()->create(['name' => 'Administrator', 'slug' => Role::ADMINISTRATOR]);
        $user = User::factory()->create()->assignRole($role);
        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $this->get(FestiveSettings::getUrl())
            ->assertOk()
            ->assertSee('Festive Landing Page')
            ->assertSee('Save Changes');

        Livewire::test(FestiveSettings::class)
            ->fillForm([
                'introduction_heading' => 'Updated Festive Introduction',
                'programme_days' => [[
                    'date' => '31 December 2026',
                    'items' => [
                        ['time' => '10:00 PM', 'activity' => 'Live Music'],
                        ['time' => '12:00 AM', 'activity' => 'New Year Countdown'],
                    ],
                ]],
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified('Festive landing page saved');

        $settings = FestiveSetting::query()->firstOrFail();
        $this->assertSame('Updated Festive Introduction', $settings->introduction_heading);
        $this->assertSame('New Year Countdown', $settings->programme_days[0]['items'][1]['activity']);
    }
}
