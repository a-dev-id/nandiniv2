<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_news', function (Blueprint $table) {
            $table->id();

            $table->string('type')->default('blog'); // blog / news

            $table->string('title');
            $table->string('slug')->unique();

            $table->string('subtitle')->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('description')->nullable();

            $table->string('hero_image')->nullable();
            $table->string('hero_image_alt')->nullable();

            $table->string('hero_mobile_image')->nullable();
            $table->string('hero_mobile_image_alt')->nullable();

            $table->string('card_image')->nullable();
            $table->string('card_image_alt')->nullable();

            $table->string('author_name')->nullable();
            $table->date('published_at')->nullable();

            $table->string('button_label')->nullable();
            $table->string('button_url')->nullable();

            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['type', 'is_active', 'published_at']);
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_news');
    }
};
