<?php

namespace Tests\Feature\Dashboard;

use App\Models\Dashboard\Admin;
use App\Models\Dashboard\Category;
use App\Models\Dashboard\Coupon;
use App\Models\Dashboard\Order;
use App\Models\Dashboard\Product;
use App\Models\Dashboard\Role;
use App\Models\Dashboard\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Tests\TestCase;

class OrdersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([LocaleSessionRedirect::class, LaravelLocalizationRedirectFilter::class]);
        Http::preventStrayRequests();
        config(['services.myfatoorah.currency' => 'EGP']);
    }

    public function test_guests_and_customers_cannot_access_the_page_or_data(): void
    {
        $this->get(route('dashboard.orders.index'))->assertRedirect();
        $this->getJson(route('dashboard.orders.index'), ['X-Requested-With' => 'XMLHttpRequest'])->assertUnauthorized();

        $this->actingAs(User::factory()->create());
        $this->get(route('dashboard.orders.index'))->assertRedirect();
        $this->getJson(route('dashboard.orders.index'), ['X-Requested-With' => 'XMLHttpRequest'])->assertUnauthorized();
    }

    public function test_an_admin_without_orders_permission_cannot_access_the_page_or_data(): void
    {
        $this->actingAs($this->admin([]), 'admin');
        $this->get(route('dashboard.orders.index'))->assertForbidden();
        $this->table()->assertForbidden();
        $this->get(route('dashboard.welcome'))->assertOk();
        $this->assertStringNotContainsString(route('dashboard.orders.index'),
            view('layouts.dashboard.partials.sidebar')->render());
    }

    public function test_authorized_admin_can_open_the_page_and_sidebar_link_in_both_languages(): void
    {
        $this->actingAs($this->admin(), 'admin');
        foreach (['ar', 'en'] as $locale) {
            app()->setLocale($locale);
            $this->get(route('dashboard.orders.index'))->assertOk()
                ->assertSee(__('orders.title'))
                ->assertSee('id="orders-table"', false)
                ->assertSee(route('dashboard.orders.index'), false);
        }
    }

    public function test_payment_evidence_is_independent_of_order_status_and_checks_all_transactions(): void
    {
        $this->actingAs($this->admin(), 'admin');
        $pending = $this->order();
        $complete = $this->order(['status' => 'completed']);
        $review = $this->order(['payment_review_reason' => 'stock_unavailable']);
        $invoiceOnly = $this->order();
        foreach ([$complete, $review] as $order) {
            Transaction::create(['order_id' => $order->id, 'payment_method' => 'myfatoorah',
                'invoice_id' => 'invoice-'.$order->id, 'transaction_id' => 'paid-'.$order->id]);
        }
        Transaction::create(['order_id' => $review->id, 'payment_method' => 'myfatoorah',
            'invoice_id' => 'another-invoice', 'transaction_id' => null]);
        Transaction::create(['order_id' => $invoiceOnly->id, 'payment_method' => 'myfatoorah',
            'invoice_id' => 'unpaid-invoice', 'transaction_id' => null]);

        foreach (['ar', 'en'] as $locale) {
            app()->setLocale($locale);
            $rows = collect($this->table()->assertOk()->json('data'))->keyBy('id');
            $this->assertSame(__('orders.payment_unconfirmed'), $rows[$pending->id]['payment_status']);
            $this->assertSame(__('orders.payment_unconfirmed'), $rows[$invoiceOnly->id]['payment_status']);
            $this->assertSame(__('orders.payment_recorded'), $rows[$complete->id]['payment_status']);
            $this->assertSame(__('orders.payment_recorded'), $rows[$review->id]['payment_status']);
            $this->assertStringContainsString(__('orders.statuses.pending'), $rows[$review->id]['status']);
            $this->assertStringContainsString(__('orders.reasons.stock_unavailable'), $rows[$review->id]['review']);
            $this->assertSame('285.00 EGP', $rows[$review->id]['total_price']);
        }
    }

    public function test_search_uses_saved_customer_name_and_order_number_and_supports_pagination(): void
    {
        $this->actingAs($this->admin(), 'admin');
        $new = $this->order(['user_name' => 'Another Customer']);
        $old = $this->order(['user_name' => 'Historic Customer']);
        $old->forceFill(['created_at' => now()->subDay()])->save();

        $this->table(['search' => ['value' => 'Historic']])->assertOk()
            ->assertJsonPath('recordsFiltered', 1)->assertJsonPath('data.0.id', $old->id)
            ->assertJsonPath('data.0.user_name', 'Historic Customer');
        $this->table(['search' => ['value' => (string) $new->id]])->assertOk()
            ->assertJsonPath('recordsFiltered', 1)->assertJsonPath('data.0.id', $new->id);
        $this->table(['length' => 1])->assertOk()->assertJsonCount(1, 'data')
            ->assertJsonPath('recordsTotal', 2)->assertJsonPath('data.0.id', $new->id);
        $this->table(['length' => 1, 'start' => 1])->assertOk()->assertJsonPath('data.0.id', $old->id);
        $this->table(['search' => ['value' => 'missing-name']])->assertOk()
            ->assertJsonPath('recordsFiltered', 0)->assertJsonCount(0, 'data');
    }

    public function test_review_reasons_are_translated_and_unknown_reasons_do_not_leak_raw_content(): void
    {
        $this->actingAs($this->admin(), 'admin');
        $coupon = $this->order(['payment_review_reason' => 'coupon_unavailable']);
        $recorded = $this->order(['payment_review_reason' => 'payment_recorded_order_pending']);
        $unknown = $this->order(['user_name' => '<script>alert(1)</script>',
            'payment_review_reason' => '<script>internal_reason</script>', 'status' => 'delivered']);
        $cancelled = $this->order(['status' => 'cancelled']);

        foreach (['ar', 'en'] as $locale) {
            app()->setLocale($locale);
            $response = $this->table()->assertOk();
            $rows = collect($response->json('data'))->keyBy('id');
            $this->assertStringContainsString(__('orders.reasons.coupon_unavailable'), $rows[$coupon->id]['review']);
            $this->assertStringContainsString(__('orders.reasons.payment_recorded_order_pending'), $rows[$recorded->id]['review']);
            $this->assertStringContainsString(__('orders.reasons.other'), $rows[$unknown->id]['review']);
            $this->assertStringContainsString(__('orders.statuses.delivered'), $rows[$unknown->id]['status']);
            $this->assertStringContainsString(__('orders.statuses.cancelled'), $rows[$cancelled->id]['status']);
            $this->assertStringNotContainsString('<script>', $rows[$unknown->id]['user_name']);
            $response->assertDontSee('internal_reason');
        }
    }

    public function test_empty_table_is_supported(): void
    {
        $this->actingAs($this->admin(), 'admin');
        $this->table()->assertOk()->assertJsonPath('recordsTotal', 0)->assertJsonCount(0, 'data');
    }

    public function test_opening_the_page_and_data_does_not_change_business_data_or_call_the_gateway(): void
    {
        $this->actingAs($this->admin(), 'admin');
        $coupon = Coupon::create(['code' => 'SAVE10', 'discount_percentage' => 10,
            'start_date' => now()->subDay()->toDateString(), 'expire_date' => now()->addDay()->toDateString(),
            'limit' => 10, 'times_used' => 2, 'reserved_uses' => 1, 'is_active' => true]);
        $category = Category::create(['name' => ['en' => 'Category'], 'slug' => 'category']);
        $product = Product::create(['category_id' => $category->id, 'name' => ['en' => 'Product'],
            'sku' => 'ORDERS-TEST', 'price' => 100, 'manage_stock' => true, 'quantity' => 5]);
        $order = $this->order(['payment_review_reason' => 'stock_unavailable', 'coupon_id' => $coupon->id,
            'coupon_code' => $coupon->code, 'coupon_usage_status' => 'reserved']);
        $order->items()->create(['product_id' => $product->id, 'product_name' => ['en' => 'Product'],
            'product_quantity' => 2, 'product_price' => 100]);
        Transaction::create(['order_id' => $order->id, 'payment_method' => 'myfatoorah',
            'invoice_id' => 'saved-invoice', 'transaction_id' => 'saved-payment']);
        $tables = ['orders', 'order_items', 'transactions', 'products', 'product_variants', 'coupons'];
        $before = [];
        foreach ($tables as $table) {
            $before[$table] = DB::table($table)->get()->toJson();
        }

        $this->get(route('dashboard.orders.index'))->assertOk();
        $this->table()->assertOk();
        foreach ($tables as $table) {
            $this->assertSame($before[$table], DB::table($table)->get()->toJson());
        }
        Http::assertNothingSent();
    }

    public function test_order_details_show_saved_data_and_all_transactions_in_both_languages_without_changes(): void
    {
        $this->actingAs($this->admin(), 'admin');
        $order = $this->order(['coupon_code' => 'SAVED10', 'coupon_discount_percentage' => 10,
            'coupon_usage_status' => 'used', 'payment_review_reason' => 'stock_unavailable']);
        User::findOrFail($order->user_id)->update(['name' => 'Current Account Name']);
        // بيانات العنصر تظل ظاهرة حتى بدون المنتج أو المتغير الأصلي.
        $order->items()->create(['product_name' => ['ar' => 'قميص محفوظ', 'en' => 'Saved shirt'],
            'product_quantity' => 2, 'product_price' => 150,
            'data' => ['ar' => ['اللون' => 'أحمر'], 'en' => ['Color' => 'Red']]]);
        Transaction::create(['order_id' => $order->id, 'payment_method' => 'myfatoorah',
            'invoice_id' => 'first-invoice', 'transaction_id' => null]);
        Transaction::create(['order_id' => $order->id, 'payment_method' => 'myfatoorah',
            'invoice_id' => 'second-invoice', 'transaction_id' => 'confirmed-payment']);
        $before = $order->fresh()->getAttributes();

        foreach (['ar' => ['قميص محفوظ', 'اللون', 'أحمر'], 'en' => ['Saved shirt', 'Color', 'Red']] as $locale => $texts) {
            app()->setLocale($locale);
            $this->get(route('dashboard.orders.show', $order->id))->assertOk()
                ->assertSee($texts)->assertSee('Saved Customer')->assertSee('Current Account Name')
                ->assertSee('Main Street')->assertSee('SAVED10')->assertSee('150.00')->assertSee('300.00')
                ->assertSee('285.00')->assertSee('first-invoice')->assertSee('second-invoice')
                ->assertSee('confirmed-payment')->assertSee(__('orders.payment_recorded'))
                ->assertSee(__('orders.reasons.stock_unavailable'));
        }
        $this->assertSame($before, $order->fresh()->getAttributes());
        $this->assertStringContainsString(route('dashboard.orders.show', $order->id),
            $this->table()->assertOk()->json('data.0.actions'));
        $empty = $this->order();
        $this->get(route('dashboard.orders.show', $empty->id))->assertOk()
            ->assertSee(__('orders.no_transactions'))->assertSee(__('orders.payment_unconfirmed'));
        Http::assertNothingSent();
    }

    public function test_order_details_require_admin_permission_and_missing_orders_return_404(): void
    {
        $order = $this->order();
        $url = route('dashboard.orders.show', $order->id);
        $this->get($url)->assertRedirect();
        $this->actingAs(User::factory()->create())->get($url)->assertRedirect();
        $admin = $this->admin([]);
        $this->actingAs($admin, 'admin')->get($url)->assertForbidden();
        $admin->role->update(['permissions' => ['orders']]);
        $this->actingAs($admin, 'admin')->get(route('dashboard.orders.show', 999999))->assertNotFound();
    }

    public function test_review_filter_and_global_completed_and_review_counts(): void
    {
        $this->actingAs($this->admin(), 'admin');
        $this->order(['status' => 'completed']);
        $this->order(['status' => 'delivered']);
        $review = $this->order(['user_name' => 'Review Match', 'payment_review_reason' => 'stock_unavailable']);
        $this->order(['status' => 'completed', 'payment_review_reason' => 'unknown_reason']);
        $this->order();

        foreach (['ar', 'en'] as $locale) {
            app()->setLocale($locale);
            $this->get(route('dashboard.orders.index'))->assertOk()
                ->assertViewHas('orderCounts', ['completed' => 2, 'needs_review' => 2])
                ->assertSee(__('orders.only_needs_review'));
        }
        $this->table(['needs_review' => 1])->assertOk()
            ->assertJsonPath('recordsTotal', 2)->assertJsonCount(2, 'data');
        $this->table(['needs_review' => 1, 'search' => ['value' => 'Review Match']])->assertOk()
            ->assertJsonPath('recordsFiltered', 1)->assertJsonPath('data.0.id', $review->id);
        $this->table(['needs_review' => 1, 'length' => 1])->assertOk()
            ->assertJsonPath('recordsFiltered', 2)->assertJsonCount(1, 'data');
        $this->table(['needs_review' => 0])->assertOk()->assertJsonPath('recordsTotal', 5);
        $this->assertSame('stock_unavailable', $review->fresh()->payment_review_reason);
        Http::assertNothingSent();
    }

    public function test_delivery_requires_only_completed_status_and_preserves_other_order_data(): void
    {
        $this->actingAs($this->admin(), 'admin');
        $order = $this->order(['status' => 'completed']);
        $before = $order->fresh()->getAttributes();
        foreach (['ar', 'en'] as $locale) {
            app()->setLocale($locale);
            $this->get(route('dashboard.orders.show', $order->id))->assertOk()
                ->assertSee(__('orders.mark_delivered'));
        }

        $this->patch(route('dashboard.orders.deliver', $order->id))
            ->assertRedirect(route('dashboard.orders.show', $order->id))
            ->assertSessionHas('success', __('orders.delivery_success'));
        $this->assertSame('delivered', $order->fresh()->status);
        unset($before['status'], $before['updated_at']);
        $after = $order->fresh()->getAttributes();
        unset($after['status'], $after['updated_at']);
        $this->assertSame($before, $after);
        $this->assertDatabaseCount('transactions', 0);
        $this->get(route('dashboard.orders.show', $order->id))->assertOk()
            ->assertDontSee(__('orders.mark_delivered'));
        Http::assertNothingSent();
    }

    public function test_delivery_rejects_unauthorized_requests_and_other_current_statuses(): void
    {
        $order = $this->order(['status' => 'completed']);
        $url = route('dashboard.orders.deliver', $order->id);
        $this->patch($url)->assertRedirect();
        $this->actingAs(User::factory()->create())->patch($url)->assertRedirect();
        $admin = $this->admin([]);
        $this->actingAs($admin, 'admin')->patch($url)->assertForbidden();
        $admin->role->update(['permissions' => ['orders']]);

        foreach (['pending', 'cancelled', 'delivered'] as $status) {
            $order->update(['status' => $status]);
            $this->from(route('dashboard.orders.show', $order->id))->patch($url)
                ->assertSessionHasErrors('order');
            $this->assertSame($status, $order->fresh()->status);
        }
        $this->patch(route('dashboard.orders.deliver', 999999))->assertNotFound();
    }

    public function test_stock_recheck_completes_paid_orders_once_and_preserves_payment_and_coupon_in_both_languages(): void
    {
        $this->actingAs($this->admin(), 'admin');
        $coupon = Coupon::create(['code' => 'STOCK10', 'discount_percentage' => 10,
            'start_date' => now()->subDay()->toDateString(), 'expire_date' => now()->addDay()->toDateString(),
            'limit' => 10, 'times_used' => 2, 'reserved_uses' => 1, 'is_active' => true]);
        $couponBefore = $coupon->fresh()->getAttributes();

        foreach (['ar', 'en'] as $locale) {
            app()->setLocale($locale);
            $product = $this->stockProduct();
            $variant = $product->variants()->create(['price' => 100, 'stock' => 5]);
            $order = $this->stockReviewOrder(['coupon_id' => $coupon->id,
                'coupon_code' => $coupon->code, 'coupon_usage_status' => 'used']);
            $this->stockItem($order, $product);
            $this->stockItem($order, $product, $variant->id);
            // وجود محاولة غير مدفوعة لا يلغي دليل الدفع في المعاملة الأخرى.
            Transaction::create(['order_id' => $order->id, 'payment_method' => 'myfatoorah',
                'invoice_id' => uniqid('unpaid-')]);
            $transactionsBefore = $order->transactions()->get()->toJson();
            $before = $order->fresh()->getAttributes();
            $show = route('dashboard.orders.show', $order->id);
            $url = route('dashboard.orders.recheck-stock', $order->id);

            $this->get($show)->assertOk()->assertSee(__('orders.recheck_stock'))->assertSee($url, false);
            $this->from($show)->patch($url)->assertRedirect($show)
                ->assertSessionHas('success', __('orders.stock_recheck_success'));
            $this->get($show)->assertOk()->assertSee(__('orders.stock_recheck_success'))
                ->assertDontSee(__('orders.recheck_stock'));
            $this->assertSame('completed', $order->fresh()->status);
            $this->assertNull($order->fresh()->payment_review_reason);
            $this->assertEquals(3, $product->fresh()->quantity);
            $this->assertEquals(3, $variant->fresh()->stock);
            $after = $order->fresh()->getAttributes();
            unset($before['status'], $before['payment_review_reason'], $before['updated_at']);
            unset($after['status'], $after['payment_review_reason'], $after['updated_at']);
            $this->assertSame($before, $after);
            $this->assertSame($transactionsBefore, $order->transactions()->get()->toJson());
            $this->assertSame($couponBefore, $coupon->fresh()->getAttributes());

            $this->from($show)->patch($url)->assertSessionHasErrors(['order' => __('orders.stock_recheck_requires_review')]);
            $this->assertEquals(3, $product->fresh()->quantity);
            $this->assertEquals(3, $variant->fresh()->stock);
        }
        Http::assertNothingSent();
    }

    public function test_stock_recheck_rolls_back_earlier_deductions_and_can_succeed_after_restocking(): void
    {
        $this->actingAs($this->admin(), 'admin');
        foreach (['ar', 'en'] as $locale) {
            app()->setLocale($locale);
            $product = $this->stockProduct();
            $variant = $product->variants()->create(['price' => 100, 'stock' => 1]);
            $order = $this->stockReviewOrder();
            $this->stockItem($order, $product);
            $this->stockItem($order, $product, $variant->id);
            $before = $order->fresh()->getAttributes();
            $show = route('dashboard.orders.show', $order->id);
            $url = route('dashboard.orders.recheck-stock', $order->id);

            $this->from($show)->patch($url)->assertRedirect($show)
                ->assertSessionHasErrors(['order' => __('orders.stock_recheck_unavailable')]);
            $this->get($show)->assertOk()->assertSee(__('orders.stock_recheck_unavailable'));
            $this->assertEquals(5, $product->fresh()->quantity);
            $this->assertEquals(1, $variant->fresh()->stock);
            $this->assertSame($before, $order->fresh()->getAttributes());

            $variant->update(['stock' => 4]);
            $this->patch($url)->assertRedirect($show)->assertSessionHasNoErrors();
            $this->assertSame('completed', $order->fresh()->status);
            $this->assertEquals(3, $product->fresh()->quantity);
            $this->assertEquals(2, $variant->fresh()->stock);
        }
        Http::assertNothingSent();
    }

    public function test_stock_recheck_rejects_missing_products_variants_and_empty_orders_without_deducting_stock(): void
    {
        $this->actingAs($this->admin(), 'admin');
        foreach (['product', 'variant', 'empty'] as $missing) {
            $product = $this->stockProduct();
            $order = $this->stockReviewOrder();
            if ($missing === 'product') {
                $this->stockItem($order, $product);
                $product->delete();
            } elseif ($missing === 'variant') {
                $variant = $product->variants()->create(['price' => 100, 'stock' => 5]);
                $this->stockItem($order, $product, $variant->id);
                $variant->delete();
            }
            $before = $order->fresh()->getAttributes();

            $this->patch(route('dashboard.orders.recheck-stock', $order->id))
                ->assertSessionHasErrors(['order' => __('orders.stock_recheck_unavailable')]);
            $this->assertSame($before, $order->fresh()->getAttributes());
            if ($missing !== 'product') {
                $this->assertEquals(5, $product->fresh()->quantity);
            }
        }
    }

    public function test_stock_recheck_rejects_other_states_review_reasons_and_unconfirmed_payments(): void
    {
        $this->actingAs($this->admin(), 'admin');
        $product = $this->stockProduct();
        foreach (['ar', 'en'] as $locale) {
            app()->setLocale($locale);
            foreach ([['completed', 'stock_unavailable'], ['delivered', 'stock_unavailable'],
                ['cancelled', 'stock_unavailable'], ['pending', null], ['pending', 'coupon_unavailable'],
                ['pending', 'payment_recorded_order_pending']] as [$status, $reason]) {
                $order = $this->stockReviewOrder(['status' => $status, 'payment_review_reason' => $reason]);
                $this->stockItem($order, $product);
                $before = $order->fresh()->getAttributes();
                $show = route('dashboard.orders.show', $order->id);
                $this->get($show)->assertOk()->assertDontSee(__('orders.recheck_stock'));
                $this->from($show)->patch(route('dashboard.orders.recheck-stock', $order->id))
                    ->assertSessionHasErrors(['order' => __('orders.stock_recheck_requires_review')]);
                $this->assertSame($before, $order->fresh()->getAttributes());
            }

            foreach ([null, ''] as $paymentId) {
                $order = $this->stockReviewOrder();
                $order->transactions()->update(['transaction_id' => $paymentId]);
                $this->stockItem($order, $product);
                $this->get(route('dashboard.orders.show', $order->id))->assertOk()->assertDontSee(__('orders.recheck_stock'));
                $this->patch(route('dashboard.orders.recheck-stock', $order->id))
                    ->assertSessionHasErrors(['order' => __('orders.stock_recheck_requires_payment')]);
                $this->assertSame('pending', $order->fresh()->status);
                $this->assertSame('stock_unavailable', $order->fresh()->payment_review_reason);
            }
        }
        $this->assertEquals(5, $product->fresh()->quantity);
        Http::assertNothingSent();
    }

    public function test_stock_recheck_requires_admin_permission_and_missing_orders_return_404(): void
    {
        $order = $this->stockReviewOrder();
        $product = $this->stockProduct();
        $this->stockItem($order, $product);
        $url = route('dashboard.orders.recheck-stock', $order->id);
        $this->patch($url)->assertRedirect();
        $this->actingAs(User::factory()->create())->patch($url)->assertRedirect();
        $admin = $this->admin([]);
        $this->actingAs($admin, 'admin')->patch($url)->assertForbidden();
        $admin->role->update(['permissions' => ['orders']]);
        $this->patch(route('dashboard.orders.recheck-stock', 999999))->assertNotFound();
        $this->assertEquals(5, $product->fresh()->quantity);
        $this->assertSame('pending', $order->fresh()->status);
    }

    public function test_cancel_detected_payment_only_shows_a_translated_error_without_fulfilling(): void
    {
        $this->actingAs($this->admin(), 'admin');
        $gateway = $this->mock(\App\Services\website\MyFatoorahService::class);
        $gateway->shouldReceive('getInvoiceStatus')->twice()->andReturn([]);
        $gateway->shouldReceive('getInvoicePaymentResult')->twice()->andReturn(['status' => 'paid', 'payment_id' => '07001']);
        $gateway->shouldNotReceive('getPaymentStatus');
        $gateway->shouldNotReceive('isPaymentSuccessful');

        foreach (['ar', 'en'] as $locale) {
            app()->setLocale($locale);
            $order = $this->order();
            $product = $this->stockProduct();
            $this->stockItem($order, $product);
            Transaction::create(['order_id' => $order->id, 'payment_method' => 'myfatoorah',
                'invoice_id' => uniqid('cancel-invoice-')]);
            $before = $order->fresh()->getAttributes();
            $transactionsBefore = $order->transactions()->get()->toJson();
            $show = route('dashboard.orders.show', $order->id);

            $this->from($show)->patch(route('dashboard.orders.cancel', $order->id))
                ->assertRedirect($show)
                ->assertSessionHasErrors(['order' => __('orders.cancellation_payment_detected')]);
            $this->get($show)->assertOk()->assertSee(__('orders.cancellation_payment_detected'));
            $this->assertSame($before, $order->fresh()->getAttributes());
            $this->assertSame($transactionsBefore, $order->transactions()->get()->toJson());
            $this->assertEquals(5, $product->fresh()->quantity);
        }
        Http::assertNothingSent();
    }

    public function test_cancelling_an_unpaid_order_releases_its_coupon_reservation_only_once(): void
    {
        $this->actingAs($this->admin(), 'admin');
        $coupon = Coupon::create(['code' => 'CANCEL10', 'discount_percentage' => 10,
            'start_date' => now()->subDay()->toDateString(), 'expire_date' => now()->addDay()->toDateString(),
            'limit' => 10, 'times_used' => 2, 'reserved_uses' => 3, 'is_active' => true]);
        $order = $this->order(['coupon_id' => $coupon->id, 'coupon_code' => $coupon->code,
            'coupon_usage_status' => 'reserved', 'payment_expires_at' => now()->subMinute()]);
        $product = $this->stockProduct();
        $this->stockItem($order, $product);
        Transaction::create(['order_id' => $order->id, 'payment_method' => 'myfatoorah',
            'invoice_id' => 'cancel-coupon-invoice', 'invoice_expires_at' => $order->payment_expires_at]);
        $gateway = $this->mock(\App\Services\website\MyFatoorahService::class);
        $gateway->shouldReceive('getInvoiceStatus')->with('cancel-coupon-invoice')->once()->andReturn([]);
        $gateway->shouldReceive('getInvoicePaymentResult')->once()->andReturn(['status' => 'unpaid', 'payment_id' => null]);
        $total = $order->total_price;
        $discount = $order->coupon_discount_amount;
        $show = route('dashboard.orders.show', $order->id);
        $cancel = route('dashboard.orders.cancel', $order->id);

        $this->from($show)->patch($cancel)->assertRedirect($show)->assertSessionHasNoErrors()
            ->assertSessionHas('success', __('orders.cancellation_success'));
        $this->assertSame('cancelled', $order->fresh()->status);
        $this->assertSame('released', $order->fresh()->coupon_usage_status);
        $this->assertEquals(2, $coupon->fresh()->reserved_uses);

        $this->from($show)->patch($cancel)->assertRedirect($show)
            ->assertSessionHasErrors(['order' => __('orders.cancellation_requires_pending')]);
        $this->assertEquals(2, $coupon->fresh()->reserved_uses);
        $this->assertEquals(2, $coupon->fresh()->times_used);
        $this->assertSame($total, $order->fresh()->total_price);
        $this->assertSame($discount, $order->fresh()->coupon_discount_amount);
        $this->assertEquals(5, $product->fresh()->quantity);
        Http::assertNothingSent();
    }

    public function test_cancellation_handles_no_coupon_and_an_already_released_reservation(): void
    {
        $this->actingAs($this->admin(), 'admin');
        $coupon = Coupon::create(['code' => 'RELEASED10', 'discount_percentage' => 10,
            'start_date' => now()->subDay()->toDateString(), 'expire_date' => now()->addDay()->toDateString(),
            'limit' => 10, 'times_used' => 2, 'reserved_uses' => 3, 'is_active' => true]);
        $gateway = $this->mock(\App\Services\website\MyFatoorahService::class);
        $gateway->shouldReceive('getInvoiceStatus')->twice()->andReturn([]);
        $gateway->shouldReceive('getInvoicePaymentResult')->twice()->andReturn(['status' => 'unpaid', 'payment_id' => null]);

        foreach ([null, 'released'] as $couponStatus) {
            $order = $this->order(['coupon_id' => $couponStatus ? $coupon->id : null,
                'coupon_usage_status' => $couponStatus, 'payment_expires_at' => now()->subMinute()]);
            Transaction::create(['order_id' => $order->id, 'payment_method' => 'myfatoorah',
                'invoice_id' => 'released-invoice-'.$order->id, 'invoice_expires_at' => $order->payment_expires_at]);

            $this->patch(route('dashboard.orders.cancel', $order->id))->assertRedirect()->assertSessionHasNoErrors();
            $this->assertSame('cancelled', $order->fresh()->status);
            $this->assertSame($couponStatus, $order->fresh()->coupon_usage_status);
            $this->assertEquals(3, $coupon->fresh()->reserved_uses);
            $this->assertEquals(2, $coupon->fresh()->times_used);
        }
        Http::assertNothingSent();
    }

    public function test_cancellation_is_rolled_back_if_coupon_release_fails(): void
    {
        $coupon = Coupon::create(['code' => 'INCONSISTENT10', 'discount_percentage' => 10,
            'start_date' => now()->subDay()->toDateString(), 'expire_date' => now()->addDay()->toDateString(),
            'limit' => 10, 'times_used' => 2, 'reserved_uses' => 0, 'is_active' => true]);
        $order = $this->order(['coupon_id' => $coupon->id, 'coupon_usage_status' => 'reserved',
            'payment_expires_at' => now()->subMinute()]);
        Transaction::create(['order_id' => $order->id, 'payment_method' => 'myfatoorah',
            'invoice_id' => 'inconsistent-coupon-invoice', 'invoice_expires_at' => $order->payment_expires_at]);
        $gateway = $this->mock(\App\Services\website\MyFatoorahService::class);
        $gateway->shouldReceive('getInvoiceStatus')->once()->andReturn([]);
        $gateway->shouldReceive('getInvoicePaymentResult')->once()->andReturn(['status' => 'unpaid', 'payment_id' => null]);

        try {
            $this->app->make(\App\Services\dashboard\OrderService::class)->cancelOrder($order->id);
            $this->fail('Cancellation should fail when the coupon reservation counter is inconsistent.');
        } catch (\RuntimeException $e) {
            $this->assertSame('Coupon reservation counter is inconsistent.', $e->getMessage());
        }

        $this->assertSame('pending', $order->fresh()->status);
        $this->assertSame('reserved', $order->fresh()->coupon_usage_status);
        $this->assertEquals(0, $coupon->fresh()->reserved_uses);
        $this->assertEquals(2, $coupon->fresh()->times_used);
        Http::assertNothingSent();
    }

    public function test_cancellation_rejects_payment_recorded_during_invoice_inquiry(): void
    {
        $this->actingAs($this->admin(), 'admin');
        $order = $this->order(['payment_expires_at' => now()->subMinute()]);
        $transaction = $order->transactions()->create([
            'payment_method' => 'myfatoorah',
            'invoice_id' => 'cancel-concurrent-invoice',
            'invoice_expires_at' => $order->payment_expires_at,
        ]);
        $gateway = $this->mock(\App\Services\website\MyFatoorahService::class);
        $gateway->shouldReceive('getInvoiceStatus')->once()->andReturnUsing(function () use ($transaction) {
            $transaction->update(['transaction_id' => 'recorded-payment']);

            return [];
        });
        $gateway->shouldReceive('getInvoicePaymentResult')->once()->andReturn(['status' => 'unpaid', 'payment_id' => null]);

        $show = route('dashboard.orders.show', $order->id);
        $this->from($show)->patch(route('dashboard.orders.cancel', $order->id))
            ->assertRedirect($show)
            ->assertSessionHasErrors(['order' => __('orders.cancellation_payment_detected')]);

        $this->assertSame('pending', $order->fresh()->status);
        $this->assertSame('recorded-payment', $transaction->fresh()->transaction_id);
    }

    private function stockReviewOrder(array $overrides = []): Order
    {
        $order = $this->order(array_replace(['payment_review_reason' => 'stock_unavailable'], $overrides));
        Transaction::create(['order_id' => $order->id, 'payment_method' => 'myfatoorah',
            'invoice_id' => uniqid('stock-invoice-'), 'transaction_id' => uniqid('stock-payment-')]);

        return $order;
    }

    private function stockProduct(): Product
    {
        $category = Category::create(['name' => ['en' => uniqid('Stock category ')], 'slug' => uniqid('stock-category-')]);

        return Product::create(['category_id' => $category->id, 'name' => ['ar' => 'منتج', 'en' => 'Product'],
            'sku' => uniqid('stock-'), 'price' => 100, 'manage_stock' => true, 'quantity' => 5]);
    }

    private function stockItem(Order $order, Product $product, ?int $variantId = null): void
    {
        $order->items()->create(['product_id' => $product->id, 'product_variant_id' => $variantId,
            'product_name' => ['ar' => 'منتج', 'en' => 'Product'], 'product_quantity' => 2, 'product_price' => 100,
            'data' => $variantId ? ['ar' => ['اللون' => 'أحمر'], 'en' => ['Color' => 'Red']] : null]);
    }

    private function table(array $overrides = [])
    {
        // اختبارات Laravel تشارك التطبيق بين الطلبات؛ نجدد طلب Yajra لكل استدعاء.
        $this->app->forgetInstance('datatables.request');
        $columns = [];
        foreach (['id', 'user_name', 'user_phone', 'created_at', 'total_price', 'status', 'payment_status', 'review', 'actions'] as $name) {
            $columns[] = ['data' => $name, 'name' => '',
                'searchable' => in_array($name, ['id', 'user_name']) ? 'true' : 'false',
                'orderable' => in_array($name, ['payment_status', 'review', 'actions']) ? 'false' : 'true',
                'search' => ['value' => '', 'regex' => 'false']];
        }
        $query = array_replace(['draw' => 1, 'start' => 0, 'length' => 10, 'columns' => $columns,
            'order' => [['column' => 3, 'dir' => 'desc'], ['column' => 0, 'dir' => 'desc']],
            'search' => ['value' => '', 'regex' => 'false']], $overrides);

        return $this->getJson(route('dashboard.orders.index').'?'.http_build_query($query),
            ['X-Requested-With' => 'XMLHttpRequest']);
    }

    private function admin(array $permissions = ['orders']): Admin
    {
        $role = Role::create(['name' => 'Orders role', 'permissions' => $permissions]);
        return Admin::create(['name' => 'Orders Admin', 'email' => 'admin@example.test',
            'password' => bcrypt('test-password'), 'role_id' => $role->id]);
    }

    private function order(array $overrides = []): Order
    {
        return Order::create(array_replace([
            'user_id' => User::factory()->create()->id, 'user_name' => 'Saved Customer',
            'user_phone' => '01000000000', 'user_email' => 'customer@example.test',
            'price' => 300, 'shipping_price' => 15, 'total_price' => 285,
            'coupon_discount_amount' => 30, 'status' => 'pending', 'note' => '',
            'country' => 'Egypt', 'governorate' => 'Cairo', 'city' => 'Nasr City', 'street' => 'Main Street',
        ], $overrides));
    }
}
