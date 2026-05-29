<?php

use App\Models\BlogNews;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_news_sections', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(BlogNews::class)
                ->constrained('blog_news')
                ->cascadeOnDelete();

            $table->string('section_key')->default('split_media_section');

            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('description')->nullable();

            $table->string('video_url')->nullable();
            $table->string('video_label')->nullable();

            $table->string('button_label')->nullable();
            $table->string('button_link_type')->default('manual');
            $table->string('button_url')->nullable();
            $table->string('button_route')->nullable();

            $table->string('text_align')->default('center');
            $table->string('background_color')->default('white');

            $table->json('items')->nullable();

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['blog_news_id', 'section_key']);
            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_news_sections');
    }
};
