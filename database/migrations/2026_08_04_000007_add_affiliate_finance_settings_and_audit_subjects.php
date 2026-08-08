<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('affiliate_program_settings', function (Blueprint $table): void {
            $table->renameColumn('payment_release_days', 'payout_release_days');
        });

        Schema::table('affiliate_program_settings', function (Blueprint $table): void {
            $table->unsignedTinyInteger('commission_validation_start_day')->default(1)->after('payout_release_days');
            $table->unsignedTinyInteger('commission_validation_end_day')->default(7)->after('commission_validation_start_day');
        });

        Schema::table('affiliate_audit_events', function (Blueprint $table): void {
            $table->foreignId('affiliate_id')->nullable()->change();
            $table->string('subject_type')->nullable()->after('event');
            $table->unsignedBigInteger('subject_id')->nullable()->after('subject_type');
            $table->index(['subject_type', 'subject_id'], 'affiliate_audit_subject_index');
        });
    }

    public function down(): void
    {
        Schema::table('affiliate_audit_events', function (Blueprint $table): void {
            $table->dropIndex('affiliate_audit_subject_index');
            $table->dropColumn(['subject_type', 'subject_id']);
        });

        DB::table('affiliate_audit_events')->whereNull('affiliate_id')->delete();
        Schema::table('affiliate_audit_events', function (Blueprint $table): void {
            $table->foreignId('affiliate_id')->nullable(false)->change();
        });

        Schema::table('affiliate_program_settings', function (Blueprint $table): void {
            $table->dropColumn(['commission_validation_start_day', 'commission_validation_end_day']);
            $table->renameColumn('payout_release_days', 'payment_release_days');
        });
    }
};
