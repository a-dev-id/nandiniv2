<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('member_point_transactions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('member_id');

            // earn = add point
            // redeem = use point
            // adjustment = manual correction
            $table->string('type', 50);

            // Earn point should be positive: 100
            // Redeem point should be negative: -100
            $table->integer('points');

            $table->text('description')->nullable();

            // Optional reference, example:
            // reference_type = rewards
            // reference_id = reward id
            $table->string('reference_type', 100)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->timestamps();

            $table->index('member_id');
            $table->index('type');
            $table->index(['reference_type', 'reference_id'], 'member_point_transactions_reference_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_point_transactions');
    }
};
