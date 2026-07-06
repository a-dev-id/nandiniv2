<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blog_news', function (Blueprint $table) {
            if (! Schema::hasColumn('blog_news', 'hero_image_file_name')) {
                $table->string('hero_image_file_name')->nullable()->after('hero_image');
            }

            if (! Schema::hasColumn('blog_news', 'hero_mobile_image_file_name')) {
                $table->string('hero_mobile_image_file_name')->nullable()->after('hero_mobile_image');
            }

            if (! Schema::hasColumn('blog_news', 'card_image_file_name')) {
                $table->string('card_image_file_name')->nullable()->after('card_image');
            }
        });

        Schema::table('blog_news_section_images', function (Blueprint $table) {
            if (! Schema::hasColumn('blog_news_section_images', 'image_file_name')) {
                $table->string('image_file_name')->nullable()->after('image');
            }

            if (! Schema::hasColumn('blog_news_section_images', 'mobile_image_file_name')) {
                $table->string('mobile_image_file_name')->nullable()->after('mobile_image');
            }
        });

        DB::table('blog_news')
            ->whereNull('hero_image_file_name')
            ->whereNotNull('hero_image')
            ->orderBy('id')
            ->chunkById(100, function ($blogs): void {
                foreach ($blogs as $blog) {
                    DB::table('blog_news')
                        ->where('id', $blog->id)
                        ->update([
                            'hero_image_file_name' => pathinfo((string) $blog->hero_image, PATHINFO_FILENAME),
                        ]);
                }
            });

        DB::table('blog_news')
            ->whereNull('hero_mobile_image_file_name')
            ->whereNotNull('hero_mobile_image')
            ->orderBy('id')
            ->chunkById(100, function ($blogs): void {
                foreach ($blogs as $blog) {
                    DB::table('blog_news')
                        ->where('id', $blog->id)
                        ->update([
                            'hero_mobile_image_file_name' => pathinfo((string) $blog->hero_mobile_image, PATHINFO_FILENAME),
                        ]);
                }
            });

        DB::table('blog_news')
            ->whereNull('card_image_file_name')
            ->whereNotNull('card_image')
            ->orderBy('id')
            ->chunkById(100, function ($blogs): void {
                foreach ($blogs as $blog) {
                    DB::table('blog_news')
                        ->where('id', $blog->id)
                        ->update([
                            'card_image_file_name' => pathinfo((string) $blog->card_image, PATHINFO_FILENAME),
                        ]);
                }
            });

        DB::table('blog_news_section_images')
            ->whereNull('image_file_name')
            ->whereNotNull('image')
            ->orderBy('id')
            ->chunkById(100, function ($images): void {
                foreach ($images as $image) {
                    DB::table('blog_news_section_images')
                        ->where('id', $image->id)
                        ->update([
                            'image_file_name' => pathinfo((string) $image->image, PATHINFO_FILENAME),
                        ]);
                }
            });

        DB::table('blog_news_section_images')
            ->whereNull('mobile_image_file_name')
            ->whereNotNull('mobile_image')
            ->orderBy('id')
            ->chunkById(100, function ($images): void {
                foreach ($images as $image) {
                    DB::table('blog_news_section_images')
                        ->where('id', $image->id)
                        ->update([
                            'mobile_image_file_name' => pathinfo((string) $image->mobile_image, PATHINFO_FILENAME),
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('blog_news', function (Blueprint $table) {
            foreach ([
                'hero_image_file_name',
                'hero_mobile_image_file_name',
                'card_image_file_name',
            ] as $column) {
                if (Schema::hasColumn('blog_news', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('blog_news_section_images', function (Blueprint $table) {
            foreach ([
                'image_file_name',
                'mobile_image_file_name',
            ] as $column) {
                if (Schema::hasColumn('blog_news_section_images', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
