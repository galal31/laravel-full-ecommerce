<?php

namespace Database\Seeders;

use App\Models\Dashboard\Brand;
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
            ['name'=>['en'=>'adidas', 'ar'=>'اديداس'],'slug'=>'adidas','logo'=>'default.png'],
            ['name'=>['en'=>'reebok', 'ar'=>'ريبوك'],'slug'=>'reebok','logo'=>'default.png'],
            ['name'=>['en'=>'new balance', 'ar'=>'نيو بالانس'],'slug'=>'new-balance','logo'=>'default.png'],
            ['name'=>['en'=>'under armour', 'ar'=>'اندر ارمر'],'slug'=>'under-armour','logo'=>'default.png'],
            ['name'=>['en'=>'converse', 'ar'=>'كونفرس'],'slug'=>'converse','logo'=>'default.png'],
            ['name'=>['en'=>'vans', 'ar'=>'فانز'],'slug'=>'vans','logo'=>'default.png'],
            ['name'=>['en'=>'fila', 'ar'=>'فيلا'],'slug'=>'fila','logo'=>'default.png'],
            ['name'=>['en'=>'saucony', 'ar'=>'ساكوني'],'slug'=>'saucony','logo'=>'default.png'],
            ['name'=>['en'=>'brooks', 'ar'=>'بروكس'],'slug'=>'brooks','logo'=>'default.png'],
            ['name'=>['en'=>'mizuno', 'ar'=>'ميزونو'],'slug'=>'mizuno','logo'=>'default.png'],
            ['name'=>['en'=>'nike', 'ar'=>'نايك'],'slug'=>'nike','logo'=>'default.png'],
            ['name'=>['en'=>'puma', 'ar'=>'بوما'],'slug'=>'puma','logo'=>'default.png'],
            ['name'=>['en'=>'asics', 'ar'=>'اسيكس'],'slug'=>'asics','logo'=>'default.png'],
            ['name'=>['en'=>'hoka one one', 'ar'=>'هوكا ون ون'],'slug'=>'hoka-one-one','logo'=>'default.png'],
            ['name'=>['en'=>'salomon', 'ar'=>'سالومون'],'slug'=>'salomon','logo'=>'default.png'],
            ['name'=>['en'=>'merrell', 'ar'=>'ميريل'],'slug'=>'merrell','logo'=>'default.png'],
            ['name'=>['en'=>'columbia', 'ar'=>'كولومبيا'],'slug'=>'columbia','logo'=>'default.png'],
            ['name'=>['en'=>'the north face', 'ar'=>'ذا نورث فيس'],'slug'=>'the-north-face','logo'=>'default.png'],
            ['name'=>['en'=>'patagonia', 'ar'=>'باتاغونيا'],'slug'=>'patagonia','logo'=>'default.png'],
            ['name'=>['en'=>'marmot', 'ar'=>'مارموت'],'slug'=>'marmot','logo'=>'default.png'],
        
        ];

        foreach ($brands as $brand) {
            Brand::query()->updateOrCreate(
                ['slug' => $brand['slug']],
                [
                    'name' => $brand['name'],
                    'logo' => $brand['logo'],
                    'status' => true,
                ]
            );
        }
    }
}
