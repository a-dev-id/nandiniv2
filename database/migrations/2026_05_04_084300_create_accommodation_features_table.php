<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accommodation_features', function (Blueprint $table) {
            $table->id();

            $table->foreignId('accommodation_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('label');
            $table->string('icon_key')->nullable();

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(
                ['accommodation_id', 'is_active', 'sort_order'],
                'acc_feat_active_sort_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accommodation_features');
    }
};
