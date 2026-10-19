<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('pages')->where('site', 'main')->where('slug', 'jungle-villas')->update([
            'description' => '<p>Set within the lush rainforest landscape of Payangan, in the greater Ubud area of Bali, Nandini Jungle by Hanging Gardens offers a collection of private jungle villas inspired by traditional Balinese architecture. Each villa features a private balcony, refined interiors and views of the surrounding tropical landscape, creating a tranquil retreat close to nature.</p>',
            'excerpt' => 'Set within the lush rainforest landscape of Payangan, in the greater Ubud area of Bali, Nandini Jungle by Hanging Gardens offers a collection of private jungle villas inspired by traditional Balinese architecture.',
            'meta_description' => 'Discover private jungle villas in Ubud, Bali, with Balinese-inspired interiors, rainforest views and private balconies. Book directly with Nandini Jungle.',
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('pages')->where('site', 'main')->where('slug', 'jungle-villas')->update([
            'description' => '<p>Indulge in the ultimate luxury in our exquisite villas, where elegance meets privacy. Each villa is a private oasis, featuring spacious living areas, bespoke furnishings, and breathtaking views. Enjoy your secluded outdoor sanctuary, complete with lavish amenities and personalized service designed for your comfort. Experience a serene escape that redefines opulence—your exclusive haven awaits.</p>',
            'excerpt' => 'Indulge in the ultimate luxury in our exquisite villas, where elegance meets privacy. Each villa is a private oasis, featuring spacious living areas, bespoke furnishings, and breathtaking views.',
            'meta_description' => 'Discover Jungle Villas in Ubud Bali at Nandini Jungle by Hanging Gardens, offering private luxury stays with spacious living areas, elegant design, and jungle views.',
            'updated_at' => now(),
        ]);
    }
};
