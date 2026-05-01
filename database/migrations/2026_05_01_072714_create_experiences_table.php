<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experiences', function (Blueprint $table) {
            $table->id();

            $table->foreignId('experience_category_id')
                ->nullable()
                ->constrained('experience_categories')
                ->nullOnDelete();

            $table->string('title');
            $table->string('slug')->unique();

            $table->string('subtitle')->nullable();
            $table->text('excerpt')->nullable();

            $table->longText('description')->nullable();

            // Rich text inclusion content
            $table->longText('inclusions')->nullable();

            $table->string('duration')->nullable();
            $table->string('location')->nullable();

            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();

            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            $table->timestamps();

            $table->index(['experience_category_id', 'is_active', 'sort_order']);
            $table->index(['is_featured', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experiences');
    }
};
