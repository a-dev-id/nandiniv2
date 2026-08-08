<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_marketing_assets', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('asset_type', 40)->index();
            $table->string('file_path')->nullable();
            $table->string('external_url', 2048)->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_extension', 10)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->boolean('is_active')->default(false)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->timestamp('available_from')->nullable()->index();
            $table->timestamp('available_until')->nullable()->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('affiliate_operational_states', function (Blueprint $table): void {
            $table->string('key')->primary();
            $table->string('status', 40)->nullable();
            $table->string('summary')->nullable();
            $table->timestamp('last_attempted_at')->nullable();
            $table->timestamp('last_successful_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::table('affiliate_program_settings', function (Blueprint $table): void {
            $table->unsignedSmallInteger('review_time_expectation_hours')->default(48)->after('review_time_message');
            $table->boolean('registration_confirmation_enabled')->default(true);
            $table->boolean('internal_invitation_enabled')->default(true);
            $table->boolean('approval_notification_enabled')->default(true);
            $table->boolean('rejection_notification_enabled')->default(true);
            $table->boolean('payment_details_needed_notification_enabled')->default(true);
            $table->boolean('payout_paid_notification_enabled')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('affiliate_program_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'review_time_expectation_hours',
                'registration_confirmation_enabled',
                'internal_invitation_enabled',
                'approval_notification_enabled',
                'rejection_notification_enabled',
                'payment_details_needed_notification_enabled',
                'payout_paid_notification_enabled',
            ]);
        });

        Schema::dropIfExists('affiliate_operational_states');
        Schema::dropIfExists('affiliate_marketing_assets');
    }
};
