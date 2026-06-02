<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('title', 20);
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('country');
            $table->string('phone_code', 10);
            $table->string('phone', 40);
            $table->text('note')->nullable();
            $table->text('source_url')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('email_sent_at')->nullable();
            $table->text('email_error')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index('email');
            $table->index('is_read');
            $table->index('submitted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};
