<?php

namespace Tests\Feature\Website;

use App\Models\Dashboard\Order;
use App\Models\Dashboard\Category;
use App\Models\Dashboard\Product;
use App\Models\Dashboard\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PaymentCallbackTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.myfatoorah.token' => 'test-token', 'services.myfatoorah.currency' => 'EGP']);
        Http::preventStrayRequests();
    }

    public function test_paid_callback_completes_matching_order_without_a_session_and_is_repeatable(): void
    {
        $order = $this->pendingOrder();
        Http::fake(['*/v3/payments/999' => Http::response($this->paidResponse())]);
        for ($attempt = 0; $attempt < 2; $attempt++) {
            $this->get('/payment/callback?paymentId=999')
                ->assertRedirect(route('website.checkout.success', ['order' => $order->id]));
        }
        $this->assertSame('completed', $order->fresh()->status);
        $this->assertDatabaseHas('transactions', ['order_id' => $order->id, 'transaction_id' => '456']);
        $this->assertDatabaseCount('transactions', 1);
    }

    public function test_failed_payment_stays_on_store_instead_of_redirecting_to_gateway_referer(): void
    {
        $order = $this->pendingOrder();
        $response = $this->paidResponse();
        $response['Data']['Invoice']['Status'] = 'UNPAID';
        Http::fake(['*/v3/payments/999' => Http::response($response)]);
        $this->withHeader('Referer', 'https://demo.myfatoorah.com/')
            ->get('/payment/callback?paymentId=999')->assertStatus(422)
            ->assertSee(__('checkout_success.payment_unconfirmed'));
        $this->assertSame('pending', $order->fresh()->status);
    }

    public function test_wrong_amount_or_currency_cannot_complete_the_order(): void
    {
        $order = $this->pendingOrder();
        foreach ([['ValueInDisplayCurrency' => '1', 'DisplayCurrency' => 'EGP'],
            ['ValueInDisplayCurrency' => '100', 'DisplayCurrency' => 'KWD']] as $amount) {
            $response = $this->paidResponse();
            $response['Data']['Amount'] = $amount;
            Http::fake(['*/v3/payments/999' => Http::response($response)]);
            $this->get('/payment/callback?paymentId=999')->assertStatus(422);
            $this->assertSame('pending', $order->fresh()->status);
            $this->assertDatabaseHas('transactions', ['order_id' => $order->id, 'transaction_id' => null]);
        }
    }

    public function test_missing_payment_id_does_not_call_provider(): void
    {
        Http::fake();
        $this->get('/payment/callback')->assertStatus(422);
        Http::assertNothingSent();
    }

    public function test_provider_connection_failure_shows_local_error_without_completing_order(): void
    {
        $order = $this->pendingOrder();
        Http::fake(fn () => throw new ConnectionException('Timeout'));
        $this->withHeader('Referer', 'https://demo.myfatoorah.com/')
            ->get('/payment/callback?paymentId=999')->assertStatus(422);
        $this->assertSame('pending', $order->fresh()->status);
    }

    private function pendingOrder(): Order
    {
        $order = Order::create([
            'user_id' => User::factory()->create()->id,
            'user_name' => 'Test', 'user_phone' => '01000000000', 'user_email' => 'test@example.test',
            'price' => 100, 'shipping_price' => 0, 'total_price' => 100, 'note' => '',
            'status' => 'pending', 'country' => 'Egypt', 'governorate' => 'Cairo',
            'city' => 'Cairo', 'street' => 'Test street',
        ]);
        $category = Category::create(['name' => ['en' => uniqid('Category')], 'slug' => uniqid('category-')]);
        $product = Product::create([
            'category_id' => $category->id, 'name' => ['en' => 'Product'], 'sku' => uniqid('sku-'),
            'price' => 100, 'manage_stock' => true, 'quantity' => 2,
        ]);
        $order->items()->create([
            'product_id' => $product->id, 'product_name' => ['en' => 'Product'],
            'product_quantity' => 1, 'product_price' => 100,
        ]);
        Transaction::create(['order_id' => $order->id, 'invoice_id' => '123', 'payment_method' => 'myfatoorah']);
        return $order;
    }

    private function paidResponse(): array
    {
        return ['IsSuccess' => true, 'Data' => [
            'Invoice' => ['Id' => '123', 'Status' => 'PAID'],
            'Transaction' => ['Id' => '456', 'Status' => 'SUCCESS'],
            'Amount' => ['DisplayCurrency' => 'EGP', 'ValueInDisplayCurrency' => '100'],
        ]];
    }
}
