<?php

namespace Tests\Feature\Website;

use App\Models\Dashboard\Category;
use App\Models\Dashboard\Coupon;
use App\Models\Dashboard\Order;
use App\Models\Dashboard\Product;
use App\Models\Dashboard\Transaction;
use App\Models\User;
use App\Services\website\OrderPaymentService;
use App\Services\website\OrderService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use Tests\TestCase;

class OrderPaymentReconciliationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.myfatoorah.token' => 'test-token', 'services.myfatoorah.currency' => 'EGP']);
        Http::preventStrayRequests();
        $this->travelTo(Carbon::parse('2026-10-11 10:30:00', 'UTC'));
    }

    #[DataProvider('unpaidInvoices')]
    public function test_expired_unpaid_invoice_releases_only_once(string $status, array $attempts): void
    {
        [$order, $coupon, $product] = $this->order();
        $this->fakeInvoice($order, $status, $attempts);

        $this->assertSame('released', $this->check($order));
        $this->assertSame('skipped', $this->check($order));
        $this->assertSame('released', $order->fresh()->coupon_usage_status);
        $this->assertSame('pending', $order->fresh()->status);
        $this->assertEquals(0, $coupon->fresh()->reserved_uses);
        $this->assertEquals(3, $coupon->fresh()->times_used);
        $this->assertEquals(5, $product->fresh()->quantity);
        $this->assertEquals(180, $order->fresh()->total_price);
        Http::assertSentCount(1);
    }

    public static function unpaidInvoices(): array
    {
        return [
            'no attempt' => ['Pending', []],
            'failed attempts' => ['Pending', [['TransactionStatus' => 'Failed']]],
            'canceled attempt' => ['Pending', [['TransactionStatus' => 'Canceled']]],
            'canceled invoice' => ['Canceled', []],
        ];
    }

    public function test_before_deadline_does_not_query_or_release(): void
    {
        [$order, $coupon] = $this->order();
        $order->update(['payment_expires_at' => now()->addSecond()]);

        $this->assertSame('waiting', $this->check($order));
        $this->assertEquals(1, $coupon->fresh()->reserved_uses);
        Http::assertNothingSent();
    }

    public function test_deadline_migration_backfills_existing_orders_and_requires_a_value(): void
    {
        [$order] = $this->order();
        $migration = require database_path('migrations/2026_10_11_000001_add_payment_expires_at_to_orders_table.php');
        $migration->down();
        $migration->up();

        $this->assertSame('2000-01-01 00:00:00', $order->fresh()->payment_expires_at->toDateTimeString());
        $column = collect(Schema::getColumns('orders'))->firstWhere('name', 'payment_expires_at');
        $this->assertFalse($column['nullable']);

        $attributes = $order->getAttributes();
        unset($attributes['id'], $attributes['payment_expires_at']);
        $newOrder = Order::create($attributes);
        $this->assertSame('2000-01-01 00:00:00', $newOrder->fresh()->payment_expires_at->toDateTimeString());
    }

    public function test_order_deadline_cannot_be_set_to_null(): void
    {
        [$order] = $this->order();

        $this->expectException(QueryException::class);
        $order->update(['payment_expires_at' => null]);
    }

    #[DataProvider('unconfirmedInvoices')]
    public function test_processing_or_unknown_results_keep_reservation(string $status, array $attempts, string $result): void
    {
        [$order, $coupon] = $this->order();
        $this->fakeInvoice($order, $status, $attempts);

        $this->assertSame($result, $this->check($order));
        $this->assertSame('reserved', $order->fresh()->coupon_usage_status);
        $this->assertEquals(1, $coupon->fresh()->reserved_uses);
    }

    public static function unconfirmedInvoices(): array
    {
        return [
            'processing' => ['Pending', [['TransactionStatus' => 'InProgress']], 'processing'],
            'authorized' => ['Pending', [['TransactionStatus' => 'Authorize']], 'processing'],
            'unknown attempt' => ['Pending', [['TransactionStatus' => 'Unexpected']], 'unknown'],
            'contradictory paid' => ['Paid', [], 'unknown'],
            'success on pending' => ['Pending', [['TransactionStatus' => 'Succss', 'PaymentId' => '07001']], 'unknown'],
        ];
    }

    public function test_paid_without_browser_return_is_verified_and_completed_once(): void
    {
        [$order, $coupon, $product] = $this->order();
        $this->fakePaid($order);

        $this->assertSame('paid', $this->check($order));
        $this->assertSame('skipped', $this->check($order));
        $this->assertSame('completed', $order->fresh()->status);
        $this->assertSame('used', $order->fresh()->coupon_usage_status);
        $this->assertEquals(4, $coupon->fresh()->times_used);
        $this->assertEquals(0, $coupon->fresh()->reserved_uses);
        $this->assertEquals(3, $product->fresh()->quantity);
        $this->assertDatabaseHas('transactions', ['order_id' => $order->id, 'transaction_id' => '456']);
        Http::assertSentCount(2);
    }

    #[DataProvider('mismatchedPayments')]
    public function test_wrong_payment_cannot_fulfill_or_release(string $field, mixed $value): void
    {
        [$order, $coupon, $product] = $this->order();
        $payment = $this->payment($order);
        data_set($payment, $field, $value);
        $this->fakePaid($order, $payment);

        try {
            $this->check($order);
            $this->fail('A mismatched payment must be rejected.');
        } catch (RuntimeException) {
            $this->assertSame('reserved', $order->fresh()->coupon_usage_status);
            $this->assertSame('pending', $order->fresh()->status);
            $this->assertEquals(1, $coupon->fresh()->reserved_uses);
            $this->assertEquals(5, $product->fresh()->quantity);
            $this->assertDatabaseHas('transactions', ['order_id' => $order->id, 'transaction_id' => null]);
        }
    }

    public static function mismatchedPayments(): array
    {
        return [
            'invoice' => ['Data.Invoice.Id', '99999'],
            'payment' => ['Data.Transaction.PaymentId', '07002'],
            'amount' => ['Data.Amount.ValueInDisplayCurrency', 1],
            'currency' => ['Data.Amount.DisplayCurrency', 'USD'],
            'status' => ['Data.Transaction.Status', 'FAILED'],
        ];
    }

    public function test_paid_stock_shortage_keeps_confirmed_coupon_and_records_review(): void
    {
        [$order, $coupon, $product] = $this->order();
        $product->update(['quantity' => 1]);
        $this->fakePaid($order);

        $this->assertSame('review', $this->check($order));
        $this->assertSame('skipped', $this->check($order));
        $this->assertSame('stock_unavailable', $order->fresh()->payment_review_reason);
        $this->assertSame('used', $order->fresh()->coupon_usage_status);
        $this->assertEquals(4, $coupon->fresh()->times_used);
        $this->assertEquals(0, $coupon->fresh()->reserved_uses);
        $this->assertEquals(1, $product->fresh()->quantity);
    }

    public function test_recorded_payment_saves_review_reason_without_releasing_or_fulfilling(): void
    {
        [$order, $coupon, $product] = $this->order();
        Transaction::where('order_id', $order->id)->update(['transaction_id' => '456']);

        $this->artisan('orders:check-coupon-reservations')
            ->expectsOutput("Order {$order->id}: review")->assertSuccessful();

        $this->assertSame('payment_recorded_order_pending', $order->fresh()->payment_review_reason);
        $this->assertSame('pending', $order->fresh()->status);
        $this->assertSame('reserved', $order->fresh()->coupon_usage_status);
        $this->assertEquals(1, $coupon->fresh()->reserved_uses);
        $this->assertEquals(3, $coupon->fresh()->times_used);
        $this->assertEquals(5, $product->fresh()->quantity);
        $this->assertDatabaseHas('transactions', ['order_id' => $order->id, 'transaction_id' => '456']);

        $this->artisan('orders:check-coupon-reservations')
            ->doesntExpectOutput("Order {$order->id}: review")->assertSuccessful();
        $this->assertSame('skipped', $this->check($order));
        Http::assertNothingSent();
    }

    #[DataProvider('callbackOutcomesBeforeReview')]
    public function test_callback_before_review_write_is_not_overwritten(int $stock, string $status, ?string $reason): void
    {
        [$order, $coupon, $product] = $this->order();
        $product->update(['quantity' => $stock]);
        Transaction::where('order_id', $order->id)->update(['transaction_id' => '456']);
        $dispatcher = Order::getEventDispatcher();
        Order::setEventDispatcher(clone $dispatcher);
        $callbackHandled = false;

        try {
            // نحاكي وصول العودة بعد قراءة الطلب الأولى وقبل حفظ سبب المراجعة.
            Order::retrieved(function (Order $retrievedOrder) use ($order, &$callbackHandled) {
                if ($retrievedOrder->id !== $order->id || $callbackHandled) {
                    return;
                }
                $callbackHandled = true;
                app(OrderService::class)->completePayment([
                    'invoice_id' => (string) $order->id, 'transaction_id' => '456',
                    'amount' => 180, 'currency' => 'EGP',
                ]);
            });

            $this->assertSame('skipped', $this->check($order));
        } finally {
            Order::setEventDispatcher($dispatcher);
        }

        $this->assertTrue($callbackHandled);
        $this->assertSame($status, $order->fresh()->status);
        $this->assertSame($reason, $order->fresh()->payment_review_reason);
        $this->assertSame('used', $order->fresh()->coupon_usage_status);
        $this->assertEquals(0, $coupon->fresh()->reserved_uses);
        $this->assertEquals(4, $coupon->fresh()->times_used);
        $this->assertEquals($stock >= 2 ? $stock - 2 : $stock, $product->fresh()->quantity);
        Http::assertNothingSent();
    }

    public static function callbackOutcomesBeforeReview(): array
    {
        return [
            'completed payment' => [5, 'completed', null],
            'stock review' => [1, 'pending', 'stock_unavailable'],
        ];
    }

    #[DataProvider('missingInvoiceInformation')]
    public function test_missing_or_changed_invoice_information_never_releases(string $change): void
    {
        [$order, $coupon] = $this->order();
        $transaction = Transaction::where('order_id', $order->id)->firstOrFail();
        match ($change) {
            'no transaction' => $transaction->delete(),
            'no invoice' => $transaction->update(['invoice_id' => null]),
            'legacy default order deadline' => $order->update(['payment_expires_at' => '2000-01-01 00:00:00']),
            'legacy invoice' => $transaction->update(['invoice_expires_at' => null]),
            'different invoice deadline' => $transaction->update(['invoice_expires_at' => now()->addHour()]),
            'multiple invoices' => Transaction::create(['order_id' => $order->id, 'invoice_id' => '99999', 'payment_method' => 'myfatoorah']),
        };
        $this->fakeInvoice($order);

        $this->assertSame('unknown', $this->check($order));
        $this->assertEquals(1, $coupon->fresh()->reserved_uses);
        $this->assertSame('reserved', $order->fresh()->coupon_usage_status);
    }

    public static function missingInvoiceInformation(): array
    {
        return array_map(fn ($change) => [$change], [
            'no transaction', 'no invoice', 'legacy default order deadline', 'legacy invoice',
            'different invoice deadline', 'multiple invoices',
        ]);
    }

    #[DataProvider('badGatewayResponses')]
    public function test_bad_gateway_response_preserves_reservation(string $mode): void
    {
        [$order, $coupon] = $this->order();
        Http::fake(function () use ($order, $mode) {
            if ($mode === 'timeout') {
                throw new ConnectionException('Timeout');
            }

            return match ($mode) {
                'missing' => Http::response(['IsSuccess' => false, 'Data' => null], 400),
                'wrong invoice' => Http::response($this->invoice($order, invoiceId: '99999')),
                'malformed' => Http::response(['IsSuccess' => true, 'Data' => []]),
            };
        });

        try {
            $this->check($order);
            $this->fail('Invalid inquiry must be rejected.');
        } catch (ConnectionException|RuntimeException) {
            $this->assertEquals(1, $coupon->fresh()->reserved_uses);
            $this->assertSame('reserved', $order->fresh()->coupon_usage_status);
        }
    }

    public static function badGatewayResponses(): array
    {
        return [['timeout'], ['missing'], ['wrong invoice'], ['malformed']];
    }

    public function test_success_callback_during_inquiry_wins_over_unpaid_snapshot(): void
    {
        [$order, $coupon, $product] = $this->order();
        $testTransactionLevel = DB::transactionLevel();
        Http::fake(function () use ($order, $testTransactionLevel) {
            // RefreshDatabase يلف الاختبار في معاملة؛ الخدمة لا تضيف معاملة أثناء الشبكة.
            $this->assertSame($testTransactionLevel, DB::transactionLevel());
            app(OrderService::class)->completePayment([
                'invoice_id' => (string) $order->id, 'transaction_id' => '456',
                'amount' => 180, 'currency' => 'EGP',
            ]);

            return Http::response($this->invoice($order));
        });

        $this->assertSame('skipped', $this->check($order));
        $this->assertSame('used', $order->fresh()->coupon_usage_status);
        $this->assertEquals(4, $coupon->fresh()->times_used);
        $this->assertEquals(0, $coupon->fresh()->reserved_uses);
        $this->assertEquals(3, $product->fresh()->quantity);
    }

    public function test_changed_deadline_during_inquiry_prevents_release(): void
    {
        [$order, $coupon] = $this->order();
        Http::fake(function () use ($order) {
            Order::whereKey($order->id)->update(['payment_expires_at' => now()->addHour()]);

            return Http::response($this->invoice($order));
        });

        $this->assertSame('unknown', $this->check($order));
        $this->assertEquals(1, $coupon->fresh()->reserved_uses);
    }

    public function test_inconsistent_coupon_counter_rolls_back_release(): void
    {
        [$order, $coupon] = $this->order();
        $coupon->update(['reserved_uses' => 0]);
        $this->fakeInvoice($order);

        $this->expectException(RuntimeException::class);
        try {
            $this->check($order);
        } finally {
            $this->assertSame('reserved', $order->fresh()->coupon_usage_status);
            $this->assertEquals(0, $coupon->fresh()->reserved_uses);
        }
    }

    public function test_command_continues_after_one_invoice_fails_and_only_selects_due_reservations(): void
    {
        [$failedOrder, $failedCoupon] = $this->order();
        [$releasedOrder, $releasedCoupon] = $this->order();
        [$futureOrder] = $this->order();
        $futureOrder->update(['payment_expires_at' => now()->addMinute()]);
        [$completedOrder] = $this->order();
        $completedOrder->update(['status' => 'completed']);
        [$reviewOrder] = $this->order();
        $reviewOrder->update(['payment_review_reason' => 'stock_unavailable']);
        $attempts = 0;
        Http::fake(function ($request) use ($failedOrder, $releasedOrder, &$attempts) {
            $attempts++;
            if ($request['Key'] === (string) $failedOrder->id) {
                throw new ConnectionException('Timeout');
            }
            $this->assertSame((string) $releasedOrder->id, $request['Key']);

            return Http::response($this->invoice($releasedOrder));
        });

        $this->artisan('orders:check-coupon-reservations')
            ->expectsOutput("Order {$failedOrder->id}: check failed; reservation unchanged.")
            ->expectsOutput("Order {$releasedOrder->id}: released")->assertFailed();
        $this->assertEquals(1, $failedCoupon->fresh()->reserved_uses);
        $this->assertEquals(0, $releasedCoupon->fresh()->reserved_uses);
        $this->assertSame(2, $attempts);
        Http::assertSentCount(1);
    }

    public function test_single_order_option_and_invalid_id(): void
    {
        [$order] = $this->order();
        $this->fakeInvoice($order);
        $this->artisan('orders:check-coupon-reservations', ['--order' => $order->id])
            ->expectsOutput("Order {$order->id}: released")->assertSuccessful();
        $this->artisan('orders:check-coupon-reservations', ['--order' => 'invalid'])->assertFailed();
        Http::assertSentCount(1);
    }

    public function test_scheduler_runs_command_every_minute_without_overlap(): void
    {
        $events = array_filter(app(Schedule::class)->events(),
            fn ($event) => str_contains($event->command ?? '', 'orders:check-coupon-reservations'));
        $this->assertCount(1, $events);
        $event = array_values($events)[0];
        $this->assertSame('* * * * *', $event->expression);
        $this->assertTrue($event->withoutOverlapping);
    }

    private function check(Order $order): string
    {
        return app(OrderPaymentService::class)->checkOrder($order->id);
    }

    private function fakeInvoice(Order $order, string $status = 'Pending', array $attempts = []): void
    {
        Http::fake(['*/v2/GetPaymentStatus' => Http::response($this->invoice($order, $status, $attempts))]);
    }

    private function fakePaid(Order $order, ?array $payment = null): void
    {
        Http::fake([
            '*/v2/GetPaymentStatus' => Http::response($this->invoice($order, 'Paid', [
                ['TransactionStatus' => 'Failed'], ['TransactionStatus' => 'Succss', 'PaymentId' => '07001'],
            ])),
            '*/v3/payments/07001' => Http::response($payment ?? $this->payment($order)),
        ]);
    }

    private function invoice(Order $order, string $status = 'Pending', array $attempts = [], ?string $invoiceId = null): array
    {
        return ['IsSuccess' => true, 'Data' => [
            'InvoiceId' => $invoiceId ?? (string) $order->id, 'InvoiceStatus' => $status,
            // وقت V2 المحلي لا يدخل في قرار انتهاء المهلة.
            'ExpiryDate' => 'October 11, 2026', 'ExpiryTime' => '13:30:00.000',
            'InvoiceTransactions' => $attempts,
        ]];
    }

    private function payment(Order $order): array
    {
        return ['IsSuccess' => true, 'Data' => [
            'Invoice' => ['Id' => (string) $order->id, 'Status' => 'PAID'],
            'Transaction' => ['Id' => '456', 'Status' => 'SUCCESS', 'PaymentId' => '07001'],
            'Amount' => ['DisplayCurrency' => 'EGP', 'ValueInDisplayCurrency' => 180],
        ]];
    }

    private function order(): array
    {
        $coupon = Coupon::create([
            'code' => uniqid('SAVE'), 'discount_percentage' => 10,
            'start_date' => now()->subDay()->toDateString(), 'expire_date' => now()->addDay()->toDateString(),
            'limit' => 4, 'times_used' => 3, 'reserved_uses' => 1, 'is_active' => true,
        ]);
        $category = Category::create(['name' => ['en' => uniqid('Category')], 'slug' => uniqid('category-')]);
        $product = Product::create([
            'category_id' => $category->id, 'name' => ['ar' => 'منتج', 'en' => 'Product'],
            'sku' => uniqid('sku-'), 'price' => 100, 'manage_stock' => true, 'quantity' => 5,
        ]);
        $order = Order::create([
            'user_id' => User::factory()->create()->id, 'user_name' => 'Test',
            'user_phone' => '01000000000', 'user_email' => 'test@example.test',
            'price' => 200, 'shipping_price' => 0, 'total_price' => 180, 'note' => '',
            'status' => 'pending', 'country' => 'Egypt', 'governorate' => 'Cairo', 'city' => 'Cairo', 'street' => 'Test',
            'coupon_id' => $coupon->id, 'coupon_code' => $coupon->code, 'coupon_usage_status' => 'reserved',
            'payment_expires_at' => now(),
        ]);
        $order->items()->create([
            'product_id' => $product->id, 'product_name' => ['en' => 'Product'],
            'product_quantity' => 2, 'product_price' => 100,
        ]);
        Transaction::create([
            'order_id' => $order->id, 'invoice_id' => (string) $order->id,
            'payment_method' => 'myfatoorah', 'invoice_expires_at' => now(),
        ]);

        return [$order, $coupon, $product];
    }
}
