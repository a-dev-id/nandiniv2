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
        Schema::create('member_reward_redemptions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('member_id');
            $table->unsignedBigInteger('reward_id')->nullable();
            $table->unsignedBigInteger('member_point_transaction_id')->nullable();

            // Snapshot data, so old redemption history will not change
            // even if the reward name or point cost is changed later.
            $table->string('reward_name');
            $table->integer('points_used');

            // Example:
            // pending = member already redeemed, waiting to be used
            // used = reward already claimed/used
            // cancelled = redemption cancelled
            // expired = redemption expired
            $table->string('status', 50)->default('pending');

            // Unique code shown to member / staff
            $table->string('redemption_code', 100)->nullable();

            $table->timestamp('used_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('member_id');
            $table->index('reward_id');
            $table->index('member_point_transaction_id');
            $table->index('status');
            $table->index('redemption_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_reward_redemptions');
    }
};
