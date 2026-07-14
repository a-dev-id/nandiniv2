<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            if (! Schema::hasColumn('members', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('checkout_notification_sent_at');
                $table->index('last_login_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            if (Schema::hasColumn('members', 'last_login_at')) {
                $table->dropColumn('last_login_at');
            }
        });
    }
};
