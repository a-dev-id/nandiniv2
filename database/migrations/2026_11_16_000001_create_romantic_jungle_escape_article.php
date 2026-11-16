<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $slug = 'where-to-stay-in-bali-for-a-romantic-jungle-escape-2026';

        // Preserve existing editorial changes if the article has already been imported.
        if (DB::table('blog_news')->where('slug', $slug)->exists()) {
            return;
        }

        $markdown = file_get_contents(database_path('content/romantic-jungle-escape-2026.md'));
        // The template already renders the article title and hero image.
        $markdown = preg_replace('/\A# [^\r\n]+\s+!\[Jungle View Villa\]\([^\r\n]+\)\s+/u', '', $markdown);
        $html = Str::markdown($markdown, ['html_input' => 'strip', 'allow_unsafe_links' => false]);
        $html = str_replace('<table>', '<div class="blog-detail-table" role="region" aria-label="Resort information" tabindex="0"><table>', $html);
        $html = str_replace('</table>', '</table></div>', $html);
        $html = str_replace('<img ', '<img loading="lazy" decoding="async" ', $html);
        $image = 'https://nandinibali.com/storage/images/gallery/villa%2081%20Jungle%20View%20Villa.jpg';
        $now = now();

        DB::table('blog_news')->insert([
            'type' => 'blog',
            'slug' => $slug,
            'title' => 'Where to Stay in Bali for a Romantic Jungle Escape (2026)',
            'subtitle' => 'Private Rainforest Villas and Royal Suites for Couples in Payangan, Greater Ubud',
            'excerpt' => 'Plan a romantic escape to Nandini Jungle by Hanging Gardens, with private rainforest villas, couples wellness, riverfront dining and the quiet beauty of Payangan.',
            'description' => $html,
            'hero_image' => $image,
            'hero_image_alt' => 'Jungle View Villa surrounded by rainforest at Nandini Jungle by Hanging Gardens',
            'card_image' => $image,
            'card_image_alt' => 'Jungle View Villa at Nandini Jungle by Hanging Gardens in Payangan, Bali',
            'author_name' => 'Nandini Jungle by Hanging Gardens',
            'published_at' => $now->toDateString(),
            'button_label' => 'Explore Villas and Suites',
            'button_url' => '/jungle-villas',
            'meta_title' => 'Romantic Jungle Escape in Bali (2026) | Nandini',
            'meta_description' => 'Discover a romantic jungle escape in Payangan, Bali: private villas, Royal Suites, couples spa rituals and riverfront dining at Nandini Jungle.',
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function down(): void
    {
        // Retain editorial content on rollback, matching other article imports.
    }
};
