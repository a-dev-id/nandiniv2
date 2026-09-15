<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dining_experiences', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('card_title')->nullable();
            $table->text('short_description')->nullable();
            $table->string('card_cta_label')->nullable();
            $table->longText('description')->nullable();
            $table->string('card_image')->nullable();
            $table->string('card_image_alt')->nullable();
            $table->string('hero_image')->nullable();
            $table->string('hero_mobile_image')->nullable();
            $table->string('hero_image_alt')->nullable();
            $table->string('intro_eyebrow')->nullable();
            $table->string('page_heading')->nullable();
            $table->string('menu_cta_label')->nullable();
            $table->text('menu_url')->nullable();
            $table->string('reservation_cta_label')->nullable();
            $table->text('reservation_url')->nullable();
            $table->string('opening_hours')->nullable();
            $table->string('experience_type')->nullable();
            $table->string('location')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
            $table->index(['is_active', 'sort_order']);
        });

        if (Schema::hasTable('experiences') && Schema::hasColumn('experiences', 'show_on_dining')) {
            DB::table('experiences')->where('show_on_dining', true)->orderBy('sort_order')->each(function ($experience): void {
                DB::table('dining_experiences')->insertOrIgnore([
                    'id' => $experience->id,
                    'title' => $experience->title,
                    'slug' => $experience->dining_slug ?: $experience->slug,
                    'card_title' => $experience->dining_card_title,
                    'short_description' => $experience->dining_short_description,
                    'card_cta_label' => $experience->dining_cta_label,
                    'description' => $experience->description,
                    'card_image' => $experience->card_image,
                    'card_image_alt' => $experience->card_image_alt,
                    'hero_image' => $experience->image,
                    'hero_mobile_image' => $experience->hero_mobile_image,
                    'hero_image_alt' => $experience->image_alt,
                    'intro_eyebrow' => $experience->intro_eyebrow,
                    'page_heading' => $experience->page_heading,
                    'menu_cta_label' => $experience->menu_cta_label,
                    'menu_url' => $experience->menu_url,
                    'reservation_cta_label' => $experience->reservation_cta_label,
                    'reservation_url' => $experience->reservation_url,
                    'opening_hours' => $experience->opening_hours,
                    'experience_type' => $experience->experience_type,
                    'location' => $experience->location,
                    'whatsapp_number' => $experience->whatsapp_number,
                    'is_active' => $experience->is_active,
                    'sort_order' => $experience->sort_order,
                    'meta_title' => $experience->meta_title,
                    'meta_description' => $experience->meta_description,
                    'created_at' => $experience->created_at,
                    'updated_at' => $experience->updated_at,
                ]);
            });
        }

        Schema::table('dining_experience_gallery', function (Blueprint $table): void {
            $table->unsignedBigInteger('dining_experience_id')->nullable()->after('experience_id')->index();
        });

        DB::table('dining_experience_gallery')->update([
            'dining_experience_id' => DB::raw('experience_id'),
        ]);
    }

    public function down(): void
    {
        Schema::table('dining_experience_gallery', fn (Blueprint $table) => $table->dropColumn('dining_experience_id'));
        Schema::dropIfExists('dining_experiences');
    }
};
