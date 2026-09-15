<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experiences', function (Blueprint $table): void {
            $table->string('dining_card_title')->nullable()->after('dining_slug');
            $table->text('dining_short_description')->nullable()->after('excerpt');
        });

        $copy = [
            'wild-ginger-restaurant' => ['Wild Ginger Restaurant', 'Dine surrounded by nature, offering a refined à la carte menu inspired by Indonesian and international cuisine.'],
            'bar-and-lounge' => ['Bar & Lounge', 'Relax with handcrafted cocktails, fine wines and light bites in a stylish open-air lounge.'],
            'afternoon-tea' => ['Afternoon Tea', 'A delightful selection of sweet and savoury creations, served in the heart of the jungle.'],
            'wine-cellar-experience' => ['Wine Cellar Experience', 'An exclusive collection of premium wines, perfect for a refined evening or a private tasting.'],
            'romantic-dining' => ['Romantic Dining', 'Create unforgettable moments with a candlelit dinner in a magical jungle atmosphere.'],
        ];

        foreach ($copy as $slug => [$title, $description]) {
            DB::table('experiences')->where('dining_slug', $slug)->update([
                'dining_card_title' => $title,
                'dining_short_description' => $description,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('experiences', fn (Blueprint $table) => $table->dropColumn(['dining_card_title', 'dining_short_description']));
    }
};
