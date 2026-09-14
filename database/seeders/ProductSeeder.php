<?php

namespace Database\Seeders;

use App\Models\Dashboard\Attribute;
use App\Models\Dashboard\Brand;
use App\Models\Dashboard\Category;
use App\Models\Dashboard\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::query()
            ->whereNotNull('parent_id')
            ->where('status', true)
            ->get(['id']);

        $brands = Brand::query()
            ->where('status', true)
            ->get(['id']);

        $images = collect(Storage::disk('products')->files())
            ->filter(fn (string $file): bool => in_array(
                Str::lower(pathinfo($file, PATHINFO_EXTENSION)),
                ['jpg', 'jpeg', 'png', 'webp'],
                true
            ))
            ->values();

        $sizeValues = $this->attributeValues('Size');
        $colorValues = $this->attributeValues('Color');

        if ($categories->isEmpty() || $brands->isEmpty()) {
            throw new RuntimeException(
                'Run CategorySeeder and BrandSeeder before ProductSeeder.'
            );
        }

        if ($images->isEmpty()) {
            throw new RuntimeException(
                'Add at least one image to storage/app/public/products before running ProductSeeder.'
            );
        }

        if ($sizeValues->isEmpty() || $colorValues->isEmpty()) {
            throw new RuntimeException(
                'Run AttributeSeeder before ProductSeeder.'
            );
        }

        DB::transaction(function () use (
            $categories,
            $brands,
            $images,
            $sizeValues,
            $colorValues
        ): void {
            foreach (array_slice($this->products(), 0, 12) as $index => $data) {
                $number = $index + 1;
                $hasVariants = $number % 3 === 0;

                $product = Product::query()->updateOrCreate(
                    ['sku' => sprintf('DEMO-%03d', $number)],
                    [
                        'category_id' => $categories->random()->id,
                        'brand_id' => $brands->random()->id,
                        'name' => $data['name'],
                        'slug' => sprintf('demo-product-%02d', $number),
                        'small_desc' => $data['small_desc'],
                        'desc' => $data['desc'],
                        'status' => true,
                        'available_for' => now()->toDateString(),
                        'views' => 0,
                        'price' => $data['price'],
                        'discount' => random_int(10, 35),
                        'start_discount' => today()->subDays(random_int(1, 7))->toDateString(),
                        'end_discount' => today()->toDateString(),
                        'manage_stock' => ! $hasVariants,
                        'quantity' => $hasVariants ? null : 25 + $number,
                        'available_in_stock' => true,
                    ]
                );

                $product->images()->delete();
                $product->images()->create([
                    'file_name' => $images->random(),
                ]);

                $product->variants()->delete();

                if ($hasVariants) {
                    $this->createVariants(
                        $product,
                        $images,
                        $sizeValues,
                        $colorValues
                    );
                }
            }
        });
    }

    private function createVariants(
        Product $product,
        Collection $images,
        Collection $sizeValues,
        Collection $colorValues
    ): void {
        foreach (range(0, 2) as $index) {
            $variant = $product->variants()->create([
                'price' => (float) $product->price + ($index * 25),
                'stock' => 8 + ($index * 4),
            ]);

            $variant->attributeValues()->sync([
                $sizeValues[$index % $sizeValues->count()]->id,
                $colorValues[$index % $colorValues->count()]->id,
            ]);

            $variant->images()->create([
                'file_name' => $images->random(),
            ]);
        }
    }

    private function attributeValues(string $englishName): Collection
    {
        $attribute = Attribute::query()
            ->with('values:id,attribute_id,value')
            ->get(['id', 'name'])
            ->first(
                fn (Attribute $item): bool => $item->getTranslation('name', 'en') === $englishName
            );

        return $attribute?->values ?? collect();
    }

    private function products(): array
    {
        return [
            $this->product('Wireless Headphones', 'سماعات لاسلكية', 500),
            $this->product('Smart Watch', 'ساعة ذكية', 750),
            $this->product('Running Shoes', 'حذاء جري', 900),
            $this->product('Leather Backpack', 'حقيبة ظهر جلدية', 620),
            $this->product('Cotton T-Shirt', 'تيشيرت قطني', 280),
            $this->product('Sports Jacket', 'جاكيت رياضي', 1100),
            $this->product('Coffee Maker', 'ماكينة قهوة', 1450),
            $this->product('Table Lamp', 'مصباح طاولة', 390),
            $this->product('Fitness Tracker', 'سوار لياقة', 680),
            $this->product('Portable Speaker', 'مكبر صوت محمول', 840),
            $this->product('Kitchen Blender', 'خلاط مطبخ', 970),
            $this->product('Classic Sneakers', 'حذاء رياضي كلاسيكي', 820),
            $this->product('Travel Bottle', 'زجاجة سفر', 190),
            $this->product('Skin Care Set', 'مجموعة عناية بالبشرة', 560),
            $this->product('Camping Bag', 'حقيبة تخييم', 1250),
            $this->product('Digital Camera', 'كاميرا رقمية', 3200),
            $this->product('Gaming Mouse', 'فأرة ألعاب', 430),
            $this->product('Casual Hoodie', 'هودي كاجوال', 710),
            $this->product('Desk Organizer', 'منظم مكتب', 240),
            $this->product('Training Shorts', 'شورت تدريب', 360),
        ];
    }

    private function product(string $englishName, string $arabicName, float $price): array
    {
        return [
            'name' => [
                'en' => $englishName,
                'ar' => $arabicName,
            ],
            'small_desc' => [
                'en' => 'A practical demo product for the storefront.',
                'ar' => 'منتج تجريبي عملي لعرض المتجر.',
            ],
            'desc' => [
                'en' => 'A seeded product with sample data for testing the catalog and product variants.',
                'ar' => 'منتج مضاف من السيدر ببيانات تجريبية لاختبار المتجر ومتغيرات المنتجات.',
            ],
            'price' => $price,
        ];
    }
}
