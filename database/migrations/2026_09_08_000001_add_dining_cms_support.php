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
            $table->boolean('show_on_dining')->default(false)->after('is_featured')->index();
            $table->string('dining_slug')->nullable()->unique()->after('slug');
            $table->string('dining_cta_label')->nullable()->after('excerpt');
            $table->string('hero_mobile_image')->nullable()->after('image_alt');
            $table->string('intro_eyebrow')->nullable()->after('hero_mobile_image');
            $table->string('page_heading')->nullable()->after('intro_eyebrow');
            $table->string('menu_cta_label')->nullable()->after('page_heading');
            $table->text('menu_url')->nullable()->after('menu_cta_label');
            $table->string('reservation_cta_label')->nullable()->after('menu_url');
            $table->text('reservation_url')->nullable()->after('reservation_cta_label');
            $table->string('opening_hours')->nullable()->after('location');
            $table->string('experience_type')->nullable()->after('opening_hours');
            $table->string('whatsapp_number')->nullable()->after('experience_type');
        });

        Schema::table('guest_reviews', function (Blueprint $table): void {
            $table->boolean('show_on_dining')->default(false)->after('is_featured')->index();
        });

        Schema::create('dining_experience_gallery', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('experience_id')->constrained()->cascadeOnDelete();
            $table->string('image');
            $table->string('image_alt')->nullable();
            $table->string('caption')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['experience_id', 'is_active', 'sort_order'], 'dining_gallery_display_index');
        });

        Schema::create('dining_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('reservation_whatsapp')->nullable();
            $table->string('reservation_email')->nullable();
            $table->text('reservation_url')->nullable();
            $table->text('food_menu_url')->nullable();
            $table->text('beverage_menu_url')->nullable();
            $table->string('opening_hours')->nullable();
            $table->string('location')->nullable();
            $table->string('cuisine')->nullable();
            $table->string('dress_code')->nullable();
            $table->text('outside_guest_information')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
        });

        DB::table('dining_settings')->insertOrIgnore([
            'id' => 1,
            'reservation_whatsapp' => '+62 812 3687 1170',
            'reservation_email' => 'reservation@nandinibali.com',
            'reservation_url' => 'https://wa.me/6281236871170',
            'opening_hours' => '7.00 AM – 10.00 PM',
            'location' => 'Nandini Jungle by Hanging Gardens, Ubud, Bali',
            'cuisine' => 'Indonesian & International',
            'outside_guest_information' => 'Yes, all are welcome',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $experiences = [
            ['lookup' => 'wild-ginger-restaurant', 'slug' => 'wild-ginger-restaurant', 'title' => 'Wild Ginger Restaurant', 'excerpt' => 'Dine surrounded by nature, offering a refined à la carte menu inspired by Indonesian and international cuisine.', 'cta' => 'Explore Restaurant', 'order' => 1],
            ['lookup' => 'bar-and-lounge', 'slug' => 'bar-and-lounge', 'title' => 'Bar & Lounge', 'excerpt' => 'Relax with handcrafted cocktails, fine wines and light bites in a stylish open-air lounge.', 'cta' => 'Explore Bar & Lounge', 'order' => 2],
            ['lookup' => 'luxe-high-tea', 'slug' => 'afternoon-tea', 'title' => 'Afternoon Tea', 'excerpt' => 'A delightful selection of sweet and savoury creations, served in the heart of the jungle.', 'cta' => 'Discover Afternoon Tea', 'order' => 3],
            ['lookup' => 'wine-cellar-experience', 'slug' => 'wine-cellar-experience', 'title' => 'Wine Cellar Experience', 'excerpt' => 'An exclusive collection of premium wines, perfect for a refined evening or a private tasting.', 'cta' => 'Explore Wine Cellar', 'order' => 4],
            ['lookup' => 'romantic-dining-by-the-chapel', 'slug' => 'romantic-dining', 'title' => 'Romantic Dining', 'excerpt' => 'Create unforgettable moments with a candlelit dinner in a magical jungle atmosphere.', 'cta' => 'Discover Romantic Dining', 'order' => 5],
        ];

        foreach ($experiences as $item) {
            $existing = DB::table('experiences')->where('slug', $item['lookup'])->first();
            if ($existing) {
                DB::table('experiences')->where('id', $existing->id)->update([
                    'show_on_dining' => true,
                    'dining_slug' => $item['slug'],
                    'dining_cta_label' => $existing->dining_cta_label ?: $item['cta'],
                    'sort_order' => $item['order'],
                    'updated_at' => now(),
                ]);
                continue;
            }

            DB::table('experiences')->insert([
                'title' => $item['title'],
                'slug' => $item['lookup'],
                'dining_slug' => $item['slug'],
                'excerpt' => $item['excerpt'],
                'description' => '<p>'.$item['excerpt'].'</p>',
                'dining_cta_label' => $item['cta'],
                'show_on_dining' => true,
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => $item['order'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('dining_settings');
        Schema::dropIfExists('dining_experience_gallery');
        Schema::table('guest_reviews', fn (Blueprint $table) => $table->dropColumn('show_on_dining'));
        Schema::table('experiences', function (Blueprint $table): void {
            $table->dropColumn([
                'show_on_dining', 'dining_slug', 'dining_cta_label', 'hero_mobile_image',
                'intro_eyebrow', 'page_heading', 'menu_cta_label', 'menu_url',
                'reservation_cta_label', 'reservation_url', 'opening_hours',
                'experience_type', 'whatsapp_number',
            ]);
        });
    }
};
