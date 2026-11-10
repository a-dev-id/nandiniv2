# Adventure travel time

Implemented on 6 November 2026 in the local project and its configured local MySQL database. Production hosting was not changed.

## Architecture and storage

`App\Models\Experience` uses the `experiences` table and belongs to `ExperienceCategory` through `experience_category_id`. Outdoor adventures are in `ubud-jungle-adventures` (local category ID 3). There was no existing travel-time field. `duration` is activity duration and remains separate.

The homepage query in `HomeController` already retrieves all experience attributes, includes them in its public cache payload, and hydrates them back into `Experience` models. No controller changes were necessary. Existing model observers invalidate the homepage cache on CMS saves.

`experiences.travel_time_minutes` is a nullable unsigned small integer, positioned after `duration` in MySQL. It stores numeric minutes only. The model adds the attribute to `$fillable` and casts it to integer.

## Changed files

- `database/migrations/2026_11_06_000001_add_travel_time_minutes_to_experiences_table.php`: adds the column; `down()` removes it.
- `database/migrations/2026_11_06_000002_backfill_approved_adventure_travel_times.php`: fills approved, currently null values by exact experience slug and category. It preserves populated CMS values and unrelated fields. Its rollback retains editorial values until the schema migration removes the column.
- `app/Models/Experience.php`: fillable attribute and integer cast.
- `app/Filament/Resources/Experiences/Schemas/ExperienceForm.php`: optional “Travel Time From Nandini” integer input, minutes suffix, resort travel helper, and nullable/integer/min:1/max:600 validation. Standard Filament create/edit persistence remains in use.
- `resources/views/components/experiences/travel-time.blade.php`: shared conditional label, using existing `text-xs`, `leading-relaxed`, and `text-slate-500` utilities. Null and zero produce no paragraph or added spacing.
- `resources/views/components/sections/item-carousel.blade.php`: label directly after experience titles, including the homepage adventure carousel.
- `resources/views/components/sections/item-list.blade.php`: same label below experience titles on the adventure listing.
- `resources/views/pages/home.blade.php`: requested Bali Adventure Journeys introduction; heading preserved.
- `tests/Feature/ExperienceTravelTimeTest.php`: rendering, CMS persistence/validation, cache invalidation, backfill, and schema rollback coverage.
- `docs/adventure-travel-time-implementation.md`: this report.

The shared card files already contained unrelated edits; those were preserved. No individual travel-time values appear in Blade, controllers, JavaScript, or CSS. No new seeder or secondary storage mechanism was introduced.

## Actual database verification

Only experience ID **38** was updated, from `NULL` to **15**. A before/after comparison of all existing experience attributes except the new field confirmed they were unchanged, including timestamps, slug, category, prices' references, activity duration, images, and metadata.

| ID | Actual title | Slug | travel_time_minutes |
| --- | --- | --- | --- |
| 38 | Ayung White Water Rafting | ayung-white-water-rafting | 15 |
| 39 | ATV Adventure | atv-adventure | NULL |
| 40 | Land Rover Jeep Sunrise Tour at Mount Batur | land-rover-jeep-sunrise-tour-at-mount-batur | NULL |
| 41 | VW Safari Adventure | vw-safari-adventure | NULL |
| 42 | Mount Batur Sunrise Trekking Adventure | mount-batur-sunrise-trekking-adventure | NULL |
| 44 | Elephant Park Tour | elephant-park-tour | NULL |

No other experiences have populated travel times. All other experience IDs remain NULL: 1, 2, 3, 4, 5, 6, 7, 16, 18, 19, 20, 22, 23, 24, 25, 26, 27, 29, 30, 31, 32, 33, 37, 39, 40, 41, 42, 43, 44, 45, 50, 51, 52, 53, 54.

The original approved Mount Batur value is **30**, but the database has two distinct sunrise experiences. Following the request to include all outdoor activities, clarification is pending on whether every non-rafting adventure should receive 30, or only both Mount Batur experiences should receive 30 while ATV, VW Safari, and Elephant Park remain NULL. No unconfirmed values were stored. Both choices are supported by the implemented CMS and cards.

## Validation and deployment results

19 targeted tests passed with 148 assertions:

```powershell
php artisan test --compact tests/Feature/ExperienceTravelTimeTest.php tests/Feature/AyungRaftingExperienceTest.php tests/Feature/HomepageCacheTest.php tests/Feature/ExperienceVoucherSynchronizerTest.php
```

Coverage includes database values 15 and 30, a changed value of 27 to rule out frontend hardcoding, null and zero suppression, homepage cache refresh, adventure listing hierarchy, CMS creation/save/reopening/clearing, integer and range validation, migration rollback/reapplication, approved-only backfill and preservation of later CMS edits, and existing rafting inquiry/voucher behavior. PHP lint, Pint for the new PHP files, and whitespace checks passed.

The actual local homepage and `/experiences/ubud-jungle-adventures` both returned HTTP 200 through the application kernel and rendered exactly `Approx. 15 min from Nandini` from the local database. The general format is `Approx. {travel_time_minutes} min from Nandini`.

Migration commands succeeded:

```powershell
php artisan migrate --path=database/migrations/2026_11_06_000001_add_travel_time_minutes_to_experiences_table.php --no-interaction
php artisan migrate --path=database/migrations/2026_11_06_000002_backfill_approved_adventure_travel_times.php --no-interaction
php artisan optimize:clear
```

The schema migration is recorded in batch 72; the data migration in batch 73. Commands were scoped to these migrations because the local database has unrelated pending migrations. Cache, configuration, compiled files, events, routes, views, Blade icons, and Filament caches cleared successfully. A frontend rebuild was unnecessary: all label utilities already exist in the compiled CSS.

Mobile and desktop visual checks remain **unverified**. Browser access was unavailable, and native Computer Use stopped because it could not determine the current browser URL reliably. The component retains the existing responsive card padding, inherits the site font, permits normal text wrapping, and adds no fixed widths or unbreakable text. This source review does not substitute for a viewport screenshot check.
