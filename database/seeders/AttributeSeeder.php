<?php

namespace Database\Seeders;

use App\Models\Dashboard\Attribute;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('attributes')->truncate();
        DB::table('attribute_values')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $size_attribute = Attribute::create([
            'name' => ['en' => 'Size', 'ar' => 'الحجم'],
        ]);

        $color_attribute = Attribute::create([
            'name' => ['en' => 'Color', 'ar' => 'اللون'],
        ]);

        $material_attribute = Attribute::create([
            'name' => ['en' => 'Material', 'ar' => 'الخامة'],
        ]);

        $weight_attribute = Attribute::create([
            'name' => ['en' => 'Weight', 'ar' => 'الوزن'],
        ]);

        $brand_attribute = Attribute::create([
            'name' => ['en' => 'Brand', 'ar' => 'العلامة التجارية'],
        ]);

        $size_attribute->values()->createMany([
            ['value' => ['en' => 'Small', 'ar' => 'صغير']],
            ['value' => ['en' => 'Medium', 'ar' => 'متوسط']],
            ['value' => ['en' => 'Large', 'ar' => 'كبير']],
        ]);

        $color_attribute->values()->createMany([
            ['value' => ['en' => 'Red', 'ar' => 'أحمر']],
            ['value' => ['en' => 'Blue', 'ar' => 'أزرق']],
            ['value' => ['en' => 'Green', 'ar' => 'أخضر']],
        ]);

        $material_attribute->values()->createMany([
            ['value' => ['en' => 'Cotton', 'ar' => 'قطن']],
            ['value' => ['en' => 'Leather', 'ar' => 'جلد']],
            ['value' => ['en' => 'Polyester', 'ar' => 'بوليستر']],
        ]);

        $weight_attribute->values()->createMany([
            ['value' => ['en' => 'Light', 'ar' => 'خفيف']],
            ['value' => ['en' => 'Medium', 'ar' => 'متوسط']],
            ['value' => ['en' => 'Heavy', 'ar' => 'ثقيل']],
        ]);

        $brand_attribute->values()->createMany([
            ['value' => ['en' => 'Nike', 'ar' => 'نايكي']],
            ['value' => ['en' => 'Adidas', 'ar' => 'أديداس']],
            ['value' => ['en' => 'Puma', 'ar' => 'بوما']],
        ]);
    }
}