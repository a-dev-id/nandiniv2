<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vouchers', function (Blueprint $table): void {
            $table->foreignId('experience_id')->nullable()->after('voucher_category_id')->unique()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('discount_percentage')->default(0)->after('selling_price');
        });
    }

    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('experience_id');
            $table->dropColumn('discount_percentage');
        });
    }
};
