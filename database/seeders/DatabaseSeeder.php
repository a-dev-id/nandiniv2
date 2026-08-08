<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AffiliateFoundationSeeder::class);

        if (app()->environment('local')) {
            User::query()->firstOrCreate([
                'email' => 'test@example.com',
            ], [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);

            $this->call(AffiliateDevelopmentSeeder::class);
            $this->call(AffiliateClickDevelopmentSeeder::class);
            $this->call(AffiliateBookingDevelopmentSeeder::class);
            $this->call(AffiliateFinanceDevelopmentSeeder::class);
            $this->call(AffiliateOperationsDevelopmentSeeder::class);
        }
    }
}
