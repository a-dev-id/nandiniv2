<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('blog_news') || ! Schema::hasTable('blog_news_sections')) {
            return;
        }

        $blogId = DB::table('blog_news')
            ->where('slug', 'ayung-river-ubud-guide')
            ->value('id');

        if (! $blogId) {
            return;
        }

        $section = DB::table('blog_news_sections')
            ->where('blog_news_id', $blogId)
            ->where('title', 'QUICK FACTS ABOUT THE AYUNG RIVER')
            ->first(['id', 'items']);

        if (! $section) {
            return;
        }

        $items = json_decode((string) $section->items, true);

        if (! is_array($items)) {
            $items = [];
        }

        $items['source'] = [
            'label' => 'Indonesia Travel, Ministry of Tourism of the Republic of Indonesia',
            'url' => 'https://www.indonesia.travel/gb/en/travel-ideas/marine/5-reasons-travelers-love-white-water-rafting-at-ayung-river-in-ubud',
        ];

        DB::table('blog_news_sections')->where('id', $section->id)->update([
            'items' => json_encode($items, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        // Editorial source attribution is intentionally retained on rollback.
    }
};
