<?php

namespace Tests\Feature;

use App\Filament\Resources\Accommodations\Pages\EditAccommodations;
use App\Filament\Resources\Accommodations\RelationManagers\ImagesRelationManager;
use App\Filament\Resources\Affiliates\Pages\CreateAffiliate;
use App\Filament\Resources\BlogNews\BlogNewsResource;
use App\Filament\Resources\DiningHomepage\DiningHomepageResource;
use App\Filament\Resources\Experiences\ExperienceResource;
use App\Filament\Resources\Members\Pages\ListMembers;
use App\Models\Accommodation;
use App\Models\BlogNews;
use App\Models\Event;
use App\Models\Experience;
use App\Models\ExperienceCategory;
use App\Models\GuestReview;
use App\Models\Member;
use App\Models\Page;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Support\WebsiteContentAccess;
use Database\Seeders\WebsiteContentEditorSeeder;
use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;
use Filament\Pages\Dashboard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Livewire\Mechanisms\HandleRequests\HandleRequests;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class WebsiteContentEditorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(WebsiteContentEditorSeeder::class);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $this->actingAs(User::where('email', 'visaible@nandinibali.com')->firstOrFail());
        Storage::fake('public');
    }

    public static function resources(): array
    {
        return collect(WebsiteContentAccess::resources())->flatMap(fn ($resource) => [[$resource, false], [$resource, true]])->all();
    }

    #[DataProvider('resources')]
    public function test_editor_can_perform_actual_crud(string $resource, bool $administrator): void
    {
        if ($administrator) {
            $role = Role::create(['name' => 'Administrator', 'slug' => Role::ADMINISTRATOR]);
            $admin = User::factory()->create();
            $admin->assignRole($role);
            $this->actingAs($admin);
        }
        $model = $resource::getModel();
        $pages = $resource::getPages();
        $data = ['title' => 'Editor test', 'slug' => 'editor-test'];
        if ($model === Page::class) {
            $data['page_name'] = 'Editor test';
        }
        if ($model === Experience::class) {
            $category = ExperienceCategory::create(['name' => 'Test', 'slug' => 'test']);
            $data['experience_category_id'] = $category->id;
        }
        if ($model === GuestReview::class) {
            $data = ['reviewer_name' => 'Test Guest', 'excerpt' => 'Great stay', 'review_text' => '<p>Great stay</p>'];
        }
        if ($model === Event::class) {
            $data = ['title' => 'Editor test', 'image' => UploadedFile::fake()->image('event.jpg'), 'alt_text' => 'Test event'];
        }
        Livewire::test($pages['create']->getPage())->fillForm($data)->call('create')->assertHasNoFormErrors();
        $record = $model::query()->latest('id')->firstOrFail();
        $this->assertTrue($resource::canEdit($record));
        Livewire::test($pages['index']->getPage())->assertCanSeeTableRecords([$record]);
        $field = $model === GuestReview::class ? 'reviewer_name' : 'title';
        Livewire::test($pages['edit']->getPage(), ['record' => $record->getRouteKey()])
            ->fillForm([$field => 'Edited content'])->call('save')->assertHasNoFormErrors();
        $record->refresh();
        $this->assertSame('Edited content', $record->$field);
        Livewire::test($pages['edit']->getPage(), ['record' => $record->getRouteKey()])
            ->callAction('delete')->assertHasNoActionErrors();
        $this->assertNull($record->fresh());
        $copy = $record->replicate();
        $copy->save();
        Livewire::test($pages['index']->getPage())
            ->callTableBulkAction('delete', [$copy])->assertHasNoTableBulkActionErrors();
        $this->assertNull($copy->fresh());
    }

    public function test_navigation_direct_routes_and_backend_authorization(): void
    {
        $panel = Filament::getPanel('admin');
        $groups = $panel->getNavigation();
        $this->assertCount(1, $groups);
        $this->assertSame('Website Content', $groups[0]->getLabel());
        $this->assertCount(12, $groups[0]->getItems());
        foreach ($panel->getResources() as $resource) {
            if (in_array($resource, WebsiteContentAccess::resources(), true)) {
                $this->assertTrue($resource::canCreate());
                $this->assertTrue($resource::canDeleteAny());
                $this->get($resource::getUrl('index'))->assertOk();
            } else {
                $this->get($resource::getUrl('index'))->assertForbidden();
            }
        }
        foreach ($panel->getPages() as $page) {
            if ($page === Dashboard::class) {
                $this->get($page::getUrl())->assertRedirect();
            } else {
                $this->get($page::getUrl())->assertForbidden();
            }
        }
        foreach ([User::class, Role::class, Permission::class, Member::class] as $model) {
            foreach (['viewAny', 'create', 'update', 'delete', 'deleteAny'] as $ability) {
                $this->assertFalse(Gate::allows($ability, $model));
            }
        }
        $this->get('http://nandinibali.test/affiliate-operations/export/affiliates')->assertForbidden();
    }

    public function test_persistent_middleware_blocks_replaying_an_admin_snapshot(): void
    {
        $editor = auth()->user();
        $role = Role::create(['name' => 'Administrator', 'slug' => Role::ADMINISTRATOR]);
        $admin = User::factory()->create();
        $admin->assignRole($role);
        $this->actingAs($admin);
        $response = $this->get(DiningHomepageResource::getUrl('index'))->assertOk();
        preg_match('/wire:snapshot="([^"]+)"/', $response->getContent(), $matches);
        $this->assertNotEmpty($matches);
        $snapshot = html_entity_decode($matches[1], ENT_QUOTES);
        $this->actingAs($editor);
        $this->postJson(app(HandleRequests::class)->getUpdateUri(), [
            'components' => [['snapshot' => $snapshot, 'updates' => [], 'calls' => []]],
        ], ['X-Livewire' => 'true'])->assertForbidden();
    }

    public function test_images_and_publishing_can_be_managed(): void
    {
        $resource = BlogNewsResource::class;
        $pages = $resource::getPages();
        Livewire::test($pages['create']->getPage())->fillForm([
            'title' => 'Publishing test', 'slug' => 'publishing-test', 'is_active' => false,
            'hero_image' => UploadedFile::fake()->image('first.jpg'),
        ])->call('create')->assertHasNoFormErrors();
        $record = BlogNews::where('slug', 'publishing-test')->firstOrFail();
        Storage::disk('public')->assertExists($record->hero_image);
        $this->assertFalse($record->is_active);
        Livewire::test($pages['edit']->getPage(), ['record' => $record->getRouteKey()])
            ->fillForm(['hero_image' => []])->fillForm([
                'is_active' => true, 'published_at' => today()->addWeek()->toDateString(),
                'hero_image' => UploadedFile::fake()->image('replacement.jpg'), 'hero_image_file_name' => 'replacement',
            ])->call('save')->assertHasNoFormErrors();
        $oldPath = $record->hero_image;
        $record->refresh();
        $this->assertNotSame($oldPath, $record->hero_image);
        Storage::disk('public')->assertExists($record->hero_image);
        $this->assertFalse(BlogNews::published()->whereKey($record->id)->exists());
        Livewire::test($pages['edit']->getPage(), ['record' => $record->getRouteKey()])->fillForm([
            'published_at' => today()->toDateString(), 'hero_image' => [],
        ])->call('save')->assertHasNoFormErrors();
        $this->assertNull($record->fresh()->hero_image);
        $this->assertTrue(BlogNews::published()->whereKey($record->id)->exists());
    }

    public function test_nested_prices_and_image_relation_manager(): void
    {
        $category = ExperienceCategory::create(['name' => 'Test', 'slug' => 'test']);
        $resource = ExperienceResource::class;
        $pages = $resource::getPages();
        Livewire::test($pages['create']->getPage())->fillForm([
            'title' => 'Nested test', 'slug' => 'nested-test', 'experience_category_id' => $category->id,
            'prices' => [['label' => 'Original', 'price' => 100, 'price_type' => 'net', 'unit_type' => 'per_person', 'currency' => 'IDR']],
        ])->call('create')->assertHasNoFormErrors();
        $record = Experience::where('slug', 'nested-test')->firstOrFail();
        $this->assertSame('Original', $record->prices()->firstOrFail()->label);
        Livewire::test($pages['edit']->getPage(), ['record' => $record->getRouteKey()])
            ->fillForm(['prices' => []])->call('save')->assertHasNoFormErrors();
        $this->assertSame(0, $record->prices()->count());
        $accommodation = Accommodation::create(['title' => 'Test', 'slug' => 'test', 'accommodation_type' => 'villa']);
        $manager = ImagesRelationManager::class;
        $properties = ['ownerRecord' => $accommodation, 'pageClass' => EditAccommodations::class];
        Livewire::test($manager, $properties)->callTableAction('create', data: [
            'image' => UploadedFile::fake()->image('related.jpg'), 'image_alt' => 'Related image',
        ])->assertHasNoTableActionErrors();
        $image = $accommodation->images()->firstOrFail();
        Storage::disk('public')->assertExists($image->image);
        Livewire::test($manager, $properties)->callTableAction('edit', $image, data: ['image_alt' => 'Edited image'])
            ->assertHasNoTableActionErrors();
        $this->assertSame('Edited image', $image->fresh()->image_alt);
        Livewire::test($manager, $properties)->callTableBulkAction('delete', [$image])->assertHasNoTableBulkActionErrors();
        $this->assertNull($image->fresh());
    }

    public function test_provisioning_is_idempotent_and_preserves_administrators(): void
    {
        $editor = auth()->user();
        $password = $editor->password;
        $adminRole = Role::create(['name' => 'Administrator', 'slug' => Role::ADMINISTRATOR]);
        $admin = User::factory()->create();
        $admin->assignRole($adminRole);
        $before = $admin->getAttributes();
        $this->seed(WebsiteContentEditorSeeder::class);
        $this->assertSame($password, $editor->fresh()->password);
        $this->assertEquals($before, $admin->fresh()->getAttributes());
        $this->assertTrue($admin->hasPermissionTo(Permission::WEBSITE_CONTENT_MANAGE));
        $this->assertTrue($admin->hasPermissionTo(Permission::AFFILIATE_SETTING_MANAGE));
        $this->assertSame(1, User::where('email', $editor->email)->count());
        $this->actingAs($admin);
        foreach (WebsiteContentAccess::resources() as $resource) {
            $this->assertTrue($resource::canCreate());
            $this->assertTrue($resource::canDeleteAny());
        }
        $this->assertGreaterThan(1, count(Filament::getPanel('admin')->getNavigation()));
    }

    public function test_provisioning_replaces_existing_roles_without_resetting_password(): void
    {
        $editor = auth()->user();
        $otherRole = Role::create(['name' => 'Finance', 'slug' => Role::FINANCE]);
        $editor->assignRole($otherRole);
        $password = $editor->password;
        $this->seed(WebsiteContentEditorSeeder::class);
        $this->assertSame([Role::WEBSITE_CONTENT_EDITOR], $editor->roles()->pluck('slug')->all());
        $this->assertSame($password, $editor->fresh()->password);
    }

    public function test_provisioning_refuses_to_modify_an_administrator_at_the_target_email(): void
    {
        $editor = auth()->user();
        $role = Role::create(['name' => 'Administrator', 'slug' => Role::ADMINISTRATOR]);
        $editor->assignRole($role);
        $attributes = $editor->fresh()->getAttributes();
        $roles = $editor->roles()->pluck('id')->all();
        try {
            $this->seed(WebsiteContentEditorSeeder::class);
            $this->fail('Provisioning must refuse an Administrator account.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('Administrator', $exception->getMessage());
        }
        $this->assertEquals($attributes, $editor->fresh()->getAttributes());
        $this->assertSame($roles, $editor->roles()->pluck('id')->all());
    }

    public function test_editor_can_authenticate_and_unrelated_livewire_components_are_denied(): void
    {
        $editor = auth()->user();
        $editor->update(['password' => 'Test-password-123!']);
        auth()->logout();
        Livewire::test(Login::class)
            ->fillForm(['email' => $editor->email, 'password' => 'Test-password-123!'])
            ->call('authenticate')->assertHasNoFormErrors();
        $this->assertAuthenticatedAs($editor);
        Livewire::test(ListMembers::class)->assertForbidden();
        Livewire::test(CreateAffiliate::class)->assertForbidden();
    }
}
