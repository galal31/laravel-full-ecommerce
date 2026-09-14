<?php

namespace Tests\Feature\Website;

use App\Models\Dashboard\Brand;
use App\Models\Dashboard\Category;
use App\Models\Dashboard\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            LocaleSessionRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ]);
    }

    public function test_category_catalog_shows_only_active_products(): void
    {
        [$category, $brand] = $this->catalogParents();

        $activeProduct = $this->product($category, $brand, 'Active product', true);
        $inactiveProduct = $this->product($category, $brand, 'Inactive product', false);

        $response = $this->get(route('website.categories.products', $category->slug));

        $response->assertOk();
        $response->assertSee($activeProduct->name);
        $response->assertDontSee($inactiveProduct->name);
    }

    public function test_inactive_category_cannot_be_browsed(): void
    {
        $category = Category::query()->create([
            'name' => ['en' => 'Hidden', 'ar' => 'مخفي'],
            'slug' => 'hidden',
            'status' => false,
        ]);

        $this->get(route('website.categories.products', $category->slug))
            ->assertNotFound();
    }

    private function catalogParents(): array
    {
        $category = Category::query()->create([
            'name' => ['en' => 'Shoes', 'ar' => 'أحذية'],
            'slug' => 'shoes',
            'status' => true,
        ]);

        $brand = Brand::query()->create([
            'name' => ['en' => 'Demo', 'ar' => 'تجريبي'],
            'slug' => 'demo',
            'status' => true,
        ]);

        return [$category, $brand];
    }

    private function product(Category $category, Brand $brand, string $name, bool $active): Product
    {
        return Product::query()->create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'name' => ['en' => $name, 'ar' => $name],
            'small_desc' => ['en' => 'Description', 'ar' => 'وصف'],
            'desc' => ['en' => 'Description', 'ar' => 'وصف'],
            'status' => $active,
            'sku' => str($name)->slug()->toString(),
            'price' => 100,
            'manage_stock' => false,
            'available_in_stock' => true,
        ]);
    }
}
