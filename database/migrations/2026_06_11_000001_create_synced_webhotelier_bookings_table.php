<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('synced_webhotelier_bookings')) {
            return;
        }

        Schema::create('synced_webhotelier_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->string('booking_number')->unique();
            $table->string('guest_name')->nullable();
            $table->string('email')->index();
            $table->string('phone')->nullable();
            $table->date('check_in')->nullable();
            $table->date('check_out')->nullable();
            $table->unsignedInteger('rooms')->nullable();
            $table->string('room_type')->nullable();
            $table->string('room_name')->nullable();
            $table->string('rate_name')->nullable();
            $table->string('currency', 10)->nullable();
            $table->decimal('booking_total', 14, 2)->nullable();
            $table->string('status')->nullable()->index();
            $table->string('source_name')->nullable();
            $table->timestamp('remote_updated_at')->nullable()->index();
            $table->timestamp('last_synced_at')->nullable()->index();
            $table->timestamps();

            $table->index(['member_id', 'check_in']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('synced_webhotelier_bookings');
    }
};
