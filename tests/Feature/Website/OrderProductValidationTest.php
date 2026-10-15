<?php

namespace Tests\Feature\Website;

use App\Models\Cart;
use App\Models\Dashboard\Category;
use App\Models\Dashboard\Product;
use App\Models\User;
use App\Services\website\CheckoutService;
use App\Services\website\CouponService;
use App\Services\website\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Mockery;
use Tests\TestCase;

class OrderProductValidationTest extends TestCase
{
    use RefreshDatabase;

    private Cart $cart;
    private OrderService $service;
    private int $initialTransactionLevel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->initialTransactionLevel = DB::transactionLevel();
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->cart = Cart::create(['user_id' => $user->id]);
        // هذا الاختبار يركز على المنتجات؛ تفاصيل العنوان لها تحقق منفصل.
        $this->service = Mockery::mock(OrderService::class, [app(CheckoutService::class), app(CouponService::class)])->makePartial();
        $this->service->shouldReceive('getShippingDetailes')->andReturn([
            'country' => 'Egypt', 'governorate' => 'Cairo', 'city' => 'Cairo', 'street' => 'Test',
        ]);
    }

    public function test_valid_simple_product_is_saved_with_current_price(): void
    {
        $product = $this->product();
        $this->item($product, 2);
        $this->cart->load('items.product');
        $product->update(['price' => 150]);

        $order = $this->service->storeOrder($this->requestData());

        $this->assertEquals(300, $order->price);
        $this->assertDatabaseHas('order_items', ['order_id' => $order->id, 'product_price' => 150, 'product_quantity' => 2]);
        $this->assertDatabaseCount('cart_items', 0);
        $this->assertEquals(2, $product->fresh()->quantity);
    }

    public function test_empty_or_missing_cart_cannot_create_order(): void
    {
        foreach ([false, true] as $deleteCart) {
            if ($deleteCart) {
                $this->cart->delete();
            }
            try {
                $this->service->storeOrder($this->requestData());
                $this->fail('Empty cart must be rejected.');
            } catch (ValidationException $e) {
                $this->assertSame(__('website.cart_empty'), $e->errors()['cart'][0]);
                $this->assertDatabaseCount('orders', 0);
                $this->assertSame($this->initialTransactionLevel, DB::transactionLevel());
            }
        }
    }

    public function test_product_disabled_after_cart_was_loaded_is_rejected(): void
    {
        $product = $this->product();
        $this->item($product, 1);
        $this->cart->load('items.product');
        $product->update(['status' => false]);
        $this->assertRejected('product_not_available');
    }

    public function test_current_stock_is_checked_and_cart_is_preserved_on_failure(): void
    {
        $product = $this->product();
        $this->item($product, 2);
        $this->cart->load('items.product');
        $product->update(['quantity' => 1]);
        $this->assertRejected('not_enough_stock');
        $this->assertDatabaseCount('cart_items', 1);
    }

    public function test_zero_quantity_is_rejected(): void
    {
        $this->item($this->product(), 0);
        $this->assertRejected('invalid_cart_quantity');
    }

    public function test_variant_from_another_product_is_rejected(): void
    {
        $variant = $this->product()->variants()->create(['price' => 100, 'stock' => 2]);
        $this->item($this->product(), 1, $variant->id);
        $this->assertRejected('selected_variant_not_found');
    }

    public function test_product_with_variants_requires_a_selected_variant(): void
    {
        $product = $this->product();
        $product->variants()->create(['price' => 100, 'stock' => 2]);
        $this->item($product, 1);
        $this->assertRejected('select_variant_to_add_to_cart');
    }

    public function test_variant_quantity_cannot_exceed_its_stock(): void
    {
        $product = $this->product();
        $variant = $product->variants()->create(['price' => 100, 'stock' => 2]);
        $this->item($product, 3, $variant->id);
        $this->assertRejected('not_enough_stock');
    }

    public function test_valid_variant_uses_variant_stock_and_price(): void
    {
        $product = $this->product(['quantity' => 0]);
        $variant = $product->variants()->create(['price' => 125, 'stock' => 2]);
        $this->item($product, 2, $variant->id);
        $order = $this->service->createOrder($this->requestData());
        $this->assertEquals(250, $order->price);
        $this->assertDatabaseHas('order_items', ['order_id' => $order->id, 'product_variant_id' => $variant->id]);
    }

    private function assertRejected(string $messageKey): void
    {
        try {
            $this->service->createOrder($this->requestData());
            $this->fail('Invalid products must be rejected.');
        } catch (ValidationException $e) {
            $this->assertSame(__('website.'.$messageKey), $e->errors()['cart'][0]);
            $this->assertDatabaseCount('orders', 0);
            $this->assertDatabaseCount('order_items', 0);
            $this->assertSame($this->initialTransactionLevel, DB::transactionLevel());
        }
    }

    private function product(array $attributes = []): Product
    {
        $category = Category::create(['name' => ['en' => uniqid('Category')], 'slug' => uniqid('category-')]);
        return Product::create(array_merge([
            'category_id' => $category->id, 'name' => ['ar' => 'منتج', 'en' => 'Product'],
            'sku' => uniqid('sku-'), 'price' => 100, 'status' => true,
            'manage_stock' => true, 'quantity' => 2, 'available_in_stock' => true,
        ], $attributes));
    }

    private function item(Product $product, int $quantity, ?int $variantId = null): void
    {
        $this->cart->items()->create([
            'product_id' => $product->id, 'product_variant_id' => $variantId, 'quantity' => $quantity,
        ]);
    }

    private function requestData(): array
    {
        return ['user_name' => 'Test', 'user_phone' => '01000000000', 'user_email' => 'test@example.test',
            'governorate_id' => 1, 'note' => ''];
    }
}
