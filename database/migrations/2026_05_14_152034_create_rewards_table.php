<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();

            $table->foreignId('reward_category_id')
                ->nullable()
                ->constrained('reward_categories')
                ->nullOnDelete();

            $table->string('title');
            $table->string('slug')->unique();

            $table->text('excerpt')->nullable();
            $table->longText('description')->nullable();

            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();

            $table->unsignedInteger('points_required')->default(0);
            $table->string('points_label')->nullable();

            $table->string('button_label')->default('Redeem');
            $table->string('button_url')->nullable();

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rewards');
    }
};
