<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            if (! Schema::hasColumn('experiences', 'image')) {
                $table->string('image')->nullable()->after('location');
            }

            if (! Schema::hasColumn('experiences', 'image_alt')) {
                $table->string('image_alt')->nullable()->after('image');
            }

            if (! Schema::hasColumn('experiences', 'card_image')) {
                $table->string('card_image')->nullable()->after('image_alt');
            }

            if (! Schema::hasColumn('experiences', 'card_image_alt')) {
                $table->string('card_image_alt')->nullable()->after('card_image');
            }
        });

        // If image was renamed to card_image earlier, copy it back to image too.
        if (
            Schema::hasColumn('experiences', 'image') &&
            Schema::hasColumn('experiences', 'card_image')
        ) {
            DB::statement("
                UPDATE experiences
                SET image = card_image
                WHERE (image IS NULL OR image = '')
                AND card_image IS NOT NULL
                AND card_image != ''
            ");

            DB::statement("
                UPDATE experiences
                SET card_image = image
                WHERE (card_image IS NULL OR card_image = '')
                AND image IS NOT NULL
                AND image != ''
            ");
        }

        if (
            Schema::hasColumn('experiences', 'image_alt') &&
            Schema::hasColumn('experiences', 'card_image_alt')
        ) {
            DB::statement("
                UPDATE experiences
                SET image_alt = card_image_alt
                WHERE (image_alt IS NULL OR image_alt = '')
                AND card_image_alt IS NOT NULL
                AND card_image_alt != ''
            ");

            DB::statement("
                UPDATE experiences
                SET card_image_alt = image_alt
                WHERE (card_image_alt IS NULL OR card_image_alt = '')
                AND image_alt IS NOT NULL
                AND image_alt != ''
            ");
        }
    }

    public function down(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            if (Schema::hasColumn('experiences', 'card_image_alt')) {
                $table->dropColumn('card_image_alt');
            }

            if (Schema::hasColumn('experiences', 'card_image')) {
                $table->dropColumn('card_image');
            }
        });
    }
};
