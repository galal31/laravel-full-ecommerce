<?php

namespace Tests\Feature\Website;

use App\Models\Dashboard\Brand;
use App\Models\Dashboard\Category;
use App\Models\Dashboard\Product;
use App\Models\User;
use App\Services\website\WishlistService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WishlistTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_toggles_one_wishlist_row_for_the_user(): void
    {
        $user = User::factory()->create();
        $product = $this->product();
        $service = app(WishlistService::class);

        $this->assertTrue($service->toggle($user->id, $product->id));
        $this->assertDatabaseHas('wishlists', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $this->assertFalse($service->toggle($user->id, $product->id));
        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_catalog_ajax_dispatches_the_livewire_counter_event(): void
    {
        $view = file_get_contents(
            resource_path('views/website/products.blade.php')
        );

        $this->assertStringContainsString('Livewire.dispatch', $view);
        $this->assertStringContainsString('wishlist_item_added', $view);
        $this->assertStringContainsString('wishlist_item_removed', $view);
    }

    private function product(): Product
    {
        $category = Category::query()->create([
            'name' => ['en' => 'Wishlist category', 'ar' => 'فئة المفضلة'],
            'slug' => 'wishlist-category',
            'status' => true,
        ]);

        $brand = Brand::query()->create([
            'name' => ['en' => 'Wishlist brand', 'ar' => 'براند المفضلة'],
            'slug' => 'wishlist-brand',
            'status' => true,
        ]);

        return Product::query()->create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'name' => ['en' => 'Wishlist product', 'ar' => 'منتج المفضلة'],
            'status' => true,
            'sku' => 'WISHLIST-1',
            'price' => 100,
            'manage_stock' => false,
            'available_in_stock' => true,
        ]);
    }
}
