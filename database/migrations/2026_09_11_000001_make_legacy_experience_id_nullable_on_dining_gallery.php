<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            Schema::hasTable('dining_experience_gallery')
            && Schema::hasColumn('dining_experience_gallery', 'experience_id')
        ) {
            Schema::table('dining_experience_gallery', function (Blueprint $table): void {
                $table->unsignedBigInteger('experience_id')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        // New Dining Experience gallery records do not use the legacy field.
    }
};
