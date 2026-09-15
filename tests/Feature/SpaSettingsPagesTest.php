<?php

namespace Tests\Feature;

use App\Filament\Pages\Spa\GeneralSpaSettings;
use App\Filament\Pages\Spa\InformationBarSettings;
use App\Filament\Pages\Spa\WellnessPhilosophySettings;
use App\Models\Role;
use App\Models\SpaSetting;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SpaSettingsPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::query()->create(['name' => 'Administrator', 'slug' => Role::ADMINISTRATOR]);
        $user = User::factory()->create()->assignRole($role);
        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_each_spa_settings_page_loads_with_its_own_form(): void
    {
        $pages = [
            GeneralSpaSettings::class => 'SPA General Settings',
            InformationBarSettings::class => 'SPA Information Bar Settings',
            WellnessPhilosophySettings::class => 'Wellness Philosophy Settings',
        ];

        foreach ($pages as $page => $title) {
            $this->get($page::getUrl())
                ->assertOk()
                ->assertSee($title)
                ->assertSee('Save Changes');
        }
    }

    public function test_general_page_updates_the_hero_without_changing_the_information_bar(): void
    {
        $settings = SpaSetting::query()->firstOrFail();
        $informationBar = $settings->information_bar_items;

        Livewire::test(GeneralSpaSettings::class)
            ->fillForm([
                'hero_eyebrow' => 'Updated wellness eyebrow',
                'hero_heading' => "Updated wellness\nheading",
                'hero_description' => 'Updated SPA introduction.',
                'hero_primary_cta_label' => 'Book now',
                'hero_primary_cta_url' => 'https://wa.me/6281236871170',
                'hero_secondary_cta_label' => 'View treatments',
                'hero_secondary_cta_url' => '/treatments',
                'reservation_whatsapp' => '+62 812 3687 1170',
                'reservation_url' => 'https://wa.me/6281236871170',
                'meta_title' => 'Updated SPA SEO title',
                'meta_description' => 'Updated SPA SEO description.',
                'meta_author' => 'Nandini Jungle',
                'meta_site_name' => 'Nandini Jungle by Hanging Gardens',
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified('SPA settings saved');

        $settings->refresh();
        $this->assertSame('Updated wellness eyebrow', $settings->hero_eyebrow);
        $this->assertSame("Updated wellness\nheading", $settings->hero_heading);
        $this->assertSame($informationBar, $settings->information_bar_items);
    }

    public function test_information_bar_page_updates_only_the_four_information_items(): void
    {
        $settings = SpaSetting::query()->firstOrFail();
        $originalHeading = $settings->hero_heading;
        $items = [
            ['icon' => 'clock', 'label' => 'Hours', 'value' => '08:00 AM – 10:00 PM', 'link' => null],
            ['icon' => 'calendar', 'label' => 'Booking', 'value' => 'Advance booking recommended', 'link' => null],
            ['icon' => 'location', 'label' => 'Location', 'value' => 'Ubud, Bali', 'link' => null],
            ['icon' => 'phone', 'label' => 'Reservations', 'value' => "+62 812 3687 1170\n(WhatsApp)", 'link' => 'https://wa.me/6281236871170'],
        ];

        Livewire::test(InformationBarSettings::class)
            ->fillForm(['information_bar_items' => $items])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified('SPA settings saved');

        $settings->refresh();
        $this->assertSame('Hours', $settings->information_bar_items[0]['label']);
        $this->assertSame('Reservations', $settings->information_bar_items[3]['label']);
        $this->assertSame($originalHeading, $settings->hero_heading);
    }

    public function test_wellness_philosophy_page_updates_only_its_section(): void
    {
        $settings = SpaSetting::query()->firstOrFail();
        $originalInformationBar = $settings->information_bar_items;

        Livewire::test(WellnessPhilosophySettings::class)
            ->fillForm([
                'wellness_philosophy_eyebrow' => 'Updated philosophy eyebrow',
                'wellness_philosophy_heading' => "Updated philosophy\nheading",
                'wellness_philosophy_description' => 'Updated philosophy description.',
                'wellness_philosophy_image_alt' => 'Updated philosophy image description',
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified('SPA settings saved');

        $settings->refresh();
        $this->assertSame('Updated philosophy eyebrow', $settings->wellness_philosophy_eyebrow);
        $this->assertSame("Updated philosophy\nheading", $settings->wellness_philosophy_heading);
        $this->assertSame('Updated philosophy description.', $settings->wellness_philosophy_description);
        $this->assertSame($originalInformationBar, $settings->information_bar_items);
    }
}
