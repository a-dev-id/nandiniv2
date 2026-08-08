<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('affiliate_payment_profiles')
            ->where('is_complete', true)
            ->whereNull('verified_at')
            ->update([
                'verified_at' => now(),
                'verified_by' => null,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // Automatic approval timestamps are retained to avoid reverting valid profiles.
    }
};
