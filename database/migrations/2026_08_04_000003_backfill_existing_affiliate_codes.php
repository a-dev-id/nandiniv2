<?php

use Carbon\CarbonImmutable;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $used = DB::table('affiliates')
            ->whereNotNull('affiliate_code')
            ->pluck('affiliate_code')
            ->flip();

        DB::table('affiliates')
            ->whereNull('affiliate_code')
            ->orderBy('id')
            ->get()
            ->each(function (object $affiliate) use (&$used): void {
                $registeredAt = CarbonImmutable::parse($affiliate->created_at ?? now());
                $name = preg_replace('/[^a-z0-9]/', '', Str::lower(Str::ascii((string) $affiliate->name))) ?: 'partner';
                $base = $name.$registeredAt->day.$registeredAt->month.$registeredAt->format('y');
                $code = $base;
                $sequence = 2;

                while ($used->has($code)) {
                    $code = $base.str_pad((string) $sequence++, 2, '0', STR_PAD_LEFT);
                }

                $used->put($code, true);
                $approved = $affiliate->status === 'approved';

                DB::table('affiliates')->where('id', $affiliate->id)->update([
                    'affiliate_code' => $code,
                    'affiliate_code_generated_at' => $affiliate->created_at ?? now(),
                    'short_link_slug' => $code,
                    'short_link_activated_at' => $approved ? ($affiliate->approved_at ?? now()) : null,
                ]);
            });
    }

    public function down(): void
    {
        // Existing records keep their generated identifiers on rollback.
    }
};
