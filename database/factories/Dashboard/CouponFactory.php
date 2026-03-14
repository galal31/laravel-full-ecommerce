<?php

namespace Database\Factories\Dashboard;

use App\Models\Dashboard\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;

class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition(): array
    {
        
        $limit = $this->faker->numberBetween(10, 100); 

        
        $times_used = $this->faker->numberBetween(0, $limit); 

        return [
            'code'=> $this->faker->unique()->ean13(), 
            'discount_percentage'=> $this->faker->randomFloat(2, 0, 100),
            'start_date'=> now()->addDays(rand(0, 10)),
            'expire_date'=> now()->addDays(rand(11, 30)),
            'limit'=> $limit,
            'times_used'=> $times_used,
            'is_active'=> rand(0, 1),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}