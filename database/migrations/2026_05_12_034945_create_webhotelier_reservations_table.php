<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhotelier_reservations', function (Blueprint $table) {
            $table->id();

            $table->string('webhotelier_id')->unique();
            $table->string('property_code')->nullable()->index();

            $table->string('event_type')->nullable()->index();
            $table->string('status_code')->nullable()->index();
            $table->boolean('status')->nullable();

            $table->boolean('offline')->nullable();
            $table->boolean('channelstream')->nullable();

            $table->string('guest_email')->nullable()->index();
            $table->string('guest_first_name')->nullable();
            $table->string('guest_last_name')->nullable();
            $table->string('guest_phone')->nullable();

            $table->date('checkin_date')->nullable();
            $table->date('checkout_date')->nullable();
            $table->unsignedInteger('rooms')->nullable();

            $table->string('room_type')->nullable();
            $table->string('room_name')->nullable();
            $table->string('rate_name')->nullable();

            $table->string('currency', 10)->nullable();
            $table->decimal('room_subtotal', 15, 2)->nullable();
            $table->decimal('booking_total', 15, 2)->nullable();
            $table->decimal('extras_total', 15, 2)->nullable();
            $table->decimal('taxes_total', 15, 2)->nullable();

            $table->string('source_id')->nullable();
            $table->string('source_name')->nullable();

            $table->unsignedBigInteger('last_webhook_log_id')->nullable()->index();
            $table->json('payload')->nullable();

            $table->timestamp('last_received_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhotelier_reservations');
    }
};
