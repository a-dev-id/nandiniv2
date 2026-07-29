<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guest_reviews', function (Blueprint $table): void {
            $table->text('excerpt')->nullable()->after('review_text');
        });

        DB::table('guest_reviews')
            ->select(['id', 'review_text'])
            ->orderBy('id')
            ->chunkById(100, function ($reviews): void {
                foreach ($reviews as $review) {
                    $excerpt = html_entity_decode(
                        strip_tags((string) $review->review_text),
                        ENT_QUOTES | ENT_HTML5,
                        'UTF-8'
                    );
                    $excerpt = trim((string) preg_replace('/\s+/', ' ', $excerpt));

                    DB::table('guest_reviews')
                        ->where('id', $review->id)
                        ->update(['excerpt' => $excerpt]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('guest_reviews', function (Blueprint $table): void {
            $table->dropColumn('excerpt');
        });
    }
};
