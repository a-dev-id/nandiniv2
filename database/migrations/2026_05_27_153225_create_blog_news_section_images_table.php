<?php

use App\Models\BlogNewsSection;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_news_section_images', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(BlogNewsSection::class)
                ->constrained('blog_news_sections')
                ->cascadeOnDelete();

            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();

            $table->string('mobile_image')->nullable();
            $table->string('mobile_image_alt')->nullable();

            $table->string('caption')->nullable();

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(
                ['blog_news_section_id', 'is_active', 'sort_order'],
                'bn_section_images_active_order_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_news_section_images');
    }
};
