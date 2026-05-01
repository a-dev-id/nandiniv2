<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experience_prices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('experience_id')
                ->constrained('experiences')
                ->cascadeOnDelete();

            $table->string('label')->nullable();

            $table->decimal('price', 15, 2)->default(0);
            $table->string('currency', 10)->default('IDR');

            // plus_plus, net, inclusive
            $table->string('price_type')->default('plus_plus');

            // per_person, per_couple, per_car, per_booking
            $table->string('unit_type')->default('per_person');

            $table->unsignedInteger('min_qty')->nullable();
            $table->unsignedInteger('max_qty')->nullable();

            $table->text('notes')->nullable();

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['experience_id', 'is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experience_prices');
    }
};
