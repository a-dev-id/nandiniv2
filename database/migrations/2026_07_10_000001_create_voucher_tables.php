<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voucher_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->integer('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('vouchers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('voucher_category_id')->nullable()->constrained('voucher_categories')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('sku')->nullable()->unique();
            $table->text('excerpt')->nullable();
            $table->longText('description')->nullable();
            $table->longText('inclusions')->nullable();
            $table->longText('terms_conditions')->nullable();
            $table->string('image')->nullable();
            $table->string('card_image')->nullable();
            $table->string('image_alt')->nullable();
            $table->string('voucher_type')->default('custom')->index();
            $table->unsignedBigInteger('face_value')->nullable();
            $table->unsignedBigInteger('selling_price');
            $table->string('currency', 3)->default('IDR');
            $table->string('price_type')->nullable();
            $table->string('unit_type')->nullable();
            $table->string('validity_type')->default('days_after_issue');
            $table->unsignedInteger('validity_days')->nullable();
            $table->date('fixed_valid_from')->nullable();
            $table->date('fixed_valid_until')->nullable();
            $table->unsignedInteger('minimum_quantity')->default(1);
            $table->unsignedInteger('maximum_quantity')->nullable();
            $table->unsignedInteger('purchase_limit_per_order')->nullable();
            $table->boolean('allow_partial_redemption')->default(false);
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->integer('sort_order')->default(0)->index();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('voucher_orders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->string('order_number')->unique();
            $table->string('access_token_hash')->nullable()->index();
            $table->string('purchaser_first_name');
            $table->string('purchaser_last_name');
            $table->string('purchaser_email')->index();
            $table->string('purchaser_phone')->nullable();
            $table->string('billing_country_code', 2);
            $table->string('currency', 3)->default('IDR');
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('discount_amount')->default(0);
            $table->unsignedBigInteger('total_amount');
            $table->string('payment_gateway')->default('flywire');
            $table->string('payment_status')->default('pending')->index();
            $table->string('order_status')->default('pending_payment')->index();
            $table->string('flywire_checkout_session_id')->nullable()->index();
            $table->string('flywire_payment_id')->nullable()->index();
            $table->string('flywire_payment_reference')->nullable()->index();
            $table->string('flywire_status')->nullable()->index();
            $table->text('flywire_hosted_form_url')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('voucher_order_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('voucher_order_id')->constrained('voucher_orders')->cascadeOnDelete();
            $table->foreignId('voucher_id')->nullable()->constrained('vouchers')->nullOnDelete();
            $table->string('voucher_title');
            $table->string('voucher_sku')->nullable();
            $table->string('voucher_type');
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('unit_price');
            $table->unsignedBigInteger('line_total');
            $table->string('currency', 3)->default('IDR');
            $table->string('recipient_name');
            $table->string('recipient_email');
            $table->text('personal_message')->nullable();
            $table->string('delivery_method')->default('email');
            $table->timestamp('scheduled_delivery_at')->nullable();
            $table->json('voucher_snapshot');
            $table->timestamps();
        });

        Schema::create('issued_vouchers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('voucher_order_item_id')->constrained('voucher_order_items')->cascadeOnDelete();
            $table->foreignId('voucher_id')->nullable()->constrained('vouchers')->nullOnDelete();
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->string('voucher_code')->unique();
            $table->string('verification_token_hash')->unique();
            $table->string('recipient_name');
            $table->string('recipient_email')->index();
            $table->string('title');
            $table->longText('description_snapshot')->nullable();
            $table->longText('terms_snapshot')->nullable();
            $table->unsignedBigInteger('original_value')->nullable();
            $table->unsignedBigInteger('remaining_value')->nullable();
            $table->string('currency', 3)->default('IDR');
            $table->timestamp('issued_at')->nullable();
            $table->date('valid_from')->nullable();
            $table->date('expires_at')->nullable();
            $table->string('status')->default('pending')->index();
            $table->string('pdf_path')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('redeemed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('voucher_redemptions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('issued_voucher_id')->constrained('issued_vouchers')->cascadeOnDelete();
            $table->foreignId('redeemed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('redemption_location')->nullable();
            $table->string('department')->nullable();
            $table->string('reference_number')->nullable();
            $table->unsignedBigInteger('amount')->nullable();
            $table->unsignedBigInteger('balance_before')->nullable();
            $table->unsignedBigInteger('balance_after')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('redeemed_at');
            $table->timestamps();
        });

        Schema::create('voucher_payment_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('voucher_order_id')->nullable()->constrained('voucher_orders')->nullOnDelete();
            $table->string('gateway')->default('flywire')->index();
            $table->string('gateway_payment_id')->nullable()->index();
            $table->string('event_fingerprint')->unique();
            $table->string('event_type')->nullable();
            $table->string('gateway_status')->nullable()->index();
            $table->boolean('signature_valid')->default(false);
            $table->json('payload');
            $table->timestamp('processed_at')->nullable();
            $table->text('processing_error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voucher_payment_events');
        Schema::dropIfExists('voucher_redemptions');
        Schema::dropIfExists('issued_vouchers');
        Schema::dropIfExists('voucher_order_items');
        Schema::dropIfExists('voucher_orders');
        Schema::dropIfExists('vouchers');
        Schema::dropIfExists('voucher_categories');
    }
};
