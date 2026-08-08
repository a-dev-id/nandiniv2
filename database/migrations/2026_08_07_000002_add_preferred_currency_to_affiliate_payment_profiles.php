<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('affiliate_payment_profiles', function (Blueprint $table): void {
            $table->string('preferred_currency', 3)->default('IDR')->after('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('affiliate_payment_profiles', function (Blueprint $table): void {
            $table->dropColumn('preferred_currency');
        });
    }
};
