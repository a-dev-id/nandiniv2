<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('slug')->unique();

            $table->string('category')->nullable();

            $table->text('excerpt')->nullable();
            $table->longText('description')->nullable();

            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();

            $table->string('mobile_image')->nullable();
            $table->string('mobile_image_alt')->nullable();

            $table->string('button_label')->nullable();
            $table->string('button_url')->nullable();

            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['category', 'is_active']);
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('galleries');
    }
};
