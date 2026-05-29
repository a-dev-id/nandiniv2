<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('awards', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('slug')->unique();

            $table->string('subtitle')->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('description')->nullable();

            $table->string('award_name')->nullable();
            $table->string('awarded_by')->nullable();
            $table->string('award_year')->nullable();
            $table->date('award_date')->nullable();

            $table->string('hero_image')->nullable();
            $table->string('hero_image_alt')->nullable();

            $table->string('hero_mobile_image')->nullable();
            $table->string('hero_mobile_image_alt')->nullable();

            $table->string('card_image')->nullable();
            $table->string('card_image_alt')->nullable();

            $table->string('button_label')->nullable();
            $table->string('button_url')->nullable();

            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['is_active', 'award_year']);
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('awards');
    }
};
