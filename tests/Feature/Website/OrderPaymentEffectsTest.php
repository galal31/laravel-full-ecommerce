<?php

namespace Tests\Feature\Website;

use App\Models\Dashboard\Category;
use App\Models\Dashboard\Coupon;
use App\Models\Dashboard\Order;
use App\Models\Dashboard\Product;
use App\Models\Dashboard\ProductVariant;
use App\Models\Dashboard\Transaction;
use App\Models\User;
use App\Services\website\CouponService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class OrderPaymentEffectsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.myfatoorah.token' => 'test-token', 'services.myfatoorah.currency' => 'EGP']);
        Http::preventStrayRequests();
    }

    public function test_success_consumes_reservation_and_stock_once_even_after_delivery(): void
    {
        $coupon = $this->coupon();
        $product = $this->product();
        $order = $this->order($product, $coupon);
        $coupon->update(['is_active' => false, 'expire_date' => now()->subDay()->toDateString()]);

        $this->sendPaymentCallback($order)->assertRedirect(route('website.checkout.success', ['order' => $order->id]));
        $this->sendPaymentCallback($order)->assertRedirect();
        $order->update(['status' => 'delivered']);
        $this->sendPaymentCallback($order)->assertRedirect();

        $this->assertEquals(3, $product->fresh()->quantity);
        $this->assertEquals(4, $coupon->fresh()->times_used);
        $this->assertEquals(0, $coupon->fresh()->reserved_uses);
        $this->assertSame('used', $order->fresh()->coupon_usage_status);
        $this->assertSame('delivered', $order->fresh()->status);
        $this->assertDatabaseHas('transactions', ['order_id' => $order->id, 'transaction_id' => '456']);
    }

    public function test_variant_stock_is_decreased_without_changing_parent_quantity(): void
    {
        $product = $this->product();
        $variant = $product->variants()->create(['price' => 100, 'stock' => 5]);
        $order = $this->order($product, variant: $variant);

        $this->sendPaymentCallback($order)->assertRedirect();
        $this->sendPaymentCallback($order)->assertRedirect();

        $this->assertEquals(3, $variant->fresh()->stock);
        $this->assertEquals(5, $product->fresh()->quantity);
        $this->assertSame('completed', $order->fresh()->status);
    }

    public function test_simple_product_without_stock_management_is_not_decreased(): void
    {
        $product = $this->product(['manage_stock' => false]);
        $order = $this->order($product);

        $this->sendPaymentCallback($order)->assertRedirect();

        $this->assertEquals(5, $product->fresh()->quantity);
        $this->assertSame('completed', $order->fresh()->status);
    }

    #[DataProvider('failureStatuses')]
    public function test_failed_or_canceled_attempt_keeps_reservation_even_when_callback_is_repeated(
        string $status, string $deadline
    ): void
    {
        $this->travelTo(Carbon::parse('2026-10-11 10:05:00', 'UTC'));
        $coupon = $this->coupon();
        $product = $this->product();
        $order = $this->order($product, $coupon);
        $order->update(['payment_expires_at' => $deadline]);

        $this->sendPaymentCallback($order, $status, 'PENDING')->assertStatus(422);
        $this->sendPaymentCallback($order, $status, 'PENDING')->assertStatus(422)
            ->assertSee(__('checkout_success.payment_unconfirmed'));

        $this->assertEquals(1, $coupon->fresh()->reserved_uses);
        $this->assertEquals(3, $coupon->fresh()->times_used);
        $this->assertSame('reserved', $order->fresh()->coupon_usage_status);
        $this->assertSame('pending', $order->fresh()->status);
        $this->assertNull($order->fresh()->payment_review_reason);
        $this->assertEquals(5, $product->fresh()->quantity);
        $this->assertDatabaseHas('transactions', ['order_id' => $order->id, 'transaction_id' => null]);
    }

    public static function failureStatuses(): array
    {
        $cases = [];
        foreach (['FAILED', 'CANCELED'] as $status) {
            foreach ([
                'within deadline' => '2026-10-11 10:30:00',
                'at deadline' => '2026-10-11 10:05:00',
                'after deadline' => '2026-10-11 10:04:59',
                'legacy default deadline' => '2000-01-01 00:00:00',
            ] as $name => $deadline) {
                $cases[$status.' '.$name] = [$status, $deadline];
            }
        }

        return $cases;
    }

    #[DataProvider('unconfirmedStatuses')]
    public function test_pending_or_authorized_payment_does_not_release_reservation(string $status): void
    {
        $coupon = $this->coupon();
        $order = $this->order($this->product(), $coupon);

        $this->sendPaymentCallback($order, $status, 'PENDING')->assertStatus(422);

        $this->assertEquals(1, $coupon->fresh()->reserved_uses);
        $this->assertSame('reserved', $order->fresh()->coupon_usage_status);
    }

    public static function unconfirmedStatuses(): array
    {
        return [['INPROGRESS'], ['AUTHORIZE'], ['UNKNOWN']];
    }

    public function test_provider_connection_failure_does_not_release_reservation(): void
    {
        $coupon = $this->coupon();
        $this->order($this->product(), $coupon);
        Http::fake(fn () => throw new ConnectionException('Timeout'));

        $this->get('/payment/callback?paymentId=999')->assertStatus(422);

        $this->assertEquals(1, $coupon->fresh()->reserved_uses);
    }

    public function test_unrelated_invoice_and_wrong_amount_cannot_release_a_reservation(): void
    {
        $coupon = $this->coupon();
        $order = $this->order($this->product(), $coupon);
        foreach ([['Id' => 'other'], ['amount' => 1], ['currency' => 'USD']] as $change) {
            $response = $this->response($order, 'FAILED', 'PENDING');
            if (isset($change['Id'])) {
                $response['Data']['Invoice']['Id'] = $change['Id'];
            } elseif (isset($change['amount'])) {
                $response['Data']['Amount']['ValueInDisplayCurrency'] = $change['amount'];
            } else {
                $response['Data']['Amount']['DisplayCurrency'] = $change['currency'];
            }
            $this->fakePaymentResponse($response);
            $this->get('/payment/callback?paymentId=999')->assertStatus(422);
            $this->assertEquals(1, $coupon->fresh()->reserved_uses);
            $this->assertSame('reserved', $order->fresh()->coupon_usage_status);
        }
    }

    public function test_an_old_failed_attempt_cannot_release_a_successful_payment(): void
    {
        $coupon = $this->coupon();
        $order = $this->order($this->product(), $coupon);
        $this->sendPaymentCallback($order)->assertRedirect();

        $this->sendPaymentCallback($order, 'FAILED', 'PENDING')->assertRedirect();
        $this->sendPaymentCallback($order, 'FAILED', 'PAID')->assertStatus(422);

        $this->assertEquals(4, $coupon->fresh()->times_used);
        $this->assertEquals(0, $coupon->fresh()->reserved_uses);
        $this->assertSame('used', $order->fresh()->coupon_usage_status);
        $this->assertSame('completed', $order->fresh()->status);
    }

    public function test_success_after_failure_consumes_the_kept_reservation_once(): void
    {
        $coupon = $this->coupon();
        $order = $this->order($this->product(), $coupon);
        $this->sendPaymentCallback($order, 'FAILED', 'PENDING')->assertStatus(422);
        $this->assertSame('reserved', $order->fresh()->coupon_usage_status);
        $this->assertEquals(1, $coupon->fresh()->reserved_uses);

        $this->sendPaymentCallback($order)->assertRedirect();
        $this->sendPaymentCallback($order)->assertRedirect();

        $this->assertSame('completed', $order->fresh()->status);
        $this->assertSame('used', $order->fresh()->coupon_usage_status);
        $this->assertEquals(4, $coupon->fresh()->times_used);
        $this->assertEquals(0, $coupon->fresh()->reserved_uses);
    }

    public function test_late_success_does_not_take_another_orders_reserved_coupon_slot(): void
    {
        $coupon = $this->coupon();
        $product = $this->product();
        $order = $this->order($product, $coupon);
        // نجهز حجزًا محررًا صراحةً؛ فشل المحاولة لم يعد يحرر الحجز تلقائيًا.
        DB::transaction(fn () => app(CouponService::class)->releaseCouponReservation($order));
        $this->order($this->product(), $coupon);
        $coupon->refresh()->update(['reserved_uses' => 1]);

        $this->sendPaymentCallback($order)->assertRedirect();
        $this->sendPaymentCallback($order)->assertRedirect();
        $this->sendPaymentCallback($order, 'FAILED', 'PENDING')->assertRedirect();

        $this->assertSame('coupon_unavailable', $order->fresh()->payment_review_reason);
        $this->assertSame('pending', $order->fresh()->status);
        $this->assertSame('released', $order->fresh()->coupon_usage_status);
        $this->assertEquals(1, $coupon->fresh()->reserved_uses);
        $this->assertEquals(3, $coupon->fresh()->times_used);
        $this->assertEquals(5, $product->fresh()->quantity);
        $this->assertDatabaseHas('transactions', ['order_id' => $order->id, 'transaction_id' => '456']);
    }

    public function test_stock_shortage_records_paid_review_without_negative_stock_or_duplicate_usage(): void
    {
        $coupon = $this->coupon();
        $product = $this->product(['quantity' => 1]);
        $order = $this->order($product, $coupon);

        $this->sendPaymentCallback($order)->assertRedirect();
        $this->sendPaymentCallback($order)->assertRedirect();

        $this->assertEquals(1, $product->fresh()->quantity);
        $this->assertEquals(4, $coupon->fresh()->times_used);
        $this->assertEquals(0, $coupon->fresh()->reserved_uses);
        $this->assertSame('stock_unavailable', $order->fresh()->payment_review_reason);
        $this->assertSame('pending', $order->fresh()->status);
        $this->assertDatabaseHas('transactions', ['order_id' => $order->id, 'transaction_id' => '456']);

        $product->update(['quantity' => 5]);
        $this->sendPaymentCallback($order)->assertRedirect();
        $this->assertEquals(3, $product->fresh()->quantity);
        $this->assertEquals(4, $coupon->fresh()->times_used);
        $this->assertSame('completed', $order->fresh()->status);
        $this->assertNull($order->fresh()->payment_review_reason);
    }

    public function test_all_stock_decreases_roll_back_if_a_later_item_has_insufficient_stock(): void
    {
        $product = $this->product();
        $order = $this->order($product);
        $this->item($order, $this->product(['quantity' => 1]));

        $this->sendPaymentCallback($order)->assertRedirect();

        $this->assertEquals(5, $product->fresh()->quantity);
        $this->assertSame('stock_unavailable', $order->fresh()->payment_review_reason);
    }

    public function test_duplicate_item_rows_cannot_make_stock_negative(): void
    {
        $product = $this->product(['quantity' => 3]);
        $order = $this->order($product);
        $this->item($order, $product);

        $this->sendPaymentCallback($order)->assertRedirect();

        $this->assertEquals(3, $product->fresh()->quantity);
        $this->assertSame('stock_unavailable', $order->fresh()->payment_review_reason);
    }

    public function test_deleted_variant_does_not_decrease_the_simple_product_quantity(): void
    {
        $product = $this->product();
        $variant = $product->variants()->create(['price' => 100, 'stock' => 5]);
        $order = $this->order($product, variant: $variant);
        $variant->delete();

        $this->sendPaymentCallback($order)->assertRedirect();

        $this->assertEquals(5, $product->fresh()->quantity);
        $this->assertSame('stock_unavailable', $order->fresh()->payment_review_reason);
    }

    public function test_wrong_success_amount_cannot_change_stock_or_coupon_counters(): void
    {
        $coupon = $this->coupon();
        $product = $this->product();
        $order = $this->order($product, $coupon);
        $response = $this->response($order);
        $response['Data']['Amount']['ValueInDisplayCurrency'] = 1;
        $this->fakePaymentResponse($response);

        $this->get('/payment/callback?paymentId=999')->assertStatus(422);

        $this->assertEquals(5, $product->fresh()->quantity);
        $this->assertEquals(1, $coupon->fresh()->reserved_uses);
        $this->assertEquals(3, $coupon->fresh()->times_used);
    }

    private function coupon(): Coupon
    {
        return Coupon::create([
            'code' => uniqid('SAVE'), 'discount_percentage' => 10,
            'start_date' => now()->subDay()->toDateString(), 'expire_date' => now()->addDay()->toDateString(),
            'limit' => 4, 'times_used' => 3, 'reserved_uses' => 1, 'is_active' => true,
        ]);
    }

    private function product(array $attributes = []): Product
    {
        $category = Category::create(['name' => ['en' => uniqid('Category')], 'slug' => uniqid('category-')]);

        return Product::create(array_merge([
            'category_id' => $category->id, 'name' => ['ar' => 'منتج', 'en' => 'Product'],
            'sku' => uniqid('sku-'), 'price' => 100, 'manage_stock' => true, 'quantity' => 5,
        ], $attributes));
    }

    private function order(Product $product, ?Coupon $coupon = null, ?ProductVariant $variant = null): Order
    {
        $order = Order::create([
            'user_id' => User::factory()->create()->id, 'user_name' => 'Test',
            'user_phone' => '01000000000', 'user_email' => 'test@example.test',
            'price' => 200, 'shipping_price' => 0, 'total_price' => $coupon ? 180 : 200, 'note' => '',
            'status' => 'pending', 'country' => 'Egypt', 'governorate' => 'Cairo', 'city' => 'Cairo', 'street' => 'Test',
            'coupon_id' => $coupon?->id, 'coupon_code' => $coupon?->code,
            'coupon_usage_status' => $coupon ? 'reserved' : null,
        ]);
        $this->item($order, $product, $variant);
        Transaction::create(['order_id' => $order->id, 'invoice_id' => (string) $order->id, 'payment_method' => 'myfatoorah']);

        return $order;
    }

    private function item(Order $order, Product $product, ?ProductVariant $variant = null): void
    {
        $order->items()->create([
            'product_id' => $product->id, 'product_variant_id' => $variant?->id,
            'product_name' => ['en' => 'Product'], 'product_quantity' => 2, 'product_price' => 100,
            'data' => $variant ? ['ar' => ['اللون' => 'أحمر'], 'en' => ['Color' => 'Red']] : null,
        ]);
    }

    private function response(Order $order, string $status = 'SUCCESS', string $invoiceStatus = 'PAID'): array
    {
        return ['IsSuccess' => true, 'Data' => [
            'Invoice' => ['Id' => (string) $order->id, 'Status' => $invoiceStatus],
            'Transaction' => ['Id' => '456', 'Status' => $status],
            'Amount' => ['DisplayCurrency' => 'EGP', 'ValueInDisplayCurrency' => $order->total_price],
        ]];
    }

    private function sendPaymentCallback(Order $order, string $status = 'SUCCESS', string $invoiceStatus = 'PAID')
    {
        $this->fakePaymentResponse($this->response($order, $status, $invoiceStatus));

        return $this->get('/payment/callback?paymentId=999');
    }

    private function fakePaymentResponse(array $response): void
    {
        // كل محاولة تستخدم نتيجتها الجديدة، دون الاحتفاظ برد محاولة سابقة.
        Http::swap(new Factory);
        Http::preventStrayRequests();
        Http::fake(['*/v3/payments/999' => Http::response($response)]);
    }
}
