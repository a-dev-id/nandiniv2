<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['blog_news', 'blog_news_sections', 'page_sections'] as $table) {
            if (Schema::hasColumn($table, 'button_url')) {
                foreach (['/the-royal-suites-and-jungle-villas', 'https://nandinibali.com/the-royal-suites-and-jungle-villas'] as $oldUrl) {
                    DB::table($table)->where('button_url', $oldUrl)->update(['button_url' => '/jungle-villas']);
                }
            }

            if (Schema::hasColumn($table, 'button_route')) {
                DB::table($table)->where('button_route', 'accommodations.index')->update(['button_route' => 'accommodations.villas']);
            }
        }
    }

    public function down(): void
    {
        // Existing jungle-villa links cannot be distinguished from migrated links.
    }
};
