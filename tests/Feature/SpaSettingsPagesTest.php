<?php

namespace Tests\Feature;

use App\Filament\Pages\Spa\GeneralSpaSettings;
use App\Filament\Pages\Spa\GuestReviewSettings;
use App\Filament\Pages\Spa\InformationBarSettings;
use App\Filament\Pages\Spa\BookingCtaSettings;
use App\Filament\Pages\Spa\SignatureExperienceSettings;
use App\Filament\Pages\Spa\WellnessPhilosophySettings;
use App\Filament\Pages\Spa\WellnessJourneysSettings;
use App\Filament\Pages\Spa\WhyNandiniSettings;
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
            WhyNandiniSettings::class => 'Why Nandini Settings',
            WellnessJourneysSettings::class => 'Wellness Journeys Settings',
            SignatureExperienceSettings::class => 'SPA Signature Experience Settings',
            GuestReviewSettings::class => 'SPA Guest Review Settings',
            BookingCtaSettings::class => 'SPA Booking CTA Settings',
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
            ['icon' => 'phone', 'label' => 'Reservations', 'value' => '+62 812 3687 1170', 'link' => 'https://wa.me/6281236871170'],
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

    public function test_why_nandini_page_updates_only_its_section(): void
    {
        $settings = SpaSetting::query()->firstOrFail();
        $originalPhilosophyHeading = $settings->wellness_philosophy_heading;
        $items = [
            ['icon' => 'jungle', 'title' => 'Jungle', 'description' => 'Jungle description.'],
            ['icon' => 'ritual', 'title' => 'Ritual', 'description' => 'Ritual description.'],
            ['icon' => 'care', 'title' => 'Care', 'description' => 'Care description.'],
            ['icon' => 'river', 'title' => 'River', 'description' => 'River description.'],
        ];

        Livewire::test(WhyNandiniSettings::class)
            ->fillForm([
                'why_nandini_eyebrow' => 'Updated Why Nandini',
                'why_nandini_heading' => 'Updated wellness benefits',
                'why_nandini_items' => $items,
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified('SPA settings saved');

        $settings->refresh();
        $this->assertSame('Updated Why Nandini', $settings->why_nandini_eyebrow);
        $this->assertSame('Jungle', $settings->why_nandini_items[0]['title']);
        $this->assertSame('River', $settings->why_nandini_items[3]['title']);
        $this->assertSame($originalPhilosophyHeading, $settings->wellness_philosophy_heading);
    }

    public function test_wellness_journeys_page_updates_only_its_section(): void
    {
        $settings = SpaSetting::query()->firstOrFail();
        $originalWhyHeading = $settings->why_nandini_heading;
        $journeys = [[
            'title' => 'CMS Wellness Journey',
            'description' => 'CMS journey description.',
                    'image_alt' => 'CMS wellness image',
            'details_label' => 'Explore',
            'details_url' => '/spa-wellness/cms-wellness-journey',
            'book_label' => 'Reserve',
            'book_url' => 'https://wa.me/6281236871170',
        ]];

        Livewire::test(WellnessJourneysSettings::class)
            ->fillForm([
                'wellness_journeys_eyebrow' => 'Updated journeys eyebrow',
                'wellness_journeys_heading' => 'Updated journeys heading',
                'wellness_journeys_description' => 'Updated journeys description.',
                'wellness_journeys_items' => $journeys,
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified('SPA settings saved');

        $settings->refresh();
        $this->assertSame('Updated journeys heading', $settings->wellness_journeys_heading);
        $this->assertSame('CMS Wellness Journey', $settings->wellness_journeys_items[0]['title']);
        $this->assertSame($originalWhyHeading, $settings->why_nandini_heading);
    }

    public function test_new_spa_sections_are_independently_editable(): void
    {
        Livewire::test(SignatureExperienceSettings::class)
            ->fillForm([
                'signature_visible' => true,
                'signature_eyebrow' => 'Updated signature eyebrow',
                'signature_heading' => 'Updated signature heading',
                'signature_description' => 'Updated signature description.',
                'signature_image_alt' => 'Updated signature image alt',
                'signature_link_label' => 'Discover',
                'signature_link_url' => '/spa-wellness',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        Livewire::test(GuestReviewSettings::class)
            ->fillForm([
                'guest_review_visible' => true,
                'guest_review_quote' => 'Updated guest quote.',
                'guest_review_label' => 'Updated guest label',
                'guest_review_image_alt' => 'Updated guest image alt',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        Livewire::test(BookingCtaSettings::class)
            ->fillForm([
                'booking_cta_visible' => true,
                'booking_cta_eyebrow' => 'Updated booking eyebrow',
                'booking_cta_heading' => 'Updated booking heading',
                'booking_cta_description' => 'Updated booking description.',
                'booking_cta_button_label' => 'Reserve',
                'booking_cta_button_url' => 'https://wa.me/6281236871170',
                'booking_cta_image_alt' => 'Updated booking image alt',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $settings = SpaSetting::query()->firstOrFail();
        $this->assertSame('Updated signature heading', $settings->signature_heading);
        $this->assertSame('Updated guest quote.', $settings->guest_review_quote);
        $this->assertSame('Updated booking heading', $settings->booking_cta_heading);
    }
}
