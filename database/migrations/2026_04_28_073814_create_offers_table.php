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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('slug', 191)->unique();

            $table->string('subtitle')->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('description')->nullable();

            $table->string('hero_image')->nullable();
            $table->string('hero_image_alt')->nullable();

            $table->string('hero_mobile_image')->nullable();
            $table->string('hero_mobile_image_alt')->nullable();

            $table->string('card_image')->nullable();
            $table->string('card_image_alt')->nullable();

            $table->date('valid_start_date')->nullable();
            $table->date('valid_end_date')->nullable();

            $table->string('button_label')->nullable();
            $table->string('button_url')->nullable();

            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);

            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['is_active', 'is_featured', 'sort_order']);
            $table->index(['valid_start_date', 'valid_end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
