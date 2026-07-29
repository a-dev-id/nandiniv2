<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guest_reviews', function (Blueprint $table): void {
            $table->boolean('is_featured')->default(false)->index()->after('is_active');
        });

        DB::table('guest_reviews')
            ->where('is_active', true)
            ->update(['is_featured' => true]);
    }

    public function down(): void
    {
        Schema::table('guest_reviews', function (Blueprint $table): void {
            $table->dropColumn('is_featured');
        });
    }
};
