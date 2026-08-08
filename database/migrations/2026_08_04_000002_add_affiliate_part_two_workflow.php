<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('affiliates', function (Blueprint $table): void {
            $table->text('rejection_reason')->nullable()->after('status_note');
        });

        Schema::create('affiliate_audit_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('affiliate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event')->index();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['affiliate_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_audit_events');

        Schema::table('affiliates', function (Blueprint $table): void {
            $table->dropColumn('rejection_reason');
        });
    }
};
