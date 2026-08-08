<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('description')->nullable();
            $table->string('image');
            $table->string('image_name')->nullable();
            $table->string('alt_text');
            $table->string('status', 30)->default('draft')->index();
            $table->dateTime('event_start_at')->index();
            $table->dateTime('event_end_at')->index();
            $table->string('event_type', 30)->default('regular')->index();
            $table->timestamps();

            $table->index(['status', 'event_start_at']);
            $table->index(['status', 'event_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
