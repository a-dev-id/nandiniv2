<?php

use App\Enums\AffiliatePayoutStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Recover only from an empty, unrecorded partial creation on legacy MySQL hosts.
        if (Schema::hasTable('affiliate_payouts') || Schema::hasTable('affiliate_payout_number_sequences')) {
            $hasPayoutData = Schema::hasTable('affiliate_payouts') && DB::table('affiliate_payouts')->exists();
            $hasSequenceData = Schema::hasTable('affiliate_payout_number_sequences') && DB::table('affiliate_payout_number_sequences')->exists();

            if ($hasPayoutData || $hasSequenceData) {
                throw new RuntimeException('The pending Affiliate payout migration found existing payout data and will not overwrite it.');
            }

            Schema::dropIfExists('affiliate_payout_items');
            Schema::dropIfExists('affiliate_payouts');
            Schema::dropIfExists('affiliate_payout_number_sequences');
        }

        Schema::create('affiliate_payout_number_sequences', function (Blueprint $table): void {
            $table->unsignedSmallInteger('sequence_year')->primary();
            $table->unsignedInteger('next_number')->default(1);
            $table->timestamps();
        });

        Schema::create('affiliate_payouts', function (Blueprint $table): void {
            $table->id();
            $table->string('payout_number')->unique();
            $table->foreignId('affiliate_id')->constrained()->restrictOnDelete();
            $table->string('currency', 10);
            $table->decimal('gross_commission_amount', 15, 2);
            $table->decimal('adjustment_amount', 15, 2)->default(0);
            $table->text('adjustment_reason')->nullable();
            $table->decimal('net_payout_amount', 15, 2);
            $table->string('payment_method_snapshot', 40);
            $table->string('payment_details_masked_snapshot');
            $table->string('status', 40)->default(AffiliatePayoutStatus::Draft->value);
            $table->timestamp('due_at')->nullable()->index();
            $table->timestamp('prepared_at')->nullable();
            $table->foreignId('prepared_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processing_at')->nullable();
            $table->foreignId('processing_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('paid_at')->nullable()->index();
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('payment_reference')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
            $table->index(['affiliate_id', 'status']);
            $table->index(['currency', 'status']);
            $table->index(['payment_method_snapshot', 'status'], 'affiliate_payout_method_status_index');
        });

        Schema::create('affiliate_payout_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('affiliate_payout_id')->constrained()->cascadeOnDelete();
            $table->foreignId('affiliate_commission_item_id')->unique()->constrained()->restrictOnDelete();
            $table->decimal('amount', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_payout_items');
        Schema::dropIfExists('affiliate_payouts');
        Schema::dropIfExists('affiliate_payout_number_sequences');
    }
};
