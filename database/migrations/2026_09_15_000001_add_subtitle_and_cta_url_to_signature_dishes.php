<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('signature_dishes', function (Blueprint $table): void {
            $table->string('subtitle')->nullable()->after('eyebrow');
            $table->text('cta_url')->nullable()->after('cta_label');
        });

        DB::table('signature_dishes')->updateOrInsert(
            ['slug' => 'nasi-jinggo'],
            [
                'name' => 'NASI JINGGO $100',
                'eyebrow' => 'SIGNATURE DISH',
                'subtitle' => 'A SIGNATURE TASTE OF BALI',
                'price' => null,
                'short_description' => 'A beloved Balinese street food, reimagined with premium ingredients and refined presentation, offering an authentic taste of Indonesia in the extraordinary setting of Nandini Jungle.',
                'cta_label' => 'VIEW SIGNATURE DISH',
                'cta_url' => '/signature-dishes/nasi-jinggo',
                'image' => '/images/dining/rahang-tuna.jpeg',
                'image_alt' => 'Nasi Jinggo signature dish at Nandini Jungle',
                'content' => '<p>A beloved Balinese street food, reimagined with premium ingredients and refined presentation, offering an authentic taste of Indonesia in the extraordinary setting of Nandini Jungle.</p>',
                'is_published' => true,
                'sort_order' => 1,
                'meta_title' => 'Nasi Jinggo | Nandini Jungle Dining',
                'meta_description' => 'Discover Nasi Jinggo, a signature Balinese dish presented with premium ingredients at Nandini Jungle by Hanging Gardens.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );
    }

    public function down(): void
    {
        Schema::table('signature_dishes', function (Blueprint $table): void {
            $table->dropColumn(['subtitle', 'cta_url']);
        });
    }
};
