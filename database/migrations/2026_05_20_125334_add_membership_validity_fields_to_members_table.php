<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('tier')->default('bronze')->after('member_source');

            $table->timestamp('membership_started_at')->nullable()->after('tier');
            $table->timestamp('membership_expires_at')->nullable()->after('membership_started_at');
            $table->timestamp('last_tier_downgraded_at')->nullable()->after('membership_expires_at');

            $table->index('tier');
            $table->index('membership_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropIndex(['tier']);
            $table->dropIndex(['membership_expires_at']);

            $table->dropColumn([
                'tier',
                'membership_started_at',
                'membership_expires_at',
                'last_tier_downgraded_at',
            ]);
        });
    }
};
