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
        Schema::create('page_section_images', function (Blueprint $table) {
            $table->id();

            $table->foreignId('page_section_id')
                ->constrained('page_sections')
                ->cascadeOnDelete();

            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();

            $table->string('mobile_image')->nullable();
            $table->string('mobile_image_alt')->nullable();

            $table->string('caption')->nullable();

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['page_section_id', 'is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_section_images');
    }
};
