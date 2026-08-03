<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('vouchers', 'price_options')) {
            return;
        }

        Schema::table('vouchers', function (Blueprint $table): void {
            $table->json('price_options')->nullable()->after('selling_price');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('vouchers', 'price_options')) {
            return;
        }

        Schema::table('vouchers', function (Blueprint $table): void {
            $table->dropColumn('price_options');
        });
    }
};
