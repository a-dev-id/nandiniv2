<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('page_section_images', function (Blueprint $table) {
            $table->string('image_file_name')->nullable()->after('image');
            $table->string('mobile_image_file_name')->nullable()->after('mobile_image');
        });

        DB::table('page_section_images')
            ->whereNotNull('image')
            ->whereNull('image_file_name')
            ->get(['id', 'image'])
            ->each(function (object $image): void {
                DB::table('page_section_images')
                    ->where('id', $image->id)
                    ->update([
                        'image_file_name' => pathinfo((string) $image->image, PATHINFO_FILENAME),
                    ]);
            });

        DB::table('page_section_images')
            ->whereNotNull('mobile_image')
            ->whereNull('mobile_image_file_name')
            ->get(['id', 'mobile_image'])
            ->each(function (object $image): void {
                DB::table('page_section_images')
                    ->where('id', $image->id)
                    ->update([
                        'mobile_image_file_name' => pathinfo((string) $image->mobile_image, PATHINFO_FILENAME),
                    ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('page_section_images', function (Blueprint $table) {
            $table->dropColumn([
                'image_file_name',
                'mobile_image_file_name',
            ]);
        });
    }
};
