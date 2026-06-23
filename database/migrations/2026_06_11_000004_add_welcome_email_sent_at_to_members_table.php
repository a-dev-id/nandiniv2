<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('members') || Schema::hasColumn('members', 'welcome_email_sent_at')) {
            return;
        }

        Schema::table('members', function (Blueprint $table) {
            if (Schema::hasColumn('members', 'email_verified_at')) {
                $table->timestamp('welcome_email_sent_at')->nullable()->after('email_verified_at');

                return;
            }

            $table->timestamp('welcome_email_sent_at')->nullable()->after('email');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('members') || ! Schema::hasColumn('members', 'welcome_email_sent_at')) {
            return;
        }

        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('welcome_email_sent_at');
        });
    }
};
