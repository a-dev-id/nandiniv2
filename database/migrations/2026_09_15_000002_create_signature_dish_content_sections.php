<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('signature_dish_sections', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('signature_dish_id')->constrained('signature_dishes')->cascadeOnDelete();
            $table->string('section_key')->default('split_media_section');
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('description')->nullable();
            $table->string('video_url')->nullable();
            $table->string('video_label')->nullable();
            $table->string('button_label')->nullable();
            $table->string('button_link_type')->default('manual');
            $table->string('button_url', 500)->nullable();
            $table->string('button_route')->nullable();
            $table->string('text_align')->default('center');
            $table->string('background_color')->default('white');
            $table->json('items')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['signature_dish_id', 'section_key'], 'signature_dish_sections_dish_key_index');
            $table->index(['is_active', 'sort_order'], 'signature_dish_sections_active_order_index');
        });

        Schema::create('signature_dish_section_images', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('signature_dish_section_id')->constrained('signature_dish_sections')->cascadeOnDelete();
            $table->string('image')->nullable();
            $table->string('image_file_name', 120)->nullable();
            $table->string('image_alt')->nullable();
            $table->string('mobile_image')->nullable();
            $table->string('mobile_image_file_name', 120)->nullable();
            $table->string('mobile_image_alt')->nullable();
            $table->string('caption')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(
                ['signature_dish_section_id', 'is_active', 'sort_order'],
                'signature_dish_section_images_active_order_index',
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signature_dish_section_images');
        Schema::dropIfExists('signature_dish_sections');
    }
};
