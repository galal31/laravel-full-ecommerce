<?php

namespace Tests\Feature\Website;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_route_requires_authentication(): void
    {
        $this->withoutMiddleware([
            LocaleSessionRedirect::class,
            LaravelLocalizationRedirectFilter::class,
        ]);

        $this->get(route('website.checkout.index'))
            ->assertRedirect(route('website.login'));
    }

    public function test_address_dropdown_shows_active_governorates_and_dispatches_shipping(): void
    {
        $countryId = DB::table('countries')->insertGetId([
            'name' => json_encode(['en' => 'Egypt', 'ar' => 'مصر']),
            'phone_code' => '+20',
            'status' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $activeGovernorateId = DB::table('governorates')->insertGetId([
            'country_id' => $countryId,
            'name' => json_encode(['en' => 'Cairo', 'ar' => 'القاهرة']),
            'status' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('governorates')->insert([
            'country_id' => $countryId,
            'name' => json_encode(['en' => 'Hidden', 'ar' => 'مخفي']),
            'status' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('shipping_governorates')->insert([
            'governorate_id' => $activeGovernorateId,
            'price' => 45.50,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Livewire::test('general.address-dropdown', ['includeShipping' => true])
            ->set('country_id', $countryId)
            ->assertSee('Cairo')
            ->assertDontSee('Hidden')
            ->set('governorate_id', $activeGovernorateId)
            ->assertDispatched('shipping_price_updated', shippingPrice: 45.50);
    }

    public function test_order_summary_receives_the_live_shipping_price(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test('website.checkout.order-summary', ['cartItems' => collect()])
            ->dispatch('shipping_price_updated', shippingPrice: 35)
            ->assertSet('shippingPrice', 35.0);
    }
}
