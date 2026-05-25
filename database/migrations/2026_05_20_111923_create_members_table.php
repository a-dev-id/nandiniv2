<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();

            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('name')->nullable();

            $table->string('email')->unique();
            $table->string('phone_number')->nullable();
            $table->string('country')->nullable();
            $table->text('address')->nullable();

            $table->string('password')->nullable();

            $table->enum('member_source', [
                'auto_join',
                'manual_register',
            ])->default('manual_register');

            $table->boolean('marketing_consent')->default(false);
            $table->integer('points')->default(0);

            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();

            $table->timestamps();

            $table->index('member_source');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
