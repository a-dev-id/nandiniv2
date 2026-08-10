<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_exchange_rates', function (Blueprint $table): void {
            $table->id();
            $table->string('base_currency', 3)->default('IDR');
            $table->string('quote_currency', 3);
            $table->decimal('base_units_per_quote', 18, 6);
            $table->boolean('is_active')->default(true);
            $table->timestamp('effective_at')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['base_currency', 'quote_currency'], 'affiliate_exchange_rate_currency_unique');
        });

        Schema::table('affiliate_payouts', function (Blueprint $table): void {
            $table->string('source_currency', 10)->nullable()->after('currency');
            $table->decimal('source_amount', 15, 2)->nullable()->after('source_currency');
            $table->decimal('exchange_rate_snapshot', 18, 6)->nullable()->after('source_amount');
        });
    }

    public function down(): void
    {
        Schema::table('affiliate_payouts', function (Blueprint $table): void {
            $table->dropColumn(['source_currency', 'source_amount', 'exchange_rate_snapshot']);
        });

        Schema::dropIfExists('affiliate_exchange_rates');
    }
};
