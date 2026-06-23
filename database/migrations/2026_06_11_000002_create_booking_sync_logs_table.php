<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('booking_sync_logs')) {
            return;
        }

        Schema::create('booking_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamp('started_at')->nullable()->index();
            $table->timestamp('finished_at')->nullable();
            $table->string('status')->default('running')->index();
            $table->unsignedInteger('bookings_received')->default(0);
            $table->unsignedInteger('bookings_created')->default(0);
            $table->unsignedInteger('bookings_updated')->default(0);
            $table->unsignedInteger('members_created')->default(0);
            $table->unsignedInteger('members_updated')->default(0);
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_sync_logs');
    }
};
