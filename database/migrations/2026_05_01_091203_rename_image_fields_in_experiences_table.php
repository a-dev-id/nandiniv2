<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            if (Schema::hasColumn('experiences', 'image') && ! Schema::hasColumn('experiences', 'card_image')) {
                $table->renameColumn('image', 'card_image');
            }

            if (Schema::hasColumn('experiences', 'image_alt') && ! Schema::hasColumn('experiences', 'card_image_alt')) {
                $table->renameColumn('image_alt', 'card_image_alt');
            }
        });
    }

    public function down(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            if (Schema::hasColumn('experiences', 'card_image') && ! Schema::hasColumn('experiences', 'image')) {
                $table->renameColumn('card_image', 'image');
            }

            if (Schema::hasColumn('experiences', 'card_image_alt') && ! Schema::hasColumn('experiences', 'image_alt')) {
                $table->renameColumn('card_image_alt', 'image_alt');
            }
        });
    }
};
