<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_program_settings', function (Blueprint $table) {
            $table->id();
            $table->string('program_name')->default('Nandini Partner Circle');
            $table->decimal('affiliate_commission_percentage', 5, 2)->default(10.00);
            $table->decimal('guest_discount_percentage', 5, 2)->default(3.00);
            $table->string('payment_cycle')->default('monthly');
            $table->string('preferred_payment_method')->default('wise');
            $table->string('alternative_payment_method')->default('bank_transfer');
            $table->decimal('minimum_payout', 14, 2)->default(500000.00);
            $table->char('currency', 3)->default('IDR');
            $table->unsignedSmallInteger('payment_release_days')->default(30);
            $table->boolean('preferred_payment_method_requires_finance_confirmation')->default(true);
            $table->boolean('minimum_payout_requires_finance_confirmation')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_program_settings');
    }
};
