<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sliders = [
            [
                'file_name' => 'slider1.jpg',
                'note' => ['en' => 'This is the first slider', 'ar' => 'هذا هو السلايدر الأول'],
            ],
            [
                'file_name' => 'slider1.jpg',
                'note' => ['en' => 'This is the second slider', 'ar' => 'هذا هو السلايدر الثاني'],
            ],
            [
                'file_name' => 'slider1.jpg',
                'note' => ['en' => 'This is the third slider', 'ar' => 'هذا هو السلايدر الثالث'],
            ],
        ];

        foreach ($sliders as $slider) {
            \App\Models\Dashboard\Slider::create($slider);
        }
    }
}
