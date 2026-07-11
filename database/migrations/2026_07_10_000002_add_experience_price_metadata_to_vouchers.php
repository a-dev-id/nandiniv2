<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vouchers', function (Blueprint $table): void {
            if (! Schema::hasColumn('vouchers', 'price_type')) {
                $table->string('price_type')->nullable()->after('currency');
            }

            if (! Schema::hasColumn('vouchers', 'unit_type')) {
                $table->string('unit_type')->nullable()->after('price_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table): void {
            if (Schema::hasColumn('vouchers', 'unit_type')) {
                $table->dropColumn('unit_type');
            }

            if (Schema::hasColumn('vouchers', 'price_type')) {
                $table->dropColumn('price_type');
            }
        });
    }
};
