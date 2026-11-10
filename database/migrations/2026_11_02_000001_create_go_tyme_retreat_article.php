<?php

use App\Models\BlogNews;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Preserve any existing editorial changes and publication permissions.
        if (DB::table('blog_news')->where('slug', BlogNews::GO_TYME_SLUG)->exists()) {
            return;
        }

        $content = require database_path('content/go-tyme-retreat.php');
        DB::transaction(function () use ($content): void {
            $now = now();
            $blogId = DB::table('blog_news')->insertGetId([
                'type' => 'blog',
                'slug' => BlogNews::GO_TYME_SLUG,
                'title' => 'Six Days of Connection in the Jungle: Go Tyme Group at Nandini',
                'subtitle' => 'A Shared Journey of Reflection, Nature, and Balinese Traditions',
                'excerpt' => 'Seventeen guests, six days and five nights in Payangan: a photographic story of reflection, jungle surroundings, Balinese traditions and time shared at Nandini.',
                'description' => $content['intro'],
                'hero_image' => '/images/go-tyme-images/optimized/08615-1600.webp',
                'hero_image_alt' => 'Go Tyme Group gathered on the river deck, surrounded by the jungle at Nandini',
                'card_image' => '/images/go-tyme-images/optimized/08615-960.webp',
                'card_image_alt' => 'Go Tyme Group together on Nandini’s jungle-framed river deck',
                'author_name' => 'Nandini Jungle by Hanging Gardens',
                // Publication is the import date, never the retreat date.
                'published_at' => $now->toDateString(),
                'meta_title' => 'Go Tyme Group Retreat in Bali | Nandini Jungle',
                'meta_description' => "Explore the Go Tyme Group's six-day retreat at Nandini Jungle by Hanging Gardens, featuring jungle meditation, canang making and shared moments in Bali.",
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($content['sections'] as $index => $section) {
                $sectionId = DB::table('blog_news_sections')->insertGetId([
                    'blog_news_id' => $blogId,
                    'section_key' => 'retreat_gallery_section',
                    'title' => $section['title'],
                    'subtitle' => $section['date'],
                    'description' => $section['description'],
                    'items' => json_encode(['caption' => $section['caption'], 'featured' => $section['featured'] ?? null], JSON_THROW_ON_ERROR),
                    'text_align' => 'left',
                    'background_color' => 'white',
                    'is_active' => true,
                    'sort_order' => $index + 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                foreach (array_keys($section['images']) as $order => $id) {
                    DB::table('blog_news_section_images')->insert([
                        'blog_news_section_id' => $sectionId,
                        'image' => '/images/go-tyme-images/dsc'.$id.'-go-tyme-group.webp',
                        'image_alt' => $section['images'][$id],
                        'is_active' => true,
                        'sort_order' => $order,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        });
    }

    public function down(): void
    {
        // Published editorial content is retained on rollback to avoid data loss.
    }
};
