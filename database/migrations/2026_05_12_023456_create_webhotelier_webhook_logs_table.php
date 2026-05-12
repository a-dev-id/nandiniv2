<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhotelier_webhook_logs', function (Blueprint $table) {
            $table->id();

            $table->string('source')->default('webhotelier');
            $table->string('event_type')->nullable();
            $table->string('property_code')->nullable();

            $table->string('reservation_id')->nullable();
            $table->string('confirmation_code')->nullable();
            $table->string('booking_status')->nullable();

            $table->string('method')->nullable();
            $table->string('ip_address')->nullable();

            $table->json('headers')->nullable();
            $table->longText('raw_body')->nullable();
            $table->json('payload')->nullable();

            $table->string('processing_status')->default('pending');
            $table->text('processing_error')->nullable();
            $table->timestamp('processed_at')->nullable();

            $table->timestamps();

            $table->index('reservation_id');
            $table->index('confirmation_code');
            $table->index('booking_status');
            $table->index('processing_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhotelier_webhook_logs');
    }
};
