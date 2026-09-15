<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('signature_dishes', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('eyebrow')->nullable();
            $table->string('price')->nullable();
            $table->text('short_description')->nullable();
            $table->string('cta_label')->nullable();
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->longText('content')->nullable();
            $table->boolean('is_published')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(1)->index();
            $table->string('meta_title', 70)->nullable();
            $table->string('meta_description', 180)->nullable();
            $table->timestamps();
        });

        $legacyImage = '/images/dining/rahang-tuna.jpeg';
        $legacyImageFile = public_path('images/dining/rahang-tuna.jpeg');

        if (is_file($legacyImageFile)) {
            $legacyImage = 'dining/signature-dishes/rahang-tuna.jpeg';
            Storage::disk('public')->put($legacyImage, file_get_contents($legacyImageFile));
        }

        DB::table('signature_dishes')->insert([
            'name' => 'Nasi Jinggo', 'slug' => 'nasi-jinggo', 'eyebrow' => 'Signature Dish', 'price' => '$100',
            'short_description' => 'A beloved Balinese favourite, reimagined with premium ingredients and a refined presentation. A small plate with a big story.',
            'cta_label' => 'More Details',
            'image' => $legacyImage, 'image_alt' => 'Nasi Jinggo signature dish at Nandini Jungle',
            'content' => '<p>A beloved Balinese favourite, reimagined with premium ingredients and a refined presentation. A small plate with a big story.</p>',
            'is_published' => true, 'sort_order' => 1,
            'meta_title' => 'Nasi Jinggo | Nandini Jungle Dining',
            'meta_description' => 'Discover Nasi Jinggo, a signature Balinese dish presented with premium ingredients at Nandini Jungle by Hanging Gardens.',
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('signature_dishes');
    }
};
