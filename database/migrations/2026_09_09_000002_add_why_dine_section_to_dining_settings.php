<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dining_settings', function (Blueprint $table): void {
            $table->string('why_dine_eyebrow')->nullable()->after('philosophy_accent_text');
            $table->text('why_dine_heading')->nullable()->after('why_dine_eyebrow');
            $table->json('why_dine_items')->nullable()->after('why_dine_heading');
        });
    }

    public function down(): void
    {
        Schema::table('dining_settings', function (Blueprint $table): void {
            $table->dropColumn(['why_dine_eyebrow', 'why_dine_heading', 'why_dine_items']);
        });
    }
};
