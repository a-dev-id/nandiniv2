<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dining_settings', function (Blueprint $table): void {
            $table->string('philosophy_eyebrow')->nullable()->after('outside_guest_information');
            $table->text('philosophy_heading')->nullable()->after('philosophy_eyebrow');
            $table->text('philosophy_description')->nullable()->after('philosophy_heading');
            $table->string('philosophy_image')->nullable()->after('philosophy_description');
            $table->string('philosophy_image_alt')->nullable()->after('philosophy_image');
            $table->text('philosophy_accent_text')->nullable()->after('philosophy_image_alt');
        });
    }

    public function down(): void
    {
        Schema::table('dining_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'philosophy_eyebrow',
                'philosophy_heading',
                'philosophy_description',
                'philosophy_image',
                'philosophy_image_alt',
                'philosophy_accent_text',
            ]);
        });
    }
};
