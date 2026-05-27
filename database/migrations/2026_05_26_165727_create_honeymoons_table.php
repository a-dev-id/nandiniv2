<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('honeymoons', function (Blueprint $table) {
            $table->id();

            $table->string('title', 191);
            $table->string('slug', 191)->unique();

            $table->string('subtitle', 191)->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('description')->nullable();

            $table->string('hero_image', 191)->nullable();
            $table->string('hero_image_alt', 191)->nullable();
            $table->string('hero_mobile_image', 191)->nullable();
            $table->string('hero_mobile_image_alt', 191)->nullable();

            $table->string('card_image', 191)->nullable();
            $table->string('card_image_alt', 191)->nullable();

            $table->date('valid_start_date')->nullable()->index();
            $table->date('valid_end_date')->nullable()->index();

            $table->date('booking_checkin_date')->nullable();
            $table->unsignedTinyInteger('booking_nights')->nullable();
            $table->unsignedTinyInteger('booking_rooms')->nullable();
            $table->unsignedTinyInteger('booking_adults')->nullable();

            $table->string('booking_rate_code', 191)->nullable();
            $table->string('booking_bkcode', 191)->nullable();
            $table->text('booking_url_override')->nullable();

            $table->string('button_label', 191)->nullable();
            $table->string('button_url', 191)->nullable();

            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();

            $table->string('meta_title', 191)->nullable();
            $table->text('meta_description')->nullable();

            $table->unsignedInteger('sort_order')->default(0)->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('honeymoons');
    }
};
