<?php

namespace Database\Seeders;

use App\Models\Dashboard\Coupon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // coupons factory
        // start using coupon factory
        Coupon::truncate();
        Coupon::factory(10)->create();

    }
}
