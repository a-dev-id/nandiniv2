<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            if (! Schema::hasColumn('offers', 'hero_image_file_name')) {
                $table->string('hero_image_file_name')->nullable()->after('hero_image');
            }

            if (! Schema::hasColumn('offers', 'hero_mobile_image_file_name')) {
                $table->string('hero_mobile_image_file_name')->nullable()->after('hero_mobile_image');
            }

            if (! Schema::hasColumn('offers', 'card_image_file_name')) {
                $table->string('card_image_file_name')->nullable()->after('card_image');
            }
        });

        DB::table('offers')
            ->whereNull('hero_image_file_name')
            ->whereNotNull('hero_image')
            ->orderBy('id')
            ->chunkById(100, function ($offers): void {
                foreach ($offers as $offer) {
                    DB::table('offers')
                        ->where('id', $offer->id)
                        ->update([
                            'hero_image_file_name' => pathinfo((string) $offer->hero_image, PATHINFO_FILENAME),
                        ]);
                }
            });

        DB::table('offers')
            ->whereNull('hero_mobile_image_file_name')
            ->whereNotNull('hero_mobile_image')
            ->orderBy('id')
            ->chunkById(100, function ($offers): void {
                foreach ($offers as $offer) {
                    DB::table('offers')
                        ->where('id', $offer->id)
                        ->update([
                            'hero_mobile_image_file_name' => pathinfo((string) $offer->hero_mobile_image, PATHINFO_FILENAME),
                        ]);
                }
            });

        DB::table('offers')
            ->whereNull('card_image_file_name')
            ->whereNotNull('card_image')
            ->orderBy('id')
            ->chunkById(100, function ($offers): void {
                foreach ($offers as $offer) {
                    DB::table('offers')
                        ->where('id', $offer->id)
                        ->update([
                            'card_image_file_name' => pathinfo((string) $offer->card_image, PATHINFO_FILENAME),
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            foreach ([
                'hero_image_file_name',
                'hero_mobile_image_file_name',
                'card_image_file_name',
            ] as $column) {
                if (Schema::hasColumn('offers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
