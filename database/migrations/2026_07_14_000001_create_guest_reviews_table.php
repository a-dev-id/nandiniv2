<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guest_reviews', function (Blueprint $table): void {
            $table->id();
            $table->string('reviewer_name');
            $table->text('review_text');
            $table->unsignedTinyInteger('rating')->default(5);
            $table->date('reviewed_at')->nullable();
            $table->string('source')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_reviews');
    }
};
