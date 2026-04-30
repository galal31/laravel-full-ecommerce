<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        
        $this->call([
            AdminSeeder::class,
            WorldSeeder::class,
            CategorySeeder::class,
            CouponSeeder::class,
            FaqSeeder::class,
            BrandSeeder::class,
            AttributeSeeder::class,
            
        ]);
        User::factory(5)->create();
    }
}
