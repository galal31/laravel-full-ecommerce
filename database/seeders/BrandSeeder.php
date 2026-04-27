<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            ['name'=>['en'=>'adidas', 'ar'=>'اديداس'],'slug'=>'adidas'],
            ['name'=>['en'=>'nike', 'ar'=>'نايك'],'slug'=>'nike'],
            ['name'=>['en'=>'puma', 'ar'=>'بوما'],'slug'=>'puma'],
            ['name'=>['en'=>'asics', 'ar'=>'اسيكس'],'slug'=>'asics'],
        
        ];

        foreach ($brands as $brand) {
            \App\Models\Dashboard\Brand::create($brand);
        }
    }
}
