<?php

namespace Tests\Feature\Website;

use App\Models\Dashboard\Brand;
use App\Models\Dashboard\Category;
use App\Models\Dashboard\Product;
use App\Models\User;
use App\Services\website\CartService;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_header_does_not_create_an_empty_cart(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)->test('website.cart-header-icon');

        $this->assertDatabaseCount('carts', 0);
    }

    public function test_adding_the_same_product_updates_one_cart_line(): void
    {
        $user = User::factory()->create();
        $product = $this->product();
        $service = app(CartService::class);

        $service->add($user->id, $product->id, null, 1);
        $service->add($user->id, $product->id, null, 2);

        $this->assertDatabaseCount('carts', 1);
        $this->assertDatabaseCount('cart_items', 1);
        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity' => 3,
        ]);
    }

    public function test_user_cannot_update_another_users_cart_item(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $product = $this->product();
        $service = app(CartService::class);
        $item = $service->add($owner->id, $product->id, null, 1)['item'];

        try {
            $service->increase($otherUser->id, $item->id);
            $this->fail('Another user updated an item they do not own.');
        } catch (DomainException) {
            $this->assertSame(1, $item->fresh()->quantity);
        }
    }

    public function test_variant_quantity_cannot_exceed_variant_stock(): void
    {
        $user = User::factory()->create();
        $product = $this->product();
        $variant = $product->variants()->create([
            'price' => 125,
            'stock' => 2,
        ]);
        $service = app(CartService::class);
        $item = $service->add($user->id, $product->id, $variant->id, 1)['item'];

        $service->increase($user->id, $item->id);

        $this->expectException(DomainException::class);
        $service->increase($user->id, $item->id);
    }

    private function product(): Product
    {
        $category = Category::query()->create([
            'name' => ['en' => 'Cart category', 'ar' => 'فئة السلة'],
            'slug' => 'cart-category-'.str()->random(6),
            'status' => true,
        ]);

        $brand = Brand::query()->create([
            'name' => ['en' => 'Cart brand '.str()->random(6), 'ar' => 'براند السلة'],
            'slug' => 'cart-brand-'.str()->random(6),
            'status' => true,
        ]);

        return Product::query()->create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'name' => ['en' => 'Cart product '.str()->random(6), 'ar' => 'منتج السلة'],
            'status' => true,
            'sku' => 'CART-'.str()->random(8),
            'price' => 100,
            'manage_stock' => true,
            'quantity' => 10,
            'available_in_stock' => true,
        ]);
    }
}
