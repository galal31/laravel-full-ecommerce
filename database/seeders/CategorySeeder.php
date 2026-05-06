<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dashboard\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing categories
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Category::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $categories = [
            [
                'name' => ['en' => 'Electronics', 'ar' => 'إلكترونيات'],
                'sub' => [
                    ['en' => 'Smartphones', 'ar' => 'هواتف ذكية'],
                    ['en' => 'Laptops', 'ar' => 'أجهزة لابتوب'],
                    ['en' => 'Tablets', 'ar' => 'أجهزة تابلت'],
                    ['en' => 'Cameras', 'ar' => 'كاميرات تصوير'],
                    ['en' => 'Headphones', 'ar' => 'سماعات'],
                    ['en' => 'Video Games', 'ar' => 'ألعاب فيديو'],
                ]
            ],
            [
                'name' => ['en' => 'Fashion', 'ar' => 'أزياء'],
                'sub' => [
                    ['en' => 'Men\'s Clothing', 'ar' => 'ملابس رجالي'],
                    ['en' => 'Women\'s Clothing', 'ar' => 'ملابس حريمي'],
                    ['en' => 'Kids\' Fashion', 'ar' => 'أزياء أطفال'],
                    ['en' => 'Shoes', 'ar' => 'أحذية'],
                    ['en' => 'Watches', 'ar' => 'ساعات'],
                    ['en' => 'Jewelry', 'ar' => 'مجوهرات'],
                ]
            ],
            [
                'name' => ['en' => 'Home & Kitchen', 'ar' => 'المنزل والمطبخ'],
                'sub' => [
                    ['en' => 'Furniture', 'ar' => 'أثاث'],
                    ['en' => 'Home Decor', 'ar' => 'ديكور منزلي'],
                    ['en' => 'Kitchen Tools', 'ar' => 'أدوات المطبخ'],
                    ['en' => 'Bedding', 'ar' => 'مفروشات'],
                    ['en' => 'Large Appliances', 'ar' => 'أجهزة منزلية كبيرة'],
                ]
            ],
            [
                'name' => ['en' => 'Beauty & Health', 'ar' => 'الجمال والصحة'],
                'sub' => [
                    ['en' => 'Perfumes', 'ar' => 'عطور'],
                    ['en' => 'Skin Care', 'ar' => 'العناية بالبشرة'],
                    ['en' => 'Hair Care', 'ar' => 'العناية بالشعر'],
                    ['en' => 'Makeup', 'ar' => 'مكياج'],
                    ['en' => 'Vitamins & Supplements', 'ar' => 'فيتامينات ومكملات'],
                ]
            ],
            [
                'name' => ['en' => 'Sports & Outdoors', 'ar' => 'الرياضة واللياقة'],
                'sub' => [
                    ['en' => 'Fitness Equipment', 'ar' => 'أدوات لياقة بدنية'],
                    ['en' => 'Cycling', 'ar' => 'دراجات'],
                    ['en' => 'Camping Gear', 'ar' => 'أدوات تخييم'],
                    ['en' => 'Sportswear', 'ar' => 'ملابس رياضية'],
                ]
            ],
            [
                'name' => ['en' => 'Groceries', 'ar' => 'سوبر ماركت'],
                'sub' => [
                    ['en' => 'Beverages', 'ar' => 'مشروبات'],
                    ['en' => 'Canned Food', 'ar' => 'معلبات'],
                    ['en' => 'Snacks', 'ar' => 'تصبيرة وحلويات'],
                ]
            ],
        ];

        foreach ($categories as $categoryData) {

            $parent = Category::create([
                'name' => $categoryData['name'],
                'slug' => Str::slug($categoryData['name']['en']),
                'status' => 1,
                'parent_id' => null,
                'icon' => 'default.png',
            ]);


            foreach ($categoryData['sub'] as $subItem) {
                Category::create([
                    'name' => $subItem,
                    'slug' => Str::slug($subItem['en']),
                    'status' => 1,
                    'parent_id' => $parent->id,
                    'icon' => 'default.png',
                ]);
            }
        }
    }
}