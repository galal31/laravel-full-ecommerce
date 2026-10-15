<?php

namespace Tests\Feature\Website;

use App\Models\Dashboard\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Tests\TestCase;

class CheckoutSuccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([LocaleSessionRedirect::class, LaravelLocalizationRedirectFilter::class]);
    }

    public function test_owner_can_see_completed_order_in_both_languages(): void
    {
        $user = User::factory()->create();
        $order = $this->orderFor($user);
        $order->items()->create([
            'product_name' => ['ar' => 'تيشيرت قطن', 'en' => 'Cotton T-shirt'],
            'product_quantity' => 2, 'product_price' => 150,
            'data' => ['ar' => ['اللون' => 'أزرق', 'المقاس' => 'متوسط'], 'en' => ['Color' => 'Blue', 'Size' => 'M']],
        ]);
        $this->actingAs($user);
        foreach (['ar' => 'تيشيرت قطن', 'en' => 'Cotton T-shirt'] as $locale => $name) {
            app()->setLocale($locale);
            $response = $this->get(route('website.checkout.success', ['order' => $order->id]));
            $response->assertOk()->assertSee($name)->assertSee('285.00')
                ->assertSee(__('checkout_success.confirmed'));
        }
    }

    public function test_another_users_order_is_hidden(): void
    {
        $order = $this->orderFor(User::factory()->create());
        $this->actingAs(User::factory()->create())
            ->get(route('website.checkout.success', ['order' => $order->id]))->assertNotFound();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $order = $this->orderFor(User::factory()->create());
        $this->get(route('website.checkout.success', ['order' => $order->id]))->assertRedirect();
    }

    public function test_pending_order_does_not_display_confirmation(): void
    {
        $user = User::factory()->create();
        $order = $this->orderFor($user, 'pending');
        $this->actingAs($user)->get(route('website.checkout.success', ['order' => $order->id]))
            ->assertOk()->assertSee(__('checkout_success.received'))
            ->assertDontSee(__('checkout_success.confirmed'));
        $this->assertSame('pending', $order->fresh()->status);
    }

    public function test_paid_order_needing_review_has_clear_message_in_both_languages(): void
    {
        $user = User::factory()->create();
        $order = $this->orderFor($user, 'pending');
        $order->update(['payment_review_reason' => 'stock_unavailable']);
        $this->actingAs($user);

        foreach (['ar', 'en'] as $locale) {
            app()->setLocale($locale);
            $this->get(route('website.checkout.success', ['order' => $order->id]))
                ->assertOk()->assertSee(__('checkout_success.payment_received_review'))
                ->assertSee(__('checkout_success.payment_received_review_hint'))
                ->assertSee(__('checkout_success.in_review'))
                ->assertDontSee(__('checkout_success.confirmed'));
        }
    }

    private function orderFor(User $user, string $status = 'completed'): Order
    {
        return Order::create([
            'user_id' => $user->id, 'user_name' => 'Test Customer',
            'user_phone' => '01000000000', 'user_email' => 'customer@example.test',
            'price' => 300, 'shipping_price' => 15, 'total_price' => 285,
            'coupon_discount_amount' => 30, 'status' => $status, 'note' => '',
            'country' => 'Egypt', 'governorate' => 'Cairo', 'city' => 'Nasr City', 'street' => 'Main Street',
        ]);
    }
}
