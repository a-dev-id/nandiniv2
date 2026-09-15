<?php

namespace Tests\Feature;

use App\Filament\Pages\Dining\FaqSettings;
use App\Filament\Pages\Dining\DishOfTheMonthSettings;
use App\Filament\Pages\Dining\GeneralDiningSettings;
use App\Filament\Pages\Dining\InformationBarSettings;
use App\Filament\Pages\Dining\OurPhilosophySettings;
use App\Filament\Pages\Dining\PlanYourVisitSettings;
use App\Filament\Pages\Dining\PrivateDiningSettings;
use App\Filament\Pages\Dining\ReservationCtaSettings;
use App\Filament\Pages\Dining\WhyDineSettings;
use App\Models\DiningSetting;
use App\Models\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DiningSettingsPagesTest extends TestCase
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

    public function test_each_dining_settings_page_loads_with_its_own_form(): void
    {
        $pages = [
            GeneralDiningSettings::class => 'Dining General Settings',
            OurPhilosophySettings::class => 'Our Philosophy Settings',
            WhyDineSettings::class => 'Why Dine Settings',
            InformationBarSettings::class => 'Information Bar Settings',
            DishOfTheMonthSettings::class => 'Dish of the Month Settings',
            PrivateDiningSettings::class => 'Private Dining Settings',
            PlanYourVisitSettings::class => 'Plan Your Visit Settings',
            FaqSettings::class => 'FAQ Settings',
            ReservationCtaSettings::class => 'Reservation CTA Settings',
        ];

        foreach ($pages as $page => $title) {
            $this->get($page::getUrl())->assertOk()->assertSee($title)->assertSee('Save Changes');
        }

        $this->get(route('filament.admin.resources.dining-settings.index'))->assertForbidden();
    }

    public function test_section_page_save_updates_only_its_owned_fields(): void
    {
        $settings = DiningSetting::query()->firstOrCreate();
        $settings->update([
            'philosophy_eyebrow' => 'Preserved philosophy',
            'why_dine_eyebrow' => 'Preserved why dine',
            'private_dining_heading' => 'Preserved private dining',
            'reservation_cta_heading' => 'Preserved CTA',
            'faq_eyebrow' => 'Old FAQ eyebrow',
            'faq_heading' => 'Old FAQ heading',
            'faq_items' => [['question' => 'Old question?', 'answer' => 'Old answer.']],
        ]);

        Livewire::test(FaqSettings::class)
            ->fillForm([
                'faq_eyebrow' => 'Updated FAQ eyebrow',
                'faq_heading' => 'Updated FAQ heading',
                'faq_items' => [
                    ['question' => 'Second question?', 'answer' => 'Second answer.'],
                    ['question' => 'First question?', 'answer' => 'First answer.'],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified('Dining settings saved');

        $settings->refresh();
        $this->assertSame('Updated FAQ eyebrow', $settings->faq_eyebrow);
        $this->assertSame('Second question?', $settings->faq_items[0]['question']);
        $this->assertSame('Preserved philosophy', $settings->philosophy_eyebrow);
        $this->assertSame('Preserved why dine', $settings->why_dine_eyebrow);
        $this->assertSame('Preserved private dining', $settings->private_dining_heading);
        $this->assertSame('Preserved CTA', $settings->reservation_cta_heading);
    }

    public function test_plan_your_visit_page_can_update_the_premium_menu_button(): void
    {
        $settings = DiningSetting::query()->firstOrCreate();

        Livewire::test(PlanYourVisitSettings::class)
            ->fillForm([
                'visit_premium_menu_label' => 'Premium Menu Updated',
                'visit_premium_menu_url' => 'https://example.com/premium-menu',
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified('Dining settings saved');

        $settings->refresh();
        $this->assertSame('Premium Menu Updated', $settings->visit_premium_menu_label);
        $this->assertSame('https://example.com/premium-menu', $settings->visit_premium_menu_url);
    }

    public function test_dish_of_the_month_page_updates_its_content_and_action(): void
    {
        $settings = DiningSetting::query()->firstOrCreate();

        Livewire::test(DishOfTheMonthSettings::class)
            ->fillForm([
                'signature_dishes' => [[
                    'eyebrow' => 'Monthly Feature',
                    'heading' => 'Updated Dish',
                    'introduction' => 'Updated monthly dish description.',
                    'label' => 'Chef Selection',
                    'title' => 'Updated Dish',
                    'price_display' => 'IDR 500,000++',
                    'panel_description' => 'Updated panel description.',
                    'alt' => 'Updated dish image alt text',
                ]],
                'signature_menu_label' => 'See the menu',
                'signature_menu_url' => 'https://example.com/monthly-menu',
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified('Dining settings saved');

        $settings->refresh();
        $this->assertSame('Updated Dish', $settings->signature_dishes[0]['heading']);
        $this->assertSame('Updated dish image alt text', $settings->signature_dishes[0]['alt']);
        $this->assertSame('See the menu', $settings->signature_menu_label);
        $this->assertSame('https://example.com/monthly-menu', $settings->signature_menu_url);
    }
}
