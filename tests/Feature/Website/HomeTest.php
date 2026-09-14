<?php

namespace Tests\Feature\Website;

use App\Models\Dashboard\Brand;
use App\Models\Dashboard\Category;
use App\Models\Dashboard\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_displays_active_catalog_sections(): void
    {
        $this->withoutMiddleware([
            LocaleSessionRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ]);

        $category = Category::query()->create([
            'name' => ['en' => 'Home category', 'ar' => 'فئة الرئيسية'],
            'slug' => 'home-category',
            'status' => true,
        ]);

        $brand = Brand::query()->create([
            'name' => ['en' => 'Home brand', 'ar' => 'براند الرئيسية'],
            'slug' => 'home-brand',
            'status' => true,
        ]);

        $product = Product::query()->create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'name' => ['en' => 'Newest home product', 'ar' => 'أحدث منتج'],
            'status' => true,
            'sku' => 'HOME-1',
            'price' => 250,
            'manage_stock' => false,
            'available_in_stock' => true,
        ]);

        $this->get(route('website.home'))
            ->assertOk()
            ->assertSee($category->name)
            ->assertSee($brand->name)
            ->assertSee($product->name);
    }
}
