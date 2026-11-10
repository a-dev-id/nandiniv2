<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use App\Models\AccommodationImage;
use App\Models\Experience;
use App\Models\ExperienceCategory;
use App\Models\GuestReview;
use App\Models\HomepageSetting;
use App\Models\Offer;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\PageSectionImage;
use App\Support\HomepageCache;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $payload = HomepageCache::remember(fn (): array => $this->getPublicContentPayload());
        $content = $this->hydratePublicContent($payload);

        $content['diningSections']->each(function (PageSection $section): void {
            $section->setAttribute('show_url', config('dining.public_url'));
        });

        $content['spaSections']->each(function (PageSection $section): void {
            $section->setAttribute('show_url', route('spa-landing.index'));
        });

        return view('pages.home', $content);
    }

    private function getPublicContentPayload(): array
    {
        $page = Page::query()
            ->where('id', 1)
            ->where('is_active', true)
            ->firstOrFail();

        $offers = Offer::query()
            ->published()
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        $villas = Accommodation::query()
            ->published()
            ->where('slug', '!=', 'presidential-royal-suite')
            ->with('images')
            ->get();

        $presidentialSuite = Accommodation::query()
            ->where('slug', 'presidential-royal-suite')
            ->where('is_active', true)
            ->with(['activeImages' => function ($query) {
                $query->orderBy('sort_order')->orderBy('id');
            }])
            ->first();

        $experienceCategories = ExperienceCategory::query()
            ->where('is_active', true)
            ->whereNot('slug', 'holy-river')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        $diningSections = PageSection::query()
            ->where('page_id', 4)
            ->where('is_active', true)
            ->whereIn('title', [
                'Wild Ginger Restaurant',
                'Bar & Lounge',
                'Afternoon Tea in the Heart of the Jungle',
            ])
            ->with(['images' => function ($query) {
                $query
                    ->where('is_active', true)
                    ->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();

        $spaSections = PageSection::query()
            ->where('page_id', 6)
            ->where('is_active', true)
            ->whereIn('title', [
                'Spa Jacuzzi',
                'Spa Jaccuzi',
                'Wine Spa',
                'Spa by the River',
            ])
            ->with(['images' => function ($query) {
                $query
                    ->where('is_active', true)
                    ->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();

        $ubudJungleAdventures = Experience::query()
            ->where('is_active', true)
            ->where('slug', 'not like', '%day-pass%')
            ->whereRaw('LOWER(title) NOT LIKE ?', ['%day pass%'])
            ->whereHas('category', function ($query) {
                $query->where('slug', 'ubud-jungle-adventures');
            })
            ->with('category')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        $sections = $page->sections()
            ->where('is_active', true)
            ->with(['images' => function ($query) {
                $query
                    ->where('is_active', true)
                    ->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();

        $guestReviews = GuestReview::query()
            ->published()
            ->featured()
            ->orderBy('sort_order')
            ->orderByDesc('reviewed_at')
            ->orderByDesc('id')
            ->get();

        return [
            'page' => $page->getAttributes(),
            'homepageSettings' => HomepageSetting::query()->findOrFail(1)->getAttributes(),
            'offers' => $this->modelAttributes($offers),
            'villas' => $villas->map(fn (Accommodation $villa): array => [
                'attributes' => $villa->getAttributes(),
                'images' => $this->modelAttributes($villa->images),
            ])->all(),
            'presidentialSuite' => $presidentialSuite ? [
                'attributes' => $presidentialSuite->getAttributes(),
                'activeImages' => $this->modelAttributes($presidentialSuite->activeImages),
            ] : null,
            'experienceCategories' => $this->modelAttributes($experienceCategories),
            'diningSections' => $this->sectionsWithImagesAttributes($diningSections),
            'spaSections' => $this->sectionsWithImagesAttributes($spaSections),
            'ubudJungleAdventures' => $ubudJungleAdventures->map(fn (Experience $experience): array => [
                'attributes' => $experience->getAttributes(),
                'category' => $experience->category?->getAttributes(),
            ])->all(),
            'sections' => $this->sectionsWithImagesAttributes($sections),
            'guestReviews' => $this->modelAttributes($guestReviews),
        ];
    }

    private function hydratePublicContent(array $payload): array
    {
        return [
            'page' => $this->hydrateModel(Page::class, $payload['page']),
            'homepageSettings' => $this->hydrateModel(HomepageSetting::class, $payload['homepageSettings']),
            'offers' => $this->hydrateCollection(Offer::class, $payload['offers']),
            'villas' => $this->hydrateCollectionWithMany(
                Accommodation::class,
                $payload['villas'],
                'images',
                AccommodationImage::class,
            ),
            'presidentialSuite' => $payload['presidentialSuite']
                ? $this->hydrateModelWithMany(
                    Accommodation::class,
                    $payload['presidentialSuite'],
                    'activeImages',
                    AccommodationImage::class,
                )
                : null,
            'experienceCategories' => $this->hydrateCollection(
                ExperienceCategory::class,
                $payload['experienceCategories'],
            ),
            'diningSections' => $this->hydrateCollectionWithMany(
                PageSection::class,
                $payload['diningSections'],
                'images',
                PageSectionImage::class,
            ),
            'spaSections' => $this->hydrateCollectionWithMany(
                PageSection::class,
                $payload['spaSections'],
                'images',
                PageSectionImage::class,
            ),
            'ubudJungleAdventures' => $this->hydrateCollectionWithOne(
                Experience::class,
                $payload['ubudJungleAdventures'],
                'category',
                ExperienceCategory::class,
            ),
            'sections' => $this->hydrateCollectionWithMany(
                PageSection::class,
                $payload['sections'],
                'images',
                PageSectionImage::class,
            ),
            'guestReviews' => $this->hydrateCollection(GuestReview::class, $payload['guestReviews']),
        ];
    }

    private function modelAttributes(Collection $models): array
    {
        return $models
            ->map(fn (Model $model): array => $model->getAttributes())
            ->all();
    }

    private function sectionsWithImagesAttributes(Collection $sections): array
    {
        return $sections->map(fn (PageSection $section): array => [
            'attributes' => $section->getAttributes(),
            'images' => $this->modelAttributes($section->images),
        ])->all();
    }

    private function hydrateModel(string $modelClass, array $attributes): Model
    {
        return (new $modelClass)->newFromBuilder($attributes);
    }

    private function hydrateCollection(string $modelClass, array $rows): EloquentCollection
    {
        $model = new $modelClass;

        return $model->newCollection(array_map(
            fn (array $attributes): Model => $model->newFromBuilder($attributes),
            $rows,
        ));
    }

    private function hydrateCollectionWithMany(
        string $modelClass,
        array $rows,
        string $relation,
        string $relatedClass,
    ): EloquentCollection {
        $model = new $modelClass;

        return $model->newCollection(array_map(
            fn (array $row): Model => $this->hydrateModelWithMany(
                $modelClass,
                $row,
                $relation,
                $relatedClass,
            ),
            $rows,
        ));
    }

    private function hydrateModelWithMany(
        string $modelClass,
        array $row,
        string $relation,
        string $relatedClass,
    ): Model {
        $model = $this->hydrateModel($modelClass, $row['attributes']);
        $model->setRelation($relation, $this->hydrateCollection($relatedClass, $row[$relation]));

        return $model;
    }

    private function hydrateCollectionWithOne(
        string $modelClass,
        array $rows,
        string $relation,
        string $relatedClass,
    ): EloquentCollection {
        $model = new $modelClass;

        return $model->newCollection(array_map(function (array $row) use (
            $modelClass,
            $relation,
            $relatedClass,
        ): Model {
            $model = $this->hydrateModel($modelClass, $row['attributes']);
            $model->setRelation(
                $relation,
                $row[$relation] ? $this->hydrateModel($relatedClass, $row[$relation]) : null,
            );

            return $model;
        }, $rows));
    }
}
