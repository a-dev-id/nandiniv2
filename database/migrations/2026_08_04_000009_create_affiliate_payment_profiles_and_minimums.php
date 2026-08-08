<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_payment_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('affiliate_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('payment_method');
            $table->text('account_holder_name');
            $table->text('wise_email')->nullable();
            $table->text('bank_name')->nullable();
            $table->text('bank_account_name')->nullable();
            $table->text('bank_account_number')->nullable();
            $table->text('bank_country')->nullable();
            $table->text('swift_bic')->nullable();
            $table->boolean('is_complete')->default(false)->index();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('affiliate_payout_minimums', function (Blueprint $table): void {
            $table->id();
            $table->string('currency', 10)->unique();
            $table->decimal('minimum_amount', 15, 2);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_payout_minimums');
        Schema::dropIfExists('affiliate_payment_profiles');
    }
};
