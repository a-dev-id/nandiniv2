<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table): void {
            $table->boolean('include_in_sitemap')->default(true)->after('is_active');
            $table->index(['site', 'is_active', 'include_in_sitemap'], 'pages_sitemap_visibility_index');
        });

        DB::table('pages')->update(['include_in_sitemap' => false]);

        DB::table('pages')
            ->where('site', 'main')
            ->whereIn('slug', [
                'ubud-jungle-resort-in-bali',
                'ubud-wellness-retreat',
                'jungle-spa-ubud',
            ])
            ->update(['include_in_sitemap' => true]);
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table): void {
            $table->dropIndex('pages_sitemap_visibility_index');
            $table->dropColumn('include_in_sitemap');
        });
    }
};
