<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accommodation_images', function (Blueprint $table) {
            $table->id();

            $table->foreignId('accommodation_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('image');
            $table->string('image_alt')->nullable();

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['accommodation_id', 'is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accommodation_images');
    }
};
