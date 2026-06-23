<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('members') || Schema::hasColumn('members', 'membership_expiry_reminder_sent_at')) {
            return;
        }

        Schema::table('members', function (Blueprint $table): void {
            $table
                ->timestamp('membership_expiry_reminder_sent_at')
                ->nullable()
                ->after('last_tier_downgraded_at');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('members') || ! Schema::hasColumn('members', 'membership_expiry_reminder_sent_at')) {
            return;
        }

        Schema::table('members', function (Blueprint $table): void {
            $table->dropColumn('membership_expiry_reminder_sent_at');
        });
    }
};
