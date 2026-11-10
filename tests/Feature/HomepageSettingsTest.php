<?php

namespace Tests\Feature;

use App\Filament\Pages\HomepageSettings;
use App\Models\Accommodation;
use App\Models\Experience;
use App\Models\ExperienceCategory;
use App\Models\GuestReview;
use App\Models\HomepageSetting;
use App\Models\Offer;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\Role;
use App\Models\User;
use App\Support\HomepageCache;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Tests\TestCase;

class HomepageSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::query()->create(['name' => 'Administrator', 'slug' => Role::ADMINISTRATOR]);
        $this->actingAs(User::factory()->create()->assignRole($role));
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_settings_menu_loads_existing_copy_and_links_to_the_homepage_introduction(): void
    {
        $this->get(HomepageSettings::getUrl())->assertOk()
            ->assertSee('Homepage Settings')->assertSee('Save Changes')->assertSee('Edit Homepage Introduction');
        $this->assertSame('Settings', HomepageSettings::getNavigationGroup());
        $this->assertSame('Homepage', HomepageSettings::getNavigationLabel());

        Livewire::test(HomepageSettings::class)->assertFormSet([
            'villas_heading' => 'Private Jungle Villas & Royal Suites',
            'adventures_heading' => 'Bali Adventure Journeys',
            'adventures_description' => "Discover Bali's natural landscapes beyond Nandini Jungle by Hanging Gardens. From Ayung River rafting and ATV adventures to Mount Batur sunrise tours, each journey offers a different way to explore the island, with convenient access from your jungle retreat in Payangan, near Ubud.",
            'guest_reviews_heading' => 'What Our Guests Say',
        ]);
    }

    public function test_all_copy_saves_reopens_and_replaces_the_cached_homepage_text(): void
    {
        $this->createHomepageContent();
        $this->get(route('home'))->assertOk()->assertSee('Bali Adventure Journeys');
        $this->assertTrue(Cache::has(HomepageCache::KEY));
        $settings = HomepageSetting::query()->findOrFail(1);
        $oldCopy = $settings->only($settings->getFillable());
        $updated = [];
        foreach (array_keys($oldCopy) as $field) {
            $updated[$field] = 'Updated '.str_replace('_', ' ', $field);
        }

        Livewire::test(HomepageSettings::class)
            ->fillForm($updated)->call('save')->assertHasNoFormErrors()->assertNotified('Homepage settings saved');

        $this->assertFalse(Cache::has(HomepageCache::KEY));
        $this->assertSame($updated, $settings->fresh()->only(array_keys($updated)));
        Livewire::test(HomepageSettings::class)->assertFormSet($updated);

        for ($request = 0; $request < 2; $request++) {
            $response = $this->get(route('home'))->assertOk();
            foreach ($updated as $text) {
                $response->assertSee($text);
            }
            foreach ($oldCopy as $field => $text) {
                if (str_ends_with($field, '_heading') || str_ends_with($field, '_description')) {
                    $response->assertDontSee($text);
                }
            }
            $response->assertSee('Existing page introduction.')->assertSee('Approx. 15 min from Nandini');
        }
        $this->assertSame(1, HomepageSetting::query()->count());
    }

    public function test_required_headings_and_label_length_are_validated_without_saving(): void
    {
        $settings = HomepageSetting::query()->findOrFail(1);
        $original = $settings->getAttributes();

        Livewire::test(HomepageSettings::class)
            ->fillForm(['adventures_heading' => '', 'villas_action_label' => str_repeat('x', 101)])
            ->call('save')
            ->assertHasFormErrors(['adventures_heading' => 'required', 'villas_action_label' => 'max']);

        $this->assertSame($original, $settings->fresh()->getAttributes());
    }

    public function test_plain_text_copy_is_escaped_on_the_homepage(): void
    {
        $this->createHomepageContent();
        HomepageSetting::query()->findOrFail(1)->update(['adventures_description' => '<script>alert("test")</script>']);

        $this->get(route('home'))->assertOk()
            ->assertSee('&lt;script&gt;alert(&quot;test&quot;)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert("test")</script>', false);
    }

    public function test_non_administrators_cannot_open_or_save_homepage_settings(): void
    {
        $this->actingAs(User::factory()->create());

        $this->assertFalse(HomepageSettings::canAccess());
        $this->get(HomepageSettings::getUrl())->assertForbidden();
        Livewire::test(HomepageSettings::class)->assertForbidden();
    }

    private function createHomepageContent(): void
    {
        foreach ([1 => 'Home', 4 => 'Dining', 6 => 'Spa'] as $id => $name) {
            if (! Page::query()->whereKey($id)->exists()) {
                Page::query()->forceCreate([
                    'id' => $id, 'site' => Page::SITE_MAIN, 'page_name' => $name, 'title' => $name,
                    'slug' => strtolower($name), 'description' => '<p>Existing page introduction.</p>', 'is_active' => true,
                ]);
            }
        }

        Accommodation::query()->create(['title' => 'Villa', 'slug' => 'test-villa', 'is_active' => true]);
        Accommodation::query()->create(['title' => 'Royal Suite', 'slug' => 'presidential-royal-suite', 'is_active' => true]);
        Offer::query()->create(['title' => 'Test Offer', 'slug' => 'test-offer', 'is_featured' => true, 'is_active' => true]);
        $category = ExperienceCategory::query()->create(['name' => 'Adventures', 'slug' => 'ubud-jungle-adventures', 'is_active' => true]);
        Experience::query()->create([
            'title' => 'Rafting', 'slug' => 'test-rafting', 'experience_category_id' => $category->id,
            'travel_time_minutes' => 15, 'is_active' => true,
        ]);
        foreach ([4 => 'Wild Ginger Restaurant', 6 => 'Spa Jacuzzi'] as $pageId => $title) {
            PageSection::query()->create(['page_id' => $pageId, 'title' => $title, 'section_key' => 'item_carousel', 'is_active' => true]);
        }
        GuestReview::query()->create([
            'reviewer_name' => 'Test Guest', 'review_text' => 'A lovely stay.', 'rating' => 5,
            'is_active' => true, 'is_featured' => true,
        ]);
        HomepageCache::forget();
    }
}
