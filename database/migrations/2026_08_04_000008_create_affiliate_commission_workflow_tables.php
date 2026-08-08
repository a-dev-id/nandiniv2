<?php

use App\Enums\AffiliateCommissionItemStatus;
use App\Enums\AffiliateCommissionPeriodStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_commission_periods', function (Blueprint $table): void {
            $table->id();
            $table->unsignedSmallInteger('period_year');
            $table->unsignedTinyInteger('period_month');
            $table->date('period_start_date');
            $table->date('period_end_date');
            $table->string('status')->default(AffiliateCommissionPeriodStatus::Draft->value)->index();
            $table->timestamp('prepared_at')->nullable();
            $table->foreignId('prepared_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('finalized_at')->nullable();
            $table->foreignId('finalized_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['period_year', 'period_month'], 'affiliate_commission_period_unique');
        });

        Schema::create('affiliate_commission_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('commission_period_id')->constrained('affiliate_commission_periods')->cascadeOnDelete();
            $table->foreignId('affiliate_booking_id')->unique()->constrained()->restrictOnDelete();
            $table->foreignId('affiliate_id')->constrained()->restrictOnDelete();
            $table->string('currency', 10);
            $table->decimal('room_revenue_snapshot', 15, 2);
            $table->decimal('commission_rate_snapshot', 5, 2);
            $table->decimal('original_commission_amount', 15, 2);
            $table->decimal('approved_commission_amount', 15, 2)->nullable();
            $table->string('status')->default(AffiliateCommissionItemStatus::PendingReview->value);
            $table->text('hold_reason')->nullable();
            $table->text('exclusion_reason')->nullable();
            $table->text('adjustment_reason')->nullable();
            $table->boolean('source_changed_after_review')->default(false)->index();
            $table->text('discrepancy_warning')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->index();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index('commission_period_id');
            $table->index('affiliate_id');
            $table->index('status');
            $table->index('currency');
            $table->index(['affiliate_id', 'status'], 'affiliate_commission_item_affiliate_status_index');
            $table->index(['commission_period_id', 'status'], 'affiliate_commission_item_period_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_commission_items');
        Schema::dropIfExists('affiliate_commission_periods');
    }
};
