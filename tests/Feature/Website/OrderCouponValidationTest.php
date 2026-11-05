<?php

namespace Tests\Feature\Website;

use App\Models\Cart;
use App\Models\Dashboard\Category;
use App\Models\Dashboard\Coupon;
use App\Models\Dashboard\Product;
use App\Models\User;
use App\Services\website\CheckoutService;
use App\Services\website\CouponService;
use App\Services\website\OrderService;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class OrderCouponValidationTest extends TestCase
{
    use RefreshDatabase;

    private Cart $cart;
    private OrderService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(now()->setTime(12, 0));
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->cart = Cart::create(['user_id' => $user->id]);
        $category = Category::create(['name' => ['en' => 'Category'], 'slug' => 'category']);
        $product = Product::create([
            'category_id' => $category->id, 'name' => ['ar' => 'منتج', 'en' => 'Product'],
            'sku' => 'coupon-product', 'price' => 100, 'status' => true,
            'manage_stock' => true, 'quantity' => 2, 'available_in_stock' => true,
        ]);
        $this->cart->items()->create(['product_id' => $product->id, 'quantity' => 2]);
        // نختبر إنشاء الطلب الحقيقي؛ نستبدل تفاصيل العنوان فقط كما في اختبارات المنتجات.
        $this->service = Mockery::mock(OrderService::class, [
            app(CheckoutService::class), app(CouponService::class),
        ])->makePartial();
        $this->service->shouldReceive('getShippingDetailes')->andReturn([
            'country' => 'Egypt', 'governorate' => 'Cairo', 'city' => 'Cairo', 'street' => 'Test',
        ]);
    }

    public function test_order_uses_current_coupon_percentage_and_code(): void
    {
        $coupon = $this->applyCoupon();
        $this->cart->load('coupon');
        $coupon->update(['discount_percentage' => 25, 'code' => 'UPDATED']);

        $order = $this->service->createOrder($this->requestData());

        $this->assertDatabaseHas('orders', [
            'id' => $order->id, 'coupon_id' => $coupon->id, 'coupon_code' => 'UPDATED',
            'coupon_discount_percentage' => 25, 'coupon_discount_amount' => 50,
            'price' => 200, 'total_price' => 150,
        ]);
        $this->assertDatabaseCount('order_items', 1);
        $this->assertDatabaseCount('cart_items', 0);
        $this->assertDatabaseHas('carts', ['id' => $this->cart->id, 'coupon_id' => null]);
        $this->assertSame('reserved', $order->coupon_usage_status);
        $this->assertEquals(1, $coupon->fresh()->reserved_uses);
        $this->assertEquals(0, $coupon->fresh()->times_used);
    }

    #[DataProvider('paymentDeadlineCases')]
    public function test_order_saves_a_thirty_minute_payment_deadline(bool $withCoupon, string $createdAt, string $expiresAt): void
    {
        $this->travelTo(Carbon::parse($createdAt, 'UTC'));
        if ($withCoupon) {
            $this->applyCoupon();
        }

        $order = $this->service->createOrder($this->requestData());
        $savedOrder = $order->fresh();

        $this->assertSame($createdAt, $savedOrder->created_at->toDateTimeString());
        $this->assertSame($expiresAt, $savedOrder->payment_expires_at->toDateTimeString());
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'payment_expires_at' => $expiresAt]);
    }

    public static function paymentDeadlineCases(): array
    {
        return [
            'without coupon' => [false, '2026-10-11 10:00:00', '2026-10-11 10:30:00'],
            'with coupon across midnight' => [true, '2026-10-11 23:50:00', '2026-10-12 00:20:00'],
        ];
    }

    #[DataProvider('invalidCouponChanges')]
    public function test_invalid_coupon_after_application_rejects_order_and_preserves_cart(array $changes, string $messageKey): void
    {
        app()->setLocale('ar');
        $coupon = $this->applyCoupon();
        $this->cart->load('coupon');
        $coupon->update($changes);
        $initialTransactionLevel = DB::transactionLevel();

        try {
            $this->service->createOrder($this->requestData());
            $this->fail('An invalid coupon must prevent order creation.');
        } catch (ValidationException $e) {
            $this->assertSame(__('website.'.$messageKey), $e->errors()['coupon'][0]);
            $this->assertDatabaseCount('orders', 0);
            $this->assertDatabaseCount('order_items', 0);
            $this->assertDatabaseCount('cart_items', 1);
            $this->assertEquals($coupon->id, $this->cart->fresh()->coupon_id);
            $this->assertSame($initialTransactionLevel, DB::transactionLevel());
        }
    }

    public static function invalidCouponChanges(): array
    {
        return [
            'inactive' => [['is_active' => false], 'coupon_inactive'],
            'not started' => [['start_date' => '2099-01-01'], 'coupon_not_started'],
            'expired' => [['expire_date' => '2000-01-01'], 'coupon_expired'],
            'usage limit reached' => [['limit' => 2, 'times_used' => 2], 'coupon_usage_limit_reached'],
            'usage limit exceeded' => [['limit' => 2, 'times_used' => 3], 'coupon_usage_limit_reached'],
            'missing percentage' => [['discount_percentage' => null], 'coupon_invalid_percentage'],
            'zero percentage' => [['discount_percentage' => 0], 'coupon_invalid_percentage'],
            'negative percentage' => [['discount_percentage' => -1], 'coupon_invalid_percentage'],
            'percentage above 100' => [['discount_percentage' => 101], 'coupon_invalid_percentage'],
        ];
    }

    public function test_coupon_is_valid_on_start_and_expiry_day_with_unlimited_usage(): void
    {
        $this->applyCoupon([
            'start_date' => now()->toDateString(), 'expire_date' => now()->toDateString(),
            'limit' => null, 'times_used' => 100, 'discount_percentage' => 100,
        ]);

        $order = $this->service->createOrder($this->requestData());

        $this->assertEquals(200, $order->coupon_discount_amount);
        $this->assertEquals(0, $order->total_price);
    }

    public function test_applying_an_invalid_coupon_still_uses_the_same_rules(): void
    {
        $coupon = $this->applyCoupon();
        $coupon->update(['is_active' => false]);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(__('website.coupon_inactive'));
        app(CouponService::class)->validateCoupon($coupon->code);
    }

    public function test_reserved_usage_blocks_another_order_from_using_the_last_slot(): void
    {
        $coupon = $this->applyCoupon(['limit' => 1]);
        $this->service->createOrder($this->requestData());
        $productId = Product::firstOrFail()->id;
        $this->cart->items()->create(['product_id' => $productId, 'quantity' => 1]);
        $this->cart->coupon_id = $coupon->id;
        $this->cart->save();

        try {
            $this->service->createOrder($this->requestData());
            $this->fail('A reserved use must count against the coupon limit.');
        } catch (ValidationException $e) {
            $this->assertSame(__('website.coupon_usage_limit_reached'), $e->errors()['coupon'][0]);
            $this->assertDatabaseCount('orders', 1);
            $this->assertDatabaseCount('cart_items', 1);
            $this->assertEquals(1, $coupon->fresh()->reserved_uses);
            $this->assertEquals(0, $coupon->fresh()->times_used);
        }
    }

    public function test_reservation_rolls_back_when_order_creation_fails(): void
    {
        $coupon = $this->applyCoupon();
        $requestData = $this->requestData();
        $requestData['user_email'] = null;

        try {
            $this->service->createOrder($requestData);
            $this->fail('A database error must roll back the reservation.');
        } catch (\Illuminate\Database\QueryException $e) {
            $this->assertEquals(0, $coupon->fresh()->reserved_uses);
            $this->assertDatabaseCount('orders', 0);
            $this->assertDatabaseCount('cart_items', 1);
            $this->assertDatabaseHas('carts', ['id' => $this->cart->id, 'coupon_id' => $coupon->id]);
        }
    }

    public function test_final_validation_rejects_a_missing_coupon(): void
    {
        $coupon = $this->applyCoupon();
        $couponId = $coupon->id;
        $coupon->delete();

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(__('website.coupon_not_found'));
        DB::transaction(fn () => app(CouponService::class)->validateCouponForOrder($couponId));
    }

    private function applyCoupon(array $attributes = []): Coupon
    {
        $coupon = Coupon::create(array_merge([
            'code' => 'SAVE10', 'discount_percentage' => 10,
            'start_date' => now()->subDay()->toDateString(),
            'expire_date' => now()->addDay()->toDateString(),
            'limit' => 10, 'times_used' => 0, 'is_active' => true,
        ], $attributes));
        app(CouponService::class)->applyCoupon((int) auth()->id(), $coupon->code);

        return $coupon;
    }

    private function requestData(): array
    {
        return [
            'user_name' => 'Test', 'user_phone' => '01000000000', 'user_email' => 'test@example.test',
            'governorate_id' => 1, 'note' => '',
        ];
    }
}
