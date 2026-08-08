<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_click_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained()->cascadeOnDelete();
            $table->timestamp('clicked_at')->index();
            $table->date('click_date')->index();
            $table->char('country_code', 2)->nullable()->index();
            $table->string('country_name', 100)->nullable();
            $table->string('device_type', 20)->index();
            $table->string('referrer_domain')->nullable();
            $table->char('visitor_hash', 64);
            $table->boolean('is_unique')->default(false)->index();
            $table->boolean('is_bot')->default(false)->index();
            $table->string('bot_name', 50)->nullable();
            $table->timestamps();

            $table->index(['affiliate_id', 'click_date'], 'affiliate_clicks_affiliate_date_index');
            $table->index(['affiliate_id', 'is_bot', 'click_date'], 'affiliate_clicks_public_date_index');
        });

        Schema::create('affiliate_unique_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained()->cascadeOnDelete();
            $table->char('visitor_hash', 64);
            $table->date('click_date');
            $table->timestamps();

            $table->unique(['affiliate_id', 'visitor_hash', 'click_date'], 'affiliate_unique_daily');
            $table->index(['affiliate_id', 'click_date'], 'affiliate_unique_affiliate_date_index');
        });

        Schema::table('affiliate_program_settings', function (Blueprint $table) {
            $table->string('click_unique_window')->default('daily');
            $table->unsignedSmallInteger('click_event_retention_days')->default(395);
        });

        Log::info('Affiliate click tracking migration executed.');
    }

    public function down(): void
    {
        Schema::table('affiliate_program_settings', function (Blueprint $table) {
            $table->dropColumn(['click_unique_window', 'click_event_retention_days']);
        });

        Schema::dropIfExists('affiliate_unique_clicks');
        Schema::dropIfExists('affiliate_click_events');
    }
};
