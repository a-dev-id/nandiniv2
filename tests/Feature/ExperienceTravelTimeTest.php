<?php

namespace Tests\Feature;

use App\Filament\Resources\Experiences\Pages\CreateExperience;
use App\Filament\Resources\Experiences\Pages\EditExperience;
use App\Models\Experience;
use App\Models\ExperienceCategory;
use App\Models\Page;
use App\Models\Role;
use App\Models\User;
use App\Support\HomepageCache;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ExperienceTravelTimeTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_cards_use_database_values_and_refresh_after_cms_changes(): void
    {
        Page::query()->forceCreate([
            'id' => 1,
            'site' => Page::SITE_MAIN,
            'page_name' => 'Home',
            'slug' => 'home',
            'title' => 'Nandini',
            'is_active' => true,
        ]);
        $category = $this->createCategory();
        $rafting = $this->createExperience($category, 'Ayung White Water Rafting', 'ayung-white-water-rafting', 15);
        $this->createExperience($category, 'Mount Batur Sunrise Tour', 'mount-batur-sunrise-tour', 30);
        $this->createExperience($category, 'ATV Adventure', 'atv-adventure', null);
        HomepageCache::forget();

        for ($request = 0; $request < 2; $request++) {
            $response = $this->get(route('home'))->assertOk();
            $response->assertSee('Bali Adventure Journeys')
                ->assertSee('each journey offers a different way to explore the island, with convenient access from your jungle retreat in Payangan, near Ubud.')
                ->assertSeeInOrder(['Ayung White Water Rafting', 'Approx. 15 min from Nandini', 'Description for Ayung White Water Rafting'])
                ->assertSeeInOrder(['Mount Batur Sunrise Tour', 'Approx. 30 min from Nandini', 'Description for Mount Batur Sunrise Tour']);
            $this->assertSame(2, substr_count($response->getContent(), 'min from Nandini'));
        }

        $rafting->update(['travel_time_minutes' => 27]);
        $this->get(route('home'))->assertOk()
            ->assertSee('Approx. 27 min from Nandini')
            ->assertDontSee('Approx. 15 min from Nandini');

        $rafting->update(['travel_time_minutes' => null]);
        $this->get(route('home'))->assertOk()->assertDontSee('Approx. 27 min from Nandini');
    }

    public function test_cards_with_null_or_zero_have_no_label_or_empty_paragraph(): void
    {
        $category = $this->createCategory();
        foreach ([null, 0] as $minutes) {
            $experience = $this->createExperience($category, 'No travel label', 'no-label-'.($minutes ?? 'null'), $minutes);
            $html = Blade::render('<x-sections.item-carousel :items="$items" route-name="experiences.show" />', [
                'items' => collect([$experience]),
            ]);

            $this->assertStringNotContainsString('min from Nandini', $html);
            $this->assertDoesNotMatchRegularExpression('/<p\b[^>]*>\s*<\/p>/', $html);
        }
    }

    public function test_adventure_listing_uses_the_same_travel_time_label(): void
    {
        Page::query()->forceCreate([
            'id' => 5,
            'site' => Page::SITE_MAIN,
            'page_name' => 'Experiences',
            'slug' => 'experiences',
            'title' => 'Experiences',
            'is_active' => true,
        ]);
        $category = $this->createCategory();
        $this->createExperience($category, 'Ayung White Water Rafting', 'ayung-white-water-rafting', 15);
        $this->createExperience($category, 'ATV Adventure', 'atv-adventure', null);

        $response = $this->get(route('experiences.category', 'ubud-jungle-adventures'))->assertOk();
        $response->assertSeeInOrder(['Ayung White Water Rafting', 'Approx. 15 min from Nandini', 'Description for Ayung White Water Rafting']);
        $this->assertSame(1, substr_count($response->getContent(), 'min from Nandini'));
    }

    public function test_schema_migration_can_roll_back_and_run_again(): void
    {
        $migration = require database_path('migrations/2026_11_06_000001_add_travel_time_minutes_to_experiences_table.php');
        $migration->down();
        $this->assertFalse(Schema::hasColumn('experiences', 'travel_time_minutes'));
        $migration->up();
        $this->assertTrue(Schema::hasColumn('experiences', 'travel_time_minutes'));
    }

    public function test_backfill_only_updates_approved_empty_values_and_preserves_cms_edits(): void
    {
        $category = $this->createCategory();
        $rafting = $this->createExperience($category, 'Ayung White Water Rafting', 'ayung-white-water-rafting', null);
        $atv = $this->createExperience($category, 'ATV Adventure', 'atv-adventure', null);
        $migration = require database_path('migrations/2026_11_06_000002_backfill_approved_adventure_travel_times.php');
        $original = $rafting->fresh()->getAttributes();
        unset($original['travel_time_minutes']);

        $migration->up();
        $this->assertSame(15, $rafting->fresh()->travel_time_minutes);
        $this->assertNull($atv->fresh()->travel_time_minutes);
        $after = $rafting->fresh()->getAttributes();
        unset($after['travel_time_minutes']);
        $this->assertEquals($original, $after);

        $rafting->update(['travel_time_minutes' => 22]);
        $migration->up();
        $this->assertSame(22, $rafting->fresh()->travel_time_minutes);
    }

    public function test_cms_create_edit_reload_and_clear_travel_time(): void
    {
        $this->signInToCms();
        $category = $this->createCategory();

        Livewire::test(CreateExperience::class)
            ->fillForm([
                'experience_category_id' => $category->id,
                'title' => 'CMS adventure',
                'slug' => 'cms-adventure',
                'travel_time_minutes' => 15,
                'duration' => '3 hours',
                'prices' => [],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $experience = Experience::query()->where('slug', 'cms-adventure')->firstOrFail();
        $this->assertSame(15, $experience->travel_time_minutes);

        Livewire::test(EditExperience::class, ['record' => $experience->id])
            ->assertFormSet(['travel_time_minutes' => 15])
            ->fillForm(['travel_time_minutes' => 30])
            ->call('save')
            ->assertHasNoFormErrors();
        $this->assertSame(30, $experience->fresh()->travel_time_minutes);

        Livewire::test(EditExperience::class, ['record' => $experience->id])
            ->assertFormSet(['travel_time_minutes' => 30])
            ->fillForm(['travel_time_minutes' => ''])
            ->call('save')
            ->assertHasNoFormErrors();
        $this->assertNull($experience->fresh()->travel_time_minutes);
        $this->assertSame('3 hours', $experience->fresh()->duration);
    }

    #[DataProvider('invalidTravelTimes')]
    public function test_cms_rejects_invalid_travel_times(mixed $minutes, string $rule): void
    {
        $this->signInToCms();
        $experience = $this->createExperience($this->createCategory(), 'Validation adventure', 'validation-adventure', 15);

        Livewire::test(EditExperience::class, ['record' => $experience->id])
            ->fillForm(['travel_time_minutes' => $minutes])
            ->call('save')
            ->assertHasFormErrors(['travel_time_minutes' => $rule]);

        $this->assertSame(15, $experience->fresh()->travel_time_minutes);
    }

    public static function invalidTravelTimes(): array
    {
        return [
            'zero' => [0, 'min'],
            'negative' => [-1, 'min'],
            'too long' => [601, 'max'],
            'fractional' => [1.5, 'integer'],
            'formatted text' => ['15 mins', 'integer'],
        ];
    }

    private function signInToCms(): void
    {
        $role = Role::query()->create(['name' => 'Administrator', 'slug' => Role::ADMINISTRATOR]);
        $this->actingAs(User::factory()->create()->assignRole($role));
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    private function createCategory(): ExperienceCategory
    {
        return ExperienceCategory::query()->create([
            'name' => 'Ubud Jungle Adventures',
            'slug' => 'ubud-jungle-adventures',
            'is_active' => true,
        ]);
    }

    private function createExperience(ExperienceCategory $category, string $title, string $slug, ?int $minutes): Experience
    {
        return Experience::query()->create([
            'experience_category_id' => $category->id,
            'title' => $title,
            'slug' => $slug,
            'excerpt' => 'Description for '.$title,
            'travel_time_minutes' => $minutes,
            'is_active' => true,
        ]);
    }
}
