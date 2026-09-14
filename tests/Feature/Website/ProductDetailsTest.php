<?php

namespace Tests\Feature\Website;

use App\Models\Dashboard\Brand;
use App\Models\Dashboard\Category;
use App\Models\Dashboard\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Tests\TestCase;

class ProductDetailsTest extends TestCase
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

    public function test_active_product_details_are_available(): void
    {
        $product = $this->product(true);

        $this->get(route('website.products.show', $product->slug))
            ->assertOk()
            ->assertSee($product->name);
    }

    public function test_inactive_product_details_return_not_found(): void
    {
        $product = $this->product(false);

        $this->get(route('website.products.show', $product->slug))
            ->assertNotFound();
    }

    private function product(bool $active): Product
    {
        $category = Category::query()->create([
            'name' => ['en' => 'Accessories', 'ar' => 'إكسسوارات'],
            'slug' => 'accessories',
            'status' => true,
        ]);

        $brand = Brand::query()->create([
            'name' => ['en' => 'Example', 'ar' => 'مثال'],
            'slug' => 'example',
            'status' => true,
        ]);

        return Product::query()->create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'name' => ['en' => $active ? 'Visible product' : 'Hidden product', 'ar' => 'منتج'],
            'small_desc' => ['en' => 'Short description', 'ar' => 'وصف قصير'],
            'desc' => ['en' => 'Full description', 'ar' => 'وصف كامل'],
            'status' => $active,
            'sku' => $active ? 'VISIBLE-1' : 'HIDDEN-1',
            'price' => 150,
            'manage_stock' => false,
            'available_in_stock' => true,
        ]);
    }
}
