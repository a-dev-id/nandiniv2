<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('accommodation_accommodation_feature');
        Schema::dropIfExists('accommodation_features');

        Schema::create('accommodation_features', function (Blueprint $table) {
            $table->id();

            $table->string('label');
            $table->string('icon_image')->nullable();

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['is_active', 'sort_order'], 'acc_feat_active_sort_idx');
        });

        Schema::create('accommodation_accommodation_feature', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('accommodation_id');
            $table->unsignedBigInteger('accommodation_feature_id');

            $table->timestamps();

            $table->unique(
                ['accommodation_id', 'accommodation_feature_id'],
                'acc_acc_feat_unique_idx'
            );

            $table->index('accommodation_id', 'acc_acc_feat_acc_idx');
            $table->index('accommodation_feature_id', 'acc_acc_feat_feat_idx');

            $table->foreign('accommodation_id', 'acc_acc_feat_acc_fk')
                ->references('id')
                ->on('accommodations')
                ->cascadeOnDelete();

            $table->foreign('accommodation_feature_id', 'acc_acc_feat_feat_fk')
                ->references('id')
                ->on('accommodation_features')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accommodation_accommodation_feature');
        Schema::dropIfExists('accommodation_features');

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
                'acc_feat_old_active_sort_idx'
            );
        });
    }
};
