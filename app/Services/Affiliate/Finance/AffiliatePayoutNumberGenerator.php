<?php

namespace App\Services\Affiliate\Finance;

use Illuminate\Support\Facades\DB;

class AffiliatePayoutNumberGenerator
{
    public function next(?int $year = null): string
    {
        $year ??= (int) now()->timezone(config('app.timezone'))->format('Y');
        DB::table('affiliate_payout_number_sequences')->insertOrIgnore([
            'sequence_year' => $year,
            'next_number' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $sequence = DB::table('affiliate_payout_number_sequences')->where('sequence_year', $year)->lockForUpdate()->first();
        $number = (int) $sequence->next_number;
        DB::table('affiliate_payout_number_sequences')->where('sequence_year', $year)->update([
            'next_number' => $number + 1,
            'updated_at' => now(),
        ]);

        return sprintf('AFF-PAY-%d-%05d', $year, $number);
    }
}
